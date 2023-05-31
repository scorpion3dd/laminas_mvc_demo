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
use Carbon\Carbon;
use Doctrine\ORM\EntityRepository;
use Exception;
use Laminas\Authentication\AuthenticationService;
use User\Controller\UserController;
use User\Entity\Role;
use User\Entity\User;
use User\Form\PasswordChangeForm;
use User\Form\PasswordResetForm;
use User\Form\UserForm;
use User\Service\AuthManager;
use User\Service\UserManager;

/**
 * Class UserControllerNegativeTest - Unit negative tests for UserController
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package UserTest\unit\Controller
 */
class UserControllerNegativeTest extends AbstractMock
{
    public const MODULE_NAME = 'user';
    public const CONTROLLER_NAME = UserController::class;
    public const CONTROLLER_CLASS = 'UserController';
    public const ROUTE_URL = '/users';
    public const ROUTE_USERS = 'users';

    /**
     * @testCase - route index action - not access
     *
     * @return void
     * @throws Exception
     */
    public function testIndexActionNotAccess(): void
    {
        $this->setAuthMock(
            self::CONTROLLER_NAME,
            'index',
            AuthManager::ACCESS_GRANTED
        );

        $identity = false;
        $authenticationServiceMock = $this->getMockBuilder(AuthenticationService::class)
            ->onlyMethods(['getIdentity'])
            ->disableOriginalConstructor()
            ->getMock();

        $authenticationServiceMock->expects(self::once())
            ->method('getIdentity')
            ->willReturn($identity);

        $this->serviceManager->setService(AuthenticationService::class, $authenticationServiceMock);


        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['findBy'])
            ->disableOriginalConstructor()
            ->getMock();

        $user = $this->createUser();
        $this->setEntityId($user, self::USER_ID);

        $roles = [];
        $role = $this->createRole();
        $permission = $this->createPermission(self::USER_PERMISSION_USER_MANAGE);
        $role->setPermissions($permission);
        $roles[] = $role;

