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
use User\Service\AuthManager;

/**
 * Class PermissionControllerNegativeTest - Unit negative tests for PermissionController
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package UserTest\unit\Controller
 */
class PermissionControllerNegativeTest extends AbstractMock
{
    public const MODULE_NAME = 'user';
    public const CONTROLLER_NAME = PermissionController::class;
    public const CONTROLLER_CLASS = 'PermissionController';
    public const ROUTE_URL = '/permissions';
    public const ROUTE_USERS = 'permissions';

    /**
     * @testCase - route view action - not id
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
     * @testCase - route view action - empty permission
     *
     * @return void
     * @throws Exception
     */
    public function testViewActionEmptyPermission(): void
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

        $permission = null;

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

        $this->dispatch(self::ROUTE_URL . '/add', self::METHOD_POST);
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
     * @testCase - route edit action by method GET - empty permission
     *
     * @return void
     * @throws Exception
     */
    public function testEditActionGetEmptyPermission(): void
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

        $permission = null;

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

        $this->dispatch(self::ROUTE_URL . '/edit/' . self::PERMISSION_ID, self::METHOD_POST);
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
     * @testCase - route delete action by method GET - empty permission
     *
     * @return void
     * @throws Exception
     */
    public function testDeleteActionGetEmptyPermission(): void
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

        $permission = null;

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

        $this->dispatch(self::ROUTE_URL . '/delete/' . self::PERMISSION_ID, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }
}
