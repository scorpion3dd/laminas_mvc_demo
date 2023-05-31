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
 * Class AuthControllerNegativeTest - Unit negative tests for AuthController
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package UserTest\unit\Controller
 */
class AuthControllerNegativeTest extends AbstractMock
{
    public const MODULE_NAME = 'user';
    public const CONTROLLER_NAME = AuthController::class;
    public const CONTROLLER_CLASS = 'AuthController';
    public const ROUTE_URL_LOGIN = '/login';
    public const ROUTE_LOGIN = 'login';

    /**
     * @testCase - route login action by method GET - Exception
     * Too long redirectUrl argument passed
     *
     * @return void
     * @throws Exception
     */
    public function testLoginActionGetException(): void
    {
        $redirectUrl = $this->getLongRedirectUrl();
        $this->dispatch(self::ROUTE_URL_LOGIN . '?redirectUrl=' . $redirectUrl, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_500);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_LOGIN);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route login action by method POST - form is not valid
     *
     * @return void
     * @throws Exception
     */
    public function testLoginActionPostFormIsNotValid(): void
    {
        $userManagerMock = $this->getMockBuilder(UserManager::class)
            ->onlyMethods(['createAdminUserIfNotExists'])
            ->disableOriginalConstructor()
            ->getMock();

        $userManagerMock->expects(self::once())
            ->method('createAdminUserIfNotExists');

        $this->serviceManager->setService(UserManager::class, $userManagerMock);

        $form = new  LoginForm();
        $params = [
            'email' => self::USER_EMAIL,
            'password' => User::PASSWORD_ADMIN,
            'remember_me' => self::USER_FULL_NAME,
            'csrf' => $form->get('csrf')->getValue(),
            'submit' => 'Create',
        ];

        $this->dispatch(self::ROUTE_URL_LOGIN, self::METHOD_POST, $params);
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_LOGIN);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route login action by method POST - FAILURE
     * authManager->login - result->getCode() == Result::FAILURE
     *
     * @return void
     * @throws Exception
     */
    public function testLoginActionPostFailure(): void
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
        $result = new Result(Result::FAILURE, $identity);
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
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_LOGIN);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route login action by method POST - Exception
     * redirect url Exception
     *
     * @return void
     * @throws Exception
     */
    public function testLoginActionPostRredirectUrlException(): void
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

        $redirectUrl = 'http://settings';
        $form = new  LoginForm();
        $params = [
            'redirect_url' => $redirectUrl,
            'email' => self::USER_EMAIL,
            'password' => User::PASSWORD_ADMIN,
            'remember_me' => $rememberMe,
            'csrf' => $form->get('csrf')->getValue(),
            'submit' => 'Create',
        ];

        $this->dispatch(self::ROUTE_URL_LOGIN, self::METHOD_POST, $params);
        $this->assertResponseStatusCode(self::STATUS_CODE_500);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_LOGIN);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }
}
