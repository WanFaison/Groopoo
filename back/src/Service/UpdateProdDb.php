<?php

$host = '127.0.0.1';
$db   = 'irwc6611_band_it';
$user = 'irwc6611_band_it_admin';
$pass = 'zvaI]vtWT3mn';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "
    CREATE TABLE coach (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(100) DEFAULT NULL, is_archived TINYINT(1) NOT NULL, etat VARCHAR(255) DEFAULT NULL, nom VARCHAR(50) DEFAULT NULL, prenom VARCHAR(50) DEFAULT NULL, telephone VARCHAR(20) DEFAULT NULL, email VARCHAR(100) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB;
    CREATE TABLE coach_jury (coach_id INT NOT NULL, jury_id INT NOT NULL, INDEX IDX_900AD5303C105691 (coach_id), INDEX IDX_900AD530E560103C (jury_id), PRIMARY KEY(coach_id, jury_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB;
    CREATE TABLE etage (id INT AUTO_INCREMENT NOT NULL, ecole_id INT DEFAULT NULL, libelle VARCHAR(100) DEFAULT NULL, is_archived TINYINT(1) NOT NULL, INDEX IDX_2DDCF14B77EF1B1E (ecole_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB;
    CREATE TABLE jury (id INT AUTO_INCREMENT NOT NULL, liste_id INT DEFAULT NULL, libelle VARCHAR(100) DEFAULT NULL, is_archived TINYINT(1) NOT NULL, effectif INT DEFAULT NULL, INDEX IDX_1335B02CE85441D8 (liste_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB;
    CREATE TABLE salle (id INT AUTO_INCREMENT NOT NULL, etage_id INT DEFAULT NULL, libelle VARCHAR(100) DEFAULT NULL, is_archived TINYINT(1) NOT NULL, INDEX IDX_4E977E5C984CE93F (etage_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB;
    ALTER TABLE coach_jury ADD CONSTRAINT FK_900AD5303C105691 FOREIGN KEY (coach_id) REFERENCES coach (id) ON DELETE CASCADE;
    ALTER TABLE coach_jury ADD CONSTRAINT FK_900AD530E560103C FOREIGN KEY (jury_id) REFERENCES jury (id) ON DELETE CASCADE;
    ALTER TABLE etage ADD CONSTRAINT FK_2DDCF14B77EF1B1E FOREIGN KEY (ecole_id) REFERENCES ecole (id);
    ALTER TABLE jury ADD CONSTRAINT FK_1335B02CE85441D8 FOREIGN KEY (liste_id) REFERENCES liste (id);
    ALTER TABLE salle ADD CONSTRAINT FK_4E977E5C984CE93F FOREIGN KEY (etage_id) REFERENCES etage (id);
    ALTER TABLE groupe ADD salle_id INT DEFAULT NULL, ADD coach_id INT DEFAULT NULL, ADD jury_id INT DEFAULT NULL;
    ALTER TABLE groupe ADD CONSTRAINT FK_4B98C21DC304035 FOREIGN KEY (salle_id) REFERENCES salle (id);
    ALTER TABLE groupe ADD CONSTRAINT FK_4B98C213C105691 FOREIGN KEY (coach_id) REFERENCES coach (id);
    ALTER TABLE groupe ADD CONSTRAINT FK_4B98C21E560103C FOREIGN KEY (jury_id) REFERENCES jury (id);
    CREATE INDEX IDX_4B98C21DC304035 ON groupe (salle_id);
    CREATE INDEX IDX_4B98C213C105691 ON groupe (coach_id);
    CREATE INDEX IDX_4B98C21E560103C ON groupe (jury_id);
    ALTER TABLE coach ADD ecole_id INT DEFAULT NULL;
    ALTER TABLE salle ADD ecole_id INT DEFAULT NULL;
    ALTER TABLE salle ADD CONSTRAINT FK_4E977E5C77EF1B1E FOREIGN KEY (ecole_id) REFERENCES ecole (id);
    CREATE INDEX IDX_4E977E5C77EF1B1E ON salle (ecole_id);
";

if ($conn->multi_query($sql)) {
    do {
        // Store the result set if the query produces one
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->next_result());
    echo "Migration executed successfully.";
} else {
    echo "Error executing migration: " . $conn->error;
}

$conn->close();
?>
