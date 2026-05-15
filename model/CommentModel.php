<?php

/**
 * Schémas supportés :
 * - `commentaire` (export typique) : id_personne, contenu, date_commentaire ; note optionnelle ;
 * - `commentaire` (variante) : id_user, commentaire, date_commentaire, note ;
 * - `commenter` (cours bd.sql) : id_user, comment, date_comment.
 */
class CommentModel extends AbstractModel
{
    protected string $table = 'commenter';

    protected string $primaryKey = 'id';

    private function resolvedCommentTable(): string
    {
        static $t = null;
        if ($t !== null) {
            return $t;
        }
        try {
            $stmt = $this->pdo->query(
                "SELECT COUNT(*) FROM information_schema.TABLES
                 WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'commentaire'"
            );
            if ($stmt && (int) $stmt->fetchColumn() > 0) {
                return $t = 'commentaire';
            }
        } catch (Throwable $e) {
        }

        return $t = 'commenter';
    }

    private function bodyCol(string $cTbl): string
    {
        if ($this->tableHasColumn($cTbl, 'commentaire')) {
            return 'commentaire';
        }
        if ($this->tableHasColumn($cTbl, 'contenu')) {
            return 'contenu';
        }

        return 'comment';
    }

    private function dateCol(string $cTbl): string
    {
        return $this->tableHasColumn($cTbl, 'date_commentaire') ? 'date_commentaire' : 'date_comment';
    }

    private function normalizeRowForCommentaire(array $res, string $cTbl): array
    {
        $p = $this->personneFkColumnFor($cTbl);
        if ($p !== 'id_user' && isset($res[$p])) {
            $res['id_user'] = $res[$p];
        }
        $b = $this->bodyCol($cTbl);
        $d = $this->dateCol($cTbl);
        if (isset($res[$b]) && !isset($res['commentaire'])) {
            $res['commentaire'] = $res[$b];
        }
        if (isset($res[$d]) && !isset($res['date_commentaire'])) {
            $res['date_commentaire'] = $res[$d];
        }

        return $res;
    }

    public function isCommented(User $user, Vehicule $vehicule): bool
    {
        $cTbl = $this->resolvedCommentTable();
        $p = $this->personneFkColumnFor($cTbl);
        $this->table = $cTbl;
        $rows = $this->findWhere([
            $p => $user->getId(),
            'id_vehicule' => $vehicule->getId(),
        ]);
        $this->table = 'commenter';

        return count($rows) > 0;
    }

    public function ajouter(Commentaire $comment): bool
    {
        if ($this->isCommented($comment->getPersonne(), $comment->getVehicule())) {
            throw new Exception("Vous avez déjà commenté ce véhicule");
        }

        $cTbl = $this->resolvedCommentTable();
        $p = $this->personneFkColumnFor($cTbl);
        $b = $this->bodyCol($cTbl);
        $data = [
            $p => $comment->getPersonne()->getId(),
            'id_vehicule' => $comment->getVehicule()->getId(),
            $b => $comment->getComment(),
        ];
        if ($this->tableHasColumn($cTbl, 'note')) {
            $data['note'] = $comment->getNote();
        }
        $d = $this->dateCol($cTbl);
        $data[$d] = date('Y-m-d H:i:s');

        $this->table = $cTbl;
        $ok = $this->create($data);
        $this->table = 'commenter';

        return $ok;
    }

    public function getCommByVehiculeId(int $idVehicule): array
    {
        $cTbl = $this->resolvedCommentTable();
        $stmt = $this->executerReq(
            "SELECT * FROM `{$cTbl}` WHERE id_vehicule = :vehicule",
            ['vehicule' => $idVehicule]
        );

        $tab = [];
        $userMdl = new UserModel();

        while ($res = $stmt->fetch()) {
            $res = $this->normalizeRowForCommentaire($res, $cTbl);
            $uid = isset($res['id_user']) ? (int) $res['id_user'] : 0;
            $res['personne'] = $uid > 0 ? $userMdl->getUserById($uid) : null;
            $tab[] = new Commentaire($res);
        }

        return $tab;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getAllForAdmin(int $limit = 200): array
    {
        $cTbl = $this->resolvedCommentTable();
        $p = $this->personneFkColumnFor($cTbl);
        $b = $this->bodyCol($cTbl);
        $d = $this->dateCol($cTbl);
        $lim = max(1, min(500, $limit));
        $noteSel = $this->tableHasColumn($cTbl, 'note') ? ', c.note' : ', NULL AS note';
        $sql = "SELECT c.`{$p}` AS id_user, c.id_vehicule, c.`{$b}` AS commentaire, c.`{$d}` AS date_commentaire
                {$noteSel},
                       p.prenom, p.nom, p.login,
                       v.marque, v.modele
                FROM `{$cTbl}` c
                INNER JOIN personne p ON p.id = c.`{$p}`
                INNER JOIN vehicule v ON v.id = c.id_vehicule
                ORDER BY c.`{$d}` DESC
                LIMIT {$lim}";
        $stmt = $this->pdo->query($sql);

        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    public function deleteByUserAndVehicle(int $userId, int $vehiculeId): bool
    {
        $cTbl = $this->resolvedCommentTable();
        $p = $this->personneFkColumnFor($cTbl);
        $this->executerReq(
            "DELETE FROM `{$cTbl}` WHERE `{$p}` = :u AND id_vehicule = :v",
            ['u' => $userId, 'v' => $vehiculeId]
        );

        return true;
    }
}
