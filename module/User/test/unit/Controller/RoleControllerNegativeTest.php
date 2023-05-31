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
use Doctrine\ORM\EntityRepository;
use Exception;
use User\Controller\RoleController;
use User\Entity\Role;
use User\Form\RoleForm;
use User\Service\AuthManager;
use User\Service\RoleManager;

/**
 * Class RoleControllerNegativeTest - Unit negative tests for RoleControllerNegative
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package UserTest\unit\Controller
 */
class RoleControllerNegativeTest extends AbstractMock
{
    public const MODULE_NAME = 'user';
    public const CONTROLLER_NAME = RoleController::class;
    public const CONTROLLER_CLASS = 'RoleController';
    public const ROUTE_URL = '/roles';
    public const ROUTE_USERS = 'roles';

    /**
     * @testCase - route view5 action - not id
     *
     * @return void
     * @throws Exception
     */
    public function testViewActionNotId(): void
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
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route view action - empty role
     *
     * @return void
     * @throws Exception
     */
    public function testViewActionEmptyRole(): void
    {
        $this->setAuthMock(
            self::CONTROLLER_NAME,
            'view',
            AuthManager::ACCESS_GRANTED
        );

        $role = $this->createRole();
        $this->setEntityId($role, self::ROLE_ID);

        $roleManagerMock = $this->getMockBuilder(RoleManager::class)
            ->onlyMethods(['roleCheckRedis', 'roleGetRedis', 'roleSetRedis'])
            ->disableOriginalConstructor()
            ->getMock();

        $roleManagerMock->expects(self::once())
            ->method('roleCheckRedis')
            ->with($this->equalTo(self::ROLE_ID))
            ->willReturn(true);

        $roleManagerMock->expects($this->exactly(1))
            ->method('roleGetRedis')
            ->with($this->equalTo(self::ROLE_ID))
            ->willReturn(null);

        $roleManagerMock->expects($this->exactly(1))
            ->method('roleSetRedis')
            ->with($this->equalTo(null));

        $this->serviceManager->setService(RoleManager::class, $roleManagerMock);

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('find')
            ->with($this->equalTo(self::ROLE_ID))
            ->willReturn(null);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [Role::class],
            )
            ->willReturn($repositoryMock);

