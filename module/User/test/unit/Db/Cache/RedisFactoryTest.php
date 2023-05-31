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

namespace UserTest\unit\Db\Cache;

use ApplicationTest\AbstractMock;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\Db\Cache\RedisFactory;

/**
 * Class RedisFactoryTest - Unit tests for RedisFactory
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package UserTest\unit\Db\Cache
 */
class RedisFactoryTest extends AbstractMock
{
    /**
     * @testCase - method __invoke - Exception
     * redis->connect - Exception
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testInvokeException(): void
    {
        $result = (new RedisFactory())->__invoke(
            $this->serviceManager,
            '',
            ['host' => '0.0.0.0']
        );
        $this->assertIsObject($result);
    }
}
