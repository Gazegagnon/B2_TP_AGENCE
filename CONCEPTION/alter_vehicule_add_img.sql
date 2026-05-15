-- Colonne manquante dans bd.sql : sans elle, l’image n’est jamais lue depuis la BDD.
-- À exécuter une fois dans phpMyAdmin ou mysql, base b2_tp_agence :

ALTER TABLE vehicule
ADD COLUMN img VARCHAR(255) NULL DEFAULT NULL COMMENT 'Nom fichier dans public/images/'
AFTER modele;
