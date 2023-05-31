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
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\Controller\AuthController;
use User\Entity\User;
use User\Form\LoginForm;

/**
 * Class AuthControllerIntegrationTest - Integration tests for AuthController
 * with connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are real
 *
 * @package UserTest\integration\Controller
 */
class AuthControllerIntegrationTest extends AbstractMock
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
     * @testCase - route login action by method GET - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testLoginActionGet(): void
    {
        $this->dispatch(self::ROUTE_URL_LOGIN, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_LOGIN);
        $response = $this->getResponse()->getContent();
        self::assertTrue($this->assertHTML($response, false));
        $expected = file_get_contents(
            __DIR__ . '/../data/Controller/Auth/IndexActionGet.html'
        );
//        self::assertStringStartsWith($this->trim($expected), $this->trim($response));
    }

    /**
     * @testCase - route login action by method POST - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testLoginActionPost(): void
    {
        $this->setPasswordToUser();
        $rememberMe = 1;
        $redirectUrl = 'settings';
        $form = new  LoginForm();
        $params = [
            'redirect_url' => $redirectUrl,
            'email' => User::EMAIL_ADMIN,
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
        self::assertTrue($this->assertHTML($response, false));
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
        $rememberMe = 1;
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
        self::assertTrue($this->assertHTML($response, false));
        $expected = file_get_contents(
            __DIR__ . '/../data/Controller/Auth/LoginActionPostEmptyRredirectUrl.html'
        );
//        self::assertStringStartsWith($this->trim($expected), $this->trim($response));
    }

    /**
     * @testCase - route logout action by method GET - must be a success
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testLogoutActionGet(): void
    {
        $this->setAuth();
        $this->dispatch(self::ROUTE_URL_LOGOUT, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_302);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_LOGOUT);
        $response = $this->getResponse()->getContent();
        $expected = '';
        self::assertEquals($expected, $this->trim($response));
    }

    /**
     * @testCase - route notAuthorized action by method GET - must be a success
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testNotAuthorizedActionGet(): void
    {
        $this->setAuth();
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
