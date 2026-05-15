<?php
/**
 * @var string $token
 * @var list<array<string, mixed>> $fleet
 */
?>
<div class="admin-shell">
<div class="container-fluid">
    <div class="row g-0">
        <?php include __DIR__ . '/inc_sidebar.php'; ?>
        <main class="col-lg-10 px-3 px-md-4 py-4">
            <div class="alert alert-light border small mb-4">
                <strong>Suivi proche du « temps réel »</strong> — Le statut <em>En location</em> est calculé si la date du jour est entre début et fin d’une réservation. Le <em>statut parc</em> est éditable manuellement (maintenance, indisponible…). <span class="text-muted">Rechargez cette page régulièrement (ex. toutes les 1–2 min) pour rafraîchir les données.</span>
            </div>
            <h1 class="h3 fw-bold mb-4">Suivi du parc</h1>
            <div class="table-responsive card border-0 shadow-sm">
                <table class="table table-hover align-middle mb-0 admin-table">
                    <thead class="table-light">
                        <tr>
                            <th>Véhicule</th>
                            <th>Type</th>
                            <th>Agence</th>
                            <th>Prix / j</th>
                            <th>Calendrier aujourd’hui</th>
                            <th>Statut parc (BDD)</th>
                            <th class="text-end">Maj statut</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($fleet as $row): ?>
                        <?php
                        $vid = (int) ($row['id'] ?? 0);
                        $en = (int) ($row['en_location_aujourdhui'] ?? 0) > 0;
                        $curSt = (string) ($row['statut_parc'] ?? 'disponible');
                        ?>
                        <tr>
                            <td class="fw-semibold small"><?= htmlspecialchars((string) ($row['marque'] ?? '') . ' ' . (string) ($row['modele'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="small text-capitalize"><?= htmlspecialchars((string) ($row['type'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="small"><?= htmlspecialchars((string) ($row['agence_nom'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="small"><?= htmlspecialchars((string) ($row['prix_journalier'] ?? ''), ENT_QUOTES, 'UTF-8') ?> €</td>
                            <td class="small"><?= $en ? '<span class="badge text-bg-danger">En location</span>' : '<span class="badge text-bg-success">Disponible</span>' ?></td>
                            <td class="small"><span class="badge text-bg-secondary"><?= htmlspecialchars($curSt, ENT_QUOTES, 'UTF-8') ?></span></td>
                            <td class="text-end">
                                <form method="post" action="?action=admin_statut_vehicule" class="d-flex flex-wrap gap-1 justify-content-end align-items-center">
                                    <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="id" value="<?= $vid ?>">
                                    <select name="statut_parc" class="form-select form-select-sm" style="width: auto;">
                                        <?php foreach (['disponible', 'en_location', 'maintenance', 'indisponible'] as $st): ?>
                                            <option value="<?= $st ?>" <?= $curSt === $st ? 'selected' : '' ?>><?= $st ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-primary">OK</button>
                                    <a class="btn btn-sm btn-outline-secondary" href="?action=vehicule_modifier&id=<?= $vid ?>">Fiche</a>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($fleet)): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">Aucune donnée — vérifiez la base ou le script SQL.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>
</div>
