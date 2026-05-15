<?php

/**
 * Schéma cours (CONCEPTION/bd.sql) : table `reserver`, clé composite, FK `date_reservation` → `date_`.
 * Alternative moderne : exécuter create_table_reservation.sql (table `reservation` avec id auto).
 */
class ReservationModel extends AbstractModel
{
    protected string $table = 'reserver';

    /** Non utilisé sur `reserver` (PK composite) ; conservé pour compatibilité AbstractModel. */
    protected string $primaryKey = 'id';

    /** Utiliser la table normalisée `reservation` si elle existe (script create_table_reservation.sql). */
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

        $this->table = $tbl;
        $ok = $this->create($data);
        $this->table = 'reserver';

        return $ok;
    }

    public function findByUserIdForCompte(int $userId): array
    {
        $tbl = $this->resolvedTable();
        $p = $this->personneFkColumnFor($tbl);

        if ($tbl === 'reservation') {
            $sql = "SELECT r.id AS reservation_id,
                    r.id_vehicule,
                    r.date_reservation,
                    DATE(r.debut) AS debut_sql,
                    DATE(r.fin) AS fin_sql,
                    CONCAT(v.marque, ' ', v.modele) AS vehicule_nom,
                    DATE_FORMAT(r.debut, '%d/%m/%Y') AS date_debut,
                    DATE_FORMAT(r.fin, '%d/%m/%Y') AS date_fin,
                    v.prix_journalier AS prix,
                    'Confirmée' AS statut
             FROM `{$tbl}` r
             INNER JOIN vehicule v ON v.id = r.id_vehicule
             WHERE r.`{$p}` = :uid
             ORDER BY r.date_reservation DESC";
        } else {
            $sql = "SELECT 0 AS reservation_id,
                    r.id_vehicule,
                    r.date_reservation,
                    DATE(r.debut) AS debut_sql,
                    DATE(r.fin) AS fin_sql,
                    CONCAT(v.marque, ' ', v.modele) AS vehicule_nom,
                    DATE_FORMAT(r.debut, '%d/%m/%Y') AS date_debut,
                    DATE_FORMAT(r.fin, '%d/%m/%Y') AS date_fin,
                    v.prix_journalier AS prix,
                    'Confirmée' AS statut
             FROM `{$tbl}` r
             INNER JOIN vehicule v ON v.id = r.id_vehicule
             WHERE r.`{$p}` = :uid
             ORDER BY r.date_reservation DESC";
        }

        $stmt = $this->executerReq($sql, ['uid' => $userId]);

        return $stmt->fetchAll();
    }

    public function cancelIfAllowed(int $reservationId, int $userId): bool
    {
        $tbl = $this->resolvedTable();
        $p = $this->personneFkColumnFor($tbl);
        if ($tbl === 'reservation' && $reservationId > 0) {
            $stmt = $this->executerReq(
                "DELETE FROM `{$tbl}` WHERE id = :id AND `{$p}` = :u AND DATE(debut) > CURDATE()",
                ['id' => (string) $reservationId, 'u' => (string) $userId]
            );

            return $stmt->rowCount() > 0;
        }

        return false;
    }

    /**
     * Annulation schéma `reserver` : clé (id_user, id_vehicule, date_reservation).
     */
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

    public function userHasReservedVehicle(int $userId, int $vehiculeId): bool
    {
        $tbl = $this->resolvedTable();
        $p = $this->personneFkColumnFor($tbl);
        $stmt = $this->executerReq(
            "SELECT 1 FROM `{$tbl}` WHERE `{$p}` = :u AND id_vehicule = :v LIMIT 1",
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

        if ($tbl === 'reservation') {
            $sql = "SELECT r.id, r.debut, r.fin, r.date_reservation, r.`{$p}` AS id_user, r.id_vehicule,
                    p.prenom, p.nom, p.login,
                    v.marque, v.modele
             FROM `reservation` r
             INNER JOIN personne p ON p.id = r.`{$p}`
             INNER JOIN vehicule v ON v.id = r.id_vehicule
             ORDER BY r.date_reservation DESC
             LIMIT {$lim}";
        } else {
            $sql = "SELECT r.debut, r.fin, r.date_reservation, r.`{$p}` AS id_user, r.id_vehicule,
                    p.prenom, p.nom, p.login,
                    v.marque, v.modele
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
        $stmt = $this->executerReq(
            "SELECT COUNT(*) AS c FROM `{$tbl}` WHERE date_reservation >= :d",
            ['d' => $dateYmd . ' 00:00:00']
        );
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) ($row['c'] ?? 0);
    }

    public function getRevenueMonthEstimate(): float
    {
        $tbl = $this->resolvedTable();
        $stmt = $this->executerReq(
            "SELECT COALESCE(SUM((DATEDIFF(r.fin, r.debut) + 1) * v.prix_journalier), 0) AS rev
             FROM `{$tbl}` r
             INNER JOIN vehicule v ON v.id = r.id_vehicule
             WHERE YEAR(r.date_reservation) = YEAR(CURDATE())
               AND MONTH(r.date_reservation) = MONTH(CURDATE())",
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
