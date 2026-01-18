-- Script SQL pour insérer des données d'exemple supplémentaires dans GesParking
-- À exécuter après avoir créé la base de données avec database_schema.sql

USE gestion_parc_auto;

-- Insertion de véhicules supplémentaires
INSERT INTO vehicule (genre, marque, model, immatriculation, annee_de_mise_en_cir) VALUES
('Voiture', 'Toyota', 'Corolla', 'AA-111-BB', 2023),
('Voiture', 'Honda', 'Civic', 'CC-222-DD', 2022),
('Voiture', 'Nissan', 'Qashqai', 'EE-333-FF', 2021),
('Camion', 'MAN', 'TGX', 'GG-444-HH', 2020),
('Utilitaire', 'Opel', 'Vivaro', 'II-555-JJ', 2019),
('Voiture', 'Seat', 'Leon', 'KK-666-LL', 2023),
('Voiture', 'Skoda', 'Octavia', 'MM-777-NN', 2022),
('Camion', 'DAF', 'XF', 'OO-888-PP', 2021),
('Utilitaire', 'Mercedes', 'Sprinter', 'QQ-999-RR', 2020),
('Voiture', 'Hyundai', 'i30', 'SS-000-TT', 2023);

-- Insertion de chauffeurs supplémentaires
INSERT INTO chauffeur (nom_chauffeur, prenom_chauffeur, tel_chauffeur) VALUES
('Bernard', 'Luc', '06.11.22.33.44'),
('Thomas', 'Julie', '06.22.33.44.55'),
('Petit', 'Nicolas', '06.33.44.55.66'),
('Durand', 'Emilie', '06.44.55.66.77'),
('Leroy', 'Christophe', '06.55.66.77.88');

-- Insertion de garages supplémentaires
INSERT INTO garage (travaux_entretien, travaux_reparation, date_entretien) VALUES
('Révision moteur, changement huile', 'Réparation freins à disque', '2024-04-01'),
('Contrôle pneumatiques, équilibrage', 'Réparation système électrique', '2024-04-15'),
('Vidange complète, filtres à air', 'Réparation transmission automatique', '2024-05-01'),
('Révision annuelle, contrôle pollution', 'Réparation suspension pneumatique', '2024-05-10'),
('Changement pneus été, freins', 'Réparation climatisation et chauffage', '2024-05-20');

-- Insertion de pannes supplémentaires
INSERT INTO panne (libelle, id_garage) VALUES
('Batterie déchargée', 9),
('Pneu éclaté', 10),
('Problème de démarrage', 11),
('Voyant moteur allumé', 12),
('Perte de puissance', 13),
('Bruit suspect au freinage', NULL),
('Consommation excessive', NULL),
('Problème de direction', NULL);

-- Insertion de dossiers supplémentaires
INSERT INTO dossier (date) VALUES
('2024-04-01'),
('2024-04-10'),
('2024-04-15'),
('2024-05-01'),
('2024-05-05'),
('2024-05-10'),
('2024-05-15'),
('2024-05-20');

-- Association chauffeurs-véhicules supplémentaires
INSERT INTO chauffeur_vehicule (id_chauffeur, id_vehicule) VALUES
(11, 16), (11, 17),
(12, 18), (12, 19),
(13, 20), (13, 21),
(14, 22), (14, 23),
(15, 24), (15, 25);

-- Association dossiers-véhicules supplémentaires
INSERT INTO dossier_vehicule (id_dossier, id_vehicule) VALUES
(11, 16), (11, 17), (11, 18),
(12, 19), (12, 20),
(13, 21), (13, 22), (13, 23),
(14, 24), (14, 25),
(15, 16), (15, 19),
(16, 17), (16, 20),
(17, 18), (17, 21),
(18, 22), (18, 23);

-- Entrées en garage supplémentaires (véhicules actuellement en réparation)
INSERT INTO vehicule_garage_entree (id_vehicule, id_garage, date_entree) VALUES
(16, 9, '2024-04-01 09:00:00'),
(17, 10, '2024-04-10 14:30:00'),
(18, 11, '2024-04-15 11:15:00'),
(19, 12, '2024-05-01 08:45:00'),
(20, 13, '2024-05-05 16:20:00');

-- Sorties de garage supplémentaires (véhicules réparés)
INSERT INTO vehicule_garage_entree (id_vehicule, id_garage, date_entree, date_sortie) VALUES
(21, 9, '2024-03-15 09:00:00', '2024-03-18 17:00:00'),
(22, 10, '2024-03-20 10:30:00', '2024-03-22 16:45:00'),
(23, 11, '2024-03-25 08:15:00', '2024-03-27 14:20:00'),
(24, 12, '2024-04-05 11:00:00', '2024-04-07 15:30:00'),
(25, 13, '2024-04-10 09:45:00', '2024-04-12 12:15:00');