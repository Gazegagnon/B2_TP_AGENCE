<?php
/**
 * @var string $token
 * @var list<array<string, mixed>> $thread
 * @var User[] $clients
 * @var int $adminId
 */
?>
<div class="admin-shell">
<div class="container-fluid">
    <div class="row g-0">
        <?php include __DIR__ . '/inc_sidebar.php'; ?>
        <main class="col-lg-10 px-3 px-md-4 py-4">
            <h1 class="h3 fw-bold mb-2">Messagerie interne</h1>
            <p class="text-muted small mb-4">Écrire à un client (rôle CLIENT ou COMMERCIAL). Les échanges apparaissent ci-dessous (ordre antéchronologique).</p>
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h2 class="h6 fw-bold mb-3">Nouveau message</h2>
                            <form method="post" action="?action=admin_message_envoyer">
                                <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">
                                <div class="mb-3">
                                    <label class="form-label small">Destinataire</label>
                                    <select name="destinataire_id" class="form-select" required>
                                        <option value="">— Choisir —</option>
                                        <?php foreach ($clients as $cl): ?>
                                            <option value="<?= (int) $cl->getId() ?>"><?= htmlspecialchars($cl->getPrenom() . ' ' . $cl->getNom() . ' (' . $cl->getLogin() . ')') ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small">Message</label>
                                    <textarea name="corps" class="form-control" rows="4" required placeholder="Votre message au client…"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Envoyer</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 pt-3"><h2 class="h6 fw-bold mb-0">Fil récent</h2></div>
                        <div class="card-body p-0" style="max-height: 520px; overflow-y: auto;">
                            <ul class="list-group list-group-flush">
                                <?php foreach ($thread as $m): ?>
                                    <?php
                                    $isOut = (int) ($m['expediteur_id'] ?? 0) === $adminId;
                                    $exp = htmlspecialchars(trim(($m['exp_prenom'] ?? '') . ' ' . ($m['exp_nom'] ?? '')), ENT_QUOTES, 'UTF-8');
                                    $dest = htmlspecialchars(trim(($m['dest_prenom'] ?? '') . ' ' . ($m['dest_nom'] ?? '')), ENT_QUOTES, 'UTF-8');
                                    ?>
                                    <li class="list-group-item">
                                        <div class="d-flex justify-content-between small text-muted mb-1">
                                            <span><?= htmlspecialchars((string) ($m['created_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                                            <span><?= $isOut ? 'Vous → client' : 'Client → vous' ?></span>
                                        </div>
                                        <div class="small fw-semibold mb-1"><?= $isOut ? 'À : ' . $dest : 'De : ' . $exp ?></div>
                                        <div class="small"><?= nl2br(htmlspecialchars((string) ($m['corps'] ?? ''), ENT_QUOTES, 'UTF-8')) ?></div>
                                        <?php $unread = empty($m['lu_at']) && (int) ($m['destinataire_id'] ?? 0) === $adminId; ?>
                                        <?php if ($unread): ?><span class="badge bg-info mt-2">Non lu</span><?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                                <?php if (empty($thread)): ?>
                                    <li class="list-group-item text-muted small">Aucun message. Créez la table <code>message_interne</code> (script SQL) si besoin.</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
</div>
