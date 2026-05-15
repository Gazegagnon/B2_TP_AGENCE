<?php
/** @var array<int, Agence> $agences @var string $token */
?>
<div class="container py-4">
    <h1 class="h3 fw-bold mb-1">Nos agences partenaires</h1>
    <p class="text-muted small mb-4">Points de retrait et informations</p>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nom</th>
                        <th>Adresse</th>
                        <th class="text-end">Note</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($agences as $agence): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($agence->getNom()) ?></strong></td>
                        <td><?= htmlspecialchars($agence->getAdresse()) ?>, <?= htmlspecialchars((string)$agence->getCp()) ?> <?= htmlspecialchars($agence->getVille()) ?></td>
                        <td class="text-end">
                            <span class="badge text-bg-light text-muted">Édition à prévoir</span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
