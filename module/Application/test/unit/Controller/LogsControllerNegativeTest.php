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

namespace ApplicationTest\unit\Controller;

use Application\Controller\LogsController;
use Application\Service\LogService;
use ApplicationTest\AbstractMock;
use Exception;
use User\Service\AuthManager;

/**
 * Class LogsControllerNegativeTest - Unit negative tests for LogsController
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package ApplicationTest\unit\Controller
 */
class LogsControllerNegativeTest extends AbstractMock
{
    public const MODULE_NAME = 'application';
    public const CONTROLLER_NAME = LogsController::class;
    public const CONTROLLER_CLASS = 'LogsController';
    public const ROUTE_URL = '/logs';
    public const ROUTE_LOGS = 'logs';

    /**
     * @testCase - route view action - Id empty
     *
     * @return void
     * @throws Exception
     */
    public function testViewActionIdEmpty(): void
    {
        $this->setAuthMock(
            self::CONTROLLER_NAME,
            'view',
            AuthManager::ACCESS_GRANTED
        );

        $this->dispatch(self::ROUTE_URL . '/view/', self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_LOGS);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route view action - Log empty
     *
     * @return void
     * @throws Exception
     */
    public function testViewActionLogEmpty(): void
    {
        $this->setAuthMock(
            self::CONTROLLER_NAME,
            'view',
            AuthManager::ACCESS_GRANTED
        );

        $logServiceMock = $this->getMockBuilder(LogService::class)
            ->onlyMethods(['getLog'])
            ->disableOriginalConstructor()
            ->getMock();

        $log = null;
        $logServiceMock->expects(self::once())
            ->method('getLog')
            ->with($this->equalTo(self::LOG_ID))
            ->willReturn($log);

        $this->serviceManager->setService(LogService::class, $logServiceMock);

        $this->dispatch(self::ROUTE_URL . '/view/' . self::LOG_ID, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_LOGS);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route add action by method POST - form is not valid
     *
     * @return void
     * @throws Exception
     */
    public function testAddActionPostFormIsNotValid(): void
    {
        $this->setAuthMock(
            self::CONTROLLER_NAME,
            'add',
            AuthManager::ACCESS_GRANTED
        );

        $this->dispatch(self::ROUTE_URL . '/add', self::METHOD_POST, [
            'message' => '{orm_default} DB Connected.',
            'priority' => '2',
            'timestamp' => '2023-02-01',
            'submit' => 'Create',
        ]);
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_LOGS);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route edit action by method GET - not id
     *
     * @return void
     * @throws Exception
     */
    public function testEditActionGetNotId(): void
    {
        $this->setAuthMock(
            self::CONTROLLER_NAME,
            'edit',
            AuthManager::ACCESS_GRANTED
        );

        $this->dispatch(self::ROUTE_URL . '/edit', self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_LOGS);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route edit action by method GET - empty log
     *
     * @return void
     * @throws Exception
     */
    public function testEditActionGetEmptyLog(): void
    {
        $this->setAuthMock(
            self::CONTROLLER_NAME,
            'edit',
            AuthManager::ACCESS_GRANTED
        );

        $logServiceMock = $this->getMockBuilder(LogService::class)
            ->onlyMethods(['getLog'])
            ->disableOriginalConstructor()
            ->getMock();

        $log = null;
        $logServiceMock->expects(self::once())
            ->method('getLog')
            ->with($this->equalTo(self::LOG_ID))
            ->willReturn($log);

        $this->serviceManager->setService(LogService::class, $logServiceMock);

        $this->dispatch(self::ROUTE_URL . '/edit/' . self::LOG_ID, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_LOGS);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route edit action by method POST - form is not valid
     *
     * @return void
     * @throws Exception
     */
    public function testEditActionPostFormIsNotValid(): void
    {
        $this->setAuthMock(
            self::CONTROLLER_NAME,
            'edit',
            AuthManager::ACCESS_GRANTED
        );

        $logServiceMock = $this->getMockBuilder(LogService::class)
            ->onlyMethods(['getLog'])
            ->disableOriginalConstructor()
            ->getMock();

        $log = $this->createLog();
        $this->setEntityId($log, self::LOG_ID);
        $logServiceMock->expects(self::once())
            ->method('getLog')
            ->with($this->equalTo(self::LOG_ID))
            ->willReturn($log);

        $params = [
            'message' => '{orm_default} DB Connected.',
            'priority' => '2',
            'csrf' => '123',
            'submit' => 'Create',
        ];

        $this->serviceManager->setService(LogService::class, $logServiceMock);

        $this->dispatch(self::ROUTE_URL . '/edit/' . self::LOG_ID, self::METHOD_POST, $params);
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_LOGS);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route delete action by method GET - not id
     *
     * @return void
     * @throws Exception
     */
    public function testDeleteActionGetNotId(): void
    {
        $this->setAuthMock(
            self::CONTROLLER_NAME,
            'delete',
            AuthManager::ACCESS_GRANTED
        );

        $this->dispatch(self::ROUTE_URL . '/delete', self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_LOGS);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route delete action by method GET - empty log
     *
     * @return void
     * @throws Exception
     */
    public function testDeleteActionGetEmptyLog(): void
    {
        $this->setAuthMock(
            self::CONTROLLER_NAME,
            'delete',
            AuthManager::ACCESS_GRANTED
        );

        $logServiceMock = $this->getMockBuilder(LogService::class)
            ->onlyMethods(['getLog'])
            ->disableOriginalConstructor()
            ->getMock();

        $log = null;
        $logServiceMock->expects(self::once())
            ->method('getLog')
            ->with($this->equalTo(self::LOG_ID))
            ->willReturn($log);

        $this->serviceManager->setService(LogService::class, $logServiceMock);

        $this->dispatch(self::ROUTE_URL . '/delete/' . self::LOG_ID, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_LOGS);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }
}
