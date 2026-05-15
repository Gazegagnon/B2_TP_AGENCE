<?php
/**
 * @var string      $adminNav      dashboard|clients|staff|suivi|voitures|motos|camions|comments|notif|messages
 * @var string      $token
 * @var int         $notifUnread
 * @var int         $msgUnread
 */
$nav = $adminNav ?? 'dashboard';
$n = (int) ($notifUnread ?? 0);
$m = (int) ($msgUnread ?? 0);
$active = fn(string $k): string => ($nav === $k) ? 'active' : '';
?>
<aside class="col-lg-2 admin-sidebar px-0 pb-4">
    <div class="px-3 mb-3 d-none d-lg-block">
        <span class="text-white-50 small">Administration</span>
        <div class="text-white fw-bold fs-5">LocAuto Pro</div>
    </div>
    <div class="nav-section d-none d-lg-block">Vue d’ensemble</div>
    <nav class="nav flex-column">
        <a class="nav-link <?= $active('dashboard') ?>" href="?action=admin_dashboard"><i class="bi bi-speedometer2 me-2"></i> Tableau de bord</a>
        <a class="nav-link <?= $active('notif') ?>" href="?action=admin_notifications">
            <i class="bi bi-bell me-2"></i> Notifications
            <?php if ($n > 0): ?><span class="badge bg-warning text-dark ms-1"><?= $n ?></span><?php endif; ?>
        </a>
        <a class="nav-link <?= $active('messages') ?>" href="?action=admin_messagerie">
            <i class="bi bi-chat-dots me-2"></i> Messagerie
            <?php if ($m > 0): ?><span class="badge bg-info ms-1"><?= $m ?></span><?php endif; ?>
        </a>
    </nav>
    <div class="nav-section">Suivi &amp; parc</div>
    <nav class="nav flex-column">
        <a class="nav-link <?= $active('suivi') ?>" href="?action=admin_suivi_parc"><i class="bi bi-broadcast-pin me-2"></i> Suivi temps réel</a>
        <a class="nav-link <?= $active('voitures') ?>" href="?action=admin_parc_voitures"><i class="bi bi-car-front me-2"></i> Voitures</a>
        <a class="nav-link <?= $active('motos') ?>" href="?action=admin_parc_motos"><i class="bi bi-speedometer2 me-2"></i> Motos</a>
        <a class="nav-link <?= $active('camions') ?>" href="?action=admin_parc_camions"><i class="bi bi-truck me-2"></i> Camions</a>
        <a class="nav-link" href="?action=vehicule_ajouter"><i class="bi bi-plus-circle me-2"></i> Ajouter un véhicule</a>
    </nav>
    <div class="nav-section">Utilisateurs &amp; contenu</div>
    <nav class="nav flex-column">
        <a class="nav-link <?= $active('clients') ?>" href="?action=admin_clients"><i class="bi bi-people me-2"></i> Clients</a>
        <a class="nav-link <?= $active('staff') ?>" href="?action=admin_staff"><i class="bi bi-shield-lock me-2"></i> Équipe admin</a>
        <a class="nav-link <?= $active('comments') ?>" href="?action=admin_commentaires"><i class="bi bi-star-half me-2"></i> Avis &amp; commentaires</a>
        <a class="nav-link" href="?action=inscriptionAdmin"><i class="bi bi-person-plus me-2"></i> Nouvel admin</a>
    </nav>
    <div class="nav-section">Ressources</div>
    <nav class="nav flex-column">
        <a class="nav-link" href="?action=agence_liste"><i class="bi bi-building me-2"></i> Agences</a>
        <a class="nav-link" href="?action=catalogue_public" target="_blank" rel="noopener"><i class="bi bi-window me-2"></i> Vitrine publique</a>
        <a class="nav-link" href="?action=compte"><i class="bi bi-person-badge me-2"></i> Mon compte</a>
    </nav>
</aside>
