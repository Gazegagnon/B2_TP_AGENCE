<?php
/**
 * @var string $token
 * @var Vehicule $vehicule
 * @var array<int, Agence> $agences
 */
$v = $vehicule;
$vid = (int) $v->getId();
$t = (string) $v->getType();
$et = (string) $v->getEtat();
$stat = method_exists($v, 'getStatut_parc') ? $v->getStatut_parc() : 'disponible';
$couleur = htmlspecialchars((string) $v->getCouleur(), ENT_QUOTES, 'UTF-8');
?>
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <nav aria-label="Fil d'Ariane" class="mb-3">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="?action=admin_dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Modifier véhicule #<?= $vid ?></li>
                </ol>
            </nav>
            <div class="card shadow-lg border-0">
                <div class="card-header lap-panel-header py-3">
                    <h3 class="mb-0">Modifier — <?= htmlspecialchars((string) $v->getMarque()) ?> <?= htmlspecialchars((string) $v->getModele()) ?></h3>
                </div>
                <div class="card-body p-4">
                    <form method="post" action="?action=vehicule_modifier&id=<?= $vid ?>" id="vehiculeForm" novalidate>
                        <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Fichier image</label>
                                <input type="text" name="img" class="form-control" value="<?= htmlspecialchars((string) $v->getImg(), ENT_QUOTES, 'UTF-8') ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Marque</label>
                                <input type="text" name="marque" class="form-control" value="<?= htmlspecialchars((string) $v->getMarque(), ENT_QUOTES, 'UTF-8') ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Modèle</label>
                                <input type="text" name="modele" class="form-control" value="<?= htmlspecialchars((string) $v->getModele(), ENT_QUOTES, 'UTF-8') ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Couleur</label>
                                <input type="text" name="couleur" class="form-control" value="<?= $couleur ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Prix journalier (€)</label>
                                <input type="number" name="prix_journalier" class="form-control" value="<?= htmlspecialchars((string) $v->getPrixJournalier(), ENT_QUOTES, 'UTF-8') ?>" required min="1">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Statut parc (suivi)</label>
                                <select name="statut_parc" class="form-select">
                                    <?php foreach (['disponible', 'en_location', 'maintenance', 'indisponible'] as $st): ?>
                                        <option value="<?= $st ?>" <?= $stat === $st ? 'selected' : '' ?>><?= htmlspecialchars($st) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Poids</label>
                                <input type="number" name="poids" class="form-control" value="<?= htmlspecialchars((string) $v->getPoids(), ENT_QUOTES, 'UTF-8') ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Capacité (places)</label>
                                <input type="number" name="capacite" class="form-control" value="<?= htmlspecialchars((string) $v->getCapacite(), ENT_QUOTES, 'UTF-8') ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Longueur (cm)</label>
                                <input type="number" name="longueur" class="form-control" value="<?= $v instanceof Camion ? htmlspecialchars((string) $v->getLongueur(), ENT_QUOTES, 'UTF-8') : '0' ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Nombre de portes</label>
                                <input type="number" name="nombre_porte" class="form-control" value="<?= ($v instanceof Voiture || $v instanceof Camion) ? htmlspecialchars((string) $v->getNombre_porte(), ENT_QUOTES, 'UTF-8') : '0' ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Cylindrée</label>
                                <input type="number" name="cylindre" class="form-control" value="<?= $v instanceof DeuxRoues ? htmlspecialchars((string) $v->getCylindre(), ENT_QUOTES, 'UTF-8') : '0' ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">État</label>
                                <div class="form-check">
                                    <input type="radio" name="etat" value="neuf" class="form-check-input" <?= $et === 'neuf' ? 'checked' : '' ?> required>
                                    <label class="form-check-label">Neuf</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="etat" value="occas" class="form-check-input" <?= $et === 'occas' ? 'checked' : '' ?> required>
                                    <label class="form-check-label">Occasion</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Type</label>
                                <div class="form-check">
                                    <input type="radio" name="type" value="camion" class="form-check-input" <?= $t === 'camion' ? 'checked' : '' ?> required>
                                    <label class="form-check-label">Camion</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="type" value="voiture" class="form-check-input" <?= $t === 'voiture' ? 'checked' : '' ?>>
                                    <label class="form-check-label">Voiture</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="type" value="moto" class="form-check-input" <?= $t === 'moto' ? 'checked' : '' ?>>
                                    <label class="form-check-label">Moto</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Agence</label>
                                <select name="id_agence" class="form-select" required>
                                    <?php foreach ($agences as $ag): ?>
                                        <option value="<?= (int) $ag->getId() ?>" <?= (int) $v->getId_agence() === (int) $ag->getId() ? 'selected' : '' ?>><?= htmlspecialchars($ag->getNom()) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="text-center mt-4 d-flex flex-wrap gap-2 justify-content-center">
                            <button type="submit" class="btn btn-primary px-5">Enregistrer</button>
                            <a href="?action=admin_suivi_parc" class="btn btn-outline-secondary">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
document.getElementById('vehiculeForm').addEventListener('submit', function (e) {
    if (!this.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
    }
    this.classList.add('was-validated');
});
</script>
