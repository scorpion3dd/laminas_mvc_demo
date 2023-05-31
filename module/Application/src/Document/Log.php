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

namespace Application\Document;

use DateTime;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Id;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Field;
use Exception;
use MongoDB\BSON\ObjectID;
use Laminas\Log\Logger;

/**
 * This class represents a Log
 * @ODM\Document(collection="logs", repositoryClass="\Application\Repository\LogsRepository")
 * @package Application\Document
 */
class Log
{
    /**
     * @ODM\Id
     * @var mixed|null $id
     */
    protected mixed $id = null;

    /**
     * @ODM\Field(type="collection")
     * @var array $extra
     */
    protected array $extra = [];

    /**
     * @ODM\Field(type="string")
     * @var string $message
     */
    protected string $message;

    /**
     * @ODM\Field(type="int")
     * @var int $priority
     */
    private int $priority = 1;

    /**
     * @ODM\Field(type="string")
     * @var string $priorityName
     */
    protected string $priorityName = '';

    /**
     * @ODM\Field(type="date")
     * @var DateTime|null $timestamp
     */
    protected $timestamp;

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return (string) new ObjectID($this->id);
    }

    /**
     * @param mixed $id
     *
     * @return $this
     */
    public function setId(mixed $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     *
     * @return $this
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return string[]
     */
    public static function getPriorities(): array
    {
        return [
            Logger::EMERG  => 'EMERG',
            Logger::ALERT  => 'ALERT',
            Logger::CRIT   => 'CRIT',
            Logger::ERR    => 'ERR',
            Logger::WARN   => 'WARN',
            Logger::NOTICE => 'NOTICE',
            Logger::INFO   => 'INFO',
            Logger::DEBUG  => 'DEBUG',
        ];
    }

    /**
     * @return int
     * @throws Exception
     */
    public static function getPriorityRandom(): int
    {
        return random_int(Logger::EMERG, Logger::DEBUG);
    }

    /**
     * @param int $i
     *
     * @return int
     */
    public static function getPriorityEven(int $i): int
    {
        return ($i % 2 === 0) ? Logger::EMERG : Logger::DEBUG;
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     *
     * @return $this
     */
    public function setPriority(int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * @return string
     */
    public function getPriorityName(): string
    {
        return $this->priorityName;
    }

    /**
     * @param string $priorityName
     *
     * @return $this
     */
    public function setPriorityName(string $priorityName): self
    {
        $this->priorityName = $priorityName;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getTimestamp(): ?DateTime
    {
        return $this->timestamp;
    }

    /**
     * @return string
     */
    public function getTimestampString(): string
    {
        $timestamp = '';
        if (! empty($this->timestamp)) {
            $timestamp = $this->timestamp->format('Y-m-d H:i:s');
        }

        return $timestamp;
    }

    /**
     * @param DateTime|null $timestamp
     *
     * @return $this
     */
    public function setTimestamp(?DateTime $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * @return array
     */
    public function getExtra(): array
    {
        return $this->extra;
    }

    /**
     * @return string
     */
    public function getExtraString(): string
    {
        $extra = '';
        if (! empty($this->extra)) {
            $extra = implode(';', $this->extra);
        }

        return $extra;
    }

    /**
     * @param array $extra
     *
     * @return $this
     */
    public function setExtra(array $extra): self
    {
        $this->extra = $extra;

        return $this;
    }
}
