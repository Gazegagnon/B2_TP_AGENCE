-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 15 mai 2026 à 19:16
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `b2_tp_agence`
--

-- --------------------------------------------------------

--
-- Structure de la table `admin_notification`
--

CREATE TABLE `admin_notification` (
  `id` int(11) NOT NULL,
  `categorie` varchar(32) NOT NULL DEFAULT 'info',
  `titre` varchar(180) NOT NULL,
  `message` text DEFAULT NULL,
  `lien` varchar(255) DEFAULT NULL,
  `lu` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `admin_notification`
--

INSERT INTO `admin_notification` (`id`, `categorie`, `titre`, `message`, `lien`, `lu`, `created_at`) VALUES
(1, 'reservation', 'Nouvelle réservation', 'vv zz — BMW 4x4 du 2026-05-15 au 2026-05-24.', '?action=admin_dashboard', 1, '2026-05-15 18:28:46'),
(2, 'message', 'Message service client', 'bonjour', '?action=admin_messagerie', 1, '2026-05-15 19:04:51');

-- --------------------------------------------------------

--
-- Structure de la table `agence`
--

CREATE TABLE `agence` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) DEFAULT NULL,
  `adresse` varchar(50) DEFAULT NULL,
  `cp` int(11) DEFAULT NULL,
  `ville` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `agence`
--

INSERT INTO `agence` (`id`, `nom`, `adresse`, `cp`, `ville`) VALUES
(1, 'Agence Centrale', '10 rue Principale', 75000, 'Paris'),
(2, 'Agence Sud', '25 avenue du Midi', 13000, 'Marseille'),
(3, 'Agence Nord', '5 boulevard du Nord', 59000, 'Lille');

-- --------------------------------------------------------

--
-- Structure de la table `commentaire`
--

