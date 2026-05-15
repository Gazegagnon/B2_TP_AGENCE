<?php
class VehiculeModel extends AbstractModel
{
    protected string $table = "vehicule";
    protected string $primaryKey = "id";

    /**
     * @param list<array<string, mixed>> $rows
     * @return Vehicule[]
     */
    private function rowsToVehicules(array $rows): array
    {
        $agMdl = new AgenceModel();
        $vehicules = [];
        foreach ($rows as $res) {
            $res['agence'] = $agMdl->getAgenceById((int) ($res['id_agence'] ?? 0));
            $vehicules[] = match ($res['type'] ?? '') {
                'camion' => new Camion($res),
                'voiture' => new Voiture($res),
                'moto' => new DeuxRoues($res),
                default => new Vehicule($res)
            };
        }

        return $vehicules;
    }

    public function getAllVehicules(): array
    {
        return $this->rowsToVehicules($this->getAll());
    }

    /**
     * Catalogue avec filtres (formulaire GET sur l'accueil).
     *
     * @return Vehicule[]
     */
    public function searchVehicules(string $q, string $type, ?float $prixMax, string $tri): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];

        if ($type !== '') {
            $sql .= " AND type = :type";
            $params['type'] = $type;
        }

        if ($prixMax !== null && $prixMax > 0) {
            $sql .= " AND prix_journalier <= :pmax";
            $params['pmax'] = $prixMax;
        }

        $q = trim($q);
        if ($q !== '') {
            $sql .= " AND (marque LIKE :q OR modele LIKE :q)";
            $params['q'] = '%' . $q . '%';
        }

        $orderSql = match ($tri) {
            'prix_asc' => 'prix_journalier ASC, marque ASC',
            'prix_desc' => 'prix_journalier DESC, marque ASC',
            default => 'marque ASC, modele ASC',
        };

        $sql .= ' ORDER BY ' . $orderSql;

        $stmt = $this->executerReq($sql, $params);

        return $this->rowsToVehicules($stmt->fetchAll());
    }

    public function getVehiculeById(int $id): ?Vehicule
    {
        $res = $this->getById($id);
        if (!$res) return null;

        $agMdl = new AgenceModel();
        $res['agence'] = $agMdl->getAgenceById($res['id_agence']);

        return match($res['type']) {
            'camion' => new Camion($res),
            'voiture' => new Voiture($res),
            'moto' => new DeuxRoues($res),
            default => new Vehicule($res)
        };
    }

    public function ajouter(Vehicule $veh): bool
    {
        $data = [
            "marque" => $veh->getMarque(),
            "modele" => $veh->getModele(),
            "img" => $veh->getImg(),
            "prix_journalier" => $veh->getPrix_journalier(),
            "poids" => $veh->getPoids(),
            "type" => $veh->getType(),
            "capacite" => $veh->getCapacite(),
            "etat" => $veh->getEtat(),
            "couleur" => $veh->getCouleur(),
            "statut_parc" => $veh->getStatut_parc(),
            "id_agence" => $veh->getId_agence()
        ];

        if ($veh instanceof Camion) {
            $data += [
                "nombre_porte" => $veh->getNombre_porte(),
                "longueur" => $veh->getLongueur(),
                "cylindre" => 0,
            ];
        } elseif ($veh instanceof Voiture) {
            $data += [
                "nombre_porte" => $veh->getNombre_porte(),
                "longueur" => 0,
                "cylindre" => 0,
            ];
        } elseif ($veh instanceof DeuxRoues) {
            $data += [
                "cylindre" => $veh->getCylindre(),
                "nombre_porte" => 0,
                "longueur" => 0,
            ];
        }

        return $this->create($data);
    }

    /**
     * @return Vehicule[]
     */
    public function getVehiculesByType(string $type): array
    {
        $type = trim($type);
        if (!in_array($type, ['voiture', 'moto', 'camion'], true)) {
            return [];
        }
        $stmt = $this->executerReq(
            "SELECT * FROM {$this->table} WHERE type = :t ORDER BY marque ASC, modele ASC",
            ['t' => $type]
        );

        return $this->rowsToVehicules($stmt->fetchAll());
    }

    /**
     * Mise à jour complète (admin).
     */
    public function modifier(Vehicule $veh): bool
    {
        $id = (int) $veh->getId();
        if ($id < 1) {
            return false;
        }

        $data = [
            "marque" => $veh->getMarque(),
            "modele" => $veh->getModele(),
            "img" => $veh->getImg(),
            "prix_journalier" => $veh->getPrix_journalier(),
            "poids" => $veh->getPoids(),
            "type" => $veh->getType(),
            "capacite" => $veh->getCapacite(),
            "etat" => $veh->getEtat(),
            "couleur" => $veh->getCouleur(),
            "statut_parc" => $veh->getStatut_parc(),
            "id_agence" => $veh->getId_agence(),
        ];

        if ($veh instanceof Camion) {
            $data["nombre_porte"] = $veh->getNombre_porte();
            $data["longueur"] = $veh->getLongueur();
            $data["cylindre"] = 0;
        } elseif ($veh instanceof Voiture) {
            $data["nombre_porte"] = $veh->getNombre_porte();
            $data["longueur"] = 0;
            $data["cylindre"] = 0;
        } elseif ($veh instanceof DeuxRoues) {
            $data["nombre_porte"] = 0;
            $data["longueur"] = 0;
            $data["cylindre"] = $veh->getCylindre();
        } else {
            $data["nombre_porte"] = 0;
            $data["longueur"] = 0;
            $data["cylindre"] = 0;
        }

        return $this->update($id, $data);
    }

    public function updateStatutParc(int $id, string $statut): bool
    {
        $allowed = ['disponible', 'en_location', 'maintenance', 'indisponible'];
        if (!in_array($statut, $allowed, true)) {
            return false;
        }

        return $this->update($id, ['statut_parc' => $statut]);
    }

    /**
     * Lignes pour suivi admin (champs véhicule + indicateur location en cours).
     *
     * @return list<array<string, mixed>>
     */
    public function getFleetTrackingRows(): array
    {
        $resTable = (new ReservationModel())->getReservationTableName();
        $sql = "SELECT v.*, a.nom AS agence_nom,
            (SELECT COUNT(*) FROM `{$resTable}` r
             WHERE r.id_vehicule = v.id AND CURDATE() BETWEEN DATE(r.debut) AND DATE(r.fin)) AS en_location_aujourdhui
            FROM {$this->table} v
            INNER JOIN agence a ON a.id = v.id_agence
            ORDER BY v.type, v.marque, v.modele";
        $stmt = $this->pdo->query($sql);

        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    /** Répartition du parc par type (dashboard) */
    public function countByType(): array
    {
        $stmt = $this->executerReq(
            "SELECT type, COUNT(*) AS n FROM {$this->table} GROUP BY type ORDER BY n DESC"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Suggestions pour fiche détail : même catégorie si possible, sinon parc global (même tarif croissant).
     *
     * @return Vehicule[]
     */
    public function findSuggestions(int $excludeId, ?string $type, int $limit = 4): array
    {
        $limit = max(1, min(12, $limit));
        $type = $type !== null ? trim($type) : '';

        if ($type !== '') {
            $stmt = $this->executerReq(
                "SELECT * FROM {$this->table} WHERE id != :id AND type = :type ORDER BY prix_journalier ASC, marque ASC LIMIT {$limit}",
                ['id' => $excludeId, 'type' => $type]
            );
            $list = $this->rowsToVehicules($stmt->fetchAll());
            if (count($list) >= 1) {
                return $list;
            }
        }

        $stmt = $this->executerReq(
            "SELECT * FROM {$this->table} WHERE id != :eid ORDER BY prix_journalier ASC, marque ASC LIMIT {$limit}",
            ['eid' => $excludeId]
        );

        return $this->rowsToVehicules($stmt->fetchAll());
    }
}
