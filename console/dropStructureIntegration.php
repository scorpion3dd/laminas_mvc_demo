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

require_once __DIR__ . '/../vendor/autoload.php';

$db = new Db(Db::TYPE_INTEGRATION);
$db->execute(
    'dropStructureIntegration.sql',
    'Structure for Integration tests all DBs: MySql ' . $db->getDbName() . ', Mongo, Redis - dropped success'
);
