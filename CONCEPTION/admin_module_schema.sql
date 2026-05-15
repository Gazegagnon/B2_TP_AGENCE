-- Module administration avancé — exécuter sur la base b2_tp_agence
-- (adaptation possible si une instruction échoue car l’objet existe déjà)

-- Suivi parc : statut opérationnel par véhicule
ALTER TABLE vehicule
    ADD COLUMN statut_parc VARCHAR(32) NOT NULL DEFAULT 'disponible'
    COMMENT 'disponible|en_location|maintenance|indisponible'
    AFTER etat;

-- Notifications tableau de bord
CREATE TABLE IF NOT EXISTS admin_notification (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categorie VARCHAR(32) NOT NULL DEFAULT 'info',
    titre VARCHAR(180) NOT NULL,
    message TEXT,
    lien VARCHAR(255) NULL,
    lu TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_lu (lu),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Messagerie interne (admin ↔ client)
CREATE TABLE IF NOT EXISTS message_interne (
    id INT AUTO_INCREMENT PRIMARY KEY,
    expediteur_id INT NOT NULL,
    destinataire_id INT NOT NULL,
    corps TEXT NOT NULL,
    lu_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_msg_exp FOREIGN KEY (expediteur_id) REFERENCES personne(id) ON DELETE CASCADE,
    CONSTRAINT fk_msg_dest FOREIGN KEY (destinataire_id) REFERENCES personne(id) ON DELETE CASCADE,
    INDEX idx_dest_created (destinataire_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Si la table commentaire n’a pas encore de clé surrogate id, décommentez et adaptez :
ALTER TABLE commentaire ADD COLUMN id INT AUTO_INCREMENT PRIMARY KEY FIRST;
