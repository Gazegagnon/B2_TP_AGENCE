<?php
/**
 * @var string $token
 * @var list<array<string, mixed>> $thread
 * @var User|null $adminContact
 * @var array{email: string, phone_display: string, phone_href: string} $support
 * @var int $userId
 */
?>
<div class="container py-4">
    <div class="row g-4">
        <div class="col-lg-5">
            <h1 class="h4 fw-bold mb-3">Contacter le service client</h1>
            <p class="text-muted small mb-4">Écrivez-nous ci-dessous : votre message est transmis à l’équipe administration. Vous pouvez aussi nous joindre directement.</p>

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h2 class="h6 fw-bold mb-3">Autres canaux</h2>
                    <ul class="list-unstyled small mb-0 vstack gap-2">
                        <li>
                            <i class="bi bi-envelope me-2 text-primary" aria-hidden="true"></i>
                            <a href="mailto:<?= htmlspecialchars($support['email'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($support['email'], ENT_QUOTES, 'UTF-8') ?></a>
                        </li>
                        <li>
                            <i class="bi bi-telephone me-2 text-primary" aria-hidden="true"></i>
                            <a href="tel:<?= htmlspecialchars($support['phone_href'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($support['phone_display'], ENT_QUOTES, 'UTF-8') ?></a>
                            <span class="text-muted"> (lun.–ven., 9h–18h)</span>
                        </li>
                        <li>
                            <i class="bi bi-person-badge me-2 text-primary" aria-hidden="true"></i>
                            Destinataire messagerie :
                            <?php if ($adminContact): ?>
                                <strong><?= htmlspecialchars($adminContact->getPrenom() . ' ' . $adminContact->getNom(), ENT_QUOTES, 'UTF-8') ?></strong>
                                <span class="text-muted">(équipe admin)</span>
                            <?php else: ?>
                                <span class="text-warning">Aucun administrateur en base — créez un compte ADMIN.</span>
                            <?php endif; ?>
                        </li>
                    </ul>
                </div>
            </div>

            <?php if ($adminContact): ?>
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h2 class="h6 fw-bold mb-3">Nouveau message</h2>
                        <form method="post" action="?action=client_message_envoyer">
                            <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">
                            <div class="mb-3">
                                <label class="form-label small">Votre message</label>
                                <textarea name="corps" class="form-control" rows="5" required placeholder="Question sur une réservation, un véhicule…"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Envoyer</button>
                            <a href="?action=compte" class="btn btn-link">Retour au compte</a>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h2 class="h6 fw-bold mb-0">Votre conversation</h2>
                    <p class="small text-muted mb-0">Les échanges récents avec le service (ordre antéchronologique).</p>
                </div>
                <div class="card-body p-0" style="max-height: 560px; overflow-y: auto;">
                    <ul class="list-group list-group-flush">
                        <?php foreach ($thread as $m): ?>
                            <?php
                            $isOut = (int) ($m['expediteur_id'] ?? 0) === $userId;
                            $exp = htmlspecialchars(trim(($m['exp_prenom'] ?? '') . ' ' . ($m['exp_nom'] ?? '')), ENT_QUOTES, 'UTF-8');
                            ?>
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between small text-muted mb-1">
                                    <span><?= htmlspecialchars((string) ($m['created_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                                    <span><?= $isOut ? 'Vous' : 'LocAuto Pro' ?></span>
                                </div>
                                <?php if (!$isOut): ?>
                                    <div class="small fw-semibold mb-1">De : <?= $exp ?></div>
                                <?php endif; ?>
                                <div class="small"><?= nl2br(htmlspecialchars((string) ($m['corps'] ?? ''), ENT_QUOTES, 'UTF-8')) ?></div>
                            </li>
                        <?php endforeach; ?>
                        <?php if (empty($thread)): ?>
                            <li class="list-group-item text-muted small py-5 text-center">Pas encore de message. Si la messagerie ne fonctionne pas, exécutez le script SQL <code>message_interne</code> (voir documentation projet).</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
