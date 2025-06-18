-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : sam. 14 juin 2025 à 20:14
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
-- Base de données : `match_reservation`
--

-- --------------------------------------------------------

--
-- Structure de la table `activites`
--

CREATE TABLE `activites` (
  `id` int(11) NOT NULL,
  `lieu` varchar(100) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `titre` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `classements`
--

CREATE TABLE `classements` (
  `id` int(11) NOT NULL,
  `equipe_id` int(11) NOT NULL,
  `points` int(11) DEFAULT 0,
  `joues` int(11) DEFAULT 0,
  `gagnes` int(11) DEFAULT 0,
  `nuls` int(11) DEFAULT 0,
  `perdues` int(11) DEFAULT 0,
  `buts_marques` int(11) DEFAULT 0,
  `buts_encaisses` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `equipes`
--

CREATE TABLE `equipes` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `groupe` varchar(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `equipes`
--

INSERT INTO `equipes` (`id`, `nom`, `groupe`) VALUES
(1, 'États‑Unis', ''),
(2, 'Canada', ''),
(3, 'Mexique', ''),
(4, 'Japon', 'AF'),
(5, 'Iran', 'AF'),
(6, 'Corée du Sud', 'AF'),
(7, 'Jordanie', 'AF'),
(8, 'Ouzbékistan', 'AF'),
(9, 'Australie', 'AF'),
(10, 'Nouvelle-Zélande', 'OF'),
(11, 'Argentine', 'CO'),
(12, 'Brésil', 'CO'),
(13, 'Équateur', 'CO'),
(14, 'A2', ''),
(15, 'A1', ''),
(16, 'A3', ''),
(17, 'A4', ''),
(18, 'C1', ''),
(19, 'C3', ''),
(20, 'D3', ''),
(21, 'D4', ''),
(22, 'B2', ''),
(23, 'D2', ''),
(24, 'Bolivie', ''),
(25, 'Chili', ''),
(26, 'Uruguay', ''),
(27, 'Venezuela', ''),
(28, 'Colombie', ''),
(29, 'Paraguay', ''),
(30, 'Pérou', '');

-- --------------------------------------------------------

--
-- Structure de la table `matchs`
--

CREATE TABLE `matchs` (
  `id` int(11) NOT NULL,
  `date_match` date NOT NULL,
  `heure_match` time NOT NULL,
  `lieu` varchar(100) NOT NULL,
  `equipe1_id` int(11) DEFAULT NULL,
  `equipe2_id` int(11) DEFAULT NULL,
  `stade_id` int(11) DEFAULT NULL,
  `score_equipe1` int(11) DEFAULT NULL,
  `score_equipe2` int(11) DEFAULT NULL,
  `phase` varchar(100) DEFAULT NULL,
  `groupe` varchar(10) DEFAULT NULL,
  `categorie` varchar(100) DEFAULT 'Coupe du Monde 2026'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `matchs`
--

INSERT INTO `matchs` (`id`, `date_match`, `heure_match`, `lieu`, `equipe1_id`, `equipe2_id`, `stade_id`, `score_equipe1`, `score_equipe2`, `phase`, `groupe`, `categorie`) VALUES
(1, '2025-06-11', '21:00:00', 'France', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Coupe du Monde 2026'),
(3, '2025-06-07', '18:00:00', 'Ta’ Qali', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Coupe du Monde 2026'),
(4, '2025-06-07', '21:45:00', 'Helsinki', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Coupe du Monde 2026'),
(5, '2025-07-10', '20:45:00', 'Groningen', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Coupe du Monde 2026'),
(6, '2025-06-10', '21:45:00', 'Helsinki', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Coupe du Monde 2026'),
(7, '2026-06-11', '21:00:00', '', 3, 14, 1, NULL, NULL, 'phase de groupes', 'A', 'Coupe du Monde 2026'),
(8, '2026-06-11', '18:00:00', '', 16, 17, 3, NULL, NULL, 'phase de groupes', 'A', 'Coupe du Monde 2026'),
(9, '2026-06-13', '20:00:00', '', 18, 19, 15, NULL, NULL, 'phase de groupes', 'C', 'Coupe du Monde 2026'),
(10, '2026-06-13', '14:00:00', '', 20, 21, 5, NULL, NULL, 'phase de groupes', 'D', 'Coupe du Monde 2026'),
(11, '2026-06-12', '18:00:00', '', 2, 22, 4, NULL, NULL, 'phase de groupes', 'B', 'Coupe du Monde 2026'),
(12, '2026-06-12', '20:00:00', '', 1, 23, 11, NULL, NULL, 'phase de groupes', 'D', 'Coupe du Monde 2026'),
(13, '2025-06-10', '16:00:00', '', 24, 25, 17, NULL, NULL, 'Journée 16 – aller', '---', 'Coupe du Monde 2026'),
(14, '2025-06-10', '19:00:00', '', 26, 27, 18, NULL, NULL, 'Journée 16 – aller', '---', 'Coupe du Monde 2026'),
(15, '2025-06-10', '20:00:00', '', 11, 28, 19, NULL, NULL, 'Journée 16 – aller', '---', 'Coupe du Monde 2026'),
(16, '2025-06-10', '20:45:00', '', 12, 29, 20, NULL, NULL, 'Journée 16 – aller', '---', 'Coupe du Monde 2026'),
(17, '2025-06-10', '21:30:00', '', 30, 13, 21, NULL, NULL, 'Journée 16 – aller', '---', 'Coupe du Monde 2026');

-- --------------------------------------------------------

--
-- Structure de la table `paiements`
--

CREATE TABLE `paiements` (
  `id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `montant` float NOT NULL,
  `statut` varchar(50) DEFAULT NULL,
  `date_paiement` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `quantite` int(11) NOT NULL DEFAULT 1,
  `prix_total` float NOT NULL,
  `date_reservation` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `stades`
--

CREATE TABLE `stades` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `ville` varchar(100) DEFAULT NULL,
  `capacite` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `stades`
--

INSERT INTO `stades` (`id`, `nom`, `ville`, `capacite`) VALUES
(1, 'Estadio Azteca', 'Mexico City, Mexique', 87523),
(2, 'Estadio BBVA', 'Monterrey, Mexique', 53500),
(3, 'Estadio Akron', 'Zapopan (Guadalajara), Mexique', 49850),
(4, 'BMO Field', 'Toronto, Canada', 30000),
(5, 'BC Place', 'Vancouver, Canada', 54500),
(6, 'MetLife Stadium', 'East Rutherford (New Jersey, USA)', 82500),
(7, 'AT&T Stadium', 'Arlington (Dallas, USA)', 80000),
(8, 'Arrowhead Stadium', 'Kansas City, USA', 76416),
(9, 'NRG Stadium', 'Houston, USA', 72220),
(10, 'Mercedes-Benz Stadium', 'Atlanta, USA', 71000),
(11, 'SoFi Stadium', 'Inglewood (Los Angeles), USA', 70240),
(12, 'Lincoln Financial Field', 'Philadelphia, USA', 69796),
(13, 'Lumen Field', 'Seattle, USA', 69000),
(14, 'Levi’s Stadium', 'Santa Clara (SF Bay Area), USA', 68500),
(15, 'Gillette Stadium', 'Foxborough (Boston), USA', 65878),
(16, 'Hard Rock Stadium', 'Miami Gardens, USA', 64767),
(17, 'Estadio Municipal Villa Ingenio', 'El Alto (La Paz), Bolivie', 0),
(18, 'Estadio Centenario', 'Montevideo, Uruguay', 0),
(19, 'Estadio Mâs Monumental (River Plate)', 'Buenos Aires, Argentine', 0),
(20, 'Neo Química Arena', 'São Paulo, Brésil', 0),
(21, 'Estadio Nacional', 'Lima, Pérou', 0);

-- --------------------------------------------------------

--
-- Structure de la table `tickets`
--

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `match_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `prix_base` float NOT NULL,
  `entree` varchar(10) NOT NULL,
  `numero_place` varchar(20) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `tickets`
--

INSERT INTO `tickets` (`id`, `match_id`, `type`, `prix_base`, `entree`, `numero_place`, `description`) VALUES
(1, 7, 'Standard', 100, 'A', 'A12', 'Place standard derrière les buts.'),
(2, 7, 'Standard', 100, 'A', 'A13', 'Place standard derrière les buts.'),
(3, 7, 'VIP', 250, 'B', 'B01', 'Accès VIP avec buffet et siège premium.'),
(4, 7, 'VIP', 250, 'B', 'B02', 'Accès VIP avec buffet et siège premium.'),
(5, 7, 'Famille', 80, 'C', 'C10', 'Zone famille, proche des toilettes et snack.'),
(6, 7, 'Handicapé', 50, 'D', 'D01', 'Espace accessible pour personnes à mobilité réduite.');

-- --------------------------------------------------------

--
-- Structure de la table `transports`
--

CREATE TABLE `transports` (
  `id` int(11) NOT NULL,
  `nom_transport` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `contact` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `transports`
--

INSERT INTO `transports` (`id`, `nom_transport`, `description`, `contact`) VALUES
(1, 'Bus Ligne 1', 'Bus qui dessert le stade toutes les 30 minutes.', 'Téléphone : 0123456789'),
(2, 'Métro Station Stade', 'Station de métro à 5 minutes du stade.', 'Email : contact@metro.com');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `prenom` varchar(100) NOT NULL,
  `telephone` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `date_inscription` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `nom`, `prenom`, `telephone`, `email`, `mot_de_passe`, `date_inscription`) VALUES
(1, 'Alice Dupont', '', 0, 'alice@example.com', '123', '2025-06-12 15:12:25'),
(2, 'Bob Martin', '', 0, 'bob@example.com', '$2y$10$NDs2c9m7p2NElTmZL6pTUOaB1mbkBRXhStF.YhC2AIh4h81MG6iD6', '2025-06-12 15:12:25'),
(3, 'Claire Leblanc', '', 0, 'claire@example.com', '12345', '2025-06-12 15:12:25'),
(4, 'Moumene', 'Meri', 0, 'meryemmoumene11@gmail.com', '$2y$10$Foe1slEKht6O5ZBgmby99.3OlTA7/a4TnIb/wU.CfWhbcPhiuKqVa', '2025-06-12 21:49:33'),
(5, 'a aa', '', 0, 'aa@example.com', '$2y$10$6DVY/odk0xNkNuo9TDj1AeGavlvVoydKKKgeuKQ6V9j1mnz.EySI.', '2025-06-12 22:53:17'),
(6, 'bb', '', 0, 'bb@example.com', '$2y$10$5tqQnK3ZzZfvA4W3XM3wOOv5v2CnY27mBGhBa15Ge3QKJJmE5Rrz2', '2025-06-13 14:29:46');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `activites`
--
ALTER TABLE `activites`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `classements`
--
ALTER TABLE `classements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_classements_equipe` (`equipe_id`);

--
-- Index pour la table `equipes`
--
ALTER TABLE `equipes`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `matchs`
--
ALTER TABLE `matchs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `equipe1_id` (`equipe1_id`),
  ADD KEY `equipe2_id` (`equipe2_id`),
  ADD KEY `stade_id` (`stade_id`);

--
-- Index pour la table `paiements`
--
ALTER TABLE `paiements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservation_id` (`reservation_id`);

--
-- Index pour la table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`),
  ADD KEY `ticket_id` (`ticket_id`);

--
-- Index pour la table `stades`
--
ALTER TABLE `stades`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `match_id` (`match_id`);

--
-- Index pour la table `transports`
--
ALTER TABLE `transports`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `activites`
--
ALTER TABLE `activites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `classements`
--
ALTER TABLE `classements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `equipes`
--
ALTER TABLE `equipes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT pour la table `matchs`
--
ALTER TABLE `matchs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT pour la table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `stades`
--
ALTER TABLE `stades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT pour la table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `transports`
--
ALTER TABLE `transports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `classements`
--
ALTER TABLE `classements`
  ADD CONSTRAINT `fk_classements_equipe` FOREIGN KEY (`equipe_id`) REFERENCES `equipes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `matchs`
--
ALTER TABLE `matchs`
  ADD CONSTRAINT `matchs_ibfk_1` FOREIGN KEY (`equipe1_id`) REFERENCES `equipes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `matchs_ibfk_2` FOREIGN KEY (`equipe2_id`) REFERENCES `equipes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `matchs_ibfk_3` FOREIGN KEY (`stade_id`) REFERENCES `stades` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `paiements`
--
ALTER TABLE `paiements`
  ADD CONSTRAINT `paiements_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`match_id`) REFERENCES `matchs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
