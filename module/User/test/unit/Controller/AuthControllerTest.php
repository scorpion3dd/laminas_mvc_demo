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
use Exception;
use User\Controller\AuthController;
use User\Entity\User;
use User\Form\LoginForm;
use User\Service\AuthManager;
use User\Service\UserManager;
use Laminas\Authentication\Result;

/**
 * Class AuthControllerTest - Unit tests for AuthController
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package UserTest\unit\Controller
 */
class AuthControllerTest extends AbstractMock
{
    public const MODULE_NAME = 'user';
    public const CONTROLLER_NAME = AuthController::class;
    public const CONTROLLER_CLASS = 'AuthController';
    public const ROUTE_URL_LOGIN = '/login';
    public const ROUTE_LOGIN = 'login';
    public const ROUTE_URL_LOGOUT = '/logout';
    public const ROUTE_LOGOUT = 'logout';
    public const ROUTE_URL_NOT_AUTHORIZED = '/not-authorized';
    public const ROUTE_NOT_AUTHORIZED = 'not-authorized';

    /**
     * @testCase - route login action by method GET - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testLoginActionGet(): void
    {
        $userManagerMock = $this->getMockBuilder(UserManager::class)
            ->onlyMethods(['createAdminUserIfNotExists'])
            ->disableOriginalConstructor()
            ->getMock();

        $userManagerMock->expects(self::once())
            ->method('createAdminUserIfNotExists');

        $this->serviceManager->setService(UserManager::class, $userManagerMock);

        $this->dispatch(self::ROUTE_URL_LOGIN, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_LOGIN);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route login action by method POST - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testLoginActionPost(): void
    {
        $userManagerMock = $this->getMockBuilder(UserManager::class)
            ->onlyMethods(['createAdminUserIfNotExists'])
            ->disableOriginalConstructor()
            ->getMock();

        $userManagerMock->expects(self::once())
            ->method('createAdminUserIfNotExists');

        $this->serviceManager->setService(UserManager::class, $userManagerMock);


        $userManagerMock = $this->getMockBuilder(AuthManager::class)
            ->onlyMethods(['login'])
            ->disableOriginalConstructor()
            ->getMock();

        $identity = User::EMAIL_ADMIN;
        $result = new Result(Result::SUCCESS, $identity);
        $rememberMe = 1;
        $userManagerMock->expects(self::once())
            ->method('login')
            ->with(
                $this->equalTo(self::USER_EMAIL),
                $this->equalTo(User::PASSWORD_ADMIN),
                $this->equalTo($rememberMe),
            )
            ->willReturn($result);

        $this->serviceManager->setService(AuthManager::class, $userManagerMock);

        $redirectUrl = 'settings';
        $form = new  LoginForm();
        $params = [
            'redirect_url' => $redirectUrl,
            'email' => self::USER_EMAIL,
            'password' => User::PASSWORD_ADMIN,
            'remember_me' => $rememberMe,
            'csrf' => $form->get('csrf')->getValue(),
            'submit' => 'Create',
        ];

        $this->dispatch(self::ROUTE_URL_LOGIN . '?redirectUrl=' . $redirectUrl, self::METHOD_POST, $params);
        $this->assertResponseStatusCode(self::STATUS_CODE_302);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_LOGIN);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route login action by method POST - must be a success
     * empty redirect url
     *
     * @return void
     * @throws Exception
     */
    public function testLoginActionPostEmptyRredirectUrl(): void
    {
        $userManagerMock = $this->getMockBuilder(UserManager::class)
            ->onlyMethods(['createAdminUserIfNotExists'])
            ->disableOriginalConstructor()
            ->getMock();

        $userManagerMock->expects(self::once())
            ->method('createAdminUserIfNotExists');

        $this->serviceManager->setService(UserManager::class, $userManagerMock);


        $userManagerMock = $this->getMockBuilder(AuthManager::class)
            ->onlyMethods(['login'])
            ->disableOriginalConstructor()
            ->getMock();

        $identity = User::EMAIL_ADMIN;
        $result = new Result(Result::SUCCESS, $identity);
        $rememberMe = 1;
        $userManagerMock->expects(self::once())
            ->method('login')
            ->with(
                $this->equalTo(self::USER_EMAIL),
                $this->equalTo(User::PASSWORD_ADMIN),
                $this->equalTo($rememberMe),
            )
            ->willReturn($result);

        $this->serviceManager->setService(AuthManager::class, $userManagerMock);

        $form = new  LoginForm();
        $params = [
            'email' => self::USER_EMAIL,
            'password' => User::PASSWORD_ADMIN,
            'remember_me' => $rememberMe,
            'csrf' => $form->get('csrf')->getValue(),
            'submit' => 'Create',
        ];

        $this->dispatch(self::ROUTE_URL_LOGIN, self::METHOD_POST, $params);
        $this->assertResponseStatusCode(self::STATUS_CODE_302);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_LOGIN);
        $response = $this->getResponse()->getContent();
        self::assertEquals('', $this->trim($response));
    }

    /**
     * @testCase - route logout action by method GET - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testLogoutActionGet(): void
    {
        $userManagerMock = $this->getMockBuilder(AuthManager::class)
            ->onlyMethods(['logout'])
            ->disableOriginalConstructor()
            ->getMock();

        $userManagerMock->expects(self::once())
            ->method('logout');

        $this->serviceManager->setService(AuthManager::class, $userManagerMock);

        $this->dispatch(self::ROUTE_URL_LOGOUT, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_302);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_LOGOUT);
        $response = $this->getResponse()->getContent();
        self::assertEquals('', $this->trim($response));
    }

    /**
     * @testCase - route notAuthorized action by method GET - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testNotAuthorizedActionGet(): void
    {
        $this->dispatch(self::ROUTE_URL_NOT_AUTHORIZED, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_NOT_AUTHORIZED);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }
}
