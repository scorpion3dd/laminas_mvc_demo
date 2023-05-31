<?php
/**
 * This file is part of the Simple Web Demo Free Lottery Management Application.
 *
 * This project is no longer maintained.
 * The project is written in Laminas Framework Release.
 *
 * @link https://github.com/scorpion3dd
 * @author Denis Puzik <scorpion3dd@gmail.com>
 * @copyright Copyright (c) 2021-2022 scorpion3dd
 */

declare(strict_types=1);

namespace Migrations;

use Application\Command\AbstractCommand;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 * @package Migrations
 */
final class Version20230118192806 extends AbstractMigration
{
    /**
     * Returns the description of this migration
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'This is the initial migration which creates the all structure for DB ' . $this->connection->getDatabase();
    }

    /**
     * Upgrades the schema to its newer state
     * @param Schema $schema
     *
     * @return void
     */
    public function up(Schema $schema): void
    {
        $database = $this->connection->getDatabase();
        $this->write('Database = ' . $database);
        $appEnv = getenv('APP_ENV');
        if ($appEnv === AbstractCommand::TYPE_INTEGRATION) {
            $file = 'emptyStructureIntegration.sql';
        } else {
            $file = 'emptyStructure.sql';
        }
        $this->write('From file ' . $file);
        $this->write('');
        $fileSql = __DIR__ . "/../db/$file";
        if (! file_exists($fileSql)) {
            $this->abortIf(true, $file . ' - file not exists');
        }
        /** @var string $sql */
        $sql = file_get_contents($fileSql);
        $this->addSql($sql);
    }

    /**
     * Reverts the schema changes
     * @param Schema $schema
     *
     * @return void
     */
    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException();
    }
}
