<div class="container mt-5 py-3">
    <nav aria-label="Fil d'Ariane" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="?action=admin_dashboard">Tableau de bord</a></li>
            <li class="breadcrumb-item active" aria-current="page">Utilisateurs</li>
        </ol>
    </nav>
    <h2 class="text-center mb-4">Liste des utilisateurs</h2>

    <!-- Barre de recherche -->
    <form method="get" class="mb-3 d-flex justify-content-end">
        <input type="hidden" name="action" value="admin_liste">
        <input type="text" name="q" class="form-control w-25 me-2" placeholder="Rechercher..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
        <button type="submit" class="btn btn-primary">Rechercher</button>
    </form>

    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Prénom</th>
                <th>Nom</th>
                <th>Login</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user->getPrenom()) ?></td>
                    <td><?= htmlspecialchars($user->getNom()) ?></td>
                    <td><?= htmlspecialchars($user->getLogin()) ?></td>
                    <td><?= htmlspecialchars($user->getEmail()) ?></td>
                    <td><?= htmlspecialchars($user->getRole()) ?></td>
                    <td>
                        <a href="?action=admin_update&id=<?= $user->getId(); ?>" class="btn btn-sm btn-primary me-2">Modifier</a>
                        
                        <form action="" method="post" class="d-inline" onsubmit="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?');">
                            <input type="hidden" name="id" value="<?= $user->getId(); ?>">
                            <input type="hidden" name="token" value="<?= $token; ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <?php if($totalPages > 1): ?>
        <nav>
            <ul class="pagination justify-content-center">
                <?php for($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                        <a class="page-link" href="?action=admin_liste&page=<?= $i ?><?= !empty($_GET['q']) ? '&q=' . urlencode($_GET['q']) : '' ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>
