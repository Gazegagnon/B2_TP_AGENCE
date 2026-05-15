<div class="lap-auth-shell">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card border-0 shadow-lg rounded-4">
                <div class="card-body p-4 p-md-5">
                    <h1 class="h4 fw-bold text-center mb-4">Connexion</h1>

                    <?php if (!empty($error)) : ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form action="?action=connexion" method="post" novalidate>
                        <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">

                        <div class="form-floating mb-3">
                            <input type="text" name="login" id="login" class="form-control" placeholder="Login" required autocomplete="username">
                            <label for="login">Identifiant</label>
                        </div>

                        <div class="form-floating mb-4">
                            <input type="password" name="mdp" id="mdp" class="form-control" placeholder="Mot de passe" required autocomplete="current-password">
                            <label for="mdp">Mot de passe</label>
                        </div>

                        <button class="btn btn-primary w-100 btn-lg mb-3" type="submit">Se connecter</button>

                        <p class="text-center small text-muted mb-0">
                            Pas encore de compte ? <a href="?action=inscription">Créer un compte</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
