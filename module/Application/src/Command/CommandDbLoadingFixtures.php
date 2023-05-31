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

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Fixtures\PermissionFixtures;
use Fixtures\RoleFixtures;
use Fixtures\RolePermissionFixtures;
use Fixtures\UserFixtures;
use Fixtures\UserRoleFixtures;
use Laminas\Log\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CommandDbLoadingFixtures
 * @package Application\Command
 */
class CommandDbLoadingFixtures extends AbstractCommand
{
    protected static $defaultDescription = 'Command loading fixtures to DBs';

    /** @var ORMExecutor $executor */
    protected ORMExecutor $executor;

    /** @var array $fixtures */
    protected array $fixtures;

    /**
     * CommandDbLoadingFixtures constructor
     * @param Logger $logger
     * @param EntityManagerInterface $entityManager
     * @param DocumentManager $documentManager
     */
    public function __construct(Logger $logger, EntityManagerInterface $entityManager, DocumentManager $documentManager)
    {
        parent::__construct('app:db:loading-fixtures');
        $this->logger = $logger;
        $this->entityManager = $entityManager;
        $this->documentManager = $documentManager;
    }

    /**
     * @param ORMExecutor $executor
     */
    public function setExecutor(ORMExecutor $executor): void
    {
        $this->executor = $executor;
    }

    /**
     * @param array $fixtures
     */
    public function setFixtures(array $fixtures): void
    {
        $this->fixtures = $fixtures;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     * @throws MongoDBException
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (empty($this->fixtures)) {
            $this->fixtures = [
                RoleFixtures::class,
                PermissionFixtures::class,
                UserFixtures::class,
                UserRoleFixtures::class,
                RolePermissionFixtures::class,
            ];
        }
        $this->buildIo($input, $output);
        $this->getIo()->title('CommandDbLoadingFixtures app:db:loading-fixtures');
        $this->writeLog('Started', Logger::NOTICE);
        $this->buildProgressBar($output, count($this->fixtures));
        $this->getProgressBar()->advance();
        $start = microtime(true);

        $count = (int)$input->getArgument('count');
        try {
            $this->executeFixtures($this->fixtures, $count);
        } catch (Exception $e) {
            $this->logger->log(Logger::ERR, $e->getMessage());
            $this->getIo()->error($e->getMessage());

            return \Symfony\Component\Console\Command\Command::FAILURE;
        }

        $this->getProgressBar()->finish();
        $time = microtime(true) - $start;
        $this->writeLog('Time executed: ' . $time, Logger::DEBUG);
        $this->writeLog('Finished', Logger::NOTICE);
        $this->writeLog('ALL EXECUTED SUCCESS');

        return Command::SUCCESS;
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->addArgument('count', InputArgument::OPTIONAL);
        // the command help shown when running the command with the "--help" option
        $this->setHelp('This command loading fixtures to DBs');
    }

    /**
     * @param array $classes
     * @param int $count
     *
     * @return void
     * @throws Exception
     */
    private function executeFixtures(array $classes = [], int $count = 0): void
    {
        $loader = new Loader();
        /** @var string $class */
        foreach ($classes as $class) {
            if (! class_exists($class)) {
                $this->getIo()->error($class . ' - class not exists');
                throw new Exception("$class - class not exists");
            }
            if ($class === 'Fixtures\UserFixtures' || $class === 'Fixtures\UserRoleFixtures') {
                $fixture = new $class($this, $count);
            } else {
                $fixture = new $class($this);
            }
            /** @var FixtureInterface $fixture */
            $loader->addFixture($fixture);
        }
        $this->getIo()->listing($classes);
        if (empty($this->executor)) {
            // @codeCoverageIgnoreStart
            $this->executor = new ORMExecutor($this->entityManager, new ORMPurger());
            // @codeCoverageIgnoreEnd
        }
        /** @phpstan-ignore-next-line */
        $this->executor->execute($loader->getFixtures(), append: true);
    }
}
