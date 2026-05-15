<?php
class UserController extends AbstractController
{
    private const ADMIN_ACTIONS = [
        'admin_dashboard', 'admin_liste', 'admin_update', 'inscriptionAdmin',
        'admin_clients', 'admin_staff',
        'admin_commentaires', 'admin_commentaire_supprimer',
        'admin_notifications', 'admin_notification_lue', 'admin_notifications_tout_lu',
        'admin_messagerie', 'admin_message_envoyer',
        'admin_suivi_parc', 'admin_statut_vehicule',
        'admin_parc_voitures', 'admin_parc_motos', 'admin_parc_camions',
    ];

    public function userHttp()
    {
        $action = $_GET['action'] ?? '';
        $userMdl = new UserModel();

        if (in_array($action, self::ADMIN_ACTIONS, true)) {
            if (!$this->isAdmin()) {
                $this->redirect("?action=connexion");
            }

            $counts = $this->adminSidebarCounts();
            $token = $this->getToken();
            $me = (int) ($_SESSION['user_id'] ?? 0);

            switch ($action) {
                case 'admin_liste':
                    $this->redirect("?action=admin_clients");
                    return;

                case 'admin_dashboard':
                    $vehMdl = new VehiculeModel();
                    $agMdl = new AgenceModel();
                    $resMdl = new ReservationModel();
                    $moisNoms = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
                    $period_label = $moisNoms[(int) date('n') - 1] . ' ' . date('Y');
                    $fleetSample = [];
                    try {
                        $fleetSample = array_slice($vehMdl->getFleetTrackingRows(), 0, 8);
                    } catch (Throwable $e) {
                        $fleetSample = [];
                    }
                    $this->render("admin/dashboard", [
                        "token" => $token,
                        "adminNav" => 'dashboard',
                        "notifUnread" => $counts['notif'],
                        "msgUnread" => $counts['msg'],
                        "stats" => [
                            "users" => $userMdl->count(),
                            "vehicules" => $vehMdl->count(),
                            "agences" => $agMdl->count(),
                            "reservations" => $resMdl->count(),
                            "reservations_month" => $resMdl->countSince(date('Y-m-01')),
                            "revenue_month" => $resMdl->getRevenueMonthEstimate(),
                        ],
                        "roles" => $userMdl->countByRole(),
                        "types_parc" => $vehMdl->countByType(),
                        "recent" => $resMdl->getRecentForAdmin(15),
                        "period_label" => $period_label,
                        "fleet_sample" => $fleetSample,
                    ], "Tableau de bord | LocAuto Pro");
                    break;

                case 'admin_clients':
                    if ($_SERVER['REQUEST_METHOD'] === 'POST'
                        && isset($_POST['id'], $_POST['token'])
                        && $this->isValidCsrf($_POST['token'])) {
                        $delId = filter_var($_POST['id'], FILTER_VALIDATE_INT);
                        if ($delId && !$userMdl->deleteSafe($delId, $me)) {
                            $this->flash('warning', 'Suppression impossible (compte protégé ou dernière équipe admin).');
                        } else {
                            $this->flash('success', 'Utilisateur supprimé.');
                        }
                        $this->redirect("?action=admin_clients");
                    }
                    $users = $userMdl->getUsersByRoles(['CLIENT', 'COMMERCIAL']);
                    $this->render("admin/clients", [
                        "token" => $token,
                        "adminNav" => 'clients',
                        "notifUnread" => $counts['notif'],
                        "msgUnread" => $counts['msg'],
                        "users" => $users,
                    ], 'Clients | LocAuto Pro');
                    break;

                case 'admin_staff':
                    if ($_SERVER['REQUEST_METHOD'] === 'POST'
                        && isset($_POST['id'], $_POST['token'])
                        && $this->isValidCsrf($_POST['token'])) {
                        $delId = filter_var($_POST['id'], FILTER_VALIDATE_INT);
                        if ($delId && !$userMdl->deleteSafe($delId, $me)) {
                            $this->flash('warning', 'Impossible de supprimer ce compte administrateur.');
                        } else {
                            $this->flash('success', 'Compte supprimé.');
                        }
                        $this->redirect("?action=admin_staff");
                    }
                    $users = $userMdl->getUsersByRoles(['ADMIN']);
                    $this->render("admin/staff", [
                        "token" => $token,
                        "adminNav" => 'staff',
                        "notifUnread" => $counts['notif'],
                        "msgUnread" => $counts['msg'],
                        "users" => $users,
                    ], 'Équipe admin | LocAuto Pro');
                    break;

                case 'admin_commentaires':
                    $commMdl = new CommentModel();
                    $comments = [];
                    try {
                        $comments = $commMdl->getAllForAdmin();
                    } catch (Throwable $e) {
                        $comments = [];
                    }
                    $this->render("admin/commentaires", [
                        "token" => $token,
                        "adminNav" => 'comments',
                        "notifUnread" => $counts['notif'],
                        "msgUnread" => $counts['msg'],
                        "comments" => $comments,
                    ], 'Avis & commentaires | LocAuto Pro');
                    break;

                case 'admin_commentaire_supprimer':
                    if ($_SERVER['REQUEST_METHOD'] !== 'POST'
                        || !isset($_POST['id_user'], $_POST['id_vehicule'], $_POST['token'])
                        || !$this->isValidCsrf($_POST['token'])) {
                        $this->redirect("?action=admin_commentaires");
                        return;
                    }
                    $uid = filter_var($_POST['id_user'], FILTER_VALIDATE_INT);
                    $vid = filter_var($_POST['id_vehicule'], FILTER_VALIDATE_INT);
                    if ($uid && $vid) {
                        try {
                            (new CommentModel())->deleteByUserAndVehicle($uid, $vid);
                            $this->flash('success', 'Commentaire retiré.');
                        } catch (Throwable $e) {
                            $this->flash('danger', 'Erreur lors de la suppression.');
                        }
                    }
                    $this->redirect("?action=admin_commentaires");
                    break;

                case 'admin_notifications':
                    $notifMdl = new AdminNotificationModel();
                    $list = $notifMdl->listRecent(50);
                    $this->render("admin/notifications", [
                        "token" => $token,
                        "adminNav" => 'notif',
                        "notifUnread" => $counts['notif'],
                        "msgUnread" => $counts['msg'],
                        "notifications" => $list,
                    ], 'Notifications | LocAuto Pro');
                    break;

                case 'admin_notification_lue':
                    if ($_SERVER['REQUEST_METHOD'] === 'POST'
                        && isset($_POST['id'], $_POST['token'])
                        && $this->isValidCsrf($_POST['token'])) {
                        $nid = filter_var($_POST['id'], FILTER_VALIDATE_INT);
                        if ($nid) {
                            (new AdminNotificationModel())->markRead($nid);
                        }
                    }
                    $this->redirect("?action=admin_notifications");
                    break;

                case 'admin_notifications_tout_lu':
                    if ($_SERVER['REQUEST_METHOD'] === 'POST'
                        && isset($_POST['token'])
                        && $this->isValidCsrf($_POST['token'])) {
                        (new AdminNotificationModel())->markAllRead();
                        $this->flash('success', 'Toutes les notifications sont marquées comme lues.');
                    }
                    $this->redirect("?action=admin_notifications");
                    break;

                case 'admin_messagerie':
                    $msgMdl = new MessageModel();
                    $thread = $msgMdl->threadForUser($me);
                    $clients = $userMdl->getUsersByRoles(['CLIENT', 'COMMERCIAL']);
                    $this->render("admin/messagerie", [
                        "token" => $token,
                        "adminNav" => 'messages',
                        "notifUnread" => $counts['notif'],
                        "msgUnread" => $counts['msg'],
                        "thread" => $thread,
                        "clients" => $clients,
                        "adminId" => $me,
                    ], 'Messagerie | LocAuto Pro');
                    break;

                case 'admin_message_envoyer':
                    if ($_SERVER['REQUEST_METHOD'] === 'POST'
                        && isset($_POST['corps'], $_POST['destinataire_id'], $_POST['token'])
                        && $this->isValidCsrf($_POST['token'])) {
                        $to = filter_var($_POST['destinataire_id'], FILTER_VALIDATE_INT);
                        $corps = trim((string) $_POST['corps']);
                        if ($to && $corps !== '') {
                            $msgMdl = new MessageModel();
                            if ($msgMdl->send($me, $to, $corps)) {
                                $this->flash('success', 'Message envoyé.');
                            } else {
                                $this->flash('danger', 'Envoi impossible.');
                            }
                        }
                    }
                    $this->redirect("?action=admin_messagerie");
                    break;

                case 'admin_suivi_parc':
                    $vehMdl = new VehiculeModel();
                    $rows = [];
                    try {
                        $rows = $vehMdl->getFleetTrackingRows();
                    } catch (Throwable $e) {
                        $this->flash('warning', 'Exécutez le script SQL admin_module_schema.sql (colonne statut_parc, etc.).');
                    }
                    $this->render("admin/suivi_parc", [
                        "token" => $token,
                        "adminNav" => 'suivi',
                        "notifUnread" => $counts['notif'],
                        "msgUnread" => $counts['msg'],
                        "fleet" => $rows,
                    ], 'Suivi parc | LocAuto Pro');
                    break;

                case 'admin_statut_vehicule':
                    if ($_SERVER['REQUEST_METHOD'] === 'POST'
                        && isset($_POST['id'], $_POST['statut_parc'], $_POST['token'])
                        && $this->isValidCsrf($_POST['token'])) {
                        $vid = filter_var($_POST['id'], FILTER_VALIDATE_INT);
                        $st = trim((string) $_POST['statut_parc']);
                        if ($vid) {
                            try {
                                (new VehiculeModel())->updateStatutParc($vid, $st);
                                $this->flash('success', 'Statut mis à jour.');
                            } catch (Throwable $e) {
                                $this->flash('danger', 'Mise à jour impossible — vérifiez la colonne statut_parc.');
                            }
                        }
                    }
                    $this->redirect("?action=admin_suivi_parc");
                    break;

                case 'admin_parc_voitures':
                    $this->renderParcType('voiture', 'voitures', $counts, $token);
                    break;
                case 'admin_parc_motos':
                    $this->renderParcType('moto', 'motos', $counts, $token);
                    break;
                case 'admin_parc_camions':
                    $this->renderParcType('camion', 'camions', $counts, $token);
                    break;

                case 'admin_update':
                    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
                    if (!$id) {
                        $this->render("404/404", ["erreur" => "ID utilisateur invalide"]);
                        return;
                    }

                    if ($_SERVER['REQUEST_METHOD'] === 'POST'
                        && isset($_POST['login'], $_POST['token'])
                        && $this->isValidCsrf($_POST['token'])) {
                        $user = new User($_POST);
                        $user->setId($id);
                        $userMdl->updateUser($user);
                        $this->flash('success', 'Profil mis à jour.');
                        $updated = $userMdl->getUserById($id);
                        $role = $updated?->getRole() ?? '';
                        $this->redirect(in_array($role, ['ADMIN'], true) ? "?action=admin_staff" : "?action=admin_clients");
                    }

                    $user = $userMdl->getUserById($id);
                    if (!$user) {
                        $this->render("404/404", ["erreur" => "Utilisateur introuvable"]);
                        return;
                    }

                    $this->render("user/ajouter", [
                        "user" => $user,
                        "token" => $token,
                    ]);
                    break;

                case 'inscriptionAdmin':
                    if ($_SERVER['REQUEST_METHOD'] === 'POST'
                        && isset($_POST['login'], $_POST['token'])
                        && $this->isValidCsrf($_POST['token'])) {
                        $user = new User($_POST);
                        $user->setRole("ADMIN");

                        $success = $userMdl->inscription($user);

                        if ($success) {
                            $this->flash('success', 'Nouvel administrateur créé.');
                            $this->redirect("?action=admin_staff");
                        }

                        $this->render("user/inscription", [
                            "token" => $token,
                            "error" => "Login ou email déjà utilisé",
                            "isAdmin" => true,
                        ]);
                        return;
                    }

                    $this->render("user/inscription", [
                        "token" => $token,
                        "isAdmin" => true,
                    ]);
                    break;
            }
            return;
        }

        switch ($action) {
            case "reservation_annuler":
                if (!$this->isConnected() || $this->isAdmin()) {
                    $this->redirect("?action=compte");
                    break;
                }
                if ($_SERVER['REQUEST_METHOD'] === 'POST'
                    && isset($_POST['token'])
                    && $this->isValidCsrf($_POST['token'])) {
                    $uid = (int) ($_SESSION['user_id'] ?? 0);
                    $resMdl = new ReservationModel();
                    $ok = false;
                    if ($uid && isset($_POST['id_vehicule'], $_POST['date_reservation'])
                        && trim((string) $_POST['date_reservation']) !== '') {
                        $vid = filter_var($_POST['id_vehicule'], FILTER_VALIDATE_INT);
                        $dr = trim((string) $_POST['date_reservation']);
                        if ($vid) {
                            $ok = $resMdl->cancelReserverRow($uid, $vid, $dr);
                        }
                    } elseif ($uid && isset($_POST['id'])) {
                        $rid = filter_var($_POST['id'], FILTER_VALIDATE_INT);
                        if ($rid) {
                            $ok = $resMdl->cancelIfAllowed($rid, $uid);
                        }
                    }
                    if ($ok) {
                        $this->flash('success', 'Votre réservation a été annulée.');
                    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token'])) {
                        $this->flash('warning', 'Annulation impossible (location déjà commencée ou réservation introuvable).');
                    }
                }
                $this->redirect("?action=compte");
                break;

            case "client_messagerie":
                if (!$this->isConnected()) {
                    $_SESSION['detail'] = '?action=client_messagerie';
                    $this->redirect("?action=connexion");
                    break;
                }
                if ($this->isAdmin()) {
                    $this->redirect("?action=admin_messagerie");
                    break;
                }
                $me = (int) ($_SESSION['user_id'] ?? 0);
                $thread = (new MessageModel())->threadForUser($me);
                $this->render("user/messagerie_client", [
                    "token" => $this->getToken(),
                    "thread" => $thread,
                    "adminContact" => $userMdl->getFirstAdminUser(),
                    "support" => $this->clientSupportInfo(),
                    "userId" => $me,
                ], 'Service client — Messagerie | LocAuto Pro');
                break;

            case "client_message_envoyer":
                if (!$this->isConnected() || $this->isAdmin()) {
                    $this->redirect($this->isAdmin() ? "?action=admin_messagerie" : "?action=compte");
                    break;
                }
                if ($_SERVER['REQUEST_METHOD'] === 'POST'
                    && isset($_POST['corps'], $_POST['token'])
                    && $this->isValidCsrf($_POST['token'])) {
                    $admin = $userMdl->getFirstAdminUser();
                    $corps = trim((string) $_POST['corps']);
                    if ($admin && $corps !== '') {
                        $from = (int) ($_SESSION['user_id'] ?? 0);
                        if ((new MessageModel())->send($from, (int) $admin->getId(), $corps)) {
                            try {
                                (new AdminNotificationModel())->notify(
                                    'message',
                                    'Message service client',
                                    mb_substr($corps, 0, 180),
                                    '?action=admin_messagerie'
                                );
                            } catch (Throwable $e) {
                            }
                            $this->flash('success', 'Message envoyé. Nous vous répondrons via ce fil ou par e-mail.');
                        } else {
                            $this->flash('danger', 'Envoi impossible. Vérifiez que la table message_interne existe.');
                        }
                    } else {
                        $this->flash('warning', 'Message vide ou service indisponible.');
                    }
                }
                $this->redirect("?action=client_messagerie");
                break;

            case "inscription":
                if ($_SERVER['REQUEST_METHOD'] === 'POST'
                    && isset($_POST['token'])
                    && $this->isValidCsrf($_POST['token'])) {
                    $_SESSION["errors"] = [];

                    if (strlen($_POST['nom'] ?? '') < 2) {
                        $_SESSION["errors"]["nom"] = "Nom trop court";
                    }

                    if (strlen($_POST['prenom'] ?? '') < 2) {
                        $_SESSION["errors"]["prenom"] = "Prénom trop court";
                    }

                    if (!filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
                        $_SESSION["errors"]["email"] = "Email invalide";
                    }

                    if (strlen($_POST['login'] ?? '') < 4) {
                        $_SESSION["errors"]["login"] = "Login trop court";
                    }

                    if (strlen($_POST['mdp'] ?? '') < 4) {
                        $_SESSION["errors"]["mdp"] = "Mot de passe trop court";
                    }

                    if (!isset($_POST['sexe'])) {
                        $_SESSION["errors"]["sexe"] = "Choisir votre sexe";
                    }

                    if (!empty($_SESSION["errors"])) {
                        $this->render("user/inscription", [
                            "token" => $this->getToken(),
                            "isAdmin" => false,
                        ]);
                        return;
                    }

                    $user = new User($_POST);
                    $user->setRole("CLIENT");

                    $success = $userMdl->inscription($user);

                    if ($success) {
                        $newUserData = $userMdl->getByLogin($_POST['login']);
                        if ($newUserData) {
                            $userId = (int) $newUserData['id'];
                            $_SESSION['user_id'] = $userId;
                            $_SESSION['role'] = $newUserData['role'];
                            $_SESSION['user'] = serialize($userMdl->getUserById($userId));
                        }

                        $this->redirect("?action=compte");
                    }

                    $this->render("user/inscription", [
                        "token" => $this->getToken(),
                        "error" => "Login ou email déjà utilisé",
                    ]);
                    return;
                }

                $this->render("user/inscription", [
                    "token" => $this->getToken(),
                ]);
                break;

            case "connexion":
                if ($_SERVER['REQUEST_METHOD'] === "POST"
                    && isset($_POST['login'], $_POST['mdp'], $_POST['token'])
                    && $this->isValidCsrf($_POST['token'])) {
                    $user = $userMdl->connexion($_POST['login'], $_POST['mdp']);

                    if ($user) {
                        $_SESSION['user_id'] = (int) $user->getId();
                        $_SESSION['role'] = $user->getRole();
                        $_SESSION['user'] = serialize($user);

                        $return = $_SESSION['detail'] ?? null;
                        unset($_SESSION['detail']);
                        if (is_string($return) && $return !== '') {
                            $this->redirect($return);
                            return;
                        }

                        if ($user->getRole() === "ADMIN") {
                            $this->redirect("?action=home");
                        } else {
                            $this->redirect("?action=compte");
                        }
                        return;
                    }

                    $this->render("user/connexion", [
                        "token" => $this->getToken(),
                        "error" => "Identifiants incorrects",
                    ], 'Connexion | LocAuto Pro');
                    return;
                }

                $this->render("user/connexion", [
                    "token" => $this->getToken(),
                ], 'Connexion | LocAuto Pro');
                break;

            case "deconnexion":
                $_SESSION = [];
                session_destroy();
                $this->redirect("?action=home");
                break;

            case "compte":
                $userId = $_SESSION['user_id'] ?? null;
                if (!$userId) {
                    $this->redirect("?action=connexion");
                }

                $user = $userMdl->getUserById($userId);
                if (!$user) {
                    $this->redirect("?action=connexion");
                }

                if ($this->isAdmin()) {
                    $this->render("user/compte_admin", [
                        "user" => $user,
                    ], 'Mon espace administrateur | LocAuto Pro');
                    break;
                }

                $resMdl = new ReservationModel();
                $reservations = $resMdl->findByUserIdForCompte((int) $userId);

                $this->render("user/compte", [
                    "user" => $user,
                    "reservations" => $reservations,
                    "token" => $this->getToken(),
                    "support" => $this->clientSupportInfo(),
                ], 'Mon compte | LocAuto Pro');
                break;

            case "commercial_dashboard":
                if (!$this->isCommercial()) {
                    $this->redirect("?action=connexion");
                }
                $this->render("commercial/dashboard", [], 'Espace commercial | LocAuto Pro');
                break;
        }
    }

    /**
     * @param array{notif: int, msg: int} $counts
     */
    private function renderParcType(string $type, string $navKey, array $counts, string $token): void
    {
        $vehMdl = new VehiculeModel();
        try {
            $vehicules = $vehMdl->getVehiculesByType($type);
        } catch (Throwable $e) {
            $vehicules = [];
        }
        $labels = [
            'voiture' => 'Voitures',
            'moto' => 'Motos',
            'camion' => 'Camions',
        ];
        $this->render("admin/parc_type", [
            "token" => $token,
            "adminNav" => $navKey,
            "notifUnread" => $counts['notif'],
            "msgUnread" => $counts['msg'],
            "vehicules" => $vehicules,
            "type_filter" => $type,
            "type_title" => $labels[$type] ?? ucfirst($type),
        ], ($labels[$type] ?? 'Parc') . ' | LocAuto Pro');
    }
}
