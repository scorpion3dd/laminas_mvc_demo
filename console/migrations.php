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

use Application\Command\AbstractCommand;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\Tools\Console\Command;
use Doctrine\Migrations\Tools\Console\Helper\ConfigurationHelper;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

require_once __DIR__ . '/../vendor/autoload.php';
$params = require_once __DIR__ . '/../config/autoload_test/local.php';
if (empty($params['doctrine']['connection']['orm_default']['params'])) {
    echo 'dbParams - not read';
}
$dbParams = $params['doctrine']['connection']['orm_default']['params'];
if (! is_array($dbParams)) {
    echo 'dbParams - not array';
}
$dbParams['driver'] = 'pdo_mysql';
$input = new ArgvInput();
$output = new ConsoleOutput();
$io = new SymfonyStyle($input, $output);
try {
    $io->title('Migrations');
    $io->note('Start');
    $connection = DriverManager::getConnection($dbParams);

    $configuration = new Configuration($connection);
    $configuration->setName('My Project Migrations');
    $configuration->setMigrationsNamespace('Migrations');
    $configuration->setMigrationsTableName('migrations');
    $configuration->setMigrationsColumnName('version');
    $configuration->setMigrationsColumnLength(255);
    $configuration->setMigrationsExecutedAtColumnName('executed_at');
    $configuration->setMigrationsDirectory(__DIR__ . '/../data/Migrations');
    $configuration->setAllOrNothing(true);
    $configuration->setCheckDatabasePlatform(false);

    $helperSet = new HelperSet();
    $helperSet->set(new QuestionHelper(), 'question');
    $helperSet->set(new ConnectionHelper($connection), 'db');
    $helperSet->set(new ConfigurationHelper($connection, $configuration));

    $cli = new Application('Doctrine Migrations');
    $cli->setCatchExceptions(true);
    $cli->setHelperSet($helperSet);
    $cli->addCommands([
        new Command\DumpSchemaCommand(),
        new Command\ExecuteCommand(),
        new Command\GenerateCommand(),
        new Command\LatestCommand(),
        new Command\MigrateCommand(),
        new Command\RollupCommand(),
        new Command\StatusCommand(),
        new Command\VersionCommand()
    ]);
    putenv('APP_ENV=' . $_SERVER['APP_ENV'] = $_ENV['APP_ENV'] = AbstractCommand::TYPE_INTEGRATION);
    $cli->run();
    $io->note('Finish');
    $io->success('ALL EXECUTED SUCCESS');
} catch (Exception $e) {
    $io->error('Error: Message - ' . $e->getMessage()
        . ', in file - ' . $e->getFile()
        . ', in line - ' . $e->getLine());
}
