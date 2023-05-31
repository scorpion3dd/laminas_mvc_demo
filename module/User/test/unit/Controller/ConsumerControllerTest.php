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

namespace UserTest\unit\Controller;

use ApplicationTest\AbstractMock;
use Exception;
use User\Controller\ConsumerController;
use User\Kafka\ConsumerKafka;
use User\Service\AuthManager;

/**
 * Class ConsumerControllerTest - Unit tests for ConsumerController
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package UserTest\unit\Controller
 */
class ConsumerControllerTest extends AbstractMock
{
    public const MODULE_NAME = 'user';
    public const CONTROLLER_NAME = ConsumerController::class;
    public const CONTROLLER_CLASS = 'ConsumerController';
    public const ROUTE_URL = '/consumer';
    public const ROUTE_USERS = 'consumer';

    /**
     * @testCase - route index action - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testIndexAction(): void
    {
        $this->setAuthMock(
            self::CONTROLLER_NAME,
            'index',
            AuthManager::ACCESS_GRANTED
        );

        $consumerKafkaMock = $this->getMockBuilder(ConsumerKafka::class)
            ->onlyMethods(['start'])
            ->disableOriginalConstructor()
            ->getMock();

        $consumerKafkaMock->expects(self::once())
            ->method('start');

        $this->serviceManager->setService(ConsumerKafka::class, $consumerKafkaMock);

        $this->dispatch(self::ROUTE_URL, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        $expected = file_get_contents(
            __DIR__ . '/../data/Controller/Consumer/GetIndexActionSuccess.html'
        );
//        self::assertSame($this->trim($expected), $this->trim($response));
    }
}
