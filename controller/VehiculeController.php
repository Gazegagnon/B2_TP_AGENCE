<?php

class VehiculeController extends AbstractController
{
    public function vehiculeHttp(): void
    {
        $action = $_GET['action'] ?? '';
        $vehMdl = new VehiculeModel();
        $agMdl = new AgenceModel();
        $resMdl = new ReservationModel();
        $commMdl = new CommentModel();

        switch ($action) {

            case "vehicule_commenter":
                if ($_SERVER['REQUEST_METHOD'] !== 'POST'
                    || !isset($_POST['id_vehicule'], $_POST['token'])
                    || !$this->isValidCsrf($_POST['token'])) {
                    $this->flash('danger', 'Action invalide ou session expirée.');
                    $this->redirect('?action=catalogue_public');
                    return;
                }
                if ($this->isAdmin()) {
                    $this->flash('info', 'Les administrateurs modèrent les avis depuis le tableau de bord.');
                    $this->redirect('?action=admin_commentaires');
                    return;
                }
                if (!$this->isConnected()) {
                    $_SESSION['detail'] = '?action=catalogue_public';
                    $this->redirect('?action=connexion');
                    return;
                }
                $vid = filter_var($_POST['id_vehicule'], FILTER_VALIDATE_INT);
                if (!$vid) {
                    $this->redirect('?action=catalogue_public');
                    return;
                }
                $user = $this->getUser();
                if (!$user) {
                    $this->redirect('?action=connexion');
                    return;
                }
                if (!$resMdl->userHasReservedVehicle((int) $user->getId(), $vid)) {
                    $this->flash('warning', 'Vous ne pouvez donner un avis qu’après une réservation pour ce véhicule.');
                    $this->redirect("?action=vehicule_detail&id={$vid}");
                    return;
                }
                $vehiculeC = $vehMdl->getVehiculeById($vid);
                if (!$vehiculeC) {
                    $this->render("404/404", ["erreur" => "Véhicule introuvable."], 'Erreur | LocAuto Pro');
                    return;
                }
                if ($commMdl->isCommented($user, $vehiculeC)) {
                    $this->flash('info', 'Vous avez déjà publié un avis pour ce véhicule.');
                    $this->redirect("?action=vehicule_detail&id={$vid}");
                    return;
                }
                $note = filter_var($_POST['note'] ?? null, FILTER_VALIDATE_INT);
                if ($note === false || $note < 1 || $note > 5) {
                    $this->flash('warning', 'Choisissez une note entre 1 et 5.');
                    $this->redirect("?action=vehicule_detail&id={$vid}");
                    return;
                }
                $texte = trim((string) ($_POST['commentaire'] ?? ''));
                if (strlen($texte) < 3) {
                    $this->flash('warning', 'Votre commentaire est trop court.');
                    $this->redirect("?action=vehicule_detail&id={$vid}");
                    return;
                }
                $comment = new Commentaire([]);
                $comment->setPersonne($user);
                $comment->setVehicule($vehiculeC);
                $comment->setNote($note);
                $comment->setCommentaire($texte);
                try {
                    $commMdl->ajouter($comment);
                    $this->flash('success', 'Merci ! Votre avis a été publié.');
                } catch (Throwable $e) {
                    $this->flash('danger', $e->getMessage());
                }
                $this->redirect("?action=vehicule_detail&id={$vid}");
                break;

            case "vehicule_detail":
                $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
                if (!$id) {
                    $this->render("404/404", ["erreur" => "ID invalide"], 'Erreur | LocAuto Pro');
                    return;
                }
                $vehicule = $vehMdl->getVehiculeById($id);
                if (!$vehicule) {
                    $this->render("404/404", ["erreur" => "Ce véhicule n'existe pas ou n'est plus disponible."], 'Erreur | LocAuto Pro');
                    return;
                }
                $commentaires = $commMdl->getCommByVehiculeId($id);
                $suggestions = $vehMdl->findSuggestions($id, (string) $vehicule->getType(), 4);
                $marque = htmlspecialchars((string) $vehicule->getMarque(), ENT_QUOTES, 'UTF-8');
                $modele = htmlspecialchars((string) $vehicule->getModele(), ENT_QUOTES, 'UTF-8');

                $canReview = false;
                $alreadyCommented = false;
                if ($this->isConnected() && !$this->isAdmin()) {
                    $u = $this->getUser();
                    if ($u) {
                        $alreadyCommented = $commMdl->isCommented($u, $vehicule);
                        $canReview = !$alreadyCommented && $resMdl->userHasReservedVehicle((int) $u->getId(), $id);
                    }
                }

                $this->render("vehicule/show", [
                    "vehicule" => $vehicule,
                    "commentaires" => $commentaires,
                    "suggestions" => $suggestions,
                    "canReview" => $canReview,
                    "alreadyCommented" => $alreadyCommented,
                    "token" => $this->getToken(),
                    "support" => $this->clientSupportInfo(),
                ], "{$marque} {$modele} — Fiche | LocAuto Pro");
                break;

            case "vehicule_reserver":
                $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $idPost = filter_input(INPUT_POST, 'id_vehicule', FILTER_VALIDATE_INT);
                    if ($idPost) {
                        $id = $idPost;
                    }
                }
                if (!$id) {
                    $this->render("404/404", ["erreur" => "Véhicule non précisé."], 'Erreur | LocAuto Pro');
                    return;
                }

                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['debut'], $_POST['fin'])) {
                    if (!$this->isConnected()) {
                        $_SESSION['detail'] = "?action=vehicule_reserver&id={$id}";
                        $this->redirect("?action=connexion");
                        return;
                    }

                    if (!isset($_POST['token']) || !$this->isValidCsrf($_POST['token'])) {
                        $this->render("404/404", ["erreur" => "Token invalide"], 'Erreur | LocAuto Pro');
                        return;
                    }

                    $debut = trim((string) $_POST['debut']);
                    $fin = trim((string) $_POST['fin']);
                    if ($debut === '' || $fin === '') {
                        $this->renderReservationPage($vehMdl, $id, "Veuillez renseigner les deux dates.");
                        return;
                    }

                    $d0 = DateTime::createFromFormat('Y-m-d', $debut);
                    $d1 = DateTime::createFromFormat('Y-m-d', $fin);
                    if (!$d0 || !$d1 || $d1 < $d0) {
                        $this->renderReservationPage($vehMdl, $id, "La date de fin doit être le même jour ou après la date de début.");
                        return;
                    }

                    $reservation = new Reservation($_POST);
                    $reservation->setPersonne($this->getUser());
                    $vehiculeRes = $vehMdl->getVehiculeById((int) ($_POST['id_vehicule'] ?? 0));
                    if (!$vehiculeRes) {
                        $this->render("404/404", ["erreur" => "Véhicule introuvable."], 'Erreur | LocAuto Pro');
                        return;
                    }
                    $reservation->setVehicule($vehiculeRes);
                    $resMdl->ajouter($reservation);

                    try {
                        $u = $this->getUser();
                        $v = $vehiculeRes;
                        if ($u && $v) {
                            (new AdminNotificationModel())->notify(
                                'reservation',
                                'Nouvelle réservation',
                                trim($u->getPrenom() . ' ' . $u->getNom()) . ' — ' . $v->getMarque() . ' ' . $v->getModele()
                                . ' du ' . $debut . ' au ' . $fin . '.',
                                '?action=admin_dashboard'
                            );
                        }
                    } catch (Throwable $e) {
                    }

                    $this->flash('success', 'Votre réservation a bien été enregistrée. Elle apparaît dans « Mes réservations ».');
                    $this->redirect("?action=compte");
                    return;
                }

                if (!$this->isConnected()) {
                    $_SESSION['detail'] = "?action=vehicule_reserver&id={$id}";
                }

                $this->renderReservationPage($vehMdl, $id, null);
                break;

            case "vehicule_liste":
                $vehicules = $vehMdl->getAllVehicules();
                if ($this->isAdmin()) {
                    $this->render("admin/catalogue_parc", [
                        "vehicules" => $vehicules,
                        "token" => $this->getToken()
                    ], 'Parc (gestion) | LocAuto Pro');
                } else {
                    $this->render("vehicule/index", [
                        "vehicules" => $vehicules,
                        "token" => $this->getToken()
                    ], 'Parc véhicules | LocAuto Pro');
                }
                break;

            case "vehicule_ajouter":
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['marque'])) {
                    if (!isset($_POST['token']) || !$this->isValidCsrf($_POST['token'])) {
                        $this->render("404/404", ["erreur" => "Token invalide"], 'Erreur | LocAuto Pro');
                        return;
                    }

                    $type = $this->sanitizeString($_POST['type'] ?? '');
                    $veh = match ($type) {
                        "camion" => new Camion($_POST),
                        "voiture" => new Voiture($_POST),
                        "moto" => new DeuxRoues($_POST),
                        default => null
                    };

                    if (!$veh) {
                        $this->render("404/404", ["erreur" => "Type de véhicule invalide"], 'Erreur | LocAuto Pro');
                        return;
                    }

                    $vehMdl->ajouter($veh);
                    $this->flash('success', 'Le véhicule a été ajouté au parc.');
                    $this->redirect("?action=vehicule_liste");
                }

                $this->render("vehicule/new", [
                    "token" => $this->getToken(),
                    "agences" => $agMdl->getAllAgences(),
                ], 'Nouveau véhicule | LocAuto Pro');
                break;

            case "vehicule_modifier":
                if (!$this->isAdmin()) {
                    $this->flash('danger', 'Accès réservé aux administrateurs.');
                    $this->redirect("?action=home");
                    return;
                }
                $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
                if (!$id) {
                    $this->render("404/404", ["erreur" => "ID invalide"], 'Erreur | LocAuto Pro');
                    return;
                }

                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['marque'])) {
                    if (!isset($_POST['token']) || !$this->isValidCsrf($_POST['token'])) {
                        $this->render("404/404", ["erreur" => "Token invalide"], 'Erreur | LocAuto Pro');
                        return;
                    }

                    $type = $this->sanitizeString($_POST['type'] ?? '');
                    $veh = match ($type) {
                        "camion" => new Camion($_POST),
                        "voiture" => new Voiture($_POST),
                        "moto" => new DeuxRoues($_POST),
                        default => null
                    };

                    if (!$veh) {
                        $this->flash('danger', 'Type de véhicule invalide.');
                        $this->redirect("?action=vehicule_modifier&id={$id}");
                        return;
                    }
                    $veh->setId($id);
                    if ($vehMdl->modifier($veh)) {
                        $this->flash('success', 'Véhicule mis à jour.');
                    } else {
                        $this->flash('danger', 'Échec de la mise à jour (vérifiez les champs et le SQL statut_parc / couleur).');
                    }
                    $t = (string) $veh->getType();
                    $redir = match ($t) {
                        'voiture' => 'admin_parc_voitures',
                        'moto' => 'admin_parc_motos',
                        'camion' => 'admin_parc_camions',
                        default => 'vehicule_liste',
                    };
                    $this->redirect("?action={$redir}");
                    return;
                }

                $vehicule = $vehMdl->getVehiculeById($id);
                if (!$vehicule) {
                    $this->render("404/404", ["erreur" => "Véhicule introuvable."], 'Erreur | LocAuto Pro');
                    return;
                }

                $this->render("vehicule/edit", [
                    "token" => $this->getToken(),
                    "agences" => $agMdl->getAllAgences(),
                    "vehicule" => $vehicule,
                ], 'Modifier véhicule | LocAuto Pro');
                break;

            case "vehicule_supprimer":
                if (!$this->isAdmin()) {
                    $this->flash('danger', 'Accès réservé aux administrateurs.');
                    $this->redirect("?action=home");
                    return;
                }
                if ($_SERVER['REQUEST_METHOD'] !== 'POST'
                    || !isset($_POST['id'], $_POST['token'])
                    || !$this->isValidCsrf($_POST['token'])) {
                    $this->flash('danger', 'Action de suppression invalide.');
                    $this->redirect("?action=vehicule_liste");
                    return;
                }
                $delId = filter_var($_POST['id'], FILTER_VALIDATE_INT);
                if ($delId) {
                    $vehMdl->delete($delId);
                    $this->flash('success', 'Le véhicule a été retiré du parc.');
                }
                $this->redirect("?action=vehicule_liste");
                break;

            default:
                $this->render("404/404", ["erreur" => "Action véhicule inconnue"], 'Erreur | LocAuto Pro');
                break;
        }
    }

    private function renderReservationPage(VehiculeModel $vehMdl, int $id, ?string $erreurReservation): void
    {
        $vehicule = $vehMdl->getVehiculeById($id);
        if (!$vehicule) {
            $this->render("404/404", ["erreur" => "Véhicule introuvable."], 'Erreur | LocAuto Pro');
            return;
        }
        $marque = htmlspecialchars((string) $vehicule->getMarque(), ENT_QUOTES, 'UTF-8');
        $modele = htmlspecialchars((string) $vehicule->getModele(), ENT_QUOTES, 'UTF-8');
        $this->render("vehicule/reserver", [
            "vehicule" => $vehicule,
            "token" => $this->getToken(),
            "erreur_reservation" => $erreurReservation,
        ], "Réservation — {$marque} {$modele} | LocAuto Pro");
    }
}
