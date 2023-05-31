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
 * Class AuthControllerIntegrationNegativeTest - Integration negative tests for AuthController
 * with connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are real
 *
 * @package UserTest\integration\Controller
 */
class AuthControllerIntegrationNegativeTest extends AbstractMock
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
        self::assertTrue($this->assertHTML($response, false));
        $expected = file_get_contents(
            __DIR__ . '/../data/Controller/Auth/LoginActionGetException.html'
        );
//        self::assertStringStartsWith($this->trim($expected), $this->trim($response));
    }

    /**
     * @testCase - route login action by method POST - form is not valid
     *
     * @return void
     * @throws Exception
     */
    public function testLoginActionPostFormIsNotValid(): void
    {
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
        self::assertTrue($this->assertHTML($response, false));
        $expected = file_get_contents(
            __DIR__ . '/../data/Controller/Auth/LoginActionPostFormIsNotValid.html'
        );
//        self::assertStringStartsWith($this->trim($expected), $this->trim($response));
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
            __DIR__ . '/../data/Controller/Auth/LoginActionPostFailure.html'
        );
//        self::assertStringStartsWith($this->trim($expected), $this->trim($response));
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
        $rememberMe = 1;
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
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_LOGIN);
        $response = $this->getResponse()->getContent();
        self::assertTrue($this->assertHTML($response, false));
        $expected = file_get_contents(
            __DIR__ . '/../data/Controller/Auth/LoginActionPostRredirectUrlException.html'
        );
//        self::assertStringStartsWith($this->trim($expected), $this->trim($response));
    }
}
