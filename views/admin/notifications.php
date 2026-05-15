<?php
/**
 * @var string $token
 * @var list<array<string, mixed>> $notifications
 */
?>
<div class="admin-shell">
<div class="container-fluid">
    <div class="row g-0">
        <?php include __DIR__ . '/inc_sidebar.php'; ?>
        <main class="col-lg-10 px-3 px-md-4 py-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                <div>
                    <h1 class="h3 fw-bold mb-1">Notifications</h1>
                    <p class="text-muted small mb-0">Alertes générées automatiquement (ex. nouvelle réservation) ou futures intégrations.</p>
                </div>
                <form method="post" action="?action=admin_notifications_tout_lu" class="m-0">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">
                    <button type="submit" class="btn btn-outline-secondary btn-sm">Tout marquer comme lu</button>
                </form>
            </div>
            <div class="vstack gap-3">
                <?php foreach ($notifications as $n): ?>
                    <?php
                    $lu = !empty($n['lu']);
                    $id = (int) ($n['id'] ?? 0);
                    ?>
                    <div class="card border-0 shadow-sm <?= $lu ? '' : 'border-start border-4 border-warning' ?>">
                        <div class="card-body d-flex flex-wrap justify-content-between gap-3">
                            <div>
                                <span class="badge text-bg-secondary mb-1"><?= htmlspecialchars((string) ($n['categorie'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                                <h2 class="h6 fw-bold mb-1"><?= htmlspecialchars((string) ($n['titre'] ?? ''), ENT_QUOTES, 'UTF-8') ?></h2>
                                <p class="small text-muted mb-0"><?= htmlspecialchars((string) ($n['message'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                                <p class="small mb-0 mt-2"><span class="text-muted"><?= htmlspecialchars((string) ($n['created_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                                <?php if (!empty($n['lien'])): ?>
                                    · <a href="<?= htmlspecialchars((string) $n['lien'], ENT_QUOTES, 'UTF-8') ?>">Ouvrir</a>
                                <?php endif; ?>
                                </p>
                            </div>
                            <?php if (!$lu && $id): ?>
                                <form method="post" action="?action=admin_notification_lue" class="align-self-start">
                                    <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="id" value="<?= $id ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Lu</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($notifications)): ?>
                    <p class="text-muted">Aucune notification. Les nouvelles réservations clients en créent une si la table <code>admin_notification</code> existe.</p>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>
</div>
