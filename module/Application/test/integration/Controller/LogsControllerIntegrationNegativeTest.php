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
use User\Entity\User;
use Laminas\View\Renderer\PhpRenderer;

/**
 * Class LogsControllerIntegrationNegativeTest - Integration negative tests for LogsController
 * with connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are real
 *
 * @package ApplicationTest\integration\Controller
 */
class LogsControllerIntegrationNegativeTest extends AbstractMock
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
     * @testCase - route view action test - id not valid
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testViewActionIdNotValid(): void
    {
        $this->setAuth();
        $this->dispatch(self::ROUTE_URL . '/view/', self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_LOGS);
        $response = $this->getResponse()->getContent();
        self::assertTrue($this->assertHTML($response, false));
        $expected = file_get_contents(
            __DIR__ . '/../data/Controller/Logs/GetViewActionIdNotValid.html'
        );
        self::assertSame($this->trim($expected), $this->trim($response));
    }

    /**
     * @testCase - route view action test - Log empty
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testViewActionLogEmpty(): void
    {
        $this->setAuth();
        $this->dispatch(self::ROUTE_URL . '/view/' . self::LOG_ID, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_LOGS);
        $response = $this->getResponse()->getContent();
        self::assertTrue($this->assertHTML($response, false));
        $expected = file_get_contents(
            __DIR__ . '/../data/Controller/Logs/GetViewActionLogEmpty.html'
        );
        self::assertSame($this->trim($expected), $this->trim($response));
    }

    /**
     * @testCase - route add action by method POST - form is not valid
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testAddActionPostFormIsNotValid(): void
    {
        $this->setAuth();
        $params = [
            'message' => '{orm_default} DB Connected.',
            'priority' => '200',
            'timestamp' => '2023-02-01',
            'submit' => 'Create',
        ];
        $this->dispatch(self::ROUTE_URL . '/add', self::METHOD_POST, $params);
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_LOGS);
        $expected = file_get_contents(
            __DIR__ . '/../data/Controller/Logs/AddActionPostFormIsNotValid.html'
        );
        $message = $this->escapeHtml($params['message']);
        $expected = str_replace('|MESSAGE|', $message, $expected);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith($this->trim($expected), $this->trim($response));
    }

    /**
     * @testCase - route edit action by method GET - not id
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testEditActionGetNotId(): void
    {
        $this->setAuth();
        $this->dispatch(self::ROUTE_URL . '/edit', self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_LOGS);
        $expected = file_get_contents(
            __DIR__ . '/../data/Controller/Logs/EditActionGetNotId.html'
        );
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith($this->trim($expected), $this->trim($response));
    }

    /**
     * @testCase - route edit action by method GET - empty log
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testEditActionGetEmptyLog(): void
    {
        $this->setAuth();
        $this->dispatch(self::ROUTE_URL . '/edit/' . self::LOG_ID, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_LOGS);
        $expected = file_get_contents(
            __DIR__ . '/../data/Controller/Logs/EditActionGetNotId.html'
        );
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith($this->trim($expected), $this->trim($response));
    }

    /**
     * @testCase - route edit action by method POST - form is not valid
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testEditActionPostFormIsNotValid(): void
    {
        $this->setAuth();
        $this->prepareDbMongoIntegration();
        /** @var array|null $logs */
        $logs = $this->documentManagerIntegration->getRepository(Log::class)->findBy([], null, 1);
        $expected = '';
        $response = 'error';
        if (! empty($logs)) {
            /** @var Log|null $log */
            $log = $logs[0];
            $params = [
                'message' => '{orm_default} DB Connected.',
                'priority' => '2',
                'csrf' => '123',
                'submit' => 'Create',
            ];
            $this->dispatch(self::ROUTE_URL . '/edit/' . $log->getId(), self::METHOD_POST, $params);
            $this->assertResponseStatusCode(self::STATUS_CODE_200);
            $this->assertModuleName(self::MODULE_NAME);
            $this->assertControllerName(self::CONTROLLER_NAME);
            $this->assertControllerClass(self::CONTROLLER_CLASS);
            $this->assertMatchedRouteName(self::ROUTE_LOGS);
            $expected = file_get_contents(
                __DIR__ . '/../data/Controller/Logs/EditActionPostFormIsNotValid.html'
            );
            $expected = str_replace('|LOG_ID|', $log->getId(), $expected);
            $response = $this->getResponse()->getContent();
        }
        self::assertStringStartsWith($this->trim($expected), $this->trim($response));
    }

    /**
     * @testCase - route delete action by method GET - not id
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testDeleteActionGetNotId(): void
    {
        $this->setAuth();
        $this->dispatch(self::ROUTE_URL . '/delete', self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_LOGS);
        $expected = file_get_contents(
            __DIR__ . '/../data/Controller/Logs/EditActionGetNotId.html'
        );
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith($this->trim($expected), $this->trim($response));
    }

    /**
     * @testCase - route delete action by method GET - empty log
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testDeleteActionGetEmptyLog(): void
    {
        $this->setAuth();
        $this->dispatch(self::ROUTE_URL . '/delete/' . self::LOG_ID, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_LOGS);
        $expected = file_get_contents(
            __DIR__ . '/../data/Controller/Logs/EditActionGetNotId.html'
        );
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith($this->trim($expected), $this->trim($response));
    }
}
