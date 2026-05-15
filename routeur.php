<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Autoload
spl_autoload_register(function($class){
    $paths = ["classes/", "controller/", "model/"];
    foreach ($paths as $path) {
        $file = $path . $class . ".php";
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Instanciation des contrôleurs
$userCtl = new UserController();
$homeCtl = new HomeController();
$agcCtl  = new AgenceController();
$vehCtl  = new VehiculeController();

try {
    $action = isset($_GET['action']) && $_GET['action'] !== ''
        ? (string) $_GET['action']
        : 'home';

    // ----- USER -----
    $userActions = [
        'inscription', 'connexion', 'deconnexion', 'compte',
        'reservation_annuler',
        'client_messagerie', 'client_message_envoyer',
        'admin_dashboard', 'admin_liste', 'admin_update', 'inscriptionAdmin',
        'admin_clients', 'admin_staff',
        'admin_commentaires', 'admin_commentaire_supprimer',
        'admin_notifications', 'admin_notification_lue', 'admin_notifications_tout_lu',
        'admin_messagerie', 'admin_message_envoyer',
        'admin_suivi_parc', 'admin_statut_vehicule',
        'admin_parc_voitures', 'admin_parc_motos', 'admin_parc_camions',
        'commercial_dashboard',
    ];

    // ----- VEHICULE -----
    $vehActions = ['vehicule_liste', 'vehicule_detail', 'vehicule_reserver', 'vehicule_commenter', 'vehicule_ajouter', 'vehicule_modifier', 'vehicule_supprimer'];

    // ----- AGENCE -----
    $agcActions = ['agence_liste', 'agence'];

    if ($action === 'catalogue_public') {
        $homeCtl->cataloguePublic();
    }
    elseif ($action === 'home') {
        $homeCtl->home();
    }
    elseif (in_array($action, $userActions, true)) {
        $userCtl->userHttp();
    }
    elseif (in_array($action, $vehActions, true)) {
        $vehCtl->vehiculeHttp();
    }
    elseif (in_array($action, $agcActions, true)) {
        $agcCtl->httpAgence();
    }
    else {
        $homeCtl->home();
    }

} catch(Exception $e) {
    $homeCtl->render("404/404", ["erreur" => $e->getMessage()], 'Erreur | LocAuto Pro');
}
