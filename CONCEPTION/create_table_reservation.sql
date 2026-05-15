-- Table attendue par model/ReservationModel.php ($table = "reservation")
-- À exécuter dans la base b2_tp_agence (phpMyAdmin : SQL, ou mysql en ligne de commande)

CREATE TABLE IF NOT EXISTS reservation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    id_vehicule INT NOT NULL,
    message TEXT NULL,
    debut DATE NOT NULL,
    fin DATE NOT NULL,
    date_reservation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reservation_personne FOREIGN KEY (id_user) REFERENCES personne(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_reservation_vehicule FOREIGN KEY (id_vehicule) REFERENCES vehicule(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
