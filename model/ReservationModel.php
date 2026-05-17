<?php

/**
 * Schéma cours : `reserver`. Schéma projet : `reservation` (+ workflow voir CONCEPTION/location_workflow.sql).
 */
class ReservationModel extends AbstractModel
{
    protected string $table = 'reserver';

    protected string $primaryKey = 'id';

    private function resolvedTable(): string
    {
        static $t = null;
        if ($t !== null) {
            return $t;
        }
        try {
            $stmt = $this->pdo->query(
                "SELECT COUNT(*) FROM information_schema.TABLES
                 WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'reservation'"
            );
            if ($stmt && (int) $stmt->fetchColumn() > 0) {
                return $t = 'reservation';
            }
        } catch (Throwable $e) {
        }

        return $t = 'reserver';
    }

    /** Workflow statuts réservé à la table `reservation` (colonnes statut / paiement). */
    public function hasReservationWorkflow(): bool
    {
        return $this->resolvedTable() === 'reservation' && $this->tableHasColumn('reservation', 'statut');
    }

    private function ensureDateDimensionRow(string $dateReservation): void
    {
        if (!$this->tableHasColumn('date_', 'date_reservation')) {
            return;
        }
        try {
            $this->executerReq(
                'INSERT IGNORE INTO `date_` (date_reservation) VALUES (:d)',
                ['d' => $dateReservation]
            );
        } catch (Throwable $e) {
        }
    }

    /**
     * Phase affichée client : tient compte des dates (en cours / terminée) même sans mise à jour SQL.
     *
     * @param array<string, mixed> $row Ligne issue de findByUserIdForCompte
     */
    public static function phaseVisuelle(array $row): string
    {
        $s = (string) ($row['statut_code'] ?? $row['statut'] ?? 'en_attente');
        if ($s === 'annulee') {
            return 'annulee';
        }
        $d0 = (string) ($row['debut_sql'] ?? '');
        $d1 = (string) ($row['fin_sql'] ?? '');
        $today = date('Y-m-d');
        if ($d0 !== '' && $d1 !== '' && ($s === 'confirmee_ligne' || $s === 'confirmee_agence')) {
            if ($d1 < $today) {
                return 'terminee';
            }
            if ($d0 <= $today && $d1 >= $today) {
                return 'en_cours';
            }
        }

        return $s;
    }

    /**
     * @return array{label: string, class: string}
     */
    public static function badgePhase(string $phase): array
    {
        $map = [
            'en_attente' => ['label' => 'En attente de validation', 'class' => 'warning'],
            'confirmee_ligne' => ['label' => 'Confirmée — paiement en ligne', 'class' => 'success'],
            'confirmee_agence' => ['label' => 'Confirmée — agence', 'class' => 'success'],
            'en_cours' => ['label' => 'Location en cours', 'class' => 'primary'],
            'terminee' => ['label' => 'Terminée', 'class' => 'secondary'],
            'annulee' => ['label' => 'Annulée', 'class' => 'danger'],
        ];

        return $map[$phase] ?? ['label' => $phase, 'class' => 'secondary'];
    }

    public function reservationsByClient(User $user): array
    {
        $tbl = $this->resolvedTable();
        $p = $this->personneFkColumnFor($tbl);
        $stmt = $this->executerReq(
            "SELECT r.*, DATE_FORMAT(r.debut, '%d/%m/%Y') AS debut_formatted, 
                    DATE_FORMAT(r.fin, '%d/%m/%Y') AS fin_formatted
             FROM `{$tbl}` r
             WHERE r.`{$p}` = :uid",
            ['uid' => $user->getId()]
        );

        $reservations = [];
        $vehMdl = new VehiculeModel();

        while ($res = $stmt->fetch()) {
            $reservation = new Reservation($res);
            $reservation->setPersonne($user);
            $reservation->setVehicule($vehMdl->getVehiculeById((int) $res['id_vehicule']));
            $reservations[] = $reservation;
        }

        return $reservations;
    }

    public function ajouter(Reservation $reservation): bool
    {
        $tbl = $this->resolvedTable();
        $p = $this->personneFkColumnFor($tbl);
        $ts = date('Y-m-d H:i:s');

        $debut = (string) $reservation->getDebut();
        $fin = (string) $reservation->getFin();
        if (strlen($debut) === 10) {
            $debut .= ' 00:00:00';
        }
        if (strlen($fin) === 10) {
            $fin .= ' 00:00:00';
        }

        if ($tbl === 'reserver') {
            $this->ensureDateDimensionRow($ts);
        }

        $data = [
            $p => $reservation->getPersonne()->getId(),
            'id_vehicule' => $reservation->getVehicule()->getId(),
            'debut' => $debut,
            'fin' => $fin,
            'date_reservation' => $ts,
        ];
        if ($tbl === 'reservation' && $this->tableHasColumn($tbl, 'message')) {
            $data['message'] = $reservation->getMessage();
        }
        if ($tbl === 'reservation' && $this->tableHasColumn($tbl, 'statut')) {
            $data['statut'] = 'en_attente';
            if ($this->tableHasColumn($tbl, 'paiement_en_ligne')) {
                $data['paiement_en_ligne'] = 0;
            }
        }

        $this->table = $tbl;
        try {
            return $this->create($data);
        } finally {
            $this->table = 'reserver';
        }
    }

