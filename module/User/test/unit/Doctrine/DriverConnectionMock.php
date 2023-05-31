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

namespace UserTest\unit\Doctrine;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Driver\Statement;

/**
 * Class DriverConnectionMock - For unit tests for all Entities
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package UserTest\unit\Doctrine
 */
class DriverConnectionMock implements Connection
{
    /**
     * {@inheritdoc}
     */
    public function prepare($prepareString): Statement
    {
        return new StatementMock();
    }

    /**
     * @inheritDoc
     */
    public function query()
    {
        return new HydratorMockStatement([]);
    }

    public function quote($input, $type = \PDO::PARAM_STR)
    {
    }

    public function exec($statement)
    {
    }

    public function lastInsertId($name = null)
    {
    }

    public function beginTransaction()
    {
    }

    public function commit()
    {
    }

    public function rollBack()
    {
    }

    public function errorCode()
    {
    }

    public function errorInfo()
    {
    }
}