        $repositoryMock->expects($this->exactly(1))
            ->method('findBy')
            ->with(
                $this->equalTo([]),
                $this->equalTo(['id' => 'ASC']),
            )
            ->willReturn($roles);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [Role::class],
            )
            ->willReturn($repositoryMock);

        $this->sleep();
        $this->dispatch(self::ROUTE_URL, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_401);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

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
     * @testCase - route view action - empty user
     *
     * @return void
     * @throws Exception
     */
    public function testViewActionEmptyUser(): void
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

        $this->dispatch(self::ROUTE_URL . '/view/' . self::USER_ID, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
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

        $this->dispatch(self::ROUTE_URL . '/edit', self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route edit action by method GET - empty user
     *
     * @return void
     * @throws Exception
     */
    public function testEditActionGetEmptyUser(): void
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

        $user = null;

        $repositoryMock->expects($this->exactly(1))
            ->method('find')
            ->with(
                $this->equalTo(self::USER_ID),
            )
            ->willReturn($user);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [User::class],
            )
            ->willReturn($repositoryMock);

        $this->dispatch(self::ROUTE_URL . '/edit/' . self::USER_ID, self::METHOD_GET);
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
            ->onlyMethods(['find', 'findBy'])
            ->disableOriginalConstructor()
            ->getMock();

        $user = $this->createUser();
        $this->setEntityId($user, self::USER_ID);

        $repositoryMock->expects($this->exactly(1))
            ->method('find')
            ->with(
                $this->equalTo(self::USER_ID),
            )
            ->willReturn($user);

        $roles = [];
        $role = $this->createRole();
        $this->setEntityId($role, self::ROLE_ID);
        $roles[] = $role;

        $repositoryMock->expects($this->exactly(1))
            ->method('findBy')
            ->with(
                $this->equalTo([]),
                $this->equalTo(['name' => 'ASC']),
            )
            ->willReturn($roles);

        $this->entityManager->expects($this->exactly(2))
            ->method('getRepository')
            ->withConsecutive(
                [User::class],
                [Role::class],
            )
            ->willReturn($repositoryMock);

        $form = new  UserForm('update', $this->entityManager, $user);
        $params = [
            'status' => User::STATUS_ACTIVE_ID,
            'access' => User::ACCESS_YES_ID,
            'gender' => User::GENDER_MALE_ID,
            'roles' => [$role->getId()],
            'csrf' => $form->get('csrf')->getValue(),
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
     * @throws Exception
     */
    public function testChangePasswordActionGetNotId(): void
    {
        $this->setAuthMock(
            self::CONTROLLER_NAME,
            'changePassword',
            AuthManager::ACCESS_GRANTED
        );

        $this->dispatch(self::ROUTE_URL . '/change-password', self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route changePassword action by method GET - empty user
     *
     * @return void
     * @throws Exception
     */
    public function testChangePasswordActionGetEmptyUser(): void
    {
        $this->setAuthMock(
            self::CONTROLLER_NAME,
            'changePassword',
            AuthManager::ACCESS_GRANTED
        );

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        $user = null;

        $repositoryMock->expects($this->exactly(1))
            ->method('find')
            ->with(
                $this->equalTo(self::USER_ID),
            )
            ->willReturn($user);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [User::class],
            )
            ->willReturn($repositoryMock);

        $this->dispatch(self::ROUTE_URL . '/change-password/' . self::USER_ID, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route changePassword action by method POST - error message
     * "Sorry, the old password is incorrect. Could not set the new password."
     *
     * @return void
     * @throws Exception
     */
    public function testChangePasswordActionPostErrorMessage(): void
    {
        $this->setAuthMock(
            self::CONTROLLER_NAME,
            'changePassword',
            AuthManager::ACCESS_GRANTED
        );

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        $user = $this->createUser();
        $this->setEntityId($user, self::USER_ID);

        $repositoryMock->expects($this->exactly(1))
            ->method('find')
            ->with(
                $this->equalTo(self::USER_ID),
            )
            ->willReturn($user);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [User::class],
            )
            ->willReturn($repositoryMock);

        $form = new  PasswordChangeForm('change');
        $params = [
            'old_password' => User::PASSWORD_ADMIN . 'old',
            'new_password' => User::PASSWORD_ADMIN,
            'confirm_new_password' => User::PASSWORD_ADMIN,
            'csrf' => $form->get('csrf')->getValue(),
            'submit' => 'Change Password',
        ];
        $userManagerMock = $this->getMockBuilder(UserManager::class)
            ->onlyMethods(['changePassword'])
            ->disableOriginalConstructor()
            ->getMock();

        $userManagerMock->expects(self::once())
            ->method('changePassword')
            ->with($this->equalTo($user), $this->equalTo($params))
            ->willReturn(false);

        $this->serviceManager->setService(UserManager::class, $userManagerMock);


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
        self::assertEquals('', $this->trim($response));
    }

    /**
     * @testCase - route changePassword action by method POST - form is not valid
     *
     * @return void
     * @throws Exception
     */
    public function testChangePasswordActionPostFormIsNotValid(): void
    {
        $this->setAuthMock(
            self::CONTROLLER_NAME,
            'changePassword',
            AuthManager::ACCESS_GRANTED
        );

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        $user = $this->createUser();
        $this->setEntityId($user, self::USER_ID);

        $repositoryMock->expects($this->exactly(1))
            ->method('find')
            ->with(
                $this->equalTo(self::USER_ID),
            )
            ->willReturn($user);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [User::class],
            )
            ->willReturn($repositoryMock);

        $form = new  PasswordChangeForm('change');
        $params = [
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
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route resetPassword action by method POST - form is not valid
     *
     * @return void
     * @throws Exception
     */
    public function testResetPasswordActionPostFormIsNotValid(): void
    {
//        self::markTestSkipped('skiped bitbucket-pipelines failures');
        $this->setAuthMock(
            self::CONTROLLER_NAME,
            'resetPassword',
            AuthManager::ACCESS_GRANTED
        );

        $user = $this->createUser();
        $this->setEntityId($user, self::USER_ID);

        $form = new  PasswordResetForm();
        $params = [
            'email' => self::USER_EMAIL,
            'captcha' => '12346566',
            'csrf' => $form->get('csrf')->getValue(),
            'submit' => 'Reset Password',
        ];

        $this->dispatch(self::ROUTE_URL . '/reset-password', self::METHOD_POST, $params);
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route resetPassword action by method POST - redirect to invalid email
     *
     * @return void
     * @throws Exception
     */
    public function testResetPasswordActionPostRedirectToInvalidEmail(): void
    {
//        self::markTestSkipped('skiped bitbucket-pipelines failures');
        $this->setAuthMock(
            self::CONTROLLER_NAME,
            'resetPassword',
            AuthManager::ACCESS_GRANTED
        );

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['findOneBy'])
            ->disableOriginalConstructor()
            ->getMock();

        $user = $this->createUser();
        $user->setStatus(User::STATUS_DISACTIVE_ID);
        $this->setEntityId($user, self::USER_ID);

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneBy')
            ->with($this->equalTo(['email' => self::USER_EMAIL]))
            ->willReturn($user);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [User::class],
            )
            ->willReturn($repositoryMock);

        $this->setEnvTest();
        $form = new PasswordResetForm();
        $params = [
            'email' => self::USER_EMAIL,
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
        self::assertEquals('', $this->trim($response));
    }

    /**
     * @testCase - route message action by method GET - Exception
     *
     * @return void
     * @throws Exception
     */
    public function testMessageGetException(): void
    {
        $this->setAuthMock(
            self::CONTROLLER_NAME,
            'message',
            AuthManager::ACCESS_GRANTED
        );

        $this->dispatch(self::ROUTE_URL . '/message/sented', self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_500);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route setPassword action by method POST - invalid token
     *
     * @return void
     * @throws Exception
     */
    public function testSetPassworddActionPostInvalidToken(): void
    {
        $this->setAuthMock(
            self::CONTROLLER_NAME,
            'setPassword',
            AuthManager::ACCESS_GRANTED
        );

        $token = UserManager::getRandomToken(12);

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
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route setPassword action by method POST - empty token
     *
     * @return void
     * @throws Exception
     */
    public function testSetPassworddActionPostEmptyToken(): void
    {
        $this->setAuthMock(
            self::CONTROLLER_NAME,
            'setPassword',
            AuthManager::ACCESS_GRANTED
        );

        $this->dispatch(self::ROUTE_URL . '/set-password', self::METHOD_POST);
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
     * @throws Exception
     */
    public function testSetPassworddActionPostFormIsNotValid(): void
    {
        $this->setAuthMock(
            self::CONTROLLER_NAME,
            'setPassword',
            AuthManager::ACCESS_GRANTED
        );

        $user = $this->createUser();
        $this->setEntityId($user, self::USER_ID);

        $token = UserManager::getRandomToken();
        $form = new  PasswordChangeForm('reset');
        $params = [
            'old_password' => User::PASSWORD_ADMIN . 'old',
            'new_password' => User::PASSWORD_ADMIN,
            'confirm_new_password' => User::PASSWORD_ADMIN . '123',
            'csrf' => $form->get('csrf')->getValue(),
            'submit' => 'Change Password',
        ];

        $userManagerMock = $this->getMockBuilder(UserManager::class)
            ->onlyMethods(['validatePasswordResetToken'])
            ->disableOriginalConstructor()
            ->getMock();

        $userManagerMock->expects(self::once())
            ->method('validatePasswordResetToken')
            ->with($this->equalTo(self::USER_EMAIL), $this->equalTo($token))
            ->willReturn(true);

        $this->serviceManager->setService(UserManager::class, $userManagerMock);

        $this->dispatch(
            self::ROUTE_URL . '/set-password?token=' . $token . "&email=" . self::USER_EMAIL,
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
     * @throws Exception
     */
    public function testSetPasswordActionGetException(): void
    {
        $this->setAuthMock(
            self::CONTROLLER_NAME,
            'setPassword',
            AuthManager::ACCESS_GRANTED
        );

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
