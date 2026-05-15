<?php
class LoginController extends AbstractController
{
    private UserModel $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new UserModel();
    }

    public function showLogin()
    {
        $this->render("user/connexion", [
            "token" => $this->getToken()
        ]);
    }

    public function submitLogin()
    {
        if (!isset($_POST['login'], $_POST['mdp'])) {
            $_SESSION['error'] = "Veuillez remplir tous les champs.";
            $this->redirect("routeur.php?action=connexion");
        }

        $login = $_POST['login'];
        $mdp   = $_POST['mdp'];

        $user = $this->userModel->connexion($login, $mdp);

        if (!$user) {
            $_SESSION['error'] = "Login ou mot de passe incorrect.";
            $this->redirect("routeur.php?action=connexion");
        }

        // Stockage cohérent dans ton projet
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['role']    = $user->getRole();

        // Redirection selon rôle
        if ($user->getRole() === "ADMIN") {
            $this->redirect("routeur.php?action=admin_liste");
        } else {
            $this->redirect("routeur.php?action=home");
        }
    }

    public function logout()
    {
        $_SESSION = [];
        session_destroy();
        $this->redirect("routeur.php?action=home");
    }
}
