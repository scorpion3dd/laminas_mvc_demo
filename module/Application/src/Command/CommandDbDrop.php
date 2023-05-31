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

namespace Application\Command;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Laminas\Log\Logger;
use Redis;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CommandDbDrop
 * @package Application\Command
 */
class CommandDbDrop extends AbstractCommand
{
    protected static $defaultDescription = 'Command drop structures DBs';

    /** @var Redis $redis */
    protected Redis $redis;

    /** @var Db $db */
    protected Db $db;

    /**
     * CommandDbDrop constructor
     * @param Logger $logger
     * @param EntityManagerInterface $entityManager
     * @param DocumentManager $documentManager
     * @param Redis $redis
     */
    public function __construct(
        Logger $logger,
        EntityManagerInterface $entityManager,
        DocumentManager $documentManager,
        Redis $redis,
    ) {
        parent::__construct('app:db:drop');
        $this->logger = $logger;
        $this->entityManager = $entityManager;
        $this->documentManager = $documentManager;
        $this->redis = $redis;
        $this->db = new Db;
    }

    /**
     * @param Db $db
     */
    public function setDb(Db $db): void
    {
        $this->db = $db;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->buildIo($input, $output);
        $this->getIo()->title('CommandDbDrop app:db:drop');
        $this->writeLog('Started', Logger::NOTICE);
        $this->buildProgressBar($output, 4);
        $this->getProgressBar()->advance();
        $start = microtime(true);

        $result = $this->executeDrop();

        $this->getProgressBar()->finish();
        $time = microtime(true) - $start;
        $this->writeLog('Time executed: ' . $time, Logger::DEBUG);
        $this->writeLog('Finished', Logger::NOTICE);
        if ($result) {
            $this->writeLog('ALL EXECUTED SUCCESS');
        } else {
            $this->writeLog('EXECUTED WITH ERRORS', Logger::ERR);
        }

        return Command::SUCCESS;
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        // the command help shown when running the command with the "--help" option
        $this->setHelp('This command loading fixtures to DB MySql.');
    }

    /**
     * @return bool
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    private function executeDrop(): bool
    {
        $result = true;
        try {
            $this->db->dropMongo($this->documentManager);
            $this->getProgressBar()->advance();
            $this->getIo()->info('Mongo DB collection Log dropped');
        } catch (Exception $e) {
            $this->getIo()->error('Mongo DB collection Log NOT dropped: ' . $this->getExceptionMessage($e));
            $result = false;
        }

        $appEnv = getenv('APP_ENV');
        if ($appEnv === self::TYPE_INTEGRATION) {
            $file = 'dropStructureIntegration.sql';
            $this->db->dropRedis($this->redis, $this->entityManager, self::TYPE_INTEGRATION);
        } else {
            $file = 'dropStructure.sql';
            $this->db->dropRedis($this->redis, $this->entityManager);
        }
        $this->getProgressBar()->advance();
        $this->getIo()->info('Redis DB sets dropped');

        $dbName = $this->db->getMySqlDbName($this->entityManager);
        try {
            $this->db->dropMySql($this->entityManager, $file);
            $this->getProgressBar()->advance();
            $this->getIo()->info('MySql DB ' . $dbName . ' dropped');
        } catch (Exception $e) {
            $this->getIo()->error('MySql DB ' . $dbName . ' NOT dropped: ' . $this->getExceptionMessage($e));
            $result = false;
        }

        return $result;
    }
}
