/*
  Auteur : Enzo Pauchet
  Projet : EcoRide
  Description :
    Ce fichier SQL couvre :
      - La création complète de la base de données relationnelle du projet
      - Les contraintes d’intégrité référentielle
      - Des requêtes avancées de sélection, insertion et mise à jour
      - La gestion transactionnelle pour éviter les conflits d’accès
    Il sert de preuve de ma capacité à maîtriser le langage SQL de manière manuelle
    sans dépendance aux outils automatiques (ORM, migrations, fixtures).
*/

SET NAMES utf8mb4;

CREATE DATABASE ecoride;
USE ecoride;

-- Table des marques de voiture
CREATE TABLE `car_brand` (
  `id` int NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `label` varchar(255) NOT NULL COMMENT 'Nom de la marque'
);

-- Table des modèles de voiture
CREATE TABLE `car_model` (
  `id` int NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `brand_id` int NOT NULL COMMENT 'Référence à la marque',
  `label` varchar(255) NOT NULL COMMENT 'Nom du modèle',
  `energy` varchar(255) NOT NULL COMMENT "Type d'énergie (essence, diesel, électrique, etc.)"
);

-- Table des transactions de crédits des utilisateurs
CREATE TABLE `credit_transaction` (
  `id` int NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `user_id` int NOT NULL COMMENT 'Utilisateur concerné',
  `ride_id` int NOT NULL COMMENT 'Trajet concerné',
  `amount` int NOT NULL COMMENT 'Montant des crédits',
  `reason` varchar(255) NOT NULL COMMENT 'Raison de la transaction',
  `created_at` datetime NOT NULL COMMENT 'Date de création de la transaction'
);

-- Table des participations des utilisateurs aux trajets
CREATE TABLE `participation` (
  `id` int NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `user_id` int NOT NULL COMMENT 'Utilisateur participant',
  `ride_id` int NOT NULL COMMENT 'Trajet concerné',
  `status` varchar(255) NOT NULL COMMENT 'Statut de la participation',
  `credits_used` int NOT NULL COMMENT 'Crédits utilisés'
);

-- Table des rapports signalés par les utilisateurs
CREATE TABLE `report` (
  `id` int NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `author_id` int NOT NULL COMMENT 'Auteur du rapport',
  `participation_id` int NOT NULL COMMENT 'Participation concernée',
  `description` longtext NOT NULL COMMENT 'Description du problème',
  `status` varchar(50) NOT NULL COMMENT 'Statut du rapport',
  `created_at` datetime NOT NULL COMMENT 'Date de création'
);

-- Table des avis laissés par les utilisateurs
CREATE TABLE `review` (
  `id` int NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `author_id` int NOT NULL COMMENT "Auteur de l'avis",
  `target_id` int NOT NULL COMMENT "Utilisateur cible de l'avis",
  `ride_id` int NOT NULL COMMENT 'Trajet concerné',
  `rating` int NOT NULL COMMENT 'Note attribuée',
  `comment` longtext COMMENT 'Commentaire',
  `validated` tinyint NOT NULL COMMENT "Validation de l'avis" -- 0 = false, 1 = true,
  `created_at` datetime NOT NULL COMMENT 'Date de création'
);

-- Table des trajets proposés
CREATE TABLE `ride` (
  `id` int NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `driver_id` int NOT NULL COMMENT 'Conducteur du trajet',
  `vehicle_id` int NOT NULL COMMENT 'Véhicule utilisé',
  `departure_time` datetime NOT NULL COMMENT 'Heure de départ',
  `arrival_time` datetime NOT NULL COMMENT "Heure d'arrivée",
  `price` int NOT NULL COMMENT 'Prix du trajet',
  `seats_available` int NOT NULL COMMENT 'Nombre de places disponibles',
  `ecological` tinyint NOT NULL COMMENT 'Trajet écologique -- 0 = false, 1 = true',
  `status` varchar(255) NOT NULL COMMENT 'Statut du trajet',
  `departure_city` varchar(255) NOT NULL COMMENT 'Ville de départ',
  `departure_address` varchar(255) NOT NULL COMMENT 'Adresse de départ',
  `arrival_city` varchar(255) NOT NULL COMMENT "Ville d'arrivée",
  `arrival_address` varchar(255) NOT NULL COMMENT "Adresse d'arrivée",
  `departure_post_code` varchar(10) NOT NULL COMMENT 'Code postal de départ',
  `arrival_post_code` varchar(10) NOT NULL COMMENT "Code postal d'arrivée"
);

