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

use Application\Controller\IndexController;
use ApplicationTest\AbstractMock;
use Doctrine\ORM\EntityRepository;
use Exception;
use User\Entity\Role;
use User\Entity\User;
use User\Service\AuthManager;
use Laminas\Authentication\AuthenticationService;

/**
 * Class IndexControllerNegativeTest - Unit negative tests for IndexController
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package ApplicationTest\unit\Controller
 */
class IndexControllerNegativeTest extends AbstractMock
{
    public const MODULE_NAME = 'application';
    public const CONTROLLER_NAME = IndexController::class;
    public const CONTROLLER_CLASS = 'IndexController';
    public const ROUTE_URL = '/';
    public const ROUTE_HOME = 'home';
    public const ROUTE_APPLICATION = 'application';

    /**
     * @testCase - route view action test - id not valid
     *
     * @return void
     * @throws Exception
     */
    public function testViewActionIdNotValid(): void
    {
        $this->dispatch(self::ROUTE_URL . 'application/view/' . 0, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_APPLICATION);
        $response = $this->getResponse()->getContent();
        $expected = file_get_contents(
            __DIR__ . '/../data/Controller/Index/GetViewActionIdNotValid.html'
        );
//        self::assertSame($this->trim($expected), $this->trim($response));
    }

    /**
     * @testCase - route view action test - User empty
     *
     * @return void
     * @throws Exception
     */
    public function testViewActionUserEmpty(): void
    {
        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        $user = null;

        $repositoryMock->expects($this->exactly(1))
            ->method('find')
            ->with($this->equalTo(self::USER_ID))
            ->willReturn($user);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [User::class]
            )
            ->willReturn($repositoryMock);

        $this->dispatch(self::ROUTE_URL . 'application/view/' . self::USER_ID, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_APPLICATION);
        $response = $this->getResponse()->getContent();
        $expected = file_get_contents(
            __DIR__ . '/../data/Controller/Index/GetViewActionUserEmpty.html'
        );
//        self::assertSame($this->trim($expected), $this->trim($response));
    }

    /**
     * @testCase - route settings action - User empty
     *
     * @return void
     * @throws Exception
     */
    public function testSettingsActionUserEmpty(): void
    {
        $this->setAuthMock(
            self::CONTROLLER_NAME,
            'settings',
            AuthManager::ACCESS_GRANTED
        );

        $this->dispatch(self::ROUTE_URL . 'application/settings', self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_APPLICATION);
        $response = $this->getResponse()->getContent();
        $expected = file_get_contents(
            __DIR__ . '/../data/Controller/Index/GetSettingsActionUserEmpty.html'
        );
//        self::assertSame($this->trim($expected), $this->trim($response));
    }

    /**
     * @testCase - route settings action - User access not - redirect to not-authorized route
     *
     * @return void
     * @throws Exception
     */
    public function testSettingsActionRedirectToNotAauthorized(): void
    {
//        self::markTestSkipped('skiped');
        $this->setAuthMock(
            self::CONTROLLER_NAME,
            'settings',
            AuthManager::ACCESS_GRANTED
        );

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['find', 'findBy'])
            ->disableOriginalConstructor()
            ->getMock();

        $user = $this->createUser();
        $this->setEntityId($user, self::USER_ID);

        $repositoryMock->expects($this->exactly(1))
            ->method('find')
            ->with($this->equalTo(self::USER_ID))
            ->willReturn($user);

        $roles = [];
        $role = $this->createRole();
        $roles[] = $role;

        $repositoryMock->expects($this->exactly(1))
            ->method('findBy')
            ->with(
                $this->equalTo([]),
                $this->equalTo(['id' => 'ASC']),
            )
            ->willReturn($roles);

        $this->entityManager->expects($this->exactly(2))
            ->method('getRepository')
            ->withConsecutive(
                [User::class],
                [Role::class],
            )
            ->willReturn($repositoryMock);

        $this->dispatch(self::ROUTE_URL . 'application/settings/' . self::USER_ID, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_302);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_APPLICATION);
        $response = $this->getResponse()->getContent();
        $expected = '';
        self::assertSame($this->trim($expected), $this->trim($response));
    }

    /**
     * @testCase - route settings action - User access not - Exception
     * User access not - RbacManager isGranted - Exception There is no user with such identity
     *
     * @return void
     * @throws Exception
     */
    public function testSettingsActionException(): void
    {
        $this->setAuthMock(
            self::CONTROLLER_NAME,
            'settings',
            AuthManager::ACCESS_GRANTED
        );

        $identity = User::EMAIL_ADMIN;
        $authenticationServiceMock = $this->getMockBuilder(AuthenticationService::class)
            ->onlyMethods(['getIdentity'])
            ->disableOriginalConstructor()
            ->getMock();

        $authenticationServiceMock->expects(self::once())
            ->method('getIdentity')
            ->willReturn($identity);

        $this->serviceManager->setService(AuthenticationService::class, $authenticationServiceMock);


        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['find', 'findBy', 'findOneBy'])
            ->disableOriginalConstructor()
            ->getMock();

        $user = $this->createUser();
        $this->setEntityId($user, self::USER_ID);

        $repositoryMock->expects($this->exactly(1))
            ->method('find')
            ->with($this->equalTo(self::USER_ID))
            ->willReturn($user);

        $roles = [];
        $role = $this->createRole();
        $permission = $this->createPermission();
        $role->setPermissions($permission);
        $roles[] = $role;

        $repositoryMock->expects($this->exactly(1))
            ->method('findBy')
            ->with(
                $this->equalTo([]),
                $this->equalTo(['id' => 'ASC']),
            )
            ->willReturn($roles);

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneBy')
            ->with($this->equalTo(['email' => $identity]))
            ->willReturn(null);

        $this->entityManager->expects($this->exactly(3))
            ->method('getRepository')
            ->withConsecutive(
                [User::class],
                [Role::class],
                [User::class],
            )
            ->willReturn($repositoryMock);

        $this->sleep();
        $this->dispatch(self::ROUTE_URL . 'application/settings/' . self::USER_ID, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_500);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_APPLICATION);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - invalid route - does not crash
     *
     * @return void
     * @throws Exception
     */
    public function testInvalidRouteDoesNotCrash(): void
    {
        $this->dispatch(self::INVALID_ROUTE_URL, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
    }

    /**
     * @testCase - route language action - Exception Incorrect redirect URL
     *
     * @return void
     * @throws Exception
     */
    public function testLanguageActionException(): void
    {
        $_SERVER['HTTP_REFERER'] = 'application/settings';

        $this->dispatch(self::ROUTE_URL . 'application/language/ru_RU', self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_500);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_APPLICATION);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }
}
