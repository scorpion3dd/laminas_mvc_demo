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

namespace UserTest\integration\Controller;

use ApplicationTest\AbstractMock;
use Doctrine\ORM\ORMException;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\Controller\PermissionController;
use User\Entity\Permission;
use User\Form\PermissionForm;

/**
 * Class PermissionControllerIntegrationTest - Integration tests for PermissionController
 * with connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are real
 *
 * @package UserTest\integration\Controller
 */
class PermissionControllerIntegrationTest extends AbstractMock
{
    public const MODULE_NAME = 'user';
    public const CONTROLLER_NAME = PermissionController::class;
    public const CONTROLLER_CLASS = 'PermissionController';
    public const ROUTE_URL = '/permissions';
    public const ROUTE_USERS = 'permissions';

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
     * @testCase - route index action - must be a success
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testIndexAction(): void
    {
        $this->setAuth();
        $this->dispatch(self::ROUTE_URL, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertTrue($this->assertHTML($response, false));
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
        /** @var Permission|null $permission */
        $permission = $this->getPermission();
        $expected = '';
        $response = 'error';
        if (! empty($permission)) {
            $this->dispatch(self::ROUTE_URL . '/view/' . $permission->getId(), self::METHOD_GET);
            $this->assertResponseStatusCode(self::STATUS_CODE_200);
            $this->assertModuleName(self::MODULE_NAME);
            $this->assertControllerName(self::CONTROLLER_NAME);
            $this->assertControllerClass(self::CONTROLLER_CLASS);
            $this->assertMatchedRouteName(self::ROUTE_USERS);
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
     */
    public function testAddActionGet(): void
    {
        $this->setAuth();
        $this->dispatch(self::ROUTE_URL . '/add', self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertTrue($this->assertHTML($response, false));
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route edit action by method GET - must be a success
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ORMException
     */
    public function testEditActionGet(): void
    {
        $this->setAuth();
        /** @var Permission|null $permission */
        $permission = $this->getPermission();
        $response = 'error';
        if (! empty($permission)) {
            $this->dispatch(self::ROUTE_URL . '/edit/' . $permission->getId(), self::METHOD_GET);
            $this->assertResponseStatusCode(self::STATUS_CODE_200);
            $this->assertModuleName(self::MODULE_NAME);
            $this->assertControllerName(self::CONTROLLER_NAME);
            $this->assertControllerClass(self::CONTROLLER_CLASS);
            $this->assertMatchedRouteName(self::ROUTE_USERS);
            $response = $this->getResponse()->getContent();
            self::assertTrue($this->assertHTML($response, false));
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
        $this->prepareDbMySqlIntegration();
        $this->addActionPost();
        /** @var array|null $permissions */
        $permissions = $this->entityManagerIntegration->getRepository(Permission::class)
            ->findBy([], ['id' => 'DESC'], 1);
        if (! empty($permissions)) {
            /** @var Permission|null $permission */
            $permission = $permissions[0];
            $this->reset();
            $this->editActionPost($permission);
            $this->reset();
            $this->deleteActionPost($permission);
        }
    }

    /**
     * @testCase - route add action by method POST - must be a success
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function addActionPost(): void
    {
        $this->setAuth();
        $form = new PermissionForm('create', $this->entityManagerIntegration);
        $params = [
            'name' => self::PERMISSION_NAME,
            'description' => self::PERMISSION_DESCRIPTION,
            'csrf' => $form->get('csrf')->getValue(),
            'submit' => 'Create',
        ];
        $this->dispatch(self::ROUTE_URL . '/add', self::METHOD_POST, $params);
        $this->assertResponseStatusCode(self::STATUS_CODE_302);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $expected = '';
        $response = $this->getResponse()->getContent();
        self::assertSame($this->trim($expected), $this->trim($response));
    }

    /**
     * @testCase - route edit action by method POST - must be a success
     *
     * @param Permission $permission
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function editActionPost(Permission $permission): void
    {
        $this->setAuth();
        $form = new PermissionForm('update', $this->entityManager, $permission);
        $params = [
            'name' => self::PERMISSION_NAME . ' edited',
            'description' => self::PERMISSION_DESCRIPTION . ' edited',
            'csrf' => $form->get('csrf')->getValue(),
            'submit' => 'Create',
        ];
        $this->dispatch(self::ROUTE_URL . '/edit/' . $permission->getId(), self::METHOD_POST, $params);
        $this->assertResponseStatusCode(self::STATUS_CODE_302);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $expected = '';
        $response = $this->getResponse()->getContent();
        self::assertSame($this->trim($expected), $this->trim($response));
    }

    /**
     * @testCase - route delete action by method POST - must be a success
     *
     * @param Permission $permission
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function deleteActionPost(Permission $permission): void
    {
        $this->setAuth();
        $this->dispatch(self::ROUTE_URL . '/delete/' . $permission->getId(), self::METHOD_POST);
        $this->assertResponseStatusCode(self::STATUS_CODE_302);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $expected = '';
        $response = $this->getResponse()->getContent();
        self::assertSame($this->trim($expected), $this->trim($response));
    }
}
