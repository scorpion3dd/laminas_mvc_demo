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

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Class ConnectionMock - For unit tests for all Entities
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package UserTest\unit\Doctrine
 */
class ConnectionMock extends Connection
{
    private DatabasePlatformMock $_fetchOneResult;
    private $_platformMock;
    private int $_lastInsertId = 0;
    private $_inserts = [];

    /** @var array */
    private $_executeStatements = [];

    /**
     * @var DatabasePlatformMock
     */
    private DatabasePlatformMock $_platform;

    public function __construct(array $params, $driver, $config = null, $eventManager = null)
    {
        $this->_platformMock = new DatabasePlatformMock();

        parent::__construct($params, $driver, $config, $eventManager);

        // Override possible assignment of platform to database platform mock
        $this->_platform = $this->_platformMock;
    }

    /**
     * @override
     */
    public function getDatabasePlatform(): AbstractPlatform
    {
        return $this->_platformMock;
    }

    /**
     * @override
     */
    public function insert($tableName, array $data, $types = [])
    {
        $this->_inserts[$tableName][] = $data;
    }

    /**
     * @override
     */
    public function lastInsertId($seqName = null)
    {
        return $this->_lastInsertId;
    }

    /**
     * @override
     */
    public function fetchColumn($statement, array $params = [], $colnum = 0, array $types = [])
    {
        return $this->_fetchOneResult;
    }

    /**
     * @override
     */
    public function quote($input, $type = null)
    {
        if (is_string($input)) {
            return "'" . $input . "'";
        }
        return $input;
    }

    /**
     * {@inheritdoc}
     */
    public function executeStatement($sql, array $params = [], array $types = []): int
    {
        $this->_executeStatements[] = ['sql' => $sql, 'params' => $params, 'types' => $types];

        return 1;
    }

    /**
     * @return array
     */
    public function getExecuteStatements(): array
    {
        return $this->_executeStatements;
    }

    /* Mock API */

    public function setFetchOneResult($fetchOneResult)
    {
        $this->_fetchOneResult = $fetchOneResult;
    }

    public function setDatabasePlatform($platform)
    {
        $this->_platformMock = $platform;
    }

    public function setLastInsertId($id)
    {
        $this->_lastInsertId = $id;
    }

    public function getInserts()
    {
        return $this->_inserts;
    }

    public function reset()
    {
        $this->_inserts = [];
        $this->_lastInsertId = 0;
    }
}
