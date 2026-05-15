<?php
class UserModel extends AbstractModel
{
    protected string $table = "personne";

    public function getUsers(): array {
    $rows = $this->getAll();
    $users = [];
    foreach ($rows as $row) {
        $users[] = new User($row); // transforme chaque tableau en objet User
    }
    return $users;
}


    public function getUserById($id): ?User {
        $id = (int)$id; // forcer le cast en int
        $data = $this->getById($id);
        return $data ? new User($data) : null;
    }

    public function getByLogin(string $login): ?array {
        $stmt = $this->executerReq("SELECT * FROM {$this->table} WHERE login = :login", ["login"=>$login]);
        return $stmt->fetch() ?: null;
    }

    public function getByEmail(string $email): ?array {
        $stmt = $this->executerReq("SELECT * FROM {$this->table} WHERE email = :email", ["email"=>$email]);
        return $stmt->fetch() ?: null;
    }

    public function updateUser(User $user): bool {
        $this->executerReq(
            "UPDATE {$this->table} SET nom=:nom, prenom=:prenom, email=:email, login=:login, role=:role WHERE id=:id",
            [
                "nom"=>$user->getNom(),
                "prenom"=>$user->getPrenom(),
                "email"=>$user->getEmail(),
                "login"=>$user->getLogin(),
                "role"=>$user->getRole(),
                "id" => (int)$user->getId()
            ]
        );
        return true;
    }

    public function delete($id): bool {
        $id = (int)$id;
        $this->executerReq("DELETE FROM {$this->table} WHERE id=:id", ["id"=>$id]);
        return true;
    }

    public function inscription(User $user): bool {
        try {
            // Vérifier login et email uniques
            if ($this->getByLogin($user->getLogin()) || $this->getByEmail($user->getEmail())) {
                return false;
            }

            $this->executerReq(
                "INSERT INTO {$this->table} (nom, prenom, login, mdp, email, sexe, role) 
                 VALUES (:nom, :prenom, :login, :mdp, :email, :sexe, :role)",
                [
                    "nom" => $user->getNom(),
                    "prenom" => $user->getPrenom(),
                    "login" => $user->getLogin(),
                    "mdp" => password_hash($user->getMdp(), PASSWORD_BCRYPT),
                    "email" => $user->getEmail(),
                    "sexe" => $user->getSexe(),
                    "role" => $user->getRole()
                ]
            );
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /** Effectifs par rôle (tableau de bord admin) */
    public function countByRole(): array
    {
        $stmt = $this->executerReq(
            "SELECT role, COUNT(*) AS n FROM {$this->table} GROUP BY role ORDER BY n DESC"
        );
        return $stmt->fetchAll();
    }

    public function connexion(string $login, string $mdp): ?User {
        $data = $this->getByLogin($login);
        if ($data && password_verify($mdp, $data['mdp'])) {
            return new User($data);
        }
        return null;
    }

    /**
     * @param list<string> $roles
     * @return User[]
     */
    public function getUsersByRoles(array $roles): array
    {
        $roles = array_values(array_filter($roles, 'strlen'));
        if ($roles === []) {
            return [];
        }
        $in = implode(',', array_fill(0, count($roles), '?'));
        $stmt = $this->pdo->prepare(
            "SELECT * FROM {$this->table} WHERE role IN ($in) ORDER BY nom, prenom"
        );
        $stmt->execute($roles);
        $users = [];
        foreach ($stmt->fetchAll() as $row) {
            $users[] = new User($row);
        }

        return $users;
    }

    public function countRole(string $role): int
    {
        $stmt = $this->executerReq(
            "SELECT COUNT(*) FROM {$this->table} WHERE role = :r",
            ['r' => $role]
        );

        return (int) $stmt->fetchColumn();
    }

    /** Destinataire par défaut des messages clients (premier compte ADMIN). */
    public function getFirstAdminUser(): ?User
    {
        $stmt = $this->executerReq(
            "SELECT * FROM {$this->table} WHERE role = :r ORDER BY id ASC LIMIT 1",
            ['r' => 'ADMIN']
        );
        $row = $stmt->fetch();

        return $row ? new User($row) : null;
    }

    /**
     * Suppression sécurisée : pas soi-même, pas le dernier admin.
     */
    public function deleteSafe(int $id, int $currentUserId): bool
    {
        $id = (int) $id;
        if ($id < 1 || $id === (int) $currentUserId) {
            return false;
        }
        $data = $this->getById($id);
        if (!$data) {
            return false;
        }
        if (($data['role'] ?? '') === 'ADMIN' && $this->countRole('ADMIN') <= 1) {
            return false;
        }

        return $this->delete($id);
    }
}
