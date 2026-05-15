<?php
declare(strict_types=1);

class AdminNotificationModel extends AbstractModel
{
    protected string $table = 'admin_notification';
    protected string $primaryKey = 'id';

    public function notify(string $categorie, string $titre, string $message, ?string $lien = null): bool
    {
        return $this->create([
            'categorie' => $categorie,
            'titre' => $titre,
            'message' => $message,
            'lien' => $lien,
            'lu' => 0,
        ]);
    }

    public function countUnread(): int
    {
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM {$this->table} WHERE lu = 0");
            return (int) ($stmt ? $stmt->fetchColumn() : 0);
        } catch (Throwable $e) {
            return 0;
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function listRecent(int $limit = 40): array
    {
        try {
            $lim = max(1, min(100, $limit));
            $stmt = $this->pdo->query(
                "SELECT * FROM {$this->table} ORDER BY created_at DESC LIMIT {$lim}"
            );

            return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
        } catch (Throwable $e) {
            return [];
        }
    }

    public function markRead(int $id): void
    {
        try {
            $this->executerReq("UPDATE {$this->table} SET lu = 1 WHERE id = :id", ['id' => $id]);
        } catch (Throwable $e) {
        }
    }

    public function markAllRead(): void
    {
        try {
            $this->pdo->exec("UPDATE {$this->table} SET lu = 1 WHERE lu = 0");
        } catch (Throwable $e) {
        }
    }
}
