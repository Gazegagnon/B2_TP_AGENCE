<?php
/** @var User $user */
/** @var array<int, array<string, mixed>> $reservations */
/** @var string $token */
/** @var bool $hasWorkflow */
/** @var bool $needsLiveRefresh */
/** @var array{email: string, phone_display: string, phone_href: string} $support */
$today = date('Y-m-d');

$active = [];
$history = [];
foreach ($reservations as $r) {
    $ph = (string) ($r['phase'] ?? '');
    if ($ph === 'terminee' || $ph === 'annulee') {
        $history[] = $r;
    } else {
        $active[] = $r;
    }
}

/**
 * @param array<string, mixed> $res
 */
$reservationRow = function (array $res) use ($today, $token, $hasWorkflow): void {
    $rid = (int) ($res['reservation_id'] ?? 0);
    $vid = (int) ($res['id_vehicule'] ?? 0);
    $debutSql = (string) ($res['debut_sql'] ?? '');
    $resDate = isset($res['date_reservation']) ? (string) $res['date_reservation'] : '';
    $statutCode = (string) ($res['statut_code'] ?? '');
    $phase = (string) ($res['phase'] ?? '');
    $badgeClass = (string) ($res['statut_class'] ?? 'secondary');
    $libelle = (string) ($res['statut_libelle'] ?? '');
    $agence = trim((string) ($res['agence_nom'] ?? ''));
    $montant = $res['montant_estime'] ?? null;
    $montantStr = is_numeric($montant) ? number_format((float) $montant, 2, ',', ' ') . ' €' : '—';
    $majRaw = (string) ($res['derniere_maj'] ?? '');
    $maj = $majRaw !== '' ? date('d/m/Y H:i', strtotime($majRaw)) : '—';

    $canCancel = $vid > 0 && $debutSql !== '' && $debutSql > $today
        && $phase !== 'annulee';
    if ($canCancel) {
        if ($hasWorkflow) {
            $canCancel = $rid > 0 && in_array($statutCode, ['en_attente', 'confirmee_ligne', 'confirmee_agence'], true);
        } else {
            $canCancel = ($rid > 0) || ($resDate !== '');
        }
    }

    $showFinaliser = $hasWorkflow && $rid > 0 && $statutCode === 'en_attente';
    ?>
    <tr>
        <td><?= htmlspecialchars((string) ($res['vehicule_nom'] ?? 'N/A')) ?></td>
        <td class="small"><?= $agence !== '' ? htmlspecialchars($agence, ENT_QUOTES, 'UTF-8') : '—' ?></td>
        <td><?= htmlspecialchars((string) ($res['date_debut'] ?? '')) ?></td>
        <td><?= htmlspecialchars((string) ($res['date_fin'] ?? '')) ?></td>
        <td><?= htmlspecialchars((string) ($res['prix'] ?? '')) ?> €</td>
        <td class="text-nowrap"><?= htmlspecialchars($montantStr, ENT_QUOTES, 'UTF-8') ?></td>
        <td>
            <span class="badge text-bg-<?= htmlspecialchars($badgeClass, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($libelle, ENT_QUOTES, 'UTF-8') ?></span>
            <div class="small text-muted mt-1">Màj <?= htmlspecialchars($maj, ENT_QUOTES, 'UTF-8') ?></div>
        </td>
        <td class="text-end text-nowrap">
            <a class="btn btn-sm btn-outline-primary" href="?action=vehicule_detail&id=<?= $vid ?>">Fiche &amp; avis</a>
            <?php if ($showFinaliser) : ?>
                <form method="post" action="?action=reservation_finaliser_ligne" class="d-inline" data-confirm="Simuler un paiement sécurisé et confirmer cette réservation en ligne ?">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="id" value="<?= $rid ?>">
                    <button type="submit" class="btn btn-sm btn-success">Paiement en ligne (démo)</button>
                </form>
            <?php endif; ?>
            <?php if ($canCancel) : ?>
                <form method="post" action="?action=reservation_annuler" class="d-inline" data-confirm="Annuler cette réservation ?">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">
                    <?php if ($rid > 0) : ?>
                        <input type="hidden" name="id" value="<?= $rid ?>">
                    <?php else : ?>
                        <input type="hidden" name="id_vehicule" value="<?= $vid ?>">
                        <input type="hidden" name="date_reservation" value="<?= htmlspecialchars($resDate, ENT_QUOTES, 'UTF-8') ?>">
                    <?php endif; ?>
                    <button type="submit" class="btn btn-sm btn-outline-danger">Annuler</button>
                </form>
            <?php elseif ($phase !== 'annulee') : ?>
                <span class="small text-muted d-inline-block ms-1">—</span>
            <?php endif; ?>
        </td>
    </tr>
    <?php
};
?>

