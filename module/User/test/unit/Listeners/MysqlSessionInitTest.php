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

namespace UserTest\unit\Listeners;

use ApplicationTest\AbstractMock;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Event\ConnectionEventArgs;
use Doctrine\DBAL\Events;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\Listeners\MysqlSessionInit;
use Laminas\Log\Logger;

/**
 * Class MysqlSessionInitTest - Unit tests for MysqlSessionInit
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package UserTest\unit\Listeners
 */
class MysqlSessionInitTest extends AbstractMock
{
    /** @var MysqlSessionInit $mysqlSessionInit */
    private MysqlSessionInit $mysqlSessionInit;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        /** @var Logger $logger */
        $logger = $this->serviceManager->get('LoggerGlobal');
        $this->mysqlSessionInit = new MysqlSessionInit($logger, $this->serviceManager, 'orm_default');
    }

    /**
     * @testCase - method postConnect - must be a success
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testPostConnect(): void
    {
        $connectionMock = $this->getMockBuilder(Connection::class)
            ->onlyMethods(['executeUpdate'])
            ->disableOriginalConstructor()
            ->getMock();

        $connectionMock->expects($this->exactly(1))
            ->method('executeUpdate')
            ->with(
                $this->equalTo('SET @SESSION.user_id = :user_id'),
                $this->equalTo(['user_id' => self::USER_ID])
            )
            ->willReturn(true);

        $this->prepareSessionContainer();
        $this->sessionContainer->user_id = self::USER_ID;
        $this->mysqlSessionInit->setSessionContainer($this->sessionContainer);

        $this->mysqlSessionInit->postConnect(new ConnectionEventArgs($connectionMock));
        $this->assertTrue(true);
    }

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testPostConnectException(): void
    {
        $connectionMock = $this->getMockBuilder(Connection::class)
            ->onlyMethods(['executeUpdate'])
            ->disableOriginalConstructor()
            ->getMock();

        $connectionMock->expects($this->once())
            ->method('executeUpdate')
            ->with(
                $this->equalTo('SET @SESSION.user_id = :user_id'),
                $this->equalTo(['user_id' => self::USER_ID])
            )
            ->willThrowException(new Exception('Error'));

        $this->prepareSessionContainer();
        $this->sessionContainer->user_id = self::USER_ID;
        $this->mysqlSessionInit->setSessionContainer($this->sessionContainer);

        $this->mysqlSessionInit->postConnect(new ConnectionEventArgs($connectionMock));
        $this->assertTrue(true);
    }

    /**
     * @return void
     */
    public function testGetSubscribedEvents(): void
    {
        $result = $this->mysqlSessionInit->getSubscribedEvents();
        $this->assertSame($result, [Events::postConnect]);
    }
}
