CREATE DATABASE IF NOT EXISTS gestion_salles;

USE gestion_salles;

CREATE TABLE salles_de_classe (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    capacite INT NOT NULL,
    disponible TINYINT(1) DEFAULT 1
);

CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_salle INT NOT NULL,
    nom_personne VARCHAR(100) NOT NULL,
    date_reservation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_salle) REFERENCES salles_de_classe(id)
);

-- Insérer des salles par défaut
INSERT INTO salles_de_classe (nom, capacite, disponible) VALUES
('Salle A', 30, 1),
('Salle B', 50, 1),
('Salle C', 20, 1);