    public function findByUserIdForCompte(int $userId): array
    {
        $tbl = $this->resolvedTable();
        $p = $this->personneFkColumnFor($tbl);

        return $this->findByUserIdForCompteInner($userId, $tbl, $p, $this->hasReservationWorkflow());
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function findByUserIdForCompteInner(int $userId, string $tbl, string $p, bool $hasWf): array
    {
        if ($tbl === 'reservation') {
            $statSel = $hasWf ? 'r.statut AS statut_code' : "'confirmee_ligne' AS statut_code";
            $updSel = $this->tableHasColumn($tbl, 'updated_at') ? 'r.updated_at' : 'r.date_reservation';
            $sql = "SELECT r.id AS reservation_id,
                    r.id_vehicule,
                    r.date_reservation,
                    {$updSel} AS derniere_maj,
                    DATE(r.debut) AS debut_sql,
                    DATE(r.fin) AS fin_sql,
                    CONCAT(v.marque, ' ', v.modele) AS vehicule_nom,
                    DATE_FORMAT(r.debut, '%d/%m/%Y') AS date_debut,
                    DATE_FORMAT(r.fin, '%d/%m/%Y') AS date_fin,
                    v.prix_journalier AS prix,
                    (DATEDIFF(r.fin, r.debut) + 1) * v.prix_journalier AS montant_estime,
                    a.nom AS agence_nom,
                    {$statSel}
             FROM `{$tbl}` r
             INNER JOIN vehicule v ON v.id = r.id_vehicule
             LEFT JOIN agence a ON a.id = v.id_agence
             WHERE r.`{$p}` = :uid
             ORDER BY r.date_reservation DESC";
        } else {
            $sql = "SELECT 0 AS reservation_id,
                    r.id_vehicule,
                    r.date_reservation,
                    r.date_reservation AS derniere_maj,
                    DATE(r.debut) AS debut_sql,
                    DATE(r.fin) AS fin_sql,
                    CONCAT(v.marque, ' ', v.modele) AS vehicule_nom,
                    DATE_FORMAT(r.debut, '%d/%m/%Y') AS date_debut,
                    DATE_FORMAT(r.fin, '%d/%m/%Y') AS date_fin,
                    v.prix_journalier AS prix,
                    (DATEDIFF(r.fin, r.debut) + 1) * v.prix_journalier AS montant_estime,
                    NULL AS agence_nom,
                    'confirmee_ligne' AS statut_code
             FROM `{$tbl}` r
             INNER JOIN vehicule v ON v.id = r.id_vehicule
             WHERE r.`{$p}` = :uid
             ORDER BY r.date_reservation DESC";
        }

        $stmt = $this->executerReq($sql, ['uid' => $userId]);
        $rows = $stmt->fetchAll();
        foreach ($rows as &$row) {
            $phase = self::phaseVisuelle($row);
            $row['phase'] = $phase;
            $badge = self::badgePhase($phase);
            $row['statut_libelle'] = $badge['label'];
            $row['statut_class'] = $badge['class'];
        }
        unset($row);

        return $rows;
    }

    public function cancelIfAllowed(int $reservationId, int $userId): bool
    {
        $tbl = $this->resolvedTable();
        $p = $this->personneFkColumnFor($tbl);
        if ($tbl !== 'reservation' || $reservationId < 1) {
            return false;
        }
        if ($this->hasReservationWorkflow()) {
            $stmt = $this->executerReq(
                "UPDATE `{$tbl}` SET statut = 'annulee' WHERE id = :id AND `{$p}` = :u
                 AND DATE(debut) > CURDATE()
                 AND statut IN ('en_attente', 'confirmee_ligne', 'confirmee_agence')",
                ['id' => (string) $reservationId, 'u' => (string) $userId]
            );

            return $stmt->rowCount() > 0;
        }
        $stmt = $this->executerReq(
            "DELETE FROM `{$tbl}` WHERE id = :id AND `{$p}` = :u AND DATE(debut) > CURDATE()",
            ['id' => (string) $reservationId, 'u' => (string) $userId]
        );

        return $stmt->rowCount() > 0;
    }

    public function cancelReserverRow(int $userId, int $vehiculeId, string $dateReservation): bool
    {
        $tbl = $this->resolvedTable();
        if ($tbl !== 'reserver') {
            return false;
        }
        $p = $this->personneFkColumnFor($tbl);
        $stmt = $this->executerReq(
            "DELETE FROM `{$tbl}` WHERE `{$p}` = :u AND id_vehicule = :v
             AND date_reservation = :dr AND DATE(debut) > CURDATE()",
            ['u' => (string) $userId, 'v' => (string) $vehiculeId, 'dr' => $dateReservation]
        );

        return $stmt->rowCount() > 0;
    }

    public function finaliserPaiementEnLigne(int $reservationId, int $userId): bool
    {
        if (!$this->hasReservationWorkflow() || $reservationId < 1) {
            return false;
        }
        $p = $this->personneFkColumnFor('reservation');
        $sets = "statut = 'confirmee_ligne'";
        if ($this->tableHasColumn('reservation', 'paiement_en_ligne')) {
            $sets .= ', paiement_en_ligne = 1';
        }
        $stmt = $this->executerReq(
            "UPDATE `reservation` SET {$sets} WHERE id = :id AND `{$p}` = :u AND statut = 'en_attente'",
            ['id' => (string) $reservationId, 'u' => (string) $userId]
        );

        return $stmt->rowCount() > 0;
    }

    public function validerEnAgenceParAdmin(int $reservationId): bool
    {
        if (!$this->hasReservationWorkflow() || $reservationId < 1) {
            return false;
        }
        $stmt = $this->executerReq(
            "UPDATE `reservation` SET statut = 'confirmee_agence' WHERE id = :id AND statut = 'en_attente'",
            ['id' => (string) $reservationId]
        );

        return $stmt->rowCount() > 0;
    }

    public function userHasReservedVehicle(int $userId, int $vehiculeId): bool
    {
        $tbl = $this->resolvedTable();
        $p = $this->personneFkColumnFor($tbl);
        $extra = '';
        if ($tbl === 'reservation' && $this->tableHasColumn($tbl, 'statut')) {
            $extra = " AND (r.statut IS NULL OR r.statut <> 'annulee')";
        }
        $stmt = $this->executerReq(
            "SELECT 1 FROM `{$tbl}` r WHERE r.`{$p}` = :u AND r.id_vehicule = :v {$extra} LIMIT 1",
            ['u' => (string) $userId, 'v' => (string) $vehiculeId]
        );

        return (bool) $stmt->fetch();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getRecentForAdmin(int $limit = 12): array
    {
        $tbl = $this->resolvedTable();
        $p = $this->personneFkColumnFor($tbl);
        $lim = max(1, min(50, $limit));
        $stat = ($tbl === 'reservation' && $this->tableHasColumn('reservation', 'statut'))
            ? ', r.statut AS statut_reservation' : ", '' AS statut_reservation";

        if ($tbl === 'reservation') {
            $sql = "SELECT r.id, r.debut, r.fin, r.date_reservation, r.`{$p}` AS id_user, r.id_vehicule,
                    p.prenom, p.nom, p.login,
                    v.marque, v.modele{$stat}
             FROM `reservation` r
             INNER JOIN personne p ON p.id = r.`{$p}`
             INNER JOIN vehicule v ON v.id = r.id_vehicule
             ORDER BY r.date_reservation DESC
             LIMIT {$lim}";
        } else {
            $sql = "SELECT r.debut, r.fin, r.date_reservation, r.`{$p}` AS id_user, r.id_vehicule,
                    p.prenom, p.nom, p.login,
                    v.marque, v.modele,
                    '' AS statut_reservation
             FROM `reserver` r
             INNER JOIN personne p ON p.id = r.`{$p}`
             INNER JOIN vehicule v ON v.id = r.id_vehicule
             ORDER BY r.date_reservation DESC
             LIMIT {$lim}";
        }

        $stmt = $this->pdo->query($sql);

        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    public function countSince(string $dateYmd): int
    {
        $tbl = $this->resolvedTable();
        $cond = '';
        if ($tbl === 'reservation' && $this->tableHasColumn($tbl, 'statut')) {
            $cond = " AND statut <> 'annulee'";
        }
        $stmt = $this->executerReq(
            "SELECT COUNT(*) AS c FROM `{$tbl}` WHERE date_reservation >= :d {$cond}",
            ['d' => $dateYmd . ' 00:00:00']
        );
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) ($row['c'] ?? 0);
    }

    public function getRevenueMonthEstimate(): float
    {
        $tbl = $this->resolvedTable();
        $cond = '';
        if ($tbl === 'reservation' && $this->tableHasColumn($tbl, 'statut')) {
            $cond = " AND r.statut <> 'annulee'";
        }
        $stmt = $this->executerReq(
            "SELECT COALESCE(SUM((DATEDIFF(r.fin, r.debut) + 1) * v.prix_journalier), 0) AS rev
             FROM `{$tbl}` r
             INNER JOIN vehicule v ON v.id = r.id_vehicule
             WHERE YEAR(r.date_reservation) = YEAR(CURDATE())
               AND MONTH(r.date_reservation) = MONTH(CURDATE()) {$cond}",
            []
        );
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return (float) ($row['rev'] ?? 0);
    }

    public function getReservationTableName(): string
    {
        return $this->resolvedTable();
    }

    public function count(): int
    {
        $tbl = $this->resolvedTable();

        return (int) $this->pdo->query("SELECT COUNT(*) FROM `{$tbl}`")->fetchColumn();
    }
}