-- Table des utilisateurs
CREATE TABLE `user` (
  `id` int NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `email` varchar(180) NOT NULL COMMENT 'Adresse email',
  `roles` json NOT NULL COMMENT "Rôles de l'utilisateur",
  `password` varchar(255) NOT NULL COMMENT 'Mot de passe hashé',
  `pseudo` varchar(255) NOT NULL UNIQUE COMMENT 'Pseudo unique',
  `created_at` datetime NOT NULL COMMENT 'Date de création du compte',
  `suspended` tinyint NOT NULL COMMENT 'Compte suspendu -- 0 = false, 1 = true',
  `photo` varchar(255) COMMENT 'Photo de profil'
);

-- Table des véhicules
CREATE TABLE `vehicle` (
  `id` int NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `car_model_id` int NOT NULL COMMENT 'Modèle de la voiture',
  `owner_id` int NOT NULL COMMENT 'Propriétaire du véhicule',
  `plate` varchar(255) NOT NULL UNIQUE COMMENT "Plaque d'immatriculation",
  `color` varchar(255) NOT NULL COMMENT 'Couleur du véhicule',
  `first_registration_date` date NOT NULL COMMENT 'Date de première immatriculation',
  `is_archived` tinyint NOT NULL COMMENT 'Véhicule archivé -- 0 = false, 1 = true'
);

-- Contraintes et clés étrangères

ALTER TABLE `car_model` 
  ADD FOREIGN KEY (`brand_id`) REFERENCES `car_brand` (`id`);

ALTER TABLE `credit_transaction`
  ADD FOREIGN KEY (`ride_id`) REFERENCES `ride` (`id`) ON DELETE CASCADE,
  ADD FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

ALTER TABLE `participation`
  ADD FOREIGN KEY (`ride_id`) REFERENCES `ride` (`id`) ON DELETE CASCADE,
  ADD FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

