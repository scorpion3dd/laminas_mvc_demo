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

use ArrayIterator;
use BadMethodCallException;
use Doctrine\DBAL\Driver\Result;
use Doctrine\DBAL\Driver\ResultStatement;
use PDO;
use Traversable;

/**
 * Class DriverResultMock - For unit tests for all Entities
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package UserTest\unit\Doctrine
 */
class DriverResultMock implements Result, ResultStatement
{
    /** @var list<array<string, mixed>> */
    private $resultSet;

    /**
     * Creates a new mock statement that will serve the provided fake result set to clients.
     *
     * @param list<array<string, mixed>> $resultSet The faked SQL result set.
     */
    public function __construct(array $resultSet = [])
    {
        $this->resultSet = $resultSet;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchNumeric()
    {
        $row = $this->fetchAssociative();

        return $row === false ? false : array_values($row);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAssociative()
    {
        $current = current($this->resultSet);
        next($this->resultSet);

        return $current;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchOne()
    {
        $row = $this->fetchNumeric();

        return $row ? $row[0] : false;
    }

    public function fetchAllNumeric(): array
    {
        $values = [];
        while (($row = $this->fetchNumeric()) !== false) {
            $values[] = $row;
        }

        return $values;
    }

    public function fetchAllAssociative(): array
    {
        $resultSet = $this->resultSet;
        reset($resultSet);

        return $resultSet;
    }

    public function fetchFirstColumn(): array
    {
        throw new BadMethodCallException('Not implemented');
    }

    public function rowCount(): int
    {
        return 0;
    }

    public function columnCount(): int
    {
        $resultSet = $this->resultSet;

        return count(reset($resultSet) ?: []);
    }

    public function free(): void
    {
    }

    public function closeCursor(): bool
    {
        $this->free();

        return true;
    }

    public function setFetchMode($fetchMode, $arg2 = null, $arg3 = null): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($fetchMode = null, $cursorOrientation = PDO::FETCH_ORI_NEXT, $cursorOffset = 0)
    {
        return $this->fetchAssociative();
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAll($fetchMode = null, $fetchArgument = null, $ctorArgs = null): array
    {
        return $this->fetchAllAssociative();
    }

    /**
     * {@inheritdoc}
     */
    public function fetchColumn($columnIndex = 0)
    {
        return $this->fetchOne();
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->fetchAllAssociative());
    }
}