CREATE TABLE `commentaire` (
  `id` int(11) NOT NULL,
  `id_vehicule` int(11) NOT NULL,
  `id_personne` int(11) NOT NULL,
  `contenu` text NOT NULL,
  `date_commentaire` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `date_`
--

CREATE TABLE `date_` (
  `date_reservation` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `locations`
--

CREATE TABLE `locations` (
  `idlocation` int(11) NOT NULL,
  `idvehicule` int(11) NOT NULL,
  `idclient` int(11) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  `statut` varchar(50) DEFAULT 'en cours'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `message_interne`
--

CREATE TABLE `message_interne` (
  `id` int(11) NOT NULL,
  `expediteur_id` int(11) NOT NULL,
  `destinataire_id` int(11) NOT NULL,
  `corps` text NOT NULL,
  `lu_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `message_interne`
--

INSERT INTO `message_interne` (`id`, `expediteur_id`, `destinataire_id`, `corps`, `lu_at`, `created_at`) VALUES
(1, 12, 1, 'bonjour', NULL, '2026-05-15 19:04:51'),
(2, 3, 12, 'nous avons bien reçu votre demande.\r\nun agent va va vous contact au plus vite', NULL, '2026-05-15 19:09:26');

-- --------------------------------------------------------

--
-- Structure de la table `personne`
--

CREATE TABLE `personne` (
  `id` int(11) NOT NULL,
  `sexe` varchar(10) DEFAULT NULL,
  `prenom` varchar(50) DEFAULT NULL,
  `nom` varchar(50) DEFAULT NULL,
  `login` varchar(10) DEFAULT NULL,
  `mdp` varchar(100) DEFAULT NULL,
  `role` varchar(10) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `date_inscription` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `personne`
--

INSERT INTO `personne` (`id`, `sexe`, `prenom`, `nom`, `login`, `mdp`, `role`, `email`, `date_inscription`) VALUES
(1, 'homme', 'Hamidou', 'BANCE', 'Hamid', '$2y$10$hI/0CJWoUMSw0Tbrl7QheO4bbZoh/vw2i.2bc2pS8PO6L7bXM7x/a', 'ADMIN', 'hamidoubance53@gmail.com', '2024-06-08 00:52:21'),
(2, 'homme', 'bab', 'BARA', 'admin', '$2y$10$6e0Tba/KAbBJW/SFsCV8YOOXo8me/3VrdnTvFPKBVfAY/jOxiFnR.', 'CLIENT', 'babrab@gmail.com', '2024-06-08 01:07:38'),
(3, 'homme', 'Hamidou', 'BANCE', 'Hamidou', '$2y$10$HQs0wOg83clKMEpNwzWeS.JHDLtavrTkAyLeBzCWTrhHLUVAdT9We', 'ADMIN', 'hamidoubance94@gmail.com', '2024-06-08 01:10:18'),
(4, 'femme', 'ilci', 'ilci', 'iesig', '$2y$10$zFWwhV/ErbFPJmwDFhWgKuU8nQDaIMM/PhxYwVT.94EvfHUus41w2', 'CLIENT', 'iesig-education@gmail.com', '2024-06-16 02:22:15'),
(5, 'homme', 'BB', 'AA', 'aabb', '$2y$10$NZUFbmZUxNFZd4HN1Kfc9.NzILK8ABd5TQPW9YbroIjY.drqghIb6', 'CLIENT', 'a@gmail.com', '2025-11-28 01:44:18'),
(7, 'femme', 'Raïnatou', 'BILLA', 'reine', '$2y$10$3uf/8LTdGTZht3yJutIxpueN3OGMKcQlVmqpfR8wzEgeieZfhKfQm', 'CLIENT', 'bil@gmail.com', '2025-11-28 05:28:34'),
(8, 'homme', 'nn', 'mm', 'mn1234', '$2y$10$gHIZ43b9rgQmGRYM5aDMi.PfJbCC6NofSV4KPsVjTGMpEMSgKQ3RK', 'CLIENT', 'mn@gmail.com', '2025-12-02 03:50:13'),
(9, 'femme', 'pk', 'kp', 'kp1234', '$2y$10$bJFo5eo7QtOYKyDeT76UHuxsSByPLNBowg2yuvLDHuK6clrQIhCGa', 'CLIENT', 'kp@gmail.com', '2025-12-02 04:00:14'),
(10, 'femme', 'dado', 'daba', 'dado1234', '$2y$10$9P9tawMeJwUNW0GnrFxuke5VK5skKCAXSYadHy/Pt2gvN/rtAQ1SO', 'ADMIN', 'dd@gmail.com', '2025-12-02 04:08:12'),
(11, 'homme', 'BB', 'AA', 'ab@gmail.c', '$2y$10$glmWTsE7Ltq3y2J3uo8SzuoKrEpWJwtRLQsxpnSnWzdIVwc30LFyu', 'CLIENT', 'ab@gmail.com', '2026-05-14 17:47:15'),
(12, 'femme', 'vv', 'zz', 'zzvv', '$2y$10$Q0ffmFObNa3h2REFh1u/zeQGy14ySJlJGSk1aQm2bg9p5mB4QYsyi', 'CLIENT', 'zv@gmail.com', '2026-05-14 17:51:10');

-- --------------------------------------------------------

--
-- Structure de la table `reservation`
--

CREATE TABLE `reservation` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_vehicule` int(11) NOT NULL,
  `message` text DEFAULT NULL,
  `debut` date NOT NULL,
  `fin` date NOT NULL,
  `date_reservation` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `reservation`
--

INSERT INTO `reservation` (`id`, `id_user`, `id_vehicule`, `message`, `debut`, `fin`, `date_reservation`) VALUES
(1, 12, 10, '', '2026-05-18', '2026-05-24', '2026-05-14 18:04:30'),
(2, 12, 4, '', '2026-05-18', '2026-05-24', '2026-05-14 18:04:57'),
(3, 12, 16, '', '2026-05-15', '2026-05-24', '2026-05-15 18:28:46');

-- --------------------------------------------------------

--
-- Structure de la table `vehicule`
--

CREATE TABLE `vehicule` (
  `id` int(11) NOT NULL,
  `marque` varchar(20) DEFAULT NULL,
  `modele` varchar(50) DEFAULT NULL,
  `img` varchar(255) DEFAULT NULL COMMENT 'Nom fichier dans public/images/',
  `prix_journalier` decimal(7,2) DEFAULT NULL,
  `couleur` varchar(50) DEFAULT NULL,
  `poids` int(11) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `capacite` int(11) DEFAULT NULL,
  `etat` varchar(50) DEFAULT NULL,
  `statut_parc` varchar(32) NOT NULL DEFAULT 'disponible' COMMENT 'disponible|en_location|maintenance|indisponible',
  `nombre_porte` int(11) DEFAULT NULL,
  `longueur` int(11) DEFAULT NULL,
  `cylindre` int(11) DEFAULT NULL,
  `id_agence` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `vehicule`
--

INSERT INTO `vehicule` (`id`, `marque`, `modele`, `img`, `prix_journalier`, `couleur`, `poids`, `type`, `capacite`, `etat`, `statut_parc`, `nombre_porte`, `longueur`, `cylindre`, `id_agence`) VALUES
(4, 'Renault', 'Kangoo', NULL, 50.00, 'Blanc', 1200, 'voiture', 5, 'disponible', 'disponible', 4, NULL, NULL, 1),
(5, 'Peugeot', '208', NULL, 40.00, 'Rouge', 1100, 'voiture', 5, 'disponible', 'disponible', 4, NULL, NULL, 2),
(6, 'Citroen', 'C3', NULL, 45.00, 'Bleu', 1150, 'voiture', 5, 'disponible', 'disponible', 4, NULL, NULL, 3),
(7, 'Peugeot', 'Boxer', NULL, 80.00, 'Bleu', 2500, 'camion', 12, 'disponible', 'disponible', 2, 5, NULL, 1),
(8, 'Renault', 'Master', NULL, 90.00, 'Blanc', 2800, 'camion', 15, 'disponible', 'disponible', 2, 6, NULL, 2),
(9, 'Mercedes', 'Sprinter', NULL, 100.00, 'Gris', 3000, 'camion', 18, 'disponible', 'disponible', 2, 7, NULL, 3),
(10, 'Yamaha', 'MT-07', 'm1.jpeg', 20.00, 'Noir', 180, 'moto', 2, 'neuf', 'disponible', 0, 0, 700, 1),
(11, 'Kawasaki', 'Ninja', 'm1.jpeg', 35.00, 'Vert', 190, 'moto', 2, 'neuf', 'disponible', 0, 0, 750, 2),
(12, 'Honda', 'CB500F', 'm2.jpeg', 28.00, 'Rouge', 175, 'moto', 2, 'neuf', 'disponible', 0, 0, 500, 3),
(13, 'AUDI', 'YARIS', 'v1.jpeg', 45.00, 'BLANCHE', 1566, 'voiture', 8, 'occas', 'disponible', 4, 0, 0, 3),
(14, 'TOYOTA ', 'kangoo', NULL, 1234.00, NULL, 300, 'camion', 0, 'occas', 'disponible', 4, 6, NULL, 2),
(15, 'BMW', 'G7', NULL, 1344.00, NULL, 345, 'camion', 24, 'neuf', 'disponible', 6, 9, NULL, 2),
(16, 'BMW', '4x4', 'v1.jpeg', 35.00, NULL, 900, 'voiture', 5, 'occas', 'en_location', 5, NULL, NULL, 2);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `admin_notification`
--
ALTER TABLE `admin_notification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_lu` (`lu`),
  ADD KEY `idx_created` (`created_at`);

--
-- Index pour la table `agence`
--
ALTER TABLE `agence`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `commentaire`
--
ALTER TABLE `commentaire`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_commentaire_vehicule` (`id_vehicule`),
  ADD KEY `fk_commentaire_personne` (`id_personne`);

--
-- Index pour la table `date_`
--
ALTER TABLE `date_`
  ADD PRIMARY KEY (`date_reservation`);

--
-- Index pour la table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`idlocation`),
  ADD KEY `fk_locations_vehicule` (`idvehicule`),
  ADD KEY `fk_locations_client` (`idclient`);

--
-- Index pour la table `message_interne`
--
ALTER TABLE `message_interne`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_msg_exp` (`expediteur_id`),
  ADD KEY `idx_dest_created` (`destinataire_id`,`created_at`);

--
-- Index pour la table `personne`
--
ALTER TABLE `personne`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_reservation_personne` (`id_user`),
  ADD KEY `fk_reservation_vehicule` (`id_vehicule`);

--
-- Index pour la table `vehicule`
--
ALTER TABLE `vehicule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_agence` (`id_agence`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `admin_notification`
--
ALTER TABLE `admin_notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `agence`
--
ALTER TABLE `agence`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `commentaire`
--
ALTER TABLE `commentaire`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `locations`
--
ALTER TABLE `locations`
  MODIFY `idlocation` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `message_interne`
--
ALTER TABLE `message_interne`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `personne`
--
ALTER TABLE `personne`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `vehicule`
--
ALTER TABLE `vehicule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `commentaire`
--
ALTER TABLE `commentaire`
  ADD CONSTRAINT `fk_commentaire_personne` FOREIGN KEY (`id_personne`) REFERENCES `personne` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_commentaire_vehicule` FOREIGN KEY (`id_vehicule`) REFERENCES `vehicule` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `locations`
--
ALTER TABLE `locations`
  ADD CONSTRAINT `fk_locations_client` FOREIGN KEY (`idclient`) REFERENCES `personne` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_locations_vehicule` FOREIGN KEY (`idvehicule`) REFERENCES `vehicule` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `message_interne`
--
ALTER TABLE `message_interne`
  ADD CONSTRAINT `fk_msg_dest` FOREIGN KEY (`destinataire_id`) REFERENCES `personne` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_msg_exp` FOREIGN KEY (`expediteur_id`) REFERENCES `personne` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `fk_reservation_personne` FOREIGN KEY (`id_user`) REFERENCES `personne` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reservation_vehicule` FOREIGN KEY (`id_vehicule`) REFERENCES `vehicule` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `vehicule`
--
ALTER TABLE `vehicule`
  ADD CONSTRAINT `vehicule_ibfk_1` FOREIGN KEY (`id_agence`) REFERENCES `agence` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
