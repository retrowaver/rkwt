<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180708101911 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE app_users (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(25) NOT NULL, password VARCHAR(64) NOT NULL, email VARCHAR(254) NOT NULL, is_active TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_C2502824F85E0677 (username), UNIQUE INDEX UNIQ_C2502824E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE search (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, status SMALLINT NOT NULL, INDEX IDX_B4F0DBA7A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE item (id INT AUTO_INCREMENT NOT NULL, search_id INT NOT NULL, auction_id BIGINT NOT NULL, auction_title VARCHAR(50) NOT NULL, auction_price DOUBLE PRECISION NOT NULL, auction_image VARCHAR(255) DEFAULT NULL, status SMALLINT NOT NULL, time_found DATETIME NOT NULL, INDEX IDX_1F1B251E650760A9 (search_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE filter_value (id INT AUTO_INCREMENT NOT NULL, filter_id INT NOT NULL, filter_value VARCHAR(255) NOT NULL, INDEX IDX_34C6ABCBD395B25E (filter_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE filter (id INT AUTO_INCREMENT NOT NULL, search_id INT NOT NULL, value_range_min VARCHAR(255) DEFAULT NULL, value_range_max VARCHAR(255) DEFAULT NULL, filter_id VARCHAR(255) NOT NULL, INDEX IDX_7FC45F1D650760A9 (search_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE search ADD CONSTRAINT FK_B4F0DBA7A76ED395 FOREIGN KEY (user_id) REFERENCES app_users (id)');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251E650760A9 FOREIGN KEY (search_id) REFERENCES search (id)');
        $this->addSql('ALTER TABLE filter_value ADD CONSTRAINT FK_34C6ABCBD395B25E FOREIGN KEY (filter_id) REFERENCES filter (id)');
        $this->addSql('ALTER TABLE filter ADD CONSTRAINT FK_7FC45F1D650760A9 FOREIGN KEY (search_id) REFERENCES search (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE search DROP FOREIGN KEY FK_B4F0DBA7A76ED395');
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251E650760A9');
        $this->addSql('ALTER TABLE filter DROP FOREIGN KEY FK_7FC45F1D650760A9');
        $this->addSql('ALTER TABLE filter_value DROP FOREIGN KEY FK_34C6ABCBD395B25E');
        $this->addSql('DROP TABLE app_users');
        $this->addSql('DROP TABLE search');
        $this->addSql('DROP TABLE item');
        $this->addSql('DROP TABLE filter_value');
        $this->addSql('DROP TABLE filter');
    }
}