<div class="container py-4">
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center mb-3" style="width:56px;height:56px">
                        <span class="fw-bold fs-4"><?= strtoupper(htmlspecialchars(substr($user->getPrenom(), 0, 1))) ?></span>
                    </div>
                    <h1 class="h5 fw-bold"><?= htmlspecialchars($user->getPrenom()) ?> <?= htmlspecialchars($user->getNom()) ?></h1>
                    <p class="small text-muted mb-3"><?= htmlspecialchars($user->getEmail()) ?></p>
                    <ul class="list-unstyled small mb-4">
                        <li class="mb-2"><span class="text-muted">Login</span><br><strong><?= htmlspecialchars($user->getLogin()) ?></strong></li>
                        <li><span class="text-muted">Rôle</span><br><span class="badge text-bg-secondary"><?= htmlspecialchars($user->getRole()) ?></span></li>
                    </ul>
                    <a href="?action=catalogue_public" class="btn btn-outline-primary w-100 mb-2">Nouvelle réservation</a>
                    <a href="?action=client_messagerie" class="btn btn-outline-dark w-100 mb-2">Service client — messagerie</a>
                    <a href="mailto:<?= htmlspecialchars($support['email'], ENT_QUOTES, 'UTF-8') ?>" class="btn btn-outline-secondary w-100 mb-2">Nous écrire (e-mail)</a>
                    <a href="tel:<?= htmlspecialchars($support['phone_href'], ENT_QUOTES, 'UTF-8') ?>" class="btn btn-outline-secondary w-100 mb-2">Appeler le <?= htmlspecialchars($support['phone_display'], ENT_QUOTES, 'UTF-8') ?></a>
                    <a href="?action=deconnexion" class="btn btn-outline-danger w-100">Se déconnecter</a>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <?php if (empty($reservations)) : ?>
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body text-center py-5 text-muted">
                        <p class="mb-3">Aucune réservation pour le moment.</p>
                        <a href="?action=catalogue_public" class="btn btn-primary">Parcourir les véhicules</a>
                    </div>
                </div>
            <?php else : ?>
                <?php if ($hasWorkflow && $needsLiveRefresh) : ?>
                    <div class="alert alert-info py-2 small mb-3" role="status">
                        <i class="bi bi-arrow-repeat me-1"></i> Suivi actif : cette page se rafraîchit automatiquement toutes les 90&nbsp;s pour refléter les validations agence ou les changements de statut.
                    </div>
                <?php endif; ?>

                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                        <h2 class="h5 fw-bold mb-0">Suivi des locations</h2>
                        <p class="small text-muted mb-0">
                            <?php if ($hasWorkflow) : ?>
                                Après réservation, vous pouvez <strong>finaliser en ligne</strong> (paiement démo) ou vous rendre en agence pour validation.
                                Une fois confirmée, récupérez le véhicule sur place aux dates prévues.
                            <?php else : ?>
                                Vos réservations s’affichent ci-dessous. Vous pouvez annuler tant que la date de début n’est pas passée.
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="card-body p-4">
                        <?php if (!empty($active)) : ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Véhicule</th>
                                            <th>Agence</th>
                                            <th>Début</th>
                                            <th>Fin</th>
                                            <th>Tarif / j</th>
                                            <th>Estimé</th>
                                            <th>Statut</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($active as $res) {
                                            $reservationRow($res);
                                        } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted small mb-0">Aucune location en cours ou à venir (hors historique).</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                        <h2 class="h5 fw-bold mb-0">Historique</h2>
                        <p class="small text-muted mb-0">Locations terminées et réservations annulées.</p>
                    </div>
                    <div class="card-body p-4">
                        <?php if (!empty($history)) : ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Véhicule</th>
                                            <th>Agence</th>
                                            <th>Début</th>
                                            <th>Fin</th>
                                            <th>Tarif / j</th>
                                            <th>Estimé</th>
                                            <th>Statut</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($history as $res) {
                                            $reservationRow($res);
                                        } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted small mb-0">Pas encore d’historique.</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
