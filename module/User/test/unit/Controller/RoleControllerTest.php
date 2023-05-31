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
use User\Entity\Permission;
use User\Entity\Role;
use User\Form\RoleForm;
use User\Form\RolePermissionsForm;
use User\Service\AuthManager;
use User\Service\RoleManager;

/**
 * Class RoleControllerTest - Unit tests for RoleController
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package UserTest\unit\Controller
 */
class RoleControllerTest extends AbstractMock
{
    public const MODULE_NAME = 'user';
    public const CONTROLLER_NAME = RoleController::class;
    public const CONTROLLER_CLASS = 'RoleController';
    public const ROUTE_URL = '/roles';
    public const ROUTE_USERS = 'roles';

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

        $roles = [];
        $role = $this->createRole();
        $this->setEntityId($role, self::ROLE_ID);
        $roles[] = $role;

        $roleManagerMock = $this->getMockBuilder(RoleManager::class)
            ->onlyMethods(['rolesGetFromQueueRedis', 'rolesPushToQueueRedis'])
            ->disableOriginalConstructor()
            ->getMock();

        $roleManagerMock->expects(self::once())
            ->method('rolesGetFromQueueRedis')
            ->willReturn([]);

        $roleManagerMock->expects($this->exactly(1))
            ->method('rolesPushToQueueRedis')
            ->with($this->equalTo($roles));

        $this->serviceManager->setService(RoleManager::class, $roleManagerMock);

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['findBy'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findBy')
            ->with(
                $this->equalTo([]),
                $this->equalTo(['id' => 'ASC']),
            )
            ->willReturn($roles);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [Role::class],
            )
            ->willReturn($repositoryMock);

