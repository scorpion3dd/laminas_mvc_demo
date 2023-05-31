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

namespace ApplicationTest\integration\Controller;

use Application\Controller\LogsController;
use Application\Document\Log;
use Application\Form\LogForm;
use ApplicationTest\AbstractMock;
use Doctrine\ORM\ORMException;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class LogsControllerIntegrationTest - Integration tests for LogsController
 * with connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are real
 *
 * @package ApplicationTest\integration\Controller
 */
class LogsControllerIntegrationTest extends AbstractMock
{
    public const MODULE_NAME = 'application';
    public const CONTROLLER_NAME = LogsController::class;
    public const CONTROLLER_CLASS = 'LogsController';
    public const ROUTE_URL = '/logs';
    public const ROUTE_LOGS = 'logs';

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        $this->setTypeTest(self::TYPE_TEST_FUNCTIONAL);
        parent::setUp();
    }

    /**
     * @testCase - route index action must be a success
     * xdebug.mode                      = debug,coverage
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ORMException
     */
    public function testIndexAction(): void
    {
        $this->setAuth();
        $this->prepareDbMongoIntegration();
        /** @var array|null $logs */
        $logs = $this->documentManagerIntegration->getRepository(Log::class)
            ->findBy([], ['id' => 'ASC'], 5);
        $response = 'error';
        if (! empty($logs) && count($logs) == 5) {
            $this->dispatch(self::ROUTE_URL, self::METHOD_GET);
            $this->assertResponseStatusCode(self::STATUS_CODE_200);
            $this->assertModuleName(self::MODULE_NAME);
            $this->assertControllerName(self::CONTROLLER_NAME);
            $this->assertControllerClass(self::CONTROLLER_CLASS);
            $this->assertMatchedRouteName(self::ROUTE_LOGS);
            $response = $this->getResponse()->getContent();
            self::assertTrue($this->assertHTML($response, false));
        }
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route view action - must be a success
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ORMException
     */
    public function testViewAction(): void
    {
        $this->setAuth();
        $this->prepareDbMongoIntegration();
        /** @var array|null $logs */
        $logs = $this->documentManagerIntegration->getRepository(Log::class)->findBy([], null, 1);
        $response = 'error';
        if (! empty($logs)) {
            /** @var Log|null $log */
            $log = $logs[0];
            $this->dispatch(self::ROUTE_URL . '/view/' . $log->getId(), self::METHOD_GET);
            $this->assertResponseStatusCode(self::STATUS_CODE_200);
            $this->assertModuleName(self::MODULE_NAME);
            $this->assertControllerName(self::CONTROLLER_NAME);
            $this->assertControllerClass(self::CONTROLLER_CLASS);
            $this->assertMatchedRouteName(self::ROUTE_LOGS);
            $response = $this->getResponse()->getContent();
            self::assertTrue($this->assertHTML($response, false));
        }
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route add action by method GET - must be a success
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testAddActionGet(): void
    {
        $this->setAuth();
        $this->dispatch(self::ROUTE_URL . '/add', self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_LOGS);
        $expected = file_get_contents(
            __DIR__ . '/../data/Controller/Logs/GetAddAction.html'
        );
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith($this->trim($expected), $this->trim($response));
    }

    /**
     * @testCase - route edit action by method GET - must be a success
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testEditActionGet(): void
    {
        $this->setAuth();
        $this->prepareDbMongoIntegration();
        /** @var array|null $logs */
        $logs = $this->documentManagerIntegration->getRepository(Log::class)->findBy([], null, 1);
        $response = 'error';
        if (! empty($logs)) {
            /** @var Log|null $log */
            $log = $logs[0];
            $this->dispatch(self::ROUTE_URL . '/edit/' . $log->getId(), self::METHOD_GET);
            $this->assertResponseStatusCode(self::STATUS_CODE_200);
            $this->assertModuleName(self::MODULE_NAME);
            $this->assertControllerName(self::CONTROLLER_NAME);
            $this->assertControllerClass(self::CONTROLLER_CLASS);
            $this->assertMatchedRouteName(self::ROUTE_LOGS);
            $response = $this->getResponse()->getContent();
        }
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - routes CRUD actions - must be a success
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ORMException
     * @throws Exception
     */
    public function testCRUDActions(): void
    {
        $this->prepareDbMongoIntegration();
        $this->addActionPost();
        /** @var array|null $logs */
        $logs = $this->documentManagerIntegration->getRepository(Log::class)
            ->findBy([], ['id' => 'DESC'], 1);
        if (! empty($logs)) {
            /** @var Log|null $log */
            $log = $logs[0];
            $this->reset();
            $this->editActionPost($log);
            $this->reset();
            $this->deleteActionPost($log);
        }
    }

    /**
     * @testCase - route add action by method POST - must be a success
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    private function addActionPost(): void
    {
        $this->setAuth();
        $form = new LogForm('create');
        $params = [
            'message' => '{orm_default} DB Connected.',
            'priority' => '6',
            'timestamp' => '2023-02-01',
            'csrf' => $form->get('csrf')->getValue(),
            'submit' => 'Create',
        ];
        $this->dispatch(self::ROUTE_URL . '/add', self::METHOD_POST, $params);
        $this->assertResponseStatusCode(self::STATUS_CODE_302);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_LOGS);
        $expected = '';
        $response = $this->getResponse()->getContent();
        self::assertSame($this->trim($expected), $this->trim($response));
    }

    /**
     * @testCase - route edit action by method POST - must be a success
     *
     * @param Log $log
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    private function editActionPost(Log $log): void
    {
        $this->setAuth();
        $form = new LogForm('update');
        $params = [
            'message' => 'controllerName: Application\Controller\IndexController, actionName: index',
            'priority' => '7',
            'csrf' => $form->get('csrf')->getValue(),
            'submit' => 'Create',
        ];
        $this->dispatch(self::ROUTE_URL . '/edit/' . $log->getId(), self::METHOD_POST, $params);
        $this->assertResponseStatusCode(self::STATUS_CODE_302);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_LOGS);
        $expected = '';
        $response = $this->getResponse()->getContent();
        self::assertSame($this->trim($expected), $this->trim($response));
    }

    /**
     * @testCase - route delete action by method POST - must be a success
     *
     * @param Log $log
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    private function deleteActionPost(Log $log): void
    {
        $this->setAuth();
        $this->dispatch(self::ROUTE_URL . '/delete/' . $log->getId(), self::METHOD_POST);
        $this->assertResponseStatusCode(self::STATUS_CODE_302);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_LOGS);
        $expected = '';
        $response = $this->getResponse()->getContent();
        self::assertSame($this->trim($expected), $this->trim($response));
    }
}
