<?php
/**
 * @var string $token
 * @var User[] $users
 */
?>
<div class="admin-shell">
<div class="container-fluid">
    <div class="row g-0">
        <?php include __DIR__ . '/inc_sidebar.php'; ?>
        <main class="col-lg-10 px-3 px-md-4 py-4">
            <nav aria-label="Fil d'Ariane" class="mb-3">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="?action=admin_dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active">Équipe administrateur</li>
                </ol>
            </nav>
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                <h1 class="h3 fw-bold mb-0">Administrateurs</h1>
                <a href="?action=inscriptionAdmin" class="btn btn-primary btn-sm">+ Nouvel admin</a>
            </div>
            <p class="text-muted small">Comptes avec rôle <strong>ADMIN</strong> uniquement. Le dernier administrateur ne peut pas être supprimé.</p>
            <div class="table-responsive card border-0 shadow-sm">
                <table class="table table-hover align-middle mb-0 admin-table">
                    <thead class="table-light">
                        <tr>
                            <th>Nom</th>
                            <th>Login</th>
                            <th>Email</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?= htmlspecialchars($u->getPrenom() . ' ' . $u->getNom()) ?></td>
                            <td><?= htmlspecialchars($u->getLogin()) ?></td>
                            <td><?= htmlspecialchars($u->getEmail()) ?></td>
                            <td class="text-end text-nowrap">
                                <a class="btn btn-sm btn-outline-primary" href="?action=admin_update&id=<?= (int) $u->getId() ?>">Modifier</a>
                                <form method="post" class="d-inline" data-confirm="Supprimer cet administrateur ?">
                                    <input type="hidden" name="id" value="<?= (int) $u->getId() ?>">
                                    <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($users)): ?>
                        <tr><td colspan="4" class="text-center text-muted py-4">Aucun administrateur.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>
</div>
