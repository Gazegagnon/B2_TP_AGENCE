<?php
/**
 * @var string $token
 * @var list<array<string, mixed>> $comments
 */
?>
<div class="admin-shell">
<div class="container-fluid">
    <div class="row g-0">
        <?php include __DIR__ . '/inc_sidebar.php'; ?>
        <main class="col-lg-10 px-3 px-md-4 py-4">
            <h1 class="h3 fw-bold mb-2">Avis &amp; commentaires</h1>
            <p class="text-muted small mb-4">Modération : suppression des avis inappropriés. Les clients voient encore la fiche véhicule sans ce commentaire.</p>
            <div class="table-responsive card border-0 shadow-sm">
                <table class="table table-hover align-middle mb-0 admin-table">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Client</th>
                            <th>Véhicule</th>
                            <th>Commentaire</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($comments as $c): ?>
                        <tr>
                            <td class="small text-nowrap"><?= htmlspecialchars((string) ($c['date_commentaire'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="small">
                                <?= htmlspecialchars(trim(($c['prenom'] ?? '') . ' ' . ($c['nom'] ?? '')), ENT_QUOTES, 'UTF-8') ?>
                                <div class="text-muted">@<?= htmlspecialchars((string) ($c['login'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                            </td>
                            <td class="small"><?= htmlspecialchars(trim(($c['marque'] ?? '') . ' ' . ($c['modele'] ?? '')), ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="small"><?= nl2br(htmlspecialchars((string) ($c['commentaire'] ?? ''), ENT_QUOTES, 'UTF-8')) ?></td>
                            <td class="text-end">
                                <form method="post" action="?action=admin_commentaire_supprimer" class="d-inline" data-confirm="Retirer cet avis ?">
                                    <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="id_user" value="<?= (int) ($c['id_user'] ?? 0) ?>">
                                    <input type="hidden" name="id_vehicule" value="<?= (int) ($c['id_vehicule'] ?? 0) ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
                                </form>
                                <a class="btn btn-sm btn-outline-primary" href="?action=vehicule_detail&id=<?= (int) ($c['id_vehicule'] ?? 0) ?>">Fiche</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($comments)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">Aucun commentaire.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>
</div>
