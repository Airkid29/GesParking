-- Script SQL pour créer la base de données complète pour GesParking
-- Base de données : gestion_parc_auto

CREATE DATABASE IF NOT EXISTS gestion_parc_auto;
USE gestion_parc_auto;

-- Table chauffeur
CREATE TABLE chauffeur (
    id_chauffeur INT PRIMARY KEY AUTO_INCREMENT,
    nom_chauffeur VARCHAR(255) NOT NULL,
    prenom_chauffeur VARCHAR(255) NOT NULL,
    tel_chauffeur VARCHAR(20)
);

-- Table vehicule
CREATE TABLE vehicule (
    id_vehicule INT PRIMARY KEY AUTO_INCREMENT,
    genre VARCHAR(50) NOT NULL,
    marque VARCHAR(100) NOT NULL,
    model VARCHAR(100) NOT NULL,
    immatriculation VARCHAR(20) UNIQUE NOT NULL,
    annee_de_mise_en_cir YEAR NOT NULL
);

-- Table garage
CREATE TABLE garage (
    id_garage INT PRIMARY KEY AUTO_INCREMENT,
    travaux_entretien TEXT,
    travaux_reparation TEXT,
    date_entretien DATE
);

-- Table panne
CREATE TABLE panne (
    id_panne INT PRIMARY KEY AUTO_INCREMENT,
    libelle VARCHAR(255) NOT NULL,
    id_garage INT,
    FOREIGN KEY (id_garage) REFERENCES garage(id_garage) ON DELETE SET NULL
);

-- Table dossier
CREATE TABLE dossier (
    id_dossier INT PRIMARY KEY AUTO_INCREMENT,
    date DATE NOT NULL
);

-- Table chauffeur_vehicule (association many-to-many)
CREATE TABLE chauffeur_vehicule (
    id_chauffeur INT NOT NULL,
    id_vehicule INT NOT NULL,
    PRIMARY KEY (id_chauffeur, id_vehicule),
    FOREIGN KEY (id_chauffeur) REFERENCES chauffeur(id_chauffeur) ON DELETE CASCADE,
    FOREIGN KEY (id_vehicule) REFERENCES vehicule(id_vehicule) ON DELETE CASCADE
);

-- Table dossier_vehicule (association many-to-many)
CREATE TABLE dossier_vehicule (
    id_dossier INT NOT NULL,
    id_vehicule INT NOT NULL,
    PRIMARY KEY (id_dossier, id_vehicule),
    FOREIGN KEY (id_dossier) REFERENCES dossier(id_dossier) ON DELETE CASCADE,
    FOREIGN KEY (id_vehicule) REFERENCES vehicule(id_vehicule) ON DELETE CASCADE
);

-- Table vehicule_garage_entree
CREATE TABLE vehicule_garage_entree (
    id_entree INT PRIMARY KEY AUTO_INCREMENT,
    id_vehicule INT NOT NULL,
    id_garage INT NOT NULL,
    date_entree DATETIME NOT NULL,
    date_sortie DATETIME,
    FOREIGN KEY (id_vehicule) REFERENCES vehicule(id_vehicule) ON DELETE CASCADE,
    FOREIGN KEY (id_garage) REFERENCES garage(id_garage) ON DELETE CASCADE
);