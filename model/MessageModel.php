<?php
declare(strict_types=1);

class MessageModel extends AbstractModel
{
    protected string $table = 'message_interne';
    protected string $primaryKey = 'id';

    public function send(int $fromId, int $toId, string $corps): bool
    {
        return $this->create([
            'expediteur_id' => $fromId,
            'destinataire_id' => $toId,
            'corps' => trim($corps),
        ]);
    }

    /**
     * Fil pour un utilisateur (messages envoyés ou reçus).
     *
     * @return list<array<string, mixed>>
     */
    public function threadForUser(int $userId, int $limit = 80): array
    {
        try {
            $lim = max(1, min(200, $limit));
            $sql = "SELECT m.*,
                    ef.prenom AS exp_prenom, ef.nom AS exp_nom, ef.login AS exp_login, ef.role AS exp_role,
                    dt.prenom AS dest_prenom, dt.nom AS dest_nom, dt.login AS dest_login, dt.role AS dest_role
                FROM {$this->table} m
                INNER JOIN personne ef ON ef.id = m.expediteur_id
                INNER JOIN personne dt ON dt.id = m.destinataire_id
                WHERE m.expediteur_id = :uid OR m.destinataire_id = :uid2
                ORDER BY m.created_at DESC
                LIMIT {$lim}";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['uid' => $userId, 'uid2' => $userId]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {
            return [];
        }
    }

    public function countUnreadFor(int $userId): int
    {
        try {
            $stmt = $this->executerReq(
                "SELECT COUNT(*) FROM {$this->table} WHERE destinataire_id = :u AND lu_at IS NULL",
                ['u' => $userId]
            );

            return (int) $stmt->fetchColumn();
        } catch (Throwable $e) {
            return 0;
        }
    }

    public function markAsRead(int $messageId, int $recipientId): void
    {
        try {
            $this->executerReq(
                "UPDATE {$this->table} SET lu_at = NOW() WHERE id = :id AND destinataire_id = :r AND lu_at IS NULL",
                ['id' => $messageId, 'r' => $recipientId]
            );
        } catch (Throwable $e) {
        }
    }
}
