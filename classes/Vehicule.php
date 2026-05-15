<?php

abstract class Vehicule
{
    private $id;
    private $marque;
    private $modele;
    private $prix_journalier;
    private $img;
    private $poids;
    private $type;
    private $etat;
    private $statut_parc = 'disponible';
    private $capacite;
	private $couleur;

    // ID agence (clé étrangère)
    private $id_agence;

    // Objet Agence (chargé après via VehiculeModel)
    private Agence $agence;

    private $comments = [];

    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            $methode = "set" . ucfirst($key);
            if (method_exists($this, $methode)) {
                $this->$methode($value);
            }
        }
    }

    /* ============================================================
        GETTERS
    ============================================================ */

    public function getId() { return $this->id; }
    public function getMarque() { return $this->marque; }
    public function getModele() { return $this->modele; }
    public function getPrixJournalier() { return $this->prix_journalier; }
    public function getPrix_journalier() { return $this->prix_journalier; }
    public function getImg() { return $this->img; }
    public function getPoids() { return $this->poids; }
    public function getType() { return $this->type; }
    public function getEtat() { return $this->etat; }
    public function getStatut_parc(): string {
        $s = $this->statut_parc;
        return is_string($s) && $s !== '' ? $s : 'disponible';
    }
    public function getCapacite() { return $this->capacite; }
	public function getCouleur() { return $this->couleur; }

    // 🔥 IMPORTANT : maintenant correct !!
    public function getId_agence() { return $this->id_agence; }

    public function getAgence(): Agence { return $this->agence; }

    /**
     * URL affichable : chemin absolu depuis la racine du site (/projet/public/images/fichier).
     * Si la BDD n’a pas de nom de fichier, image de secours.
     */
    public function getPhoto(): string
    {
        $img = $this->img;
        $img = is_string($img) ? trim($img) : '';
        if ($img === '') {
            return 'https://via.placeholder.com/400x240?text=Vehicule';
        }
        if (preg_match('#^https?://#i', $img)) {
            return $img;
        }
        if ($img !== '' && ($img[0] === '/' || str_starts_with($img, 'public/'))) {
            return $img[0] === '/' ? $img : '/' . $img;
        }
        $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '/');
        if (!is_string($scriptDir) || $scriptDir === '\\' || $scriptDir === '.') {
            $scriptDir = '';
        }
        $base = rtrim(str_replace('\\', '/', $scriptDir), '/');
        return ($base === '' ? '' : $base) . '/public/images/' . ltrim($img, '/');
    }

    /* ============================================================
        SETTERS
    ============================================================ */

    public function setId($id): void { $this->id = $id; }
    public function setMarque($marque): void { $this->marque = $marque; }
    public function setModele($modele): void { $this->modele = $modele; }
    public function setPrix_journalier($prix_journalier): void { $this->prix_journalier = $prix_journalier; }
    public function setImg($img): void { $this->img = $img; }
    public function setPoids($poids): void { $this->poids = $poids; }
    public function setType($type): void { $this->type = $type; }
    public function setEtat($etat): void { $this->etat = $etat; }
    public function setStatut_parc($statut_parc): void {
        $this->statut_parc = is_string($statut_parc) ? $statut_parc : 'disponible';
    }
    public function setCapacite($capacite): void { $this->capacite = $capacite; }
	public function setCouleur($couleur): void { $this->couleur = $couleur; }

    // 🔥 AJOUT : setter correct pour id_agence
    public function setId_agence($id_agence): void
    {
        $this->id_agence = $id_agence;
    }

    // setter pour l'objet Agence (chargé via VehiculeModel)
    public function setAgence(Agence $agence): void
    {
        $this->agence = $agence;
    }
}
