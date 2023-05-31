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

use Console\Db;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;

require_once __DIR__ . '/../vendor/autoload.php';
$db = new Db();
$entityManager = $db->getEntityManager();
$commands = [
    // If you want to add your own custom console commands,
    // you can do so here.
];
ConsoleRunner::run(
    new SingleManagerProvider($entityManager),
    $commands
);
