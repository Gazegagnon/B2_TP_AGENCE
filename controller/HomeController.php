<?php

class HomeController extends AbstractController
{
    public function home(): void
    {
        if ($this->isAdmin()) {
            $vehMdl = new VehiculeModel();
            $agMdl = new AgenceModel();
            $resMdl = new ReservationModel();
            $this->render('admin/accueil', [
                'stats' => [
                    'vehicules' => $vehMdl->count(),
                    'agences' => $agMdl->count(),
                    'reservations' => $resMdl->count(),
                ],
            ], 'Administration — Accueil | LocAuto Pro');
            return;
        }

        $vehMdl = new VehiculeModel();
        $agMdl = new AgenceModel();
        $this->render('home', [
            'total_parc' => $vehMdl->count(),
            'total_agences' => $agMdl->count(),
        ], 'Accueil | LocAuto Pro');
    }

    /**
     * Catalogue « public » (clients / aperçu) — même contenu filtrable que sur l’accueil client, sans l’espace admin.
     */
    public function cataloguePublic(): void
    {
        $this->render('catalogue_public', $this->catalogDataFromRequest(), 'Catalogue location | LocAuto Pro');
    }

    /**
     * @return array<string, mixed>
     */
    private function catalogDataFromRequest(): array
    {
        $vehMdl = new VehiculeModel();
        $agMdl = new AgenceModel();

        $q = isset($_GET['q']) ? trim((string) $_GET['q']) : '';
        $type = isset($_GET['type']) ? trim((string) $_GET['type']) : '';
        if (!in_array($type, ['', 'voiture', 'moto', 'camion'], true)) {
            $type = '';
        }

        $prixMax = isset($_GET['prix_max']) && $_GET['prix_max'] !== ''
            ? filter_var($_GET['prix_max'], FILTER_VALIDATE_FLOAT)
            : null;
        if ($prixMax === false) {
            $prixMax = null;
        }

        $tri = $_GET['tri'] ?? 'marque';
        if (!in_array($tri, ['marque', 'prix_asc', 'prix_desc'], true)) {
            $tri = 'marque';
        }

        if ($type === '') {
            $vehiculesVoiture = $vehMdl->searchVehicules($q, 'voiture', $prixMax, $tri);
            $vehiculesMoto = $vehMdl->searchVehicules($q, 'moto', $prixMax, $tri);
            $vehiculesCamion = $vehMdl->searchVehicules($q, 'camion', $prixMax, $tri);
        } else {
            $vehiculesVoiture = $type === 'voiture' ? $vehMdl->searchVehicules($q, 'voiture', $prixMax, $tri) : [];
            $vehiculesMoto = $type === 'moto' ? $vehMdl->searchVehicules($q, 'moto', $prixMax, $tri) : [];
            $vehiculesCamion = $type === 'camion' ? $vehMdl->searchVehicules($q, 'camion', $prixMax, $tri) : [];
        }

        $totalParc = $vehMdl->count();
        $totalAgences = $agMdl->count();
        $totalResults = count($vehiculesVoiture) + count($vehiculesMoto) + count($vehiculesCamion);

        return [
            'vehicules_voiture' => $vehiculesVoiture,
            'vehicules_moto' => $vehiculesMoto,
            'vehicules_camion' => $vehiculesCamion,
            'total_results' => $totalResults,
            'filter_q' => $q,
            'filter_type' => $type,
            'filter_prix_max' => $prixMax,
            'filter_tri' => $tri,
            'total_parc' => $totalParc,
            'total_agences' => $totalAgences,
            'support' => $this->clientSupportInfo(),
        ];
    }
}
