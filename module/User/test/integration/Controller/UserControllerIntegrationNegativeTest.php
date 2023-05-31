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
use Carbon\Carbon;
use Doctrine\ORM\ORMException;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\Controller\UserController;
use User\Entity\User;
use User\Form\PasswordChangeForm;
use User\Form\UserForm;
use User\Service\UserManager;

/**
 * Class UserControllerIntegrationNegativeTest - Integration negative tests for UserController
 * with connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are real
 *
 * @package UserTest\integration\Controller
 */
class UserControllerIntegrationNegativeTest extends AbstractMock
{
    public const MODULE_NAME = 'user';
    public const CONTROLLER_NAME = UserController::class;
    public const CONTROLLER_CLASS = 'UserController';
    public const ROUTE_URL = '/users';
    public const ROUTE_USERS = 'users';

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
     * @testCase - route view action - not id
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testViewActionNotId(): void
    {
        $this->setAuth();
        $this->dispatch(self::ROUTE_URL . '/view/', self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertTrue($this->assertHTML($response, false));
        $expected = file_get_contents(
            __DIR__ . '/../data/Controller/User/ViewActionNotId.html'
        );
        self::assertSame($this->trim($expected), $this->trim($response));
    }

    /**
     * @testCase - route view action - empty user
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testViewActionEmptyUser(): void
    {
        $this->setAuth();
        $this->dispatch(self::ROUTE_URL . '/view/1000', self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertTrue($this->assertHTML($response, false));
        $expected = file_get_contents(
            __DIR__ . '/../data/Controller/User/ViewActionNotId.html'
        );
        self::assertSame($this->trim($expected), $this->trim($response));
    }

    /**
     * @testCase - route add action by method POST - form is not valid
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testAddActionPostFormIsNotValid(): void
    {
        $this->setAuth();
        $form = new  UserForm('create', $this->entityManager);
        $params = [
            'email' => self::USER_EMAIL,
            'full_name' => self::USER_FULL_NAME,
            'description' => self::USER_DESCRIPTION,
            'date_birthday' => Carbon::now(),
            'password' => User::PASSWORD_ADMIN,
            'confirm_password' => User::PASSWORD_ADMIN,
            'status' => User::STATUS_ACTIVE_ID,
            'access' => User::ACCESS_YES_ID,
            'gender' => User::GENDER_MALE_ID,
            'roles' => 0,
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
        self::assertTrue($this->assertHTML($response, false));
        $expected = file_get_contents(
            __DIR__ . '/../data/Controller/User/AddActionPostFormIsNotValid.html'
        );
        self::assertStringStartsWith($this->trim($expected), $this->trim($response));
    }

    /**
     * @testCase - route edit action by method GET - not id
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testEditActionGetNotId(): void
    {
        $this->setAuth();
        $this->dispatch(self::ROUTE_URL . '/edit', self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertTrue($this->assertHTML($response, false));
        $expected = file_get_contents(
            __DIR__ . '/../data/Controller/User/EditActionGetNotId.html'
        );
        self::assertStringStartsWith($this->trim($expected), $this->trim($response));
    }

    /**
     * @testCase - route edit action by method GET - empty user
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testEditActionGetEmptyUser(): void
    {
        $this->setAuth();
        $this->dispatch(self::ROUTE_URL . '/edit/1000', self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertTrue($this->assertHTML($response, false));
        $expected = file_get_contents(
            __DIR__ . '/../data/Controller/User/EditActionGetNotId.html'
        );
        self::assertStringStartsWith($this->trim($expected), $this->trim($response));
    }

    /**
     * @testCase - route edit action by method POST - form is not valid
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testEditActionPostFormIsNotValid(): void
    {
        $this->setAuth();
        $params = [
            'date_birthday' => self::USER_DATE_BIRTHDAY,
            'status' => User::STATUS_DISACTIVE_ID,
            'access' => User::ACCESS_NO_ID,
            'gender' => User::GENDER_MALE_ID,
            'submit' => 'Create',
        ];
        $this->dispatch(self::ROUTE_URL . '/edit/' . self::USER_ID, self::METHOD_POST, $params);
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route changePassword action by method GET - not id
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testChangePasswordActionGetNotId(): void
    {
        $this->setAuth();
        $this->dispatch(self::ROUTE_URL . '/change-password', self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertTrue($this->assertHTML($response, false));
        $expected = file_get_contents(
            __DIR__ . '/../data/Controller/User/EditActionGetNotId.html'
        );
        self::assertStringStartsWith($this->trim($expected), $this->trim($response));
    }

    /**
     * @testCase - route changePassword action by method GET - empty user
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testChangePasswordActionGetEmptyUser(): void
    {
        $this->setAuth();
        $this->dispatch(self::ROUTE_URL . '/change-password/1000', self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertTrue($this->assertHTML($response, false));
        $expected = file_get_contents(
            __DIR__ . '/../data/Controller/User/EditActionGetNotId.html'
        );
        self::assertStringStartsWith($this->trim($expected), $this->trim($response));
    }

    /**
     * @testCase - route changePassword action by method POST - error message
     * "Sorry, the old password is incorrect. Could not set the new password."
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testChangePasswordActionPostErrorMessage(): void
    {
        $this->setAuth();
        $form = new  PasswordChangeForm('change');
        $params = [
            'old_password' => User::PASSWORD_ADMIN . 'old',
            'new_password' => User::PASSWORD_ADMIN . '123',
            'confirm_new_password' => User::PASSWORD_ADMIN,
            'csrf' => $form->get('csrf')->getValue(),
            'submit' => 'Change Password',
        ];
        $this->dispatch(
            self::ROUTE_URL . '/change-password/' . self::USER_ID,
            self::METHOD_POST,
            $params
        );
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertTrue($this->assertHTML($response, false));
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route changePassword action by method POST - form is not valid
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testChangePasswordActionPostFormIsNotValid(): void
    {
        $this->setAuth();
        $form = new  PasswordChangeForm('change');
        $params = [
            'new_password' => User::PASSWORD_ADMIN . '123',
            'confirm_new_password' => User::PASSWORD_ADMIN,
            'csrf' => $form->get('csrf')->getValue(),
            'submit' => 'Change Password',
        ];
        $this->dispatch(
            self::ROUTE_URL . '/change-password/' . self::USER_ID,
            self::METHOD_POST,
            $params
        );
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertTrue($this->assertHTML($response, false));
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route message action by method GET - Exception
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testMessageGetException(): void
    {
        $this->setAuth();
        $this->dispatch(self::ROUTE_URL . '/message/sented', self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_500);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertTrue($this->assertHTML($response, false));
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route setPassword action by method POST - invalid token
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testSetPassworddActionPostInvalidToken(): void
    {
        $this->setAuth();
        $token = UserManager::getRandomToken(512);

        $this->dispatch(
            self::ROUTE_URL . '/set-password?token=' . $token . "&email=" . self::USER_EMAIL,
            self::METHOD_POST
        );
        $this->assertResponseStatusCode(self::STATUS_CODE_500);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertTrue($this->assertHTML($response, false));
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route setPassword action by method POST - empty token
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testSetPassworddActionPostEmptyToken(): void
    {
        $this->setAuth();
        $token = '';

        $this->dispatch(
            self::ROUTE_URL . '/set-password?token=' . $token . "&email=" . self::USER_EMAIL,
            self::METHOD_POST
        );
        $this->assertResponseStatusCode(self::STATUS_CODE_302);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertEquals('', $this->trim($response));
    }

    /**
     * @testCase - route setPassword action by method POST - form is not valid
     * setNewPasswordByToken return true
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ORMException
     */
    public function testSetPassworddActionPostFormIsNotValid(): void
    {
        $this->setAuth();
        $this->setPasswordResetTokenToUser();
        $form = new PasswordChangeForm('reset');
        $params = [
            'old_password' => '123',
            'new_password' => User::PASSWORD_ADMIN . 'new',
            'confirm_new_password' => User::PASSWORD_ADMIN . 'new123',
            'csrf' => $form->get('csrf')->getValue(),
            'submit' => 'Change Password',
        ];
        $this->dispatch(
            self::ROUTE_URL . '/set-password?token=' . self::USER_PASSWORD_RESET_TOKEN . "&email=" . User::EMAIL_ADMIN,
            self::METHOD_POST,
            $params
        );
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route setPassword action by method GET - Exception
     * Invalid token type or length
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function testSetPasswordActionGetException(): void
    {
        $this->setAuth();

        $user = $this->createUser();
        $this->setEntityId($user, self::USER_ID);
        $token = UserManager::getRandomToken();
        $token = $token . $token . $token . $token . $token . $token . $token;
        $form = new PasswordChangeForm('reset');
        $csrf = $form->get('csrf')->getValue();
        $params = [
            'email' => self::USER_EMAIL,
            'token' => $token,
            'csrf' => $csrf,
        ];

        $this->dispatch(self::ROUTE_URL . '/set-password', self::METHOD_GET, $params);
        $this->assertResponseStatusCode(self::STATUS_CODE_500);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - invalid route does not crash
     *
     * @return void
     * @throws Exception
     */
    public function testInvalidRouteDoesNotCrash(): void
    {
        $this->dispatch(self::INVALID_ROUTE_URL, self::METHOD_GET);
        $this->assertResponseStatusCode(404);
    }
}
