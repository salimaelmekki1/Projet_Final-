-- Mise à jour de la table utilisateurs
ALTER TABLE utilisateurs MODIFY COLUMN mot_de_passe VARCHAR(255) NOT NULL;

-- Mise à jour des mots de passe existants
UPDATE utilisateurs SET mot_de_passe = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' WHERE mot_de_passe = '123'; 