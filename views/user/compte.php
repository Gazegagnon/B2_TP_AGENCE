<?php
/** @var User $user */
/** @var array<int, array<string, mixed>> $reservations */
/** @var string $token */
/** @var array{email: string, phone_display: string, phone_href: string} $support */
$today = date('Y-m-d');
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
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h2 class="h5 fw-bold mb-0">Mes réservations</h2>
                    <p class="small text-muted mb-0">Vous pouvez annuler une location tant que la date de début n’est pas atteinte. Donnez ensuite votre avis depuis la fiche du véhicule.</p>
                </div>
                <div class="card-body p-4">
                    <?php if (!empty($reservations)) : ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Véhicule</th>
                                        <th>Début</th>
                                        <th>Fin</th>
                                        <th>Tarif / j</th>
                                        <th>Statut</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reservations as $res) : ?>
                                        <?php
                                        $rid = (int) ($res['reservation_id'] ?? 0);
                                        $vid = (int) ($res['id_vehicule'] ?? 0);
                                        $debutSql = (string) ($res['debut_sql'] ?? '');
                                        $resDate = isset($res['date_reservation']) ? (string) $res['date_reservation'] : '';
                                        $canCancel = $vid > 0 && $debutSql !== '' && $debutSql > $today
                                            && (($rid > 0) || ($resDate !== ''));
                                        ?>
                                        <tr>
                                            <td><?= htmlspecialchars((string) ($res['vehicule_nom'] ?? 'N/A')) ?></td>
                                            <td><?= htmlspecialchars((string) ($res['date_debut'] ?? '')) ?></td>
                                            <td><?= htmlspecialchars((string) ($res['date_fin'] ?? '')) ?></td>
                                            <td><?= htmlspecialchars((string) ($res['prix'] ?? '')) ?> €</td>
                                            <td><span class="badge text-bg-success"><?= htmlspecialchars((string) ($res['statut'] ?? '')) ?></span></td>
                                            <td class="text-end text-nowrap">
                                                <a class="btn btn-sm btn-outline-primary" href="?action=vehicule_detail&id=<?= $vid ?>">Fiche &amp; avis</a>
                                                <?php if ($canCancel) : ?>
                                                    <form method="post" action="?action=reservation_annuler" class="d-inline" data-confirm="Annuler définitivement cette réservation ?">
                                                        <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">
                                                        <?php if ($rid > 0) : ?>
                                                            <input type="hidden" name="id" value="<?= $rid ?>">
                                                        <?php else : ?>
                                                            <input type="hidden" name="id_vehicule" value="<?= $vid ?>">
                                                            <input type="hidden" name="date_reservation" value="<?= htmlspecialchars($resDate, ENT_QUOTES, 'UTF-8') ?>">
                                                        <?php endif; ?>
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">Annuler</button>
                                                    </form>
                                                <?php else: ?>
                                                    <span class="small text-muted d-inline-block ms-1">—</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5 text-muted">
                            <p class="mb-3">Aucune réservation pour le moment.</p>
                            <a href="?action=catalogue_public" class="btn btn-primary">Parcourir les véhicules</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
