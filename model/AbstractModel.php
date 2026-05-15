<?php
declare(strict_types=1);

abstract class AbstractModel
{
    protected PDO $pdo;
    protected string $table;
    protected string $primaryKey = "id";

    public function __construct() {
        $this->pdo = new PDO(
            "mysql:host=127.0.0.1;dbname=b2_tp_agence",
            "root",
            "",
            [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );
    }

    /**
     * Exécuter une requête SQL préparée
     */
    public function executerReq(string $query, array $params = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($query);

        foreach ($params as $key => $value) {
            $params[$key] = htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
        }

        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Récupérer tous les enregistrements
     */
    public function getAll(): array
    {
        $stmt = $this->executerReq("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }

    /**
     * Récupérer un enregistrement par ID
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->executerReq(
            "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id",
            ['id' => $id]
        );

        return $stmt->rowCount() === 1 ? $stmt->fetch() : null;
    }

    /**
     * Insérer un nouvel enregistrement
     */
    public function create(array $data): bool
    {
        $columns = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));

        $query = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        return $this->executerReq($query, $data)->rowCount() > 0;
    }

    /**
     * Mettre à jour un enregistrement
     */
    public function update(int $id, array $data): bool
    {
        $fields = implode(", ", array_map(fn($col) => "$col = :$col", array_keys($data)));
        $data[$this->primaryKey] = $id;

        $query = "UPDATE {$this->table} SET $fields WHERE {$this->primaryKey} = :{$this->primaryKey}";
        return $this->executerReq($query, $data)->rowCount() > 0;
    }

    /**
     * Supprimer un enregistrement
     */
    public function delete(int $id): bool
    {
        $query = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        return $this->executerReq($query, ['id' => $id])->rowCount() > 0;
    }

    /**
     * Recherche avec conditions
     */
    public function findWhere(array $conditions): array
    {
        $where = implode(" AND ", array_map(fn($col) => "$col = :$col", array_keys($conditions)));

        $stmt = $this->executerReq("SELECT * FROM {$this->table} WHERE $where", $conditions);
        return $stmt->fetchAll();
    }

    /**
     * Colonne FK vers `personne` (schémas variés : id_user, id_personne, Personne_id…).
     * Valeur issue d’information_schema, contrôlée par une liste blanche.
     */
    protected function personneFkColumnFor(string $table): string
    {
        static $cache = [];
        $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);
        if ($table === '') {
            return 'id_user';
        }
        if (array_key_exists($table, $cache)) {
            return $cache[$table];
        }
        $candidates = [
            'id_user',
            'id_personne',
            'personne_id',
            'idPersonne',
            'Personne_id',
            'PersonneId',
            'id_client',
            'utilisateur_id',
        ];
        $fields = [];
        try {
            $stmt = $this->pdo->query("SHOW COLUMNS FROM `{$table}`");
            if ($stmt) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    if (isset($row['Field'])) {
                        $fields[] = $row['Field'];
                    }
                }
            }
        } catch (Throwable $e) {
            $fields = [];
        }
        if ($fields === []) {
            try {
                $stmt = $this->pdo->prepare(
                    'SELECT COLUMN_NAME FROM information_schema.COLUMNS
                     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?'
                );
                $stmt->execute([$table]);
                $fields = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
            } catch (Throwable $e) {
            }
        }
        foreach ($candidates as $c) {
            if (in_array($c, $fields, true)) {
                return $cache[$table] = $c;
            }
        }

        return $cache[$table] = 'id_user';
    }

    /** Indique si une colonne existe sur la table (cache par paire table.colonne). */
    protected function tableHasColumn(string $table, string $column): bool
    {
        static $cache = [];
        $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);
        $column = preg_replace('/[^a-zA-Z0-9_]/', '', $column);
        if ($table === '' || $column === '') {
            return false;
        }
        $key = $table . "\0" . $column;
        if (array_key_exists($key, $cache)) {
            return $cache[$key];
        }
        try {
            $stmt = $this->pdo->query("SHOW COLUMNS FROM `{$table}` LIKE " . $this->pdo->quote($column));
            $cache[$key] = (bool) ($stmt && $stmt->fetch(PDO::FETCH_ASSOC));
        } catch (Throwable $e) {
            $cache[$key] = false;
        }

        return $cache[$key];
    }

    /**
     * Compter les lignes de la table
     */
    public function count(): int
    {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM {$this->table}")->fetchColumn();
    }
}
