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

use Exception;
use Laminas\Log\Logger;
use Doctrine\Migrations\Tools\Console\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\Tools\Console\Helper\ConfigurationHelper;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;

/**
 * Class CommandDbMigrations
 * @package Application\Command
 */
class CommandDbMigrations extends AbstractCommand
{
    protected static $defaultDescription = 'Command migrations migrate integration';

    /** @var string $path */
    protected string $path = '';

    /** @var Application $cli */
    protected Application $cli;

    /**
     * CommandDbMigrations constructor
     * @param Logger $logger
     */
    public function __construct(
        Logger $logger,
    ) {
        parent::__construct('app:db:migrations-migrate-integration');
        $this->logger = $logger;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * @param string $appEnv
     */
    public function setAppEnv(string $appEnv): void
    {
        putenv('APP_ENV=' . $_SERVER['APP_ENV'] = $_ENV['APP_ENV'] = $appEnv);
    }

    /**
     * @param Application $cli
     */
    public function setCli(Application $cli): void
    {
        $this->cli = $cli;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->buildIo($input, $output);
        $this->getIo()->title('CommandDbMigrations app:db:migrations-migrate-integration');
        $this->writeLog('Started', Logger::NOTICE);
        $this->buildProgressBar($output, 4);
        $this->getProgressBar()->advance();
        $this->getProgressBar()->finish();

        $appEnv = getenv('APP_ENV');
        if ($appEnv === '') {
            putenv('APP_ENV=' . $_SERVER['APP_ENV'] = $_ENV['APP_ENV'] = AbstractCommand::TYPE_INTEGRATION);
        }
        try {
            $this->executeMigrate();
        } catch (Exception $e) {
            $this->logger->log(Logger::ERR, $e->getMessage());
            $this->getIo()->error($e->getMessage());

            return \Symfony\Component\Console\Command\Command::FAILURE;
        }

        return \Symfony\Component\Console\Command\Command::SUCCESS;
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        // the command help shown when running the command with the "--help" option
        $this->setHelp('This command migrations migrate integration to DB MySql.');
    }

    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     * @throws Exception
     */
    private function executeMigrate(): void
    {
        $appEnv = getenv('APP_ENV');
        if ($this->path == '') {
            if ($appEnv === AbstractCommand::TYPE_INTEGRATION) {
                $this->path = '/../../../../config/autoload_test/local.php';
            } else {
                $this->path = '/../../../../config/autoload/local.php';
            }
        }
        if (! file_exists(__DIR__ . $this->path)) {
            throw new Exception('Path ' . __DIR__ . $this->path . ' not found');
        }
        $params = require __DIR__ . $this->path;
        if (empty($params['doctrine']['connection']['orm_default']['params'])) {
            // @codeCoverageIgnoreStart
            throw new Exception('Params not read');
            // @codeCoverageIgnoreEnd
        }
        $dbParams = $params['doctrine']['connection']['orm_default']['params'];
        if (! is_array($dbParams)) {
            // @codeCoverageIgnoreStart
            throw new Exception('dbParams not array');
            // @codeCoverageIgnoreEnd
        }
        $dbParams['driver'] = 'pdo_mysql';
        $connection = DriverManager::getConnection($dbParams);

        $configuration = new Configuration($connection);
        $configuration->setName('My Project Migrations');
        $configuration->setMigrationsNamespace('Migrations');
        $configuration->setMigrationsTableName('migrations');
        $configuration->setMigrationsColumnName('version');
        $configuration->setMigrationsColumnLength(255);
        $configuration->setMigrationsExecutedAtColumnName('executed_at');
        $configuration->setMigrationsDirectory(__DIR__ . '/../../../../data/Migrations');
        $configuration->setAllOrNothing(true);
        $configuration->setCheckDatabasePlatform(false);

        $helperSet = new HelperSet();
        $helperSet->set(new QuestionHelper(), 'question');
        $helperSet->set(new ConnectionHelper($connection), 'db');
        $helperSet->set(new ConfigurationHelper($connection, $configuration));

        if (empty($this->cli)) {
            // @codeCoverageIgnoreStart
            $this->cli = new Application('Doctrine Migrations');
            // @codeCoverageIgnoreEnd
        }
        $this->cli->setCatchExceptions(true);
        $this->cli->setHelperSet($helperSet);
        $this->cli->addCommands([
            new Command\DumpSchemaCommand(),
            new Command\ExecuteCommand(),
            new Command\GenerateCommand(),
            new Command\LatestCommand(),
            new Command\MigrateCommand(),
            new Command\RollupCommand(),
            new Command\StatusCommand(),
            new Command\VersionCommand()
        ]);
        $input = new ArrayInput([
            'command' => 'migrations:migrate',
            '--no-interaction' => '',
            '--ansi' => '',
        ]);
        $this->cli->run($input);
    }
}
