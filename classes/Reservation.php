<?php
class Reservation {
    private int $id_user;
    private int $id_vehicule;
    private ?string $message = null;   // ajouter cette propriété
    private ?string $debut = null;
    private ?string $fin = null;
    private ?string $date_reservation = null;
    private ?User $personne = null;
    private ?Vehicule $vehicule = null;

    public function __construct(array $data = []) {
        foreach ($data as $key => $value) {
            if(property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    // --- Getters ---
    public function getMessage(): ?string {
        return $this->message;
    }

    public function getDebut(): ?string {
        return $this->debut;
    }

    public function getFin(): ?string {
        return $this->fin;
    }

    public function getPersonne(): ?User {
        return $this->personne;
    }

    public function getVehicule(): ?Vehicule {
        return $this->vehicule;
    }

    // --- Setters ---
    public function setPersonne(User $u): void {
        $this->personne = $u;
    }

    public function setVehicule(Vehicule $v): void {
        $this->vehicule = $v;
    }

    public function setMessage(string $msg): void {
        $this->message = $msg;
    }
}
