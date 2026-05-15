<?php
/** @var Vehicule[] $vehicules @var string $token */
?>
<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Parc véhicules</h1>
            <p class="text-muted small mb-0">Gestion du catalogue — aperçu et accès fiche.</p>
        </div>
        <a class="btn btn-primary" href="?action=vehicule_ajouter">+ Ajouter un véhicule</a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Aperçu</th>
                        <th>Véhicule</th>
                        <th>Prix / j</th>
                        <th>Agence</th>
                        <th>Type</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($vehicules as $veh): ?>
                    <tr>
                        <td style="width: 96px">
                            <img src="<?= htmlspecialchars($veh->getPhoto(), ENT_QUOTES, 'UTF-8') ?>" alt="" class="rounded" style="width:72px;height:48px;object-fit:cover;">
                        </td>
                        <td>
                            <strong><?= htmlspecialchars((string) $veh->getMarque()) ?></strong>
                            <?= htmlspecialchars((string) $veh->getModele()) ?>
                        </td>
                        <td><span class="fw-semibold text-primary"><?= htmlspecialchars((string) $veh->getPrixJournalier()) ?> €</span></td>
                        <td><?= htmlspecialchars((string) $veh->getAgence()->getNom()) ?></td>
                        <td><span class="badge text-bg-secondary text-capitalize"><?= htmlspecialchars((string) $veh->getType()) ?></span></td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-primary" href="?action=vehicule_detail&id=<?= (int) $veh->getId() ?>">Fiche</a>
                            <?php if (($_SESSION['role'] ?? '') === 'ADMIN'): ?>
                            <form action="?action=vehicule_supprimer" method="post" class="d-inline" data-confirm="Supprimer ce véhicule ? Cette action est irréversible.">
                                <input type="hidden" name="id" value="<?= (int) $veh->getId() ?>">
                                <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">
                                <button class="btn btn-sm btn-outline-danger" type="submit">Suppr.</button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
