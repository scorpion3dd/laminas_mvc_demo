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
use FixturesIntegration\LogFixtures;
use FixturesIntegration\PermissionFixtures;
use FixturesIntegration\RoleFixtures;
use FixturesIntegration\RolePermissionFixtures;
use FixturesIntegration\UserFixtures;
use FixturesIntegration\UserRoleFixtures;

require_once __DIR__ . '/../vendor/autoload.php';

$db = new Db(Db::TYPE_INTEGRATION);
$db->execute([
    RoleFixtures::class,
    PermissionFixtures::class,
    UserFixtures::class,
    UserRoleFixtures::class,
    RolePermissionFixtures::class,
], 'Loading fixtures for integration tests to DB ' . $db->getDbName() . ' success');
