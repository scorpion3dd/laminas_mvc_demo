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
use User\Service\UserManager;
use Laminas\Authentication\AuthenticationService;

/**
 * Class IndexControllerTest - Unit tests for IndexController
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package ApplicationTest\unit\Controller
 */
class IndexControllerTest extends AbstractMock
{
    public const MODULE_NAME = 'application';
    public const CONTROLLER_NAME = IndexController::class;
    public const CONTROLLER_CLASS = 'IndexController';
    public const ROUTE_URL = '/';
    public const ROUTE_HOME = 'home';
    public const ROUTE_APPLICATION = 'application';

    /**
     * @testCase - route index action - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testIndexAction(): void
    {
        $userManagerMock = $this->getMockBuilder(UserManager::class)
            ->onlyMethods(['getUsersPaginator'])
            ->disableOriginalConstructor()
            ->getMock();

        $userManagerMock->expects(self::once())
            ->method('getUsersPaginator')
            ->with(0, IndexController::COUNT_PER_PAGE)
            ->willReturn(null);

        $this->serviceManager->setService(UserManager::class, $userManagerMock);

        $this->dispatch(self::ROUTE_URL, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_HOME);
        $response = $this->getResponse()->getContent();
        $expected = file_get_contents(
            __DIR__ . '/../data/Controller/Index/GetIndexActionSuccess.html'
        );
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
        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        $user = $this->createUser();
        $this->setEntityId($user, self::USER_ID);

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
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_APPLICATION);
        $response = $this->getResponse()->getContent();
        $expected = file_get_contents(
            __DIR__ . '/../data/Controller/Index/GetViewActionSuccess.html'
        );
        $dateBirthday = $user->getDateBirthday()->format('Y-m-d');
        $expected = str_replace('|Date Birthday|', $dateBirthday, $expected);
//        self::assertSame($this->trim($expected), $this->trim($response));
    }

    /**
     * @testCase - route about action - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testAboutAction(): void
    {
        $this->dispatch(self::ROUTE_URL . 'application/about', self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_APPLICATION);
        $response = $this->getResponse()->getContent();
        $expected = file_get_contents(
            __DIR__ . '/../data/Controller/Index/GetAboutActionSuccess.html'
        );
//        self::assertSame($this->trim($expected), $this->trim($response));
    }

    /**
     * @testCase - route settings action - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testSettingsAction(): void
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
            ->willReturn($user);

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
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_APPLICATION);
        $response = $this->getResponse()->getContent();
        $expected = file_get_contents(
            __DIR__ . '/../data/Controller/Index/GetSettingsActionSuccess.html'
        );
        $dateBirthday = $user->getDateBirthday()->format('Y-m-d');
        $expected = str_replace('|Date Birthday|', $dateBirthday, $expected);
        $dateCreated = $user->getDateCreated()->format('Y-m-d H:i:s');
        $expected = str_replace('|Date Time Created|', $dateCreated, $expected);
        $dateUpdated = $user->getDateUpdated()->format('Y-m-d H:i:s');
        $expected = str_replace('|Date Time Updated|', $dateUpdated, $expected);
        self::assertStringStartsWith($this->trim($expected), $this->trim($response));
    }

    /**
     * @testCase - route language action - can be redirected to route "/application/settings"
     *
     * @return void
     * @throws Exception
     */
    public function testLanguageAction(): void
    {
        $_SERVER['HTTP_REFERER'] = 'http://zf3.os/application/settings';

        $this->dispatch(self::ROUTE_URL . 'application/language/ru_RU', self::METHOD_GET);
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
     * @testCase - route language action - redirect url empty can be redirected to route "home"
     *
     * @return void
     * @throws Exception
     */
    public function testLanguageActionRedirectUrlEmpty(): void
    {
        $_SERVER['HTTP_REFERER'] = '';

        $this->dispatch(self::ROUTE_URL . 'application/language/ru_RU', self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_302);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_APPLICATION);
        $response = $this->getResponse()->getContent();
        $expected = '';
        self::assertSame($this->trim($expected), $this->trim($response));
    }
}
