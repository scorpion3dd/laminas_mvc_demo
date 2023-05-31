<?php
/**
 * This file is part of the Simple Web Demo Free Lottery Management Application.
 *
 * This project is no longer maintained.
 * The project is written in Zend Framework 3 Release.
 *
 * @link https://github.com/scorpion3dd
 * @author Denis Puzik <scorpion3dd@gmail.com>
 * @copyright Copyright (c) 2020-2021 scorpion3dd
 */

declare(strict_types=1);

namespace ApplicationTest\unit\Document;

use Application\Document\Log;
use ApplicationTest\AbstractMock;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class LogTest - Unit tests for Log
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package ApplicationTest\unit\Document;
 */
class LogTest extends AbstractMock
{
    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @testCase - function getPriorityRandom - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testGetPriorityRandom(): void
    {
        $log = new Log();
        $this->assertIsNumeric($log->getPriorityRandom());
    }

    /**
     * @testCase - function getPriorityEven - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testGetPriorityEven(): void
    {
        $log = new Log();
        $this->assertIsNumeric($log->getPriorityEven(7));
    }
}
