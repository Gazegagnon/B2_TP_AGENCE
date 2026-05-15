<?php
/**
 * Variables fournies par VehiculeController::vehiculeHttp() via AbstractController::render().
 *
 * @var string                         $token   Jeton CSRF
 * @var array<int, Agence> $agences Liste des agences (select)
 */
?>
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <div class="card shadow-lg border-0">
                <div class="card-header lap-panel-header py-3">
                    <h3 class="mb-0">Ajouter un véhicule</h3>
                </div>

                <div class="card-body p-4">

                    <form method="post" id="vehiculeForm" novalidate>
                        <input type="hidden" name="token" value="<?= $token; ?>">

                        <div class="row g-3">

                            <!-- Image -->
                            <div class="col-md-4">
                                <label class="form-label">Fichier image</label>
                                <input type="text" name="img" class="form-control" placeholder="ex. v1.jpeg" required>
                                <div class="form-text">Nom du fichier dans le dossier <code>public/images/</code> (pas l’URL complète).</div>
                                <div class="invalid-feedback">Indiquez le nom du fichier image.</div>
                            </div>

                            <!-- Marque -->
                            <div class="col-md-4">
                                <label class="form-label">Marque</label>
                                <input type="text" name="marque" class="form-control" required>
                                <div class="invalid-feedback">La marque est obligatoire.</div>
                            </div>

                            <!-- Modèle -->
                            <div class="col-md-4">
                                <label class="form-label">Modèle</label>
                                <input type="text" name="modele" class="form-control" required>
                                <div class="invalid-feedback">Le modèle est obligatoire.</div>
                            </div>

                            <!-- Prix -->
                            <div class="col-md-4">
                                <label class="form-label">Prix journalier (€)</label>
                                <input type="number" name="prix_journalier" class="form-control" required min="1">
                                <div class="invalid-feedback">Veuillez entrer un prix valide.</div>
                            </div>

                            <!-- Poids -->
                            <div class="col-md-4">
                                <label class="form-label">Poids</label>
                                <input type="number" name="poids" class="form-control" required>
                                <div class="invalid-feedback">Veuillez indiquer le poids.</div>
                            </div>

                            <!-- Capacité -->
                            <div class="col-md-4">
                                <label class="form-label">Capacité</label>
                                <input type="number" name="capacite" class="form-control" required>
                                <div class="invalid-feedback">Indiquez la capacité.</div>
                            </div>

                            <!-- Longueur -->
                            <div class="col-md-4">
                                <label class="form-label">Longueur</label>
                                <input type="number" name="longueur" class="form-control" required>
                                <div class="invalid-feedback">Indiquez la longueur.</div>
                            </div>

                            <!-- Nombre de portes -->
                            <div class="col-md-4">
                                <label class="form-label">Nombre de portes</label>
                                <input type="number" name="nombre_porte" class="form-control" required>
                                <div class="invalid-feedback">Indiquez le nombre de portes.</div>
                            </div>

                            <!-- Cylindre -->
                            <div class="col-md-4">
                                <label class="form-label">Cylindrée</label>
                                <input type="number" name="cylindre" class="form-control" required>
                                <div class="invalid-feedback">Indiquez la cylindrée.</div>
                            </div>

                            <!-- Etat -->
                            <div class="col-md-3">
                                <label class="form-label">État</label>
                                <div class="form-check">
                                    <input type="radio" name="etat" value="neuf" class="form-check-input" required>
                                    <label class="form-check-label">Neuf</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="etat" value="occas" class="form-check-input" required>
                                    <label class="form-check-label">Occasion</label>
                                </div>
                                <div class="invalid-feedback d-block">Sélectionnez un état.</div>
                            </div>

                            <!-- Type -->
                            <div class="col-md-3">
                                <label class="form-label">Type</label>
                                <div class="form-check">
                                    <input type="radio" name="type" value="camion" class="form-check-input" required>
                                    <label class="form-check-label">Camion</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="type" value="voiture" class="form-check-input">
                                    <label class="form-check-label">Voiture</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="type" value="moto" class="form-check-input">
                                    <label class="form-check-label">Moto</label>
                                </div>
                                <div class="invalid-feedback d-block">Sélectionnez un type.</div>
                            </div>

                            <!-- Agence -->
                            <div class="col-md-6">
                                <label class="form-label">Agence</label>
                                <select name="id_agence" class="form-select" required>
                                    <option value="">Sélectionner une agence...</option>
                                    <?php foreach($agences as $ag): ?>
                                        <option value="<?= $ag->getId() ?>"><?= $ag->getNom() ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Choisissez une agence.</div>
                            </div>

                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-success px-5">
                                Ajouter
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<!-- Validation JS -->
<script>
document.getElementById('vehiculeForm').addEventListener('submit', function(event) {
    if (!this.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
    }
    this.classList.add('was-validated');
});
</script>
