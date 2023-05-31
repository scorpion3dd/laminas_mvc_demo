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
use Application\Form\LogForm;
use Application\Service\LogService;
use ApplicationTest\AbstractMock;
use Exception;
use User\Service\AuthManager;

/**
 * Class LogsControllerTest - Unit tests for LogsController
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package ApplicationTest\unit\Controller
 */
class LogsControllerTest extends AbstractMock
{
    public const MODULE_NAME = 'application';
    public const CONTROLLER_NAME = LogsController::class;
    public const CONTROLLER_CLASS = 'LogsController';
    public const ROUTE_URL = '/logs';
    public const ROUTE_LOGS = 'logs';

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

        $logServiceMock = $this->getMockBuilder(LogService::class)
            ->onlyMethods(['getLogs'])
            ->disableOriginalConstructor()
            ->getMock();

        $logs = [];
        $log = $this->createLog();
        $logs[] = $log;
        $logServiceMock->expects(self::once())
            ->method('getLogs')
            ->willReturn($logs);

        $this->serviceManager->setService(LogService::class, $logServiceMock);

        $this->dispatch(self::ROUTE_URL, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_LOGS);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route index action - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testIndexActionEmptyLogs(): void
    {
        $this->setAuthMock(
            self::CONTROLLER_NAME,
            'index',
            AuthManager::ACCESS_GRANTED
        );

        $logServiceMock = $this->getMockBuilder(LogService::class)
            ->onlyMethods(['getLogs', 'getLogsPaginator'])
            ->disableOriginalConstructor()
            ->getMock();

        $logServiceMock->expects(self::once())
            ->method('getLogs')
            ->willReturn(null);

        $logServiceMock->expects(self::once())
            ->method('getLogsPaginator')
            ->with(0)
            ->willReturn(null);

        $this->serviceManager->setService(LogService::class, $logServiceMock);

        $this->dispatch(self::ROUTE_URL, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_LOGS);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route view action - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testViewAction(): void
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

        $log = $this->createLog();
        $logServiceMock->expects(self::once())
            ->method('getLog')
            ->with($this->equalTo(self::LOG_ID))
            ->willReturn($log);

        $this->serviceManager->setService(LogService::class, $logServiceMock);

        $this->dispatch(self::ROUTE_URL . '/view/' . self::LOG_ID, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_LOGS);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route add action by method GET - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testAddActionGet(): void
    {
        $this->setAuthMock(
            self::CONTROLLER_NAME,
            'add',
            AuthManager::ACCESS_GRANTED
        );

        $this->dispatch(self::ROUTE_URL . '/add', self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_LOGS);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route add action by method POST - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testAddActionPost(): void
    {
        $this->setAuthMock(
            self::CONTROLLER_NAME,
            'add',
            AuthManager::ACCESS_GRANTED
        );

        $form = new LogForm('create');
        $params = [
            'message' => '{orm_default} DB Connected.',
            'priority' => '2',
            'timestamp' => '2023-02-01',
            'csrf' => $form->get('csrf')->getValue(),
            'submit' => 'Create',
        ];

        $logServiceMock = $this->getMockBuilder(LogService::class)
            ->onlyMethods(['addLog'])
            ->disableOriginalConstructor()
            ->getMock();

        $log = $this->createLog();
        $this->setEntityId($log, self::LOG_ID);
        $logServiceMock->expects(self::once())
            ->method('addLog')
            ->with($this->equalTo(null), $this->equalTo($params))
            ->willReturn($log);

        $this->serviceManager->setService(LogService::class, $logServiceMock);

        $this->dispatch(self::ROUTE_URL . '/add', self::METHOD_POST, $params);
        $this->assertResponseStatusCode(self::STATUS_CODE_302);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_LOGS);
        $response = $this->getResponse()->getContent();
        self::assertEquals('', $this->trim($response));
    }

    /**
     * @testCase - route edit action by method GET - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testEditActionGet(): void
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

        $this->serviceManager->setService(LogService::class, $logServiceMock);

        $this->dispatch(self::ROUTE_URL . '/edit/' . self::LOG_ID, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_LOGS);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route edit action by method POST - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testEditActionPost(): void
    {
        $this->setAuthMock(
            self::CONTROLLER_NAME,
            'edit',
            AuthManager::ACCESS_GRANTED
        );

        $logServiceMock = $this->getMockBuilder(LogService::class)
            ->onlyMethods(['getLog', 'updateLog'])
            ->disableOriginalConstructor()
            ->getMock();

        $log = $this->createLog();
        $this->setEntityId($log, self::LOG_ID);
        $logServiceMock->expects(self::once())
            ->method('getLog')
            ->with($this->equalTo(self::LOG_ID))
            ->willReturn($log);

        $form = new  LogForm('update', $log);
        $params = [
            'message' => '{orm_default} DB Connected.',
            'priority' => '2',
            'csrf' => $form->get('csrf')->getValue(),
            'submit' => 'Create',
        ];

        $logServiceMock->expects(self::once())
            ->method('updateLog')
            ->with(
                $this->equalTo($log),
                $this->equalTo(null),
                $this->equalTo($params)
            )
            ->willReturn(true);

        $this->serviceManager->setService(LogService::class, $logServiceMock);

        $this->dispatch(self::ROUTE_URL . '/edit/' . self::LOG_ID, self::METHOD_POST, $params);
        $this->assertResponseStatusCode(self::STATUS_CODE_302);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_LOGS);
        $response = $this->getResponse()->getContent();
        self::assertEquals('', $this->trim($response));
    }

    /**
     * @testCase - route delete action by method POST - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testDeleteActionPost(): void
    {
        $this->setAuthMock(
            self::CONTROLLER_NAME,
            'delete',
            AuthManager::ACCESS_GRANTED
        );

        $logServiceMock = $this->getMockBuilder(LogService::class)
            ->onlyMethods(['getLog', 'removeLog'])
            ->disableOriginalConstructor()
            ->getMock();

        $log = $this->createLog();
        $this->setEntityId($log, self::LOG_ID);
        $logServiceMock->expects(self::once())
            ->method('getLog')
            ->with($this->equalTo(self::LOG_ID))
            ->willReturn($log);

        $logServiceMock->expects(self::once())
            ->method('removeLog')
            ->with(
                $this->equalTo($log)
            )
            ->willReturn(true);

        $this->serviceManager->setService(LogService::class, $logServiceMock);

        $this->dispatch(self::ROUTE_URL . '/delete/' . self::LOG_ID, self::METHOD_POST);
        $this->assertResponseStatusCode(self::STATUS_CODE_302);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_LOGS);
        $response = $this->getResponse()->getContent();
        self::assertEquals('', $this->trim($response));
    }
}
