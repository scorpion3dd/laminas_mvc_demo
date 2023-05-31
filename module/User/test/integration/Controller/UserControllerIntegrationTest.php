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
use Doctrine\ORM\ORMException;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\Controller\UserController;
use User\Entity\Role;
use User\Entity\User;
use User\Form\PasswordChangeForm;
use User\Form\PasswordResetForm;
use User\Form\UserForm;

/**
 * Class UserControllerIntegrationTest - Integration tests for UserController
 * with connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are real
 *
 * @package UserTest\integration\Controller
 */
class UserControllerIntegrationTest extends AbstractMock
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
     * @testCase - route index action - must be a success
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testIndexAction(): void
    {
        $this->setAuth();
        $this->dispatch(self::ROUTE_URL, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertTrue($this->assertHTML($response, false));
        $expected = file_get_contents(
            __DIR__ . '/../data/Controller/User/IndexActionGet.html'
        );
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route view action - must be a success
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testViewAction(): void
    {
        $this->setAuth();
        $this->dispatch(self::ROUTE_URL . '/view/' . self::USER_ID, self::METHOD_GET);
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
     * @testCase - route add action by method GET - must be a success
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testAddActionGet(): void
    {
        $this->setAuth();
        $this->dispatch(self::ROUTE_URL . '/add', self::METHOD_GET);
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
     * @testCase - route edit action by method GET - must be a success
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testEditActionGet(): void
    {
        $this->setAuth();
        $this->dispatch(self::ROUTE_URL . '/edit/' . self::USER_ID, self::METHOD_GET);
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
     * @testCase - routes CRUD actions - must be a success
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ORMException
     * @throws Exception
     */
    public function testCRUDActions(): void
    {
        $this->prepareDbMySqlIntegration();
        /** @var Role|null $role */
        $role = $this->entityManagerIntegration->getRepository(Role::class)->findOneBy(['name' => 'Guest']);
        /** @var array|null $users */
        $users = $this->entityManagerIntegration->getRepository(User::class)
            ->findBy([], ['id' => 'DESC'], 1);
        if (! empty($role) && ! empty($users)) {
            /** @var User|null $user */
            $user = $users[0];
            $userId = $user->getId();
            $userId++;
            $this->addActionPost($role, $userId);
            $this->reset();
            $this->editActionPost($user, $role, $userId);
        }
    }

    /**
     * @testCase - route add action by method POST - must be a success
     *
     * @param Role $role
     * @param int $userId
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function addActionPost(Role $role, int $userId): void
    {
        $this->setAuth();
        $form = new UserForm('create', $this->entityManagerIntegration);
        $params = [
            'email' => 'user' . $userId . '@example.com',
            'full_name' => self::USER_FULL_NAME,
            'description' => self::USER_DESCRIPTION,
            'date_birthday' => self::USER_DATE_BIRTHDAY,
            'password' => User::PASSWORD_ADMIN,
            'confirm_password' => User::PASSWORD_ADMIN,
            'status' => User::STATUS_ACTIVE_ID,
            'access' => User::ACCESS_YES_ID,
            'gender' => User::GENDER_MALE_ID,
            'roles' => [$role->getId()],
            'csrf' => $form->get('csrf')->getValue(),
            'submit' => 'Create',
        ];
        $this->dispatch(self::ROUTE_URL . '/add', self::METHOD_POST, $params);
        $this->assertResponseStatusCode(self::STATUS_CODE_302);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $expected = '';
        $response = $this->getResponse()->getContent();
        self::assertSame($this->trim($expected), $this->trim($response));
    }

    /**
     * @testCase - route edit action by method POST - must be a success
     *
     * @param User $user
     * @param Role $role
     * @param int $userId
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function editActionPost(User $user, Role $role, int $userId): void
    {
        $this->setAuth();
        $form = new UserForm('update', $this->entityManagerIntegration, $user);
        $params = [
            'email' => 'user' . $userId . '@example.com',
            'full_name' => self::USER_FULL_NAME . ' edit',
            'description' => self::USER_DESCRIPTION . ' edit',
            'date_birthday' => self::USER_DATE_BIRTHDAY,
            'status' => User::STATUS_DISACTIVE_ID,
            'access' => User::ACCESS_NO_ID,
            'gender' => User::GENDER_MALE_ID,
            'roles' => [$role->getId()],
            'csrf' => $form->get('csrf')->getValue(),
            'submit' => 'Create',
        ];
        $this->dispatch(self::ROUTE_URL . '/edit/' . $userId, self::METHOD_POST, $params);
        $this->assertResponseStatusCode(self::STATUS_CODE_302);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $expected = '';
        $response = $this->getResponse()->getContent();
        self::assertSame($this->trim($expected), $this->trim($response));
    }

    /**
     * @testCase - route changePassword action by method GET - must be a success
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testChangePasswordActionGet(): void
    {
        $this->setAuth();
        $this->dispatch(self::ROUTE_URL . '/change-password/' . self::USER_ID, self::METHOD_GET);
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
     * @testCase - route changePassword action by method POST - must be a success
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testChangePasswordActionPost(): void
    {
        $this->setAuth();
        $form = new  PasswordChangeForm('change');
        $params = [
            'old_password' => User::PASSWORD_ADMIN . 'old',
            'new_password' => User::PASSWORD_ADMIN,
            'confirm_new_password' => User::PASSWORD_ADMIN,
            'csrf' => $form->get('csrf')->getValue(),
            'submit' => 'Change Password',
        ];
        $this->dispatch(
            self::ROUTE_URL . '/change-password/' . self::USER_ID,
            self::METHOD_POST,
            $params
        );
        $this->assertResponseStatusCode(self::STATUS_CODE_302);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        $expected = '';
        self::assertEquals($expected, $this->trim($response));
    }

    /**
     * @testCase - route resetPassword action by method GET - must be a success
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testResetPasswordActionGet(): void
    {
        $this->setAuth();
        $this->dispatch(self::ROUTE_URL . '/reset-password/' . self::USER_ID, self::METHOD_GET);
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
     * @testCase - route resetPassword action by method POST - must be a success
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testResetPasswordActionPost(): void
    {
        $this->setAuth();
        $this->setEnvTest();
        $form = new PasswordResetForm();
        $params = [
            'email' => User::EMAIL_ADMIN,
            'csrf' => $form->get('csrf')->getValue(),
            'submit' => 'Reset Password',
        ];
        $this->dispatch(self::ROUTE_URL . '/reset-password', self::METHOD_POST, $params);
        $this->assertResponseStatusCode(self::STATUS_CODE_302);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        $expected = '';
        self::assertEquals($expected, $this->trim($response));
    }

    /**
     * @testCase - route message action by method GET - must be a success
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testMessageGet(): void
    {
        $this->setAuth();
        $this->dispatch(self::ROUTE_URL . '/message/sent', self::METHOD_GET);
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
     * @testCase - route setPassword action by method GET - must be a success
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ORMException
     */
    public function testSetPasswordActionGet(): void
    {
        $this->setAuth();
        $this->setPasswordResetTokenToUser();
        $form = new PasswordChangeForm('reset');
        $csrf = $form->get('csrf')->getValue();
        $params = [
            'email' => User::EMAIL_ADMIN,
            'token' => self::USER_PASSWORD_RESET_TOKEN,
            'csrf' => $csrf,
        ];
        $this->dispatch(self::ROUTE_URL . '/set-password', self::METHOD_GET, $params);
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
     * @testCase - route setPassword action by method POST - must be a success
     * setNewPasswordByToken return true
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ORMException
     */
    public function testSetPassworddActionPost(): void
    {
        $this->setAuth();
        $this->setPasswordResetTokenToUser();
        $form = new PasswordChangeForm('reset');
        $params = [
            'old_password' => User::PASSWORD_ADMIN ,
            'new_password' => User::PASSWORD_ADMIN . 'new',
            'confirm_new_password' => User::PASSWORD_ADMIN . 'new',
            'csrf' => $form->get('csrf')->getValue(),
            'submit' => 'Change Password',
        ];
        $this->dispatch(
            self::ROUTE_URL . '/set-password?token=' . self::USER_PASSWORD_RESET_TOKEN . "&email=" . User::EMAIL_ADMIN,
            self::METHOD_POST,
            $params
        );
        $this->assertResponseStatusCode(self::STATUS_CODE_302);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertEquals('', $this->trim($response));
    }
}
