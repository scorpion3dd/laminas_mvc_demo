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

use Doctrine\DBAL\Driver\Statement;
use IteratorAggregate;
use PDO;

/**
 * Class HydratorMockStatement - For unit tests for all Entities
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package UserTest\unit\Doctrine
 */
class HydratorMockStatement implements IteratorAggregate, Statement
{
    private $_resultSet;

    /**
     * Creates a new mock statement that will serve the provided fake result set to clients.
     *
     * @param array $resultSet The faked SQL result set.
     */
    public function __construct(array $resultSet)
    {
        $this->_resultSet = $resultSet;
    }

    /**
     * Fetches all rows from the result set.
     *
     * @param null $fetchMode
     * @param null $fetchArgument
     * @param null $ctorArgs
     *
     * @return array
     */
    public function fetchAll($fetchMode = null, $fetchArgument = null, $ctorArgs = null)
    {
        return $this->_resultSet;
    }

    public function fetchColumn($columnNumber = 0)
    {
        $row = current($this->_resultSet);
        if (! is_array($row)) {
            return false;
        }
        $val = array_shift($row);
        return $val !== null ? $val : false;
    }

    /**
     * Fetches the next row in the result set.
     *
     */
    public function fetch($fetchMode = null, $cursorOrientation = PDO::FETCH_ORI_NEXT, $cursorOffset = 0)
    {
        $current = current($this->_resultSet);
        next($this->_resultSet);
        return $current;
    }

    /**
     * Closes the cursor, enabling the statement to be executed again.
     *
     * @return boolean
     */
    public function closeCursor()
    {
        return true;
    }

    public function setResultSet(array $resultSet)
    {
        reset($resultSet);
        $this->_resultSet = $resultSet;
    }

    public function bindColumn($column, &$param, $type = null)
    {
    }

    public function bindValue($param, $value, $type = null)
    {
    }

    public function bindParam($column, &$variable, $type = null, $length = null, $driverOptions = [])
    {
    }

    public function columnCount()
    {
    }

    public function errorCode()
    {
    }

    public function errorInfo()
    {
    }

    public function execute($params = [])
    {
    }

    public function rowCount()
    {
    }

    /**
     * @inheritDoc
     */
    public function setFetchMode($fetchMode, $arg2 = null, $arg3 = null)
    {
        // TODO: Implement setFetchMode() method.
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        // TODO: Implement getIterator() method.
    }
}