ALTER TABLE `report`
  ADD FOREIGN KEY (`participation_id`) REFERENCES `participation` (`id`) ON DELETE CASCADE,
  ADD FOREIGN KEY (`author_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

ALTER TABLE `review`
  ADD FOREIGN KEY (`target_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD FOREIGN KEY (`ride_id`) REFERENCES `ride` (`id`) ON DELETE CASCADE,
  ADD FOREIGN KEY (`author_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

ALTER TABLE `ride`
  ADD FOREIGN KEY (`vehicle_id`) REFERENCES `vehicle` (`id`) ON DELETE CASCADE,
  ADD FOREIGN KEY (`driver_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

ALTER TABLE `vehicle`
  ADD FOREIGN KEY (`owner_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD FOREIGN KEY (`car_model_id`) REFERENCES `car_model` (`id`);

-- Exemples de requêtes SQL :

-- RECHERCHES SQL

-- ============================================================================
-- RECHERCHE DE TRAJETS DISPONIBLES
-- ============================================================================
-- Cette requête retourne tous les trajets :
-- - avec statut 'pending'
-- - ayant au moins 1 place disponible
-- - dont l’heure de départ est à venir
-- - et correspondant à la recherche : ville de départ, ville d’arrivée, date min
-- Elle inclut aussi des infos complémentaires sur le chauffeur et le véhicule.
-- Paramètres :
--   :departureCity       → Ville de départ recherchée
--   :arrivalCity         → Ville d'arrivée recherchée
--   :minDepartureDate    → Date minimale de départ (>= maintenant)
-- ============================================================================

SELECT 
  r.*,
  u.pseudo AS driver_pseudo,
  v.plate AS vehicle_plate,
  v.color AS vehicle_color,
  cm.label AS model_label,
  cb.label AS brand_label
FROM ride r
JOIN user u ON r.driver_id = u.id
JOIN vehicle v ON r.vehicle_id = v.id
JOIN car_model cm ON v.car_model_id = cm.id
JOIN car_brand cb ON cm.brand_id = cb.id
WHERE r.seats_available > 0
  AND r.status = 'pending'
  AND r.departure_time >= NOW()
  AND r.departure_city = :departureCity
  AND r.arrival_city = :arrivalCity
  AND r.departure_time >= :minDepartureDate


-- ============================================================================
-- INSERTION D'UN NOUVEAU TRAJET
-- ============================================================================
-- Cette requête permet à un utilisateur de créer un nouveau trajet dans le système.
-- Elle suppose que le conducteur et le véhicule existent déjà en base.
-- Paramètres :
--   :driverId             → ID du conducteur
--   :vehicleId            → ID du véhicule utilisé
--   :departureTime        → Date et heure de départ
--   :arrivalTime          → Date et heure d’arrivée
--   :price                → Prix du trajet
--   :seatsAvailable       → Nombre de places disponibles
--   :ecological           → 1 si trajet écologique, 0 sinon
--   :status               → Statut du trajet (ex: 'pending')
--   :departureCity        → Ville de départ
--   :departureAddress     → Adresse de départ
--   :arrivalCity          → Ville d’arrivée
--   :arrivalAddress       → Adresse d’arrivée
--   :departurePostCode    → Code postal de départ
--   :arrivalPostCode      → Code postal d’arrivée
-- ============================================================================

INSERT INTO ride (
  driver_id,
  vehicle_id,
  departure_time,
  arrival_time,
  price,
  seats_available,
  ecological,
  status,
  departure_city,
  departure_address,
  arrival_city,
  arrival_address,
  departure_post_code,
  arrival_post_code
) VALUES (
  :driverId,
  :vehicleId,
  :departureTime,
  :arrivalTime,
  :price,
  :seatsAvailable,
  :ecological,
  'pending',
  :departureCity,
  :departureAddress,
  :arrivalCity,
  :arrivalAddress,
  :departurePostCode,
  :arrivalPostCode
);


-- ============================================================================
-- AJOUT D'UNE PARTICIPATION À UN TRAJET
-- ============================================================================
-- Cette requête permet à un utilisateur de rejoindre un trajet existant.
-- Elle suppose que le trajet existe, que l’utilisateur est inscrit,
-- et qu’il reste des places disponibles.
-- Paramètres :
--   :userId            → ID de l'utilisateur participant
--   :rideId            → ID du trajet concerné
--   :status            → Statut de la participation (ex: 'pending')
--   :creditsUsed       → Nombre de crédits utilisés pour ce trajet
-- ============================================================================

INSERT INTO participation (
  user_id,
  ride_id,
  status,
  credits_used
) VALUES (
  :userId,
  :rideId,
  'pending',
  :creditsUsed
);



-- ============================================================================
-- CONFIRMATION D’UNE PARTICIPATION PAR LE CONDUCTEUR
-- ============================================================================
-- Ce bloc de requêtes permet de confirmer une réservation 'pending' faite par un passager.
-- Étapes :
--   1. On vérifie que la participation est encore en statut 'pending'
--      et que le trajet concerné a encore des places disponibles.
--   2. Si c'est le cas, on met à jour le statut de la participation en 'confirmed'.
--   3. On décrémente le nombre de places disponibles sur le trajet.
-- Le tout est encapsulé dans une transaction pour éviter les conflits en cas d’accès concurrent.
-- Paramètre attendu :
--   :participationId   → ID de la participation à confirmer
-- ============================================================================

START TRANSACTION;

-- 1. 
SELECT r.id
FROM participation p
JOIN ride r ON p.ride_id = r.id
WHERE p.id = :participationId
  AND p.status = 'pending'
  AND r.seats_available > 0
FOR UPDATE;

-- 2.
UPDATE participation
SET status = 'confirmed'
WHERE id = :participationId
  AND status = 'pending';

-- 3.
UPDATE ride
SET seats_available = seats_available - 1
WHERE id = (
  SELECT ride_id
  FROM participation
  WHERE id = :participationId
);

COMMIT;