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
use User\Controller\UserController;
use User\Entity\Role;
use User\Entity\User;
use User\Form\PasswordChangeForm;
use User\Form\PasswordResetForm;
use User\Form\UserForm;
use User\Service\AuthManager;
use User\Service\UserManager;
use Laminas\Authentication\AuthenticationService;

/**
 * Class UserControllerTest - Unit tests for IndexController
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package UserTest\unit\Controller
 */
class UserControllerTest extends AbstractMock
{
    public const MODULE_NAME = 'user';
    public const CONTROLLER_NAME = UserController::class;
    public const CONTROLLER_CLASS = 'UserController';
    public const ROUTE_URL = '/users';
    public const ROUTE_USERS = 'users';

    /**
     * @testCase - route index action - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testIndexAction(): void
    {
        $this->setAuthMock(
            self::CONTROLLER_NAME,
            'index',
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
            ->onlyMethods(['findBy', 'findOneBy'])
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

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneBy')
            ->with($this->equalTo(['email' => $identity]))
            ->willReturn($user);

        $this->entityManager->expects($this->exactly(2))
            ->method('getRepository')
            ->withConsecutive(
                [Role::class],
                [User::class],
            )
            ->willReturn($repositoryMock);

        $userManagerMock = $this->getMockBuilder(UserManager::class)
            ->onlyMethods(['getUsersPaginator'])
            ->disableOriginalConstructor()
            ->getMock();

        $userManagerMock->expects(self::once())
            ->method('getUsersPaginator')
            ->with(0, UserController::COUNT_PER_PAGE)
            ->willReturn(null);

        $this->serviceManager->setService(UserManager::class, $userManagerMock);

        $this->sleep();
        $this->dispatch(self::ROUTE_URL, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        $expected = file_get_contents(
            __DIR__ . '/../data/Controller/User/GetIndexActionSuccess.html'
        );
        self::assertStringStartsWith($this->trim($expected), $this->trim($response));
    }

    /**
     * @testCase - route view action - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testViewAction(): void
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

        $this->dispatch(self::ROUTE_URL . '/view/' . self::USER_ID, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route add action by method GET - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testAddActionGet(): void
    {
        $this->setAuthMock(
            self::CONTROLLER_NAME,
            'add',
            AuthManager::ACCESS_GRANTED
        );

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['findBy'])
            ->disableOriginalConstructor()
            ->getMock();

        $roles = [];
        $role = $this->createRole();
        $roles[] = $role;

        $repositoryMock->expects($this->exactly(1))
            ->method('findBy')
            ->with(
                $this->equalTo([]),
                $this->equalTo(['name' => 'ASC']),
            )
            ->willReturn($roles);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [Role::class],
            )
            ->willReturn($repositoryMock);

        $this->dispatch(self::ROUTE_URL . '/add', self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route add action by method POST - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testAddActionPost(): void
    {
        $this->setAuthMock(
            self::CONTROLLER_NAME,
            'add',
            AuthManager::ACCESS_GRANTED
        );

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['findBy', 'findOneBy'])
            ->disableOriginalConstructor()
            ->getMock();

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

        $user = $this->createUser();
        $this->setEntityId($user, self::USER_ID);

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneBy')
            ->with(
                $this->equalTo(['email' => self::USER_EMAIL]),
            )
            ->willReturn($user);

        $this->entityManager->expects($this->exactly(2))
            ->method('getRepository')
            ->withConsecutive(
                [Role::class],
                [User::class],
            )
            ->willReturn($repositoryMock);

        $form = new  UserForm('create', $this->entityManager, $user);
        $params = [
            'email' => self::USER_EMAIL,
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
        $userManagerMock = $this->getMockBuilder(UserManager::class)
            ->onlyMethods(['addUser'])
            ->disableOriginalConstructor()
            ->getMock();

        $userManagerMock->expects(self::once())
            ->method('addUser')
            ->with($this->equalTo(null), $this->equalTo($params))
            ->willReturn($user);

        $this->serviceManager->setService(UserManager::class, $userManagerMock);

        $this->dispatch(self::ROUTE_URL . '/add', self::METHOD_POST, $params);
        $this->assertResponseStatusCode(self::STATUS_CODE_302);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertEquals('', $this->trim($response));
    }

    /**
     * @testCase - route edit action by method GET - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testEditActionGet(): void
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

        $this->dispatch(self::ROUTE_URL . '/edit/' . self::USER_ID, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route edit action by method POST - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testEditActionPost(): void
    {
        $this->setAuthMock(
            self::CONTROLLER_NAME,
            'edit',
            AuthManager::ACCESS_GRANTED
        );

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['find', 'findBy', 'findOneBy'])
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

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneBy')
            ->with(
                $this->equalTo(['email' => self::USER_EMAIL]),
            )
            ->willReturn($user);

        $this->entityManager->expects($this->exactly(3))
            ->method('getRepository')
            ->withConsecutive(
                [User::class],
                [Role::class],
                [User::class],
            )
            ->willReturn($repositoryMock);

        $form = new  UserForm('update', $this->entityManager, $user);
        $params = [
            'email' => self::USER_EMAIL,
            'full_name' => self::USER_FULL_NAME,
            'description' => self::USER_DESCRIPTION,
            'date_birthday' => self::USER_DATE_BIRTHDAY,
            'status' => User::STATUS_ACTIVE_ID,
            'access' => User::ACCESS_YES_ID,
            'gender' => User::GENDER_MALE_ID,
            'roles' => [$role->getId()],
            'csrf' => $form->get('csrf')->getValue(),
            'submit' => 'Create',
        ];
        $userManagerMock = $this->getMockBuilder(UserManager::class)
            ->onlyMethods(['updateUser'])
            ->disableOriginalConstructor()
            ->getMock();

        $userManagerMock->expects(self::once())
            ->method('updateUser')
            ->with($this->equalTo($user), $this->equalTo(null), $this->equalTo($params))
            ->willReturn(true);

        $this->serviceManager->setService(UserManager::class, $userManagerMock);

        $this->dispatch(self::ROUTE_URL . '/edit/' . self::USER_ID, self::METHOD_POST, $params);
        $this->assertResponseStatusCode(self::STATUS_CODE_302);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertEquals('', $this->trim($response));
    }

    /**
     * @testCase - route changePassword action by method GET - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testChangePasswordActionGet(): void
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

        $this->dispatch(self::ROUTE_URL . '/change-password/' . self::USER_ID, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route changePassword action by method POST - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testChangePasswordActionPost(): void
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
            ->willReturn(true);

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
     * @testCase - route resetPassword action by method GET - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testResetPasswordActionGet(): void
    {
//        self::markTestSkipped('skiped bitbucket-pipelines failures');
        $this->setAuthMock(
            self::CONTROLLER_NAME,
            'resetPassword',
            AuthManager::ACCESS_GRANTED
        );
        $this->setEnvTest();

        $this->dispatch(self::ROUTE_URL . '/reset-password/' . self::USER_ID, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route resetPassword action by method POST - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testResetPasswordActionPost(): void
    {
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
        $userManagerMock = $this->getMockBuilder(UserManager::class)
            ->onlyMethods(['generatePasswordResetToken'])
            ->disableOriginalConstructor()
            ->getMock();

        $userManagerMock->expects(self::once())
            ->method('generatePasswordResetToken')
            ->with($this->equalTo($user));

        $this->serviceManager->setService(UserManager::class, $userManagerMock);

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
     * @testCase - route message action by method GET - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testMessageGet(): void
    {
        $this->setAuthMock(
            self::CONTROLLER_NAME,
            'message',
            AuthManager::ACCESS_GRANTED
        );

        $this->dispatch(self::ROUTE_URL . '/message/sent', self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        $expected = file_get_contents(
            __DIR__ . '/../data/Controller/User/GetMessageActionSuccess.html'
        );
//        self::assertSame($this->trim($expected), $this->trim($response));
    }

    /**
     * @testCase - route setPassword action by method GET - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testSetPasswordActionGet(): void
    {
        $this->setAuthMock(
            self::CONTROLLER_NAME,
            'setPassword',
            AuthManager::ACCESS_GRANTED
        );

        $user = $this->createUser();
        $this->setEntityId($user, self::USER_ID);
        $token = UserManager::getRandomToken();
        $form = new PasswordChangeForm('reset');
        $csrf = $form->get('csrf')->getValue();
        $params = [
            'email' => self::USER_EMAIL,
            'token' => $token,
            'csrf' => $csrf,
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

        $this->dispatch(self::ROUTE_URL . '/set-password', self::METHOD_GET, $params);
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route setPassword action by method POST - must be a success
     * setNewPasswordByToken return true
     *
     * @return void
     * @throws Exception
     */
    public function testSetPassworddActionPost(): void
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
            'confirm_new_password' => User::PASSWORD_ADMIN,
            'csrf' => $form->get('csrf')->getValue(),
            'submit' => 'Change Password',
        ];

        $userManagerMock = $this->getMockBuilder(UserManager::class)
            ->onlyMethods(['validatePasswordResetToken', 'setNewPasswordByToken'])
            ->disableOriginalConstructor()
            ->getMock();

        $userManagerMock->expects(self::once())
            ->method('validatePasswordResetToken')
            ->with($this->equalTo(self::USER_EMAIL), $this->equalTo($token))
            ->willReturn(true);

        $userManagerMock->expects(self::once())
            ->method('setNewPasswordByToken')
            ->with(
                $this->equalTo(self::USER_EMAIL),
                $this->equalTo($token),
                $this->equalTo($params['new_password'])
            )
            ->willReturn(true);

        $this->serviceManager->setService(UserManager::class, $userManagerMock);

        $this->dispatch(
            self::ROUTE_URL . '/set-password?token=' . $token . "&email=" . self::USER_EMAIL,
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
     * @testCase - route setPassword action by method POST - must be a success
     * setNewPasswordByToken return false
     *
     * @return void
     * @throws Exception
     */
    public function testSetPassworddActionPostFalse(): void
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
            'confirm_new_password' => User::PASSWORD_ADMIN,
            'csrf' => $form->get('csrf')->getValue(),
            'submit' => 'Change Password',
        ];

        $userManagerMock = $this->getMockBuilder(UserManager::class)
            ->onlyMethods(['validatePasswordResetToken', 'setNewPasswordByToken'])
            ->disableOriginalConstructor()
            ->getMock();

        $userManagerMock->expects(self::once())
            ->method('validatePasswordResetToken')
            ->with($this->equalTo(self::USER_EMAIL), $this->equalTo($token))
            ->willReturn(true);

        $userManagerMock->expects(self::once())
            ->method('setNewPasswordByToken')
            ->with(
                $this->equalTo(self::USER_EMAIL),
                $this->equalTo($token),
                $this->equalTo($params['new_password'])
            )
            ->willReturn(false);

        $this->serviceManager->setService(UserManager::class, $userManagerMock);

        $this->dispatch(
            self::ROUTE_URL . '/set-password?token=' . $token . "&email=" . self::USER_EMAIL,
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
