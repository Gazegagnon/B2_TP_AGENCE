<div class="lap-auth-shell">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card border-0 shadow-lg rounded-4">
                <div class="card-body p-4 p-md-5">
                    <h1 class="h4 fw-bold text-center mb-2">Créer un compte</h1>
                    <p class="text-center text-muted small mb-4">Rejoignez LocAuto Pro pour réserver en ligne</p>

                    <?php if (!empty($error)) : ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form action="?action=inscription" method="post" novalidate>
                        <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nom</label>
                                <input type="text" name="nom" class="form-control <?= isset($_SESSION['errors']['nom']) ? 'is-invalid' : '' ?>"
                                       value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>
                                <?php if(isset($_SESSION['errors']['nom'])): ?>
                                    <div class="invalid-feedback"><?= $_SESSION['errors']['nom'] ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Prénom</label>
                                <input type="text" name="prenom" class="form-control <?= isset($_SESSION['errors']['prenom']) ? 'is-invalid' : '' ?>"
                                       value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>" required>
                                <?php if(isset($_SESSION['errors']['prenom'])): ?>
                                    <div class="invalid-feedback"><?= $_SESSION['errors']['prenom'] ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-12">
                                <label class="form-label">E-mail</label>
                                <input type="email" name="email" class="form-control <?= isset($_SESSION['errors']['email']) ? 'is-invalid' : '' ?>"
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                                <?php if(isset($_SESSION['errors']['email'])): ?>
                                    <div class="invalid-feedback"><?= $_SESSION['errors']['email'] ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Login</label>
                                <input type="text" name="login" class="form-control <?= isset($_SESSION['errors']['login']) ? 'is-invalid' : '' ?>"
                                       value="<?= htmlspecialchars($_POST['login'] ?? '') ?>" required>
                                <?php if(isset($_SESSION['errors']['login'])): ?>
                                    <div class="invalid-feedback"><?= $_SESSION['errors']['login'] ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mot de passe</label>
                                <input type="password" name="mdp" class="form-control <?= isset($_SESSION['errors']['mdp']) ? 'is-invalid' : '' ?>" required>
                                <?php if(isset($_SESSION['errors']['mdp'])): ?>
                                    <div class="invalid-feedback"><?= $_SESSION['errors']['mdp'] ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-12">
                                <label class="form-label d-block">Sexe</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input <?= isset($_SESSION['errors']['sexe']) ? 'is-invalid' : '' ?>" type="radio" name="sexe" value="homme" <?= (($_POST['sexe'] ?? '') === 'homme') ? 'checked' : '' ?> id="sx1" required>
                                    <label class="form-check-label" for="sx1">Homme</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input <?= isset($_SESSION['errors']['sexe']) ? 'is-invalid' : '' ?>" type="radio" name="sexe" value="femme" <?= (($_POST['sexe'] ?? '') === 'femme') ? 'checked' : '' ?> id="sx2" required>
                                    <label class="form-check-label" for="sx2">Femme</label>
                                </div>
                                <?php if(isset($_SESSION['errors']['sexe'])): ?>
                                    <div class="invalid-feedback d-block"><?= $_SESSION['errors']['sexe'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <button class="btn btn-primary w-100 btn-lg mt-4" type="submit">S'inscrire</button>

                        <p class="text-center small text-muted mt-3 mb-0">
                            <a href="?action=connexion">Déjà un compte ? Se connecter</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<?php unset($_SESSION['errors']); ?>
