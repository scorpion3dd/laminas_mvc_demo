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
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\AbstractSchemaManager;

/**
 * Class DriverMock - For unit tests for all Entities
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package UserTest\unit\Doctrine
 */
class DriverMock implements Driver
{
    private $_platformMock;

    private $_schemaManagerMock;

    /**
     * @param array $params
     * @param $username
     * @param $password
     * @param array $driverOptions
     *
     * @return DriverConnectionMock
     */
    public function connect(array $params, $username = null, $password = null, array $driverOptions = [])
    {
        return new DriverConnectionMock();
    }

    /**
     * Constructs the Sqlite PDO DSN.
     *
     * @return string  The DSN.
     * @override
     */
    protected function _constructPdoDsn(array $params)
    {
        return "";
    }

    /**
     * @override
     */
    public function getDatabasePlatform()
    {
        if (! $this->_platformMock) {
            $this->_platformMock = new DatabasePlatformMock;
        }

        return $this->_platformMock;
    }

    /**
     * @override
     */
    public function getSchemaManager(Connection $conn)
    {
        if ($this->_schemaManagerMock == null) {
            return new SchemaManagerMock($conn);
        } else {
            return $this->_schemaManagerMock;
        }
    }

    /* MOCK API */

    public function setDatabasePlatform(AbstractPlatform $platform)
    {
        $this->_platformMock = $platform;
    }

    public function setSchemaManager(AbstractSchemaManager $sm)
    {
        $this->_schemaManagerMock = $sm;
    }

    public function getName()
    {
        return 'mock';
    }

    public function getDatabase(Connection $conn)
    {
        return;
    }
}
