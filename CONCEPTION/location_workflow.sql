-- Workflow location / réservation — exécuter sur `b2_tp_agence`
-- Requiert la table `reservation` (clé `id`). Si une colonne existe déjà, adaptez ou ignorez l’erreur.

ALTER TABLE `reservation`
    ADD COLUMN `statut` VARCHAR(32) NOT NULL DEFAULT 'en_attente'
        COMMENT 'en_attente|confirmee_ligne|confirmee_agence|annulee'
        AFTER `date_reservation`,
    ADD COLUMN `paiement_en_ligne` TINYINT(1) NOT NULL DEFAULT 0 AFTER `statut`,
    ADD COLUMN `updated_at` DATETIME NULL DEFAULT NULL
        ON UPDATE CURRENT_TIMESTAMP
        COMMENT 'Dernière mise à jour (suivi)' AFTER `paiement_en_ligne`;

-- Réservations déjà présentes avant migration : traitées comme finalisées en ligne (évite de bloquer l’historique).
-- ⚠ À n’exécuter qu’une seule fois juste après l’ALTER : ne pas relancer sur une base déjà migrée (sinon les « en_attente » réelles repasseraient en confirmées).
UPDATE `reservation`
SET `statut` = 'confirmee_ligne', `paiement_en_ligne` = 1
WHERE `statut` = 'en_attente';
