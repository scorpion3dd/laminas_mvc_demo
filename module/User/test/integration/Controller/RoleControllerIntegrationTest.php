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
use User\Controller\RoleController;
use User\Entity\Role;
use User\Form\RoleForm;
use User\Form\RolePermissionsForm;

/**
 * Class RoleControllerIntegrationTest - Integration tests for RoleController
 * with connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are real
 *
 * @package UserTest\integration\Controller
 */
class RoleControllerIntegrationTest extends AbstractMock
{
    public const MODULE_NAME = 'user';
    public const CONTROLLER_NAME = RoleController::class;
    public const CONTROLLER_CLASS = 'RoleController';
    public const ROUTE_URL = '/roles';
    public const ROUTE_USERS = 'roles';

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        $this->setTypeTest(self::TYPE_TEST_FUNCTIONAL);
        parent::setUp();
        $this->setEnvTest();
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
        /** @var Role|null $role */
        $role = $this->getRole();
        $expected = '';
        $response = 'error';
        if (! empty($role)) {
            $this->dispatch(self::ROUTE_URL . '/view/' . $role->getId(), self::METHOD_GET);
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
        /** @var Role|null $role */
        $role = $this->getRole();
        $response = 'error';
        if (! empty($role)) {
            $this->dispatch(self::ROUTE_URL . '/edit/' . $role->getId(), self::METHOD_GET);
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
        /** @var array|null $roles */
        $roles = $this->entityManagerIntegration->getRepository(Role::class)
            ->findBy([], ['id' => 'DESC'], 1);
        if (! empty($roles)) {
            /** @var Role|null $role */
            $role = $roles[0];
            $this->reset();
            $this->editActionPost($role);
            $this->reset();
            $this->deleteActionPost($role);
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
        $form = new RoleForm('create', $this->entityManagerIntegration);
        $params = [
            'name' => self::ROLE_NAME,
            'description' => self::ROLE_DESCRIPTION,
            'inherit_roles' => null,
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
     * @param Role $role
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function editActionPost(Role $role): void
    {
        $this->setAuth();
        $form = new RoleForm('update', $this->entityManager, $role);
        $params = [
            'name' => self::ROLE_NAME . ' edited',
            'description' => self::ROLE_DESCRIPTION . ' edited',
            'inherit_roles' => null,
            'csrf' => $form->get('csrf')->getValue(),
            'submit' => 'Create',
        ];
        $this->dispatch(self::ROUTE_URL . '/edit/' . $role->getId(), self::METHOD_POST, $params);
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
     * @param Role $role
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function deleteActionPost(Role $role): void
    {
        $this->setAuth();
        $this->dispatch(self::ROUTE_URL . '/delete/' . $role->getId(), self::METHOD_POST);
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
     * @testCase - route editPermissions action by method GET - must be a success
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ORMException
     */
    public function testEditPermissionsActionGet(): void
    {
        $this->setAuth();
        /** @var Role|null $role */
        $role = $this->getRole();
        $response = 'error';
        if (! empty($role)) {
            $this->dispatch(self::ROUTE_URL . '/edit-permissions/' . $role->getId(), self::METHOD_GET);
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
     * @testCase - route editPermissions action by method POST - must be a success
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ORMException
     */
    public function testEditPermissionsActionPost(): void
    {
        $this->setAuth();
        /** @var Role|null $role */
        $role = $this->getRole();
        $response = 'error';
        if (! empty($role)) {
            $form = new RolePermissionsForm();
            $params = [
                'permissions' => [
                    'Administrator' => null
                ],
                'csrf' => $form->get('csrf')->getValue(),
                'submit' => 'Create',
            ];
            $this->dispatch(self::ROUTE_URL . '/edit-permissions/' . $role->getId(), self::METHOD_POST, $params);
            $this->assertResponseStatusCode(self::STATUS_CODE_302);
            $this->assertModuleName(self::MODULE_NAME);
            $this->assertControllerName(self::CONTROLLER_NAME);
            $this->assertControllerClass(self::CONTROLLER_CLASS);
            $this->assertMatchedRouteName(self::ROUTE_USERS);
            $response = $this->getResponse()->getContent();
        }
        self::assertEquals('', $this->trim($response));
    }
}
