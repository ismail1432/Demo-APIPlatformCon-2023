<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230713170153 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE adresse (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            rue VARCHAR(255) NOT NULL,
            ville VARCHAR(255) NOT NULL,
            code_postal VARCHAR(255) NOT NULL)'
        );

        $this->addSql('CREATE TABLE address (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            street_name VARCHAR(255) NOT NULL,
            city VARCHAR(255) NOT NULL,
            zip_code VARCHAR(255) NOT NULL)'
        );

        $this->addSql('CREATE TABLE commande (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            utilisateur_id INTEGER DEFAULT NULL,
            nom VARCHAR(255) NOT NULL,
            prix DOUBLE PRECISION NOT NULL,
            date DATE NOT NULL,
            CONSTRAINT FK_6EEAA67DFB88E14F 
            FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE)'
        );

        $this->addSql('CREATE INDEX IDX_6EEAA67DFB88E14F ON commande (utilisateur_id)');
        $this->addSql('CREATE TABLE utilisateur (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            adresse_id INTEGER DEFAULT NULL,
            prenom VARCHAR(255) NOT NULL,
            nom VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            CONSTRAINT FK_1D1C63B34DE7DC5C FOREIGN KEY (adresse_id) REFERENCES adresse (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        ');

        $this->addSql('CREATE TABLE user (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            address_id INTEGER DEFAULT NULL,
            firstname VARCHAR(255) NOT NULL,
            name VARCHAR(255) NOT NULL,
            CONSTRAINT FK_1D1C63B34DE7DC5C FOREIGN KEY (address_id) REFERENCES address (id) NOT DEFERRABLE INITIALLY IMMEDIATE)'
        );
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1D1C63B34DE7DC5C ON utilisateur (adresse_id)');

    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE adresse');
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE user');
    }
}
