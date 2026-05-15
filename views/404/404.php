<div class="container py-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <p class="display-1 text-muted mb-3" aria-hidden="true">:(</p>
            <h1 class="h3 fw-bold mb-3">Une erreur s’est produite</h1>
            <p class="text-muted mb-4"><?= htmlspecialchars((string)($erreur ?? 'Page introuvable'), ENT_QUOTES, 'UTF-8') ?></p>
            <a href="?action=home" class="btn btn-primary btn-lg">Retour à l’accueil</a>
        </div>
    </div>
</div>