        $this->dispatch(self::ROUTE_URL, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        $expected = file_get_contents(
            __DIR__ . '/../data/Controller/Role/GetIndexActionSuccess.html'
        );
        $dateCreated = $role->getDateCreated()->format('Y-m-d H:i:s');
        $expected = str_replace('|Date Created|', $dateCreated, $expected);
//        self::assertSame($this->trim($expected), $this->trim($response));
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

        $role = $this->createRole();
        $this->setEntityId($role, self::ROLE_ID);

        $roleManagerMock = $this->getMockBuilder(RoleManager::class)
            ->onlyMethods(['roleCheckRedis', 'roleGetRedis', 'roleSetRedis', 'getEffectivePermissions'])
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
            ->with($this->equalTo($role));

        $allPermissions = [];
        $permission = $this->createPermission(self::USER_PERMISSION_USER_MANAGE);
        $this->setEntityId($permission, self::PERMISSION_ID);
        $allPermissions[] = $permission;

        $roleManagerMock->expects($this->exactly(1))
            ->method('getEffectivePermissions')
            ->with($this->equalTo($role))
            ->willReturn($allPermissions);

        $this->serviceManager->setService(RoleManager::class, $roleManagerMock);

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['find', 'findBy'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('find')
            ->with($this->equalTo(self::ROLE_ID))
            ->willReturn($role);

        $repositoryMock->expects($this->exactly(1))
            ->method('findBy')
            ->with($this->equalTo([]), $this->equalTo(['name' => 'ASC']))
            ->willReturn($allPermissions);

        $this->entityManager->expects($this->exactly(2))
            ->method('getRepository')
            ->withConsecutive(
                [Role::class],
                [Permission::class],
            )
            ->willReturn($repositoryMock);

        $this->dispatch(self::ROUTE_URL . '/view/' . self::ROLE_ID, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        $expected = file_get_contents(
            __DIR__ . '/../data/Controller/Role/GetViewActionSuccess.html'
        );
        $dateCreated = $role->getDateCreated()->format('Y-m-d H:i:s');
        $expected = str_replace('|Date Created|', $dateCreated, $expected);
//        self::assertSame($this->trim($expected), $this->trim($response));
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

        $roles = [];
        $role = $this->createRole();
        $this->setEntityId($role, self::ROLE_ID);
        $roles[] = $role;

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['findBy'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findBy')
            ->with($this->equalTo([]), $this->equalTo(['name' => 'ASC']))
            ->willReturn($roles);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [Role::class],
            )
            ->willReturn($repositoryMock);

        $this->dispatch(self::ROUTE_URL . '/add', self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
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
            'inherit_roles' => null,
            'csrf' => $form->get('csrf')->getValue(),
            'submit' => 'Create',
        ];

        $roleManagerMock = $this->getMockBuilder(RoleManager::class)
            ->onlyMethods(['addRole', 'roleSetRedis', 'rolePushToQueueRedis'])
            ->disableOriginalConstructor()
            ->getMock();

        $roleManagerMock->expects(self::once())
            ->method('addRole')
            ->with($this->equalTo($params))
            ->willReturn($role);

        $roleManagerMock->expects(self::once())
            ->method('roleSetRedis')
            ->with($this->equalTo($role));

        $roleManagerMock->expects(self::once())
            ->method('rolePushToQueueRedis')
            ->with($this->equalTo($role));

        $this->serviceManager->setService(RoleManager::class, $roleManagerMock);

        $this->dispatch(self::ROUTE_URL . '/add', self::METHOD_POST, $params);
        $this->assertResponseStatusCode(self::STATUS_CODE_302);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
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

        $roles = [];
        $role1 = $this->createRole();
        $this->setEntityId($role1, self::ROLE_ID);

        $role2 = $this->createRole(self::USER_ROLE_NAME_GUEST);
        $this->setEntityId($role2, self::ROLE_ID + 1);
        $role1->addParent($role2);

        $roles[] = $role1;
        $roles[] = $role2;

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['find', 'findBy'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('find')
            ->with($this->equalTo(self::ROLE_ID))
            ->willReturn($role1);

        $repositoryMock->expects($this->exactly(1))
            ->method('findBy')
            ->with($this->equalTo([]), $this->equalTo(['name' => 'ASC']))
            ->willReturn($roles);

        $this->entityManager->expects($this->exactly(2))
            ->method('getRepository')
            ->withConsecutive(
                [Role::class],
            )
            ->willReturn($repositoryMock);

        $this->dispatch(self::ROUTE_URL . '/edit/' . self::ROLE_ID, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
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
            'inherit_roles' => null,
            'csrf' => $form->get('csrf')->getValue(),
            'submit' => 'Create',
        ];

        $roleManagerMock = $this->getMockBuilder(RoleManager::class)
            ->onlyMethods(['updateRole', 'roleSetRedis'])
            ->disableOriginalConstructor()
            ->getMock();

        $roleManagerMock->expects(self::once())
            ->method('updateRole')
            ->with($this->equalTo($role), $this->equalTo($params))
            ->willReturn($role);

        $roleManagerMock->expects(self::once())
            ->method('roleSetRedis')
            ->with($this->equalTo($role));

        $this->serviceManager->setService(RoleManager::class, $roleManagerMock);

        $this->dispatch(self::ROUTE_URL . '/edit/' . self::ROLE_ID, self::METHOD_POST, $params);
        $this->assertResponseStatusCode(self::STATUS_CODE_302);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertEquals('', $this->trim($response));
    }

    /**
     * @testCase - route editPermissions action by method GET - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testEditPermissionsActionGet(): void
    {
        $this->setAuthMock(
            self::CONTROLLER_NAME,
            'editPermissions',
            AuthManager::ACCESS_GRANTED
        );

        $role = $this->createRole();
        $this->setEntityId($role, self::ROLE_ID);

        $role2 = $this->createRole(self::USER_ROLE_NAME_GUEST);
        $this->setEntityId($role2, self::ROLE_ID + 1);
        $role->setParentRole($role2);

        $permissions = [];
        $permission = $this->createPermission();
        $permissions[] = $permission;

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['find', 'findBy'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('find')
            ->with($this->equalTo(self::ROLE_ID))
            ->willReturn($role);

        $repositoryMock->expects($this->exactly(1))
            ->method('findBy')
            ->with($this->equalTo([]), $this->equalTo(['name' => 'ASC']))
            ->willReturn($permissions);

        $this->entityManager->expects($this->exactly(2))
            ->method('getRepository')
            ->withConsecutive(
                [Role::class],
            )
            ->willReturn($repositoryMock);

        $this->dispatch(self::ROUTE_URL . '/edit-permissions/' . self::ROLE_ID, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route editPermissions action by method POST - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testEditPermissionsActionPost(): void
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

        $form = new RolePermissionsForm();
        $params = [
            'permissions' => [
                'profile.any.view' => null
            ],
            'csrf' => $form->get('csrf')->getValue(),
            'submit' => 'Create',
        ];

        $roleManagerMock = $this->getMockBuilder(RoleManager::class)
            ->onlyMethods(['updateRolePermissions'])
            ->disableOriginalConstructor()
            ->getMock();

        $roleManagerMock->expects(self::once())
            ->method('updateRolePermissions')
            ->with($this->equalTo($role), $this->equalTo($params));

        $this->serviceManager->setService(RoleManager::class, $roleManagerMock);

        $this->dispatch(self::ROUTE_URL . '/edit-permissions/' . self::ROLE_ID, self::METHOD_POST, $params);
        $this->assertResponseStatusCode(self::STATUS_CODE_302);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
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

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        $role = $this->createRole();
        $this->setEntityId($role, self::ROLE_ID);

        $repositoryMock->expects($this->exactly(1))
            ->method('find')
            ->with($this->equalTo(self::PERMISSION_ID))
            ->willReturn($role);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [Role::class],
            )
            ->willReturn($repositoryMock);

        $permissionManagerMock = $this->getMockBuilder(RoleManager::class)
            ->onlyMethods(['deleteRole'])
            ->disableOriginalConstructor()
            ->getMock();

        $permissionManagerMock->expects(self::once())
            ->method('deleteRole')
            ->with($this->equalTo($role));

        $this->serviceManager->setService(RoleManager::class, $permissionManagerMock);

        $this->dispatch(self::ROUTE_URL . '/delete/' . self::ROLE_ID, self::METHOD_POST);
        $this->assertResponseStatusCode(self::STATUS_CODE_302);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertEquals('', $this->trim($response));
    }
}
