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

/**
 * Example console run:
 * composer db-update file=update_20230124.sql
 */
if (isset($argv) && is_array($argv) && count($argv) >= 2) {
    $result = false;
    foreach ($argv as $arg) {
        $argArr = explode('=', $arg);
        if (is_array($argArr) && count($argArr) == 2) {
            $key = trim($argArr[0]);
            $file = trim($argArr[1]);
            if ($key == 'file' && $file != '') {
                $db = new Db();
                $db->execute($file, "Structure DB " . $db->getDbName() . " updated by file $file success");
                $result = true;
            }
        }
    }
    if (! $result) {
        echo 'No argument with key file';
    }
} else {
    echo 'No argument';
}
