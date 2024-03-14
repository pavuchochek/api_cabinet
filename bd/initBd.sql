CREATE TABLE medecin(
   id_medecin INT AUTO_INCREMENT,
   civilite VARCHAR(5) NOT NULL,
   nom VARCHAR(50) NOT NULL,
   prenom VARCHAR(50) NOT NULL,
   CONSTRAINT PK_medecin PRIMARY KEY(id_medecin)
);

CREATE TABLE usager(
   id_usager INT AUTO_INCREMENT,
   civilite VARCHAR(50) NOT NULL,
   nom VARCHAR(50) NOT NULL,
   prenom VARCHAR(50) NOT NULL,
   sexe CHAR(1) NOT NULL,
   adresse VARCHAR(50) NOT NULL,
   code_postal CHAR(5) NOT NULL,
   ville VARCHAR(50) NOT NULL,
   date_nais DATE NOT NULL,
   lieu_nais VARCHAR(50) NOT NULL,
   num_secu CHAR(15) NOT NULL,
   id_medecin INT,
   CONSTRAINT PK_usager PRIMARY KEY(id_usager),
   CONSTRAINT AK_usager UNIQUE(num_secu),
   CONSTRAINT FK_usager_medecin FOREIGN KEY(id_medecin) REFERENCES medecin(id_medecin)
);

CREATE TABLE consultation(
   id_consult INT AUTO_INCREMENT,
   date_consult DATE NOT NULL,
   heure_consult TIME NOT NULL,
   duree_consult TINYINT NOT NULL,
   id_medecin INT NOT NULL,
   id_usager INT NOT NULL,
   CONSTRAINT PK_consultation PRIMARY KEY(id_consult),
   CONSTRAINT AK_consultation_idx2 UNIQUE(id_medecin, date_consult, heure_consult),
   CONSTRAINT AK_consultation_idx1 UNIQUE(id_usager, date_consult, heure_consult),
   CONSTRAINT FK_consultation_medecin FOREIGN KEY(id_medecin) REFERENCES medecin(id_medecin),
   CONSTRAINT FK_consultation_usager FOREIGN KEY(id_usager) REFERENCES usager(id_usager)
);


INSERT INTO medecin (civilite, nom, prenom) VALUES
('M.', 'Dupont', 'Jean'),
('Mme', 'Martin', 'Marie'),
('Mme', 'Lefevre', 'Sophie'),
('M.', 'Dubois', 'Pierre'),
('Mme', 'Moreau', 'Claire'),
('M.', 'Garcia', 'Luc'),
('Mme', 'Roux', 'Emma'),
('M.', 'Fournier', 'Thomas'),
('Mme', 'Vincent', 'Camille'),
('Mme', 'Leroy', 'Julie');
INSERT INTO usager (civilite, nom, prenom, sexe, adresse, code_postal, ville, date_nais, lieu_nais, num_secu, id_medecin) 
VALUES 
('M.', 'Dubois', 'Jean', 'M', '12 Rue des Lilas', '75001', 'Paris', '1980-05-15', 'Paris', '180518012345678', 1),
('Mme', 'Martin', 'Sophie', 'F', '24 Avenue Victor Hugo', '69001', 'Lyon', '1975-07-20', 'Lyon', '750720123456789', 1),
('M.', 'Lefebvre', 'Pierre', 'M', '8 Rue de la Paix', '33000', 'Bordeaux', '1990-12-10', 'Bordeaux', '901210234567890', NULL),
('Mme', 'Leroy', 'Marie', 'F', '5 Boulevard Voltaire', '13001', 'Marseille', '1988-03-25', 'Marseille', '880325345678901', 1),
('M.', 'Garcia', 'Antoine', 'M', '3 Rue de la République', '69002', 'Lyon', '1972-09-08', 'Lyon', '720908456789012', 4),
('Mme', 'Moreau', 'Julie', 'F', '18 Avenue des Champs-Élysées', '75008', 'Paris', '1985-11-12', 'Paris', '851112567890123', NULL),
('M.', 'Roux', 'Thomas', 'M', '15 Rue du Commerce', '59000', 'Lille', '1995-02-28', 'Lille', '950228678901234', 1),
('Mme', 'Fournier', 'Emma', 'F', '10 Rue de la Liberté', '69003', 'Lyon', '1977-06-30', 'Lyon', '770630789012345', 1),
('M.', 'Petit', 'Nicolas', 'M', '7 Avenue de la République', '75010', 'Paris', '1983-08-18', 'Paris', '830818890123456', 6),
('Mme', 'Dupont', 'Céline', 'F', '22 Rue Saint-Michel', '33000', 'Bordeaux', '1992-04-05', 'Bordeaux', '920405901234567', 3);
-- Consultations passées
INSERT INTO consultation (date_consult, heure_consult, duree_consult, id_medecin, id_usager)
VALUES 
('2023-12-15', '09:30:00', 30, 1, 1),
('2023-11-20', '14:00:00', 45, 2, 2),
('2023-10-10', '11:15:00', 60, 3, 3),
('2023-09-05', '08:45:00', 30, 4, 4),
('2023-08-25', '16:30:00', 45, 5, 5);

-- Consultations futures
INSERT INTO consultation (date_consult, heure_consult, duree_consult, id_medecin, id_usager)
VALUES 
('2024-06-20', '10:00:00', 30, 1, 2),
('2024-07-05', '15:30:00', 45, 2, 3),
('2024-08-10', '09:45:00', 60, 3, 4),
('2024-09-15', '11:30:00', 30, 4, 1),
('2024-10-20', '14:15:00', 45, 5, 1);
