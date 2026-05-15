<?php
class LocationModele extends AbstractModel
{
    protected string $table = 'locations';
    protected string $primaryKey = 'idlocation';

    /**
     * Récupérer toutes les locations
     */
    public function getAllLocations(): array
    {
        $stmt = $this->executerReq(
            "SELECT l.*, v.marque, v.modele, v.prix_journalier, p.prenom, p.nom 
             FROM locations l
             JOIN vehicule v ON l.idvehicule = v.id
             JOIN personne p ON l.idclient = p.id"
        );
        return $stmt->fetchAll();
    }

    /**
     * Récupérer les locations d'un client spécifique
     */
    public function getLocationsByClient(int $clientId): array
    {
        $stmt = $this->executerReq(
            "SELECT l.*, v.marque, v.modele, v.prix_journalier 
             FROM locations l
             JOIN vehicule v ON l.idvehicule = v.id
             WHERE l.idclient = :idclient",
            ["idclient" => $clientId]
        );
        return $stmt->fetchAll();
    }

    /**
     * Créer une nouvelle location
     */
    public function createLocation(array $data): bool
    {
        return $this->create($data);
    }

    /**
     * Mettre à jour une location
     */
    public function updateLocation(int $id, array $data): bool
    {
        return $this->update($id, $data);
    }

    /**
     * Supprimer une location
     */
    public function deleteLocation(int $id): bool
    {
        return $this->delete($id);
    }
}
