<?php
/**
 * @var array{users: int, vehicules: int, agences: int, reservations: int, reservations_month: int, revenue_month: float} $stats
 * @var list<array{role: string, n: int}> $roles
 * @var list<array{type: string, n: int}> $types_parc
 * @var list<array<string, mixed>> $recent
 * @var list<array<string, mixed>> $fleet_sample
 * @var string $period_label
 * @var string $token
 * @var string $adminNav
 * @var int $notifUnread
 * @var int $msgUnread
 */
$s = $stats;
$totalUsers = max(1, (int) $s['users']);
$totalVeh = max(1, (int) $s['vehicules']);
$fleet_sample = $fleet_sample ?? [];
?>
<div class="admin-shell">
<div class="container-fluid">
    <div class="row g-0">
        <?php include __DIR__ . '/inc_sidebar.php'; ?>
        <main class="col-lg-10 px-3 px-md-4 py-4">
            <header class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                <div>
                    <h1 class="h3 fw-bold mb-1">Tableau de bord</h1>
                    <p class="text-muted small mb-0">Vue d’ensemble — <?= htmlspecialchars($period_label, ENT_QUOTES, 'UTF-8') ?></p>
                    <p class="small text-muted mb-0"><i class="bi bi-arrow-repeat me-1"></i> Actualisez la page pour rafraîchir les KPI. Suivi véhicules : voir <a href="?action=admin_suivi_parc">Suivi temps réel</a> (rechargement recommandé ~1 min).</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="?action=admin_suivi_parc" class="btn btn-outline-primary"><i class="bi bi-broadcast-pin me-1"></i> Suivi parc</a>
                    <a href="?action=vehicule_ajouter" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Nouveau véhicule</a>
                    <a href="?action=inscriptionAdmin" class="btn btn-outline-secondary"><i class="bi bi-person-plus me-1"></i> Nouvel admin</a>
                </div>
            </header>

            <div class="row g-3 mb-4">
                <div class="col-md-6 col-xl-4">
                    <div class="card kpi-card h-100 border-warning border-opacity-50">
                        <div class="card-body">
                            <h2 class="h6 fw-bold mb-2">Raccourcis module admin</h2>
                            <div class="d-grid gap-2">
                                <a class="btn btn-sm btn-outline-dark" href="?action=admin_notifications">Notifications<?= ($notifUnread ?? 0) > 0 ? ' (' . (int) $notifUnread . ')' : '' ?></a>
                                <a class="btn btn-sm btn-outline-dark" href="?action=admin_messagerie">Messagerie interne</a>
                                <a class="btn btn-sm btn-outline-dark" href="?action=admin_commentaires">Modération des avis</a>
                                <a class="btn btn-sm btn-outline-dark" href="?action=admin_clients">Clients &amp; pros</a>
                                <a class="btn btn-sm btn-outline-dark" href="?action=admin_staff">Équipe administrateur</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-8">
                    <div class="card kpi-card h-100">
                        <div class="card-header bg-white border-0 pt-3 pb-0">
                            <h2 class="h6 fw-bold mb-0">Aperçu suivi parc (extrait)</h2>
                            <p class="small text-muted mb-0">Statut + location « en cours » si une réservation couvre la date du jour.</p>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0 admin-table">
                                    <thead class="table-light"><tr><th>Véhicule</th><th>Type</th><th>Statut parc</th><th>Aujourd’hui</th><th></th></tr></thead>
                                    <tbody>
                                    <?php if (empty($fleet_sample)): ?>
                                        <tr><td colspan="5" class="text-muted small p-3">Aucune donnée — exécutez <code>CONCEPTION/admin_module_schema.sql</code> ou ajoutez des véhicules.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($fleet_sample as $fr): ?>
                                            <?php
                                            $en = (int) ($fr['en_location_aujourdhui'] ?? 0) > 0;
                                            $st = htmlspecialchars((string) ($fr['statut_parc'] ?? 'disponible'), ENT_QUOTES, 'UTF-8');
                                            ?>
                                            <tr>
                                                <td class="small"><?= htmlspecialchars((string) ($fr['marque'] ?? '') . ' ' . (string) ($fr['modele'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                <td class="small text-capitalize"><?= htmlspecialchars((string) ($fr['type'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                <td class="small"><span class="badge text-bg-secondary"><?= $st ?></span></td>
                                                <td class="small"><?= $en ? '<span class="badge text-bg-danger">En location</span>' : '<span class="badge text-bg-success">Libre</span>' ?></td>
                                                <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="?action=vehicule_modifier&id=<?= (int) ($fr['id'] ?? 0) ?>">Modifier</a></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-0"><a href="?action=admin_suivi_parc" class="small">Voir tout le parc →</a></div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-6 col-xl-3">
                    <div class="card kpi-card h-100">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="kpi-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-people-fill"></i></div>
                            <div>
                                <div class="text-muted small text-uppercase">Utilisateurs</div>
                                <div class="h4 mb-0 fw-bold"><?= (int) $s['users'] ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="card kpi-card h-100">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="kpi-icon bg-success bg-opacity-10 text-success"><i class="bi bi-truck-front"></i></div>
                            <div>
                                <div class="text-muted small text-uppercase">Véhicules</div>
                                <div class="h4 mb-0 fw-bold"><?= (int) $s['vehicules'] ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="card kpi-card h-100">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="kpi-icon bg-info bg-opacity-10 text-info"><i class="bi bi-building"></i></div>
                            <div>
                                <div class="text-muted small text-uppercase">Agences</div>
                                <div class="h4 mb-0 fw-bold"><?= (int) $s['agences'] ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="card kpi-card h-100">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="kpi-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-calendar-check"></i></div>
                            <div>
                                <div class="text-muted small text-uppercase">Réservations (mois)</div>
                                <div class="h4 mb-0 fw-bold"><?= (int) $s['reservations_month'] ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6 col-xl-4">
                    <div class="card kpi-card border-primary border-opacity-25">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="text-muted small text-uppercase">CA estimé (mois)</span>
                                <i class="bi bi-currency-euro text-primary"></i>
                            </div>
                            <div class="h3 mb-0 fw-bold text-primary"><?= number_format((float) $s['revenue_month'], 2, ',', ' ') ?> €</div>
                            <p class="small text-muted mb-0 mt-2">Basé sur la durée × prix journalier des réservations enregistrées ce mois-ci.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-4">
                    <div class="card kpi-card">
                        <div class="card-body">
                            <div class="text-muted small text-uppercase mb-2">Réservations totales</div>
                            <div class="h3 mb-0 fw-bold"><?= (int) $s['reservations'] ?></div>
                            <p class="small text-muted mb-0 mt-2">Historique complet en base de données.</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="card kpi-card h-100">
                        <div class="card-body">
                            <div class="text-muted small text-uppercase mb-2">Actions rapides</div>
                            <div class="d-grid gap-2">
                                <a class="btn btn-sm btn-outline-primary" href="?action=admin_clients">Gérer les clients</a>
                                <a class="btn btn-sm btn-outline-secondary" href="?action=agence_liste">Gérer les agences</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-lg-6">
                    <div class="card chart-card h-100">
                        <div class="card-header bg-white border-0 pt-3 pb-0">
                            <h2 class="h6 fw-bold mb-0">Répartition des rôles</h2>
                            <p class="small text-muted mb-0">Effectifs par profil</p>
                        </div>
                        <div class="card-body">
                            <?php foreach ($roles as $r): ?>
                                <?php
                                $n = (int) ($r['n'] ?? 0);
                                $roleLabel = htmlspecialchars((string) ($r['role'] ?? ''), ENT_QUOTES, 'UTF-8');
                                $pct = (int) round(100 * $n / $totalUsers);
                                ?>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between small mb-1">
                                        <span class="fw-semibold"><?= $roleLabel ?></span>
                                        <span class="text-muted"><?= $n ?> (<?= $pct ?> %)</span>
                                    </div>
                                    <div class="progress" style="height: 8px;" role="progressbar" aria-valuenow="<?= $pct ?>" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar bg-primary" style="width: <?= $pct ?>%"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <?php if (empty($roles)): ?>
                                <p class="text-muted small mb-0">Aucune donnée.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card chart-card h-100">
                        <div class="card-header bg-white border-0 pt-3 pb-0">
                            <h2 class="h6 fw-bold mb-0">Parc par type</h2>
                            <p class="small text-muted mb-0">Catégories de véhicules</p>
                        </div>
                        <div class="card-body">
                            <?php foreach ($types_parc as $t): ?>
                                <?php
                                $n = (int) ($t['n'] ?? 0);
                                $typeLabel = htmlspecialchars(ucfirst((string) ($t['type'] ?? '')), ENT_QUOTES, 'UTF-8');
                                $pct = (int) round(100 * $n / $totalVeh);
                                ?>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between small mb-1">
                                        <span class="fw-semibold"><?= $typeLabel ?></span>
                                        <span class="text-muted"><?= $n ?> (<?= $pct ?> %)</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-success" style="width: <?= $pct ?>%"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <?php if (empty($types_parc)): ?>
                                <p class="text-muted small mb-0">Aucun véhicule.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card chart-card">
                <div class="card-header bg-white border-0 pt-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h2 class="h6 fw-bold mb-0">Activité récente</h2>
                        <p class="small text-muted mb-0">Dernières réservations enregistrées</p>
                    </div>
                    <span class="badge text-bg-light text-dark border"><?= count($recent) ?> affichée(s)</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle admin-table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date réservation</th>
                                    <th>Client</th>
                                    <th>Véhicule</th>
                                    <th>Période</th>
                                    <th>Statut résa.</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (empty($recent)): ?>
                                <tr><td colspan="6" class="text-center text-muted py-4">Aucune réservation pour l’instant.</td></tr>
                            <?php else: ?>
                                <?php foreach ($recent as $row): ?>
                                    <?php
                                    $dt = !empty($row['date_reservation']) ? date('d/m/Y H:i', strtotime((string) $row['date_reservation'])) : '—';
                                    $client = htmlspecialchars(trim(($row['prenom'] ?? '') . ' ' . ($row['nom'] ?? '')), ENT_QUOTES, 'UTF-8');
                                    $login = htmlspecialchars((string) ($row['login'] ?? ''), ENT_QUOTES, 'UTF-8');
                                    $veh = htmlspecialchars(trim(($row['marque'] ?? '') . ' ' . ($row['modele'] ?? '')), ENT_QUOTES, 'UTF-8');
                                    $deb = !empty($row['debut']) ? date('d/m/Y', strtotime((string) $row['debut'])) : '—';
                                    $fin = !empty($row['fin']) ? date('d/m/Y', strtotime((string) $row['fin'])) : '—';
                                    $idV = (int) ($row['id_vehicule'] ?? 0);
                                    $idU = (int) ($row['id_user'] ?? 0);
                                    $idRes = (int) ($row['id'] ?? 0);
                                    $rawSt = (string) ($row['statut_reservation'] ?? '');
                                    $stBadge = match ($rawSt) {
                                        'en_attente' => ['En attente', 'warning'],
                                        'confirmee_ligne' => ['Payée en ligne', 'success'],
                                        'confirmee_agence' => ['Validée agence', 'success'],
                                        'annulee' => ['Annulée', 'danger'],
                                        default => [$rawSt !== '' ? $rawSt : '—', 'secondary'],
                                    };
                                    ?>
                                    <tr>
                                        <td class="text-nowrap small"><?= $dt ?></td>
                                        <td>
                                            <div class="fw-semibold"><?= $client ?></div>
                                            <div class="small text-muted">@<?= $login ?></div>
                                        </td>
                                        <td><?= $veh ?></td>
                                        <td class="small text-nowrap"><?= $deb ?> → <?= $fin ?></td>
                                        <td><span class="badge text-bg-<?= htmlspecialchars($stBadge[1], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($stBadge[0], ENT_QUOTES, 'UTF-8') ?></span></td>
                                        <td class="text-end text-nowrap">
                                            <?php if ($idRes > 0 && $rawSt === 'en_attente') : ?>
                                                <form method="post" action="?action=admin_reservation_valider_agence" class="d-inline">
                                                    <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">
                                                    <input type="hidden" name="id" value="<?= $idRes ?>">
                                                    <button type="submit" class="btn btn-sm btn-success" title="Confirme la réservation comme validée en agence">Valider agence</button>
                                                </form>
                                            <?php endif; ?>
                                            <a href="?action=vehicule_detail&id=<?= $idV ?>" class="btn btn-sm btn-outline-primary">Fiche véhicule</a>
                                            <a href="?action=admin_update&id=<?= $idU ?>" class="btn btn-sm btn-outline-secondary">Client</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
</div>
