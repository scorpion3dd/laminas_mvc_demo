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

use Doctrine\DBAL\Driver\Result;
use Doctrine\DBAL\Driver\Statement;
use EmptyIterator;
use IteratorAggregate;
use PDO;
use Traversable;

/**
 * Class StatementMock - For unit tests for all Entities
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package UserTest\unit\Doctrine
 */
class StatementMock implements IteratorAggregate, Statement
{
    /**
     * {@inheritdoc}
     */
    public function bindValue($param, $value, $type = null)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function bindParam($column, &$variable, $type = null, $length = null)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function errorCode()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function errorInfo()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function execute($params = null): Result
    {
        return new DriverResultMock();
    }

    public function rowCount(): int
    {
        return 1;
    }

    /**
     * {@inheritdoc}
     */
    public function closeCursor()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function columnCount()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function setFetchMode($fetchStyle, $arg2 = null, $arg3 = null)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($fetchMode = null, $cursorOrientation = PDO::FETCH_ORI_NEXT, $cursorOffset = 0)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAll($fetchMode = null, $fetchArgument = null, $ctorArgs = null)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function fetchColumn($columnIndex = 0)
    {
    }

    public function getIterator(): Traversable
    {
        return new EmptyIterator();
    }
}