        $this->dispatch(self::ROUTE_URL . '/view/' . self::ROLE_ID, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
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

        $roles = [];
        $role = $this->createRole();
        $this->setEntityId($role, self::ROLE_ID);
        $roles[] = $role;

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['findBy'])
            ->addMethods(['findOneByName'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findBy')
            ->with($this->equalTo([]), $this->equalTo(['name' => 'ASC']))
            ->willReturn($roles);

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneByName')
            ->with(
                $this->equalTo(self::USER_ROLE_NAME_ADMINISTRATOR),
            )
            ->willReturn($role);

        $this->entityManager->expects($this->exactly(2))
            ->method('getRepository')
            ->withConsecutive(
                [Role::class],
            )
            ->willReturn($repositoryMock);

        $form = new RoleForm('create', $this->entityManager);
        $params = [
            'name' => self::USER_ROLE_NAME_ADMINISTRATOR,
            'description' => self::USER_ROLE_DESCRIPTION,
            'inherit_roles' => 0,
            'csrf' => $form->get('csrf')->getValue(),
            'submit' => 'Create',
        ];

        $this->dispatch(self::ROUTE_URL . '/add', self::METHOD_POST, $params);
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
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

        $this->dispatch(self::ROUTE_URL . '/edit/', self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route edit action by method GET - empty role
     *
     * @return void
     * @throws Exception
     */
    public function testEditActionGetEmptyRole(): void
    {
        $this->setAuthMock(
            self::CONTROLLER_NAME,
            'edit',
            AuthManager::ACCESS_GRANTED
        );

        $role = null;

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('find')
            ->with($this->equalTo(self::ROLE_ID))
            ->willReturn($role);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [Role::class],
            )
            ->willReturn($repositoryMock);

        $this->dispatch(self::ROUTE_URL . '/edit/' . self::ROLE_ID, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
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

        $roles = [];
        $role = $this->createRole();
        $this->setEntityId($role, self::ROLE_ID);
        $roles[] = $role;

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['find', 'findBy'])
            ->addMethods(['findOneByName'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findBy')
            ->with($this->equalTo([]), $this->equalTo(['name' => 'ASC']))
            ->willReturn($roles);

        $repositoryMock->expects($this->exactly(1))
            ->method('find')
            ->with($this->equalTo(self::ROLE_ID))
            ->willReturn($role);

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneByName')
            ->with(
                $this->equalTo(self::USER_ROLE_NAME_ADMINISTRATOR),
            )
            ->willReturn($role);

        $this->entityManager->expects($this->exactly(3))
            ->method('getRepository')
            ->withConsecutive(
                [Role::class],
            )
            ->willReturn($repositoryMock);

        $form = new RoleForm('update', $this->entityManager, $role);
        $params = [
            'name' => self::USER_ROLE_NAME_ADMINISTRATOR,
            'description' => self::USER_ROLE_DESCRIPTION,
            'inherit_roles' => 0,
            'csrf' => $form->get('csrf')->getValue(),
            'submit' => 'Create',
        ];

        $this->dispatch(self::ROUTE_URL . '/edit/' . self::ROLE_ID, self::METHOD_POST, $params);
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route editPermissions action by method GET - not id
     *
     * @return void
     * @throws Exception
     */
    public function testEditPermissionsActionGetNotId(): void
    {
        $this->setAuthMock(
            self::CONTROLLER_NAME,
            'editPermissions',
            AuthManager::ACCESS_GRANTED
        );

        $this->dispatch(self::ROUTE_URL . '/edit-permissions/', self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route editPermissions action by method GET - empty role
     *
     * @return void
     * @throws Exception
     */
    public function testEditPermissionsActionGetEmptyRole(): void
    {
        $this->setAuthMock(
            self::CONTROLLER_NAME,
            'editPermissions',
            AuthManager::ACCESS_GRANTED
        );

        $role = null;

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('find')
            ->with($this->equalTo(self::ROLE_ID))
            ->willReturn($role);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [Role::class],
            )
            ->willReturn($repositoryMock);

        $this->dispatch(self::ROUTE_URL . '/edit-permissions/' . self::ROLE_ID, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route editPermissions action by method POST - form is not valid
     *
     * @return void
     * @throws Exception
     */
    public function testEditPermissionsActionPostFormIsNotValid(): void
    {
        $this->setAuthMock(
            self::CONTROLLER_NAME,
            'editPermissions',
            AuthManager::ACCESS_GRANTED
        );

        $role = $this->createRole();
        $this->setEntityId($role, self::ROLE_ID);

        $permissions = [];
        $permission = $this->createPermission();
        $permissions[] = $permission;

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['find', 'findBy'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findBy')
            ->with($this->equalTo([]), $this->equalTo(['name' => 'ASC']))
            ->willReturn($permissions);

        $repositoryMock->expects($this->exactly(1))
            ->method('find')
            ->with($this->equalTo(self::ROLE_ID))
            ->willReturn($role);

        $this->entityManager->expects($this->exactly(2))
            ->method('getRepository')
            ->withConsecutive(
                [Role::class],
            )
            ->willReturn($repositoryMock);

        $params = [
            'submit' => 'Create',
        ];

        $this->dispatch(self::ROUTE_URL . '/edit-permissions/' . self::ROLE_ID, self::METHOD_POST, $params);
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
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

        $this->dispatch(self::ROUTE_URL . '/delete/', self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route delete action by method GET - empty role
     *
     * @return void
     * @throws Exception
     */
    public function testDeleteActionGetEmptyRole(): void
    {
        $this->setAuthMock(
            self::CONTROLLER_NAME,
            'delete',
            AuthManager::ACCESS_GRANTED
        );

        $role = null;

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('find')
            ->with($this->equalTo(self::ROLE_ID))
            ->willReturn($role);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [Role::class],
            )
            ->willReturn($repositoryMock);

        $this->dispatch(self::ROUTE_URL . '/delete/' . self::ROLE_ID, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }
}
