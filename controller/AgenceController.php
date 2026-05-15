<?php
class AgenceController extends AbstractController
{
    public function httpAgence()
    {
        $action = $_GET['action'] ?? '';
        if (!$this->isAdmin()) $this->redirect("?action=connexion");

        $agMdl = new AgenceModel();

        switch ($action) {
            case "agence_liste":
            case "agence":
                $this->render("agence/index", [
                    "agences" => $agMdl->getAllAgences(),
                    "token" => $this->getToken()
                ], 'Agences | LocAuto Pro');
                break;

            default:
                $this->render("404/404", ["erreur" => "Action agence inconnue"], 'Erreur | LocAuto Pro');
                break;
        }
    }
}
