CREATE DATABASE IF NOT EXISTS gestion_salles;

USE gestion_salles;

CREATE TABLE salles_de_classe (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    capacite INT NOT NULL
);

CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_salle INT NOT NULL,
    nom_personne VARCHAR(100) NOT NULL,
    date_reservation DATE NOT NULL,
    FOREIGN KEY (id_salle) REFERENCES salles_de_classe(id)
);

-- Insérer des salles d'exemple
INSERT INTO salles_de_classe (nom, capacite) VALUES
('Salle A', 30),
('Salle B', 50),
('Salle C', 20);