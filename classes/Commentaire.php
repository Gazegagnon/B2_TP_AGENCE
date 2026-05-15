<?php
class Commentaire {
    private int $id_user;
    private int $id_vehicule;
    private ?string $commentaire = null;
    private ?int $note = null;        // ajouter cette propriété
    private ?string $date_commentaire = null;
    private ?User $personne = null;
    private ?Vehicule $vehicule = null;

    public function __construct(array $data = []) {
        foreach ($data as $key => $value) {
            if (!property_exists($this, $key)) {
                continue;
            }
            if ($key === 'id_user' || $key === 'id_vehicule') {
                $this->$key = (int) $value;
                continue;
            }
            if ($key === 'note') {
                $this->$key = $value === null || $value === '' ? null : (int) $value;
                continue;
            }
            if ($key === 'date_commentaire') {
                if ($value === null || $value === '') {
                    $this->date_commentaire = null;
                } elseif ($value instanceof \DateTimeInterface) {
                    $this->date_commentaire = $value->format('Y-m-d H:i:s');
                } else {
                    $this->date_commentaire = (string) $value;
                }
                continue;
            }
            $this->$key = $value;
        }
    }

    // --- Getters ---
    public function getNote(): ?int {
        return $this->note;
    }

    public function getComment(): ?string {
        return $this->commentaire;
    }

    public function getPersonne(): ?User {
        return $this->personne;
    }

    public function getVehicule(): ?Vehicule {
        return $this->vehicule;
    }

    public function getDateComment(): ?string {
        $d = $this->date_commentaire;
        if ($d === null || $d === '') {
            return null;
        }
        $ts = strtotime($d);
        return $ts ? date('d/m/Y H:i', $ts) : $d;
    }

    // --- Setters ---
    public function setPersonne(User $u): void {
        $this->personne = $u;
    }

    public function setVehicule(Vehicule $v): void {
        $this->vehicule = $v;
    }

    public function setNote(int $note): void {
        $this->note = $note;
    }

    public function setCommentaire(string $c): void {
        $this->commentaire = $c;
    }
}
