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
use User\Controller\PermissionController;
use User\Entity\Permission;
use User\Form\PermissionForm;
use User\Service\AuthManager;
use User\Service\PermissionManager;

/**
 * Class PermissionControllerTest - Unit tests for PermissionController
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package UserTest\unit\Controller
 */
class PermissionControllerTest extends AbstractMock
{
    public const MODULE_NAME = 'user';
    public const CONTROLLER_NAME = PermissionController::class;
    public const CONTROLLER_CLASS = 'PermissionController';
    public const ROUTE_URL = '/permissions';
    public const ROUTE_USERS = 'permissions';

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

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['findBy'])
            ->disableOriginalConstructor()
            ->getMock();

        $permissions = [];
        $permission = $this->createPermission(self::USER_PERMISSION_USER_MANAGE);
        $this->setEntityId($permission, self::PERMISSION_ID);
        $permissions[] = $permission;

        $repositoryMock->expects($this->exactly(1))
            ->method('findBy')
            ->with(
                $this->equalTo([]),
                $this->equalTo(['name' => 'ASC']),
            )
            ->willReturn($permissions);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [Permission::class],
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
            __DIR__ . '/../data/Controller/Permission/GetIndexActionSuccess.html'
        );
        $dateCreated = $permission->getDateCreated()->format('Y-m-d H:i:s');
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

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        $permission = $this->createPermission(self::USER_PERMISSION_USER_MANAGE);
        $this->setEntityId($permission, self::PERMISSION_ID);

        $repositoryMock->expects($this->exactly(1))
            ->method('find')
            ->with(
                $this->equalTo(self::PERMISSION_ID),
            )
            ->willReturn($permission);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [Permission::class],
            )
            ->willReturn($repositoryMock);

        $this->dispatch(self::ROUTE_URL . '/view/' . self::PERMISSION_ID, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        $expected = file_get_contents(
            __DIR__ . '/../data/Controller/Permission/GetViewActionSuccess.html'
        );
        $dateCreated = $permission->getDateCreated()->format('Y-m-d H:i:s');
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

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->addMethods(['findOneByName'])
            ->disableOriginalConstructor()
            ->getMock();

        $permission = $this->createPermission(self::USER_PERMISSION_USER_MANAGE);
        $this->setEntityId($permission, self::PERMISSION_ID);

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneByName')
            ->with(
                $this->equalTo(self::USER_PERMISSION_USER_MANAGE),
            )
            ->willReturn($permission);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [Permission::class],
            )
            ->willReturn($repositoryMock);

        $form = new PermissionForm('create', $this->entityManager);
        $params = [
            'name' => self::USER_PERMISSION_USER_MANAGE,
            'description' => self::USER_PERMISSION_DESCRIPTION,
            'csrf' => $form->get('csrf')->getValue(),
            'submit' => 'Create',
        ];
        $permissionManagerMock = $this->getMockBuilder(PermissionManager::class)
            ->onlyMethods(['addPermission'])
            ->disableOriginalConstructor()
            ->getMock();

        $permissionManagerMock->expects(self::once())
            ->method('addPermission')
            ->with($this->equalTo($params));

        $this->serviceManager->setService(PermissionManager::class, $permissionManagerMock);

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

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        $permission = $this->createPermission(self::USER_PERMISSION_USER_MANAGE);
        $this->setEntityId($permission, self::PERMISSION_ID);

        $repositoryMock->expects($this->exactly(1))
            ->method('find')
            ->with($this->equalTo(self::PERMISSION_ID))
            ->willReturn($permission);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [Permission::class],
            )
            ->willReturn($repositoryMock);

        $this->dispatch(self::ROUTE_URL . '/edit/' . self::PERMISSION_ID, self::METHOD_GET);
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

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['find'])
            ->addMethods(['findOneByName'])
            ->disableOriginalConstructor()
            ->getMock();

        $permission = $this->createPermission(self::USER_PERMISSION_USER_MANAGE);
        $this->setEntityId($permission, self::PERMISSION_ID);

        $repositoryMock->expects($this->exactly(1))
            ->method('find')
            ->with($this->equalTo(self::PERMISSION_ID))
            ->willReturn($permission);

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneByName')
            ->with($this->equalTo(self::USER_PERMISSION_USER_MANAGE))
            ->willReturn($permission);

        $this->entityManager->expects($this->exactly(2))
            ->method('getRepository')
            ->withConsecutive(
                [Permission::class],
            )
            ->willReturn($repositoryMock);

        $form = new PermissionForm('update', $this->entityManager, $permission);
        $params = [
            'name' => self::USER_PERMISSION_USER_MANAGE,
            'description' => self::USER_PERMISSION_DESCRIPTION,
            'csrf' => $form->get('csrf')->getValue(),
            'submit' => 'Create',
        ];
        $permissionManagerMock = $this->getMockBuilder(PermissionManager::class)
            ->onlyMethods(['updatePermission'])
            ->disableOriginalConstructor()
            ->getMock();

        $permissionManagerMock->expects(self::once())
            ->method('updatePermission')
            ->with($this->equalTo($permission), $this->equalTo($params));

        $this->serviceManager->setService(PermissionManager::class, $permissionManagerMock);

        $this->dispatch(self::ROUTE_URL . '/edit/' . self::PERMISSION_ID, self::METHOD_POST, $params);
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

        $permission = $this->createPermission(self::USER_PERMISSION_USER_MANAGE);
        $this->setEntityId($permission, self::PERMISSION_ID);

        $repositoryMock->expects($this->exactly(1))
            ->method('find')
            ->with($this->equalTo(self::PERMISSION_ID))
            ->willReturn($permission);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [Permission::class],
            )
            ->willReturn($repositoryMock);

        $permissionManagerMock = $this->getMockBuilder(PermissionManager::class)
            ->onlyMethods(['deletePermission'])
            ->disableOriginalConstructor()
            ->getMock();

        $permissionManagerMock->expects(self::once())
            ->method('deletePermission')
            ->with($this->equalTo($permission));

        $this->serviceManager->setService(PermissionManager::class, $permissionManagerMock);

        $this->dispatch(self::ROUTE_URL . '/delete/' . self::PERMISSION_ID, self::METHOD_POST);
        $this->assertResponseStatusCode(self::STATUS_CODE_302);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertEquals('', $this->trim($response));
    }
}
