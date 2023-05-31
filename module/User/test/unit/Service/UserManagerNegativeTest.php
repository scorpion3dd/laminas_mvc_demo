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

namespace UserTest\unit\Service;

use ApplicationTest\AbstractMock;
use Doctrine\ORM\EntityRepository;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\Entity\Role;
use User\Entity\User;
use User\Service\PermissionManager;
use User\Service\RoleManager;
use User\Service\UserManager;

/**
 * Class UserManagerNegativeTest - Unit negative tests for UserManager
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package UserTest\unit\Service
 */
class UserManagerNegativeTest extends AbstractMock
{
    /** @var UserManager $userManager */
    public UserManager $userManager;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->userManager = $this->serviceManager->get(UserManager::class);
    }

    /**
     * @testCase - method addUser - Exception
     * User with email address ... already exists
     *
     * @return void
     * @throws Exception
     */
    public function testAddUserException(): void
    {
        $currentUser = $this->createUser();
        $this->setEntityId($currentUser, self::USER_ID);

        $user = $this->createUser();
        $this->setEntityId($user, self::USER_ID);

        $data = [
            'email' => self::USER_EMAIL,
            'full_name' => self::USER_FULL_NAME,
            'password' => User::PASSWORD_ADMIN,
            'gender' => User::GENDER_MALE_ID,
            'status' => User::STATUS_ACTIVE_ID,
            'access' => User::ACCESS_YES_ID,
            'date_birthday' => '2023-02-07',
            'roles' => [],
        ];

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['findOneBy'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneBy')
            ->with(
                $this->equalTo(['email' => self::USER_EMAIL]),
            )
            ->willReturn($user);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [User::class],
            )
            ->willReturn($repositoryMock);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('User with email address ' . self::USER_EMAIL . ' already exists');
        $this->expectExceptionCode(0);
        $this->userManager->addUser($currentUser, $data);
    }

    /**
     * @testCase - method addUser - Exception
     * empty role - Not found role by ID
     *
     * @return void
     * @throws Exception
     */
    public function testAddUserExceptionEmptyRole(): void
    {
        $currentUser = $this->createUser();
        $this->setEntityId($currentUser, self::USER_ID);

        $user = $this->createUser();

        $data = [
            'email' => self::USER_EMAIL,
            'full_name' => self::USER_FULL_NAME,
            'description' => self::USER_DESCRIPTION,
            'password' => User::PASSWORD_ADMIN,
            'gender' => User::GENDER_MALE_ID,
            'status' => User::STATUS_ACTIVE_ID,
            'access' => User::ACCESS_NO_ID,
            'date_birthday' => '2023-02-07',
            'roles' => [self::ROLE_ID],
        ];

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['findOneBy', 'find'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneBy')
            ->with(
                $this->equalTo(['email' => self::USER_EMAIL]),
            )
            ->willReturn(null);

        $repositoryMock->expects($this->exactly(1))
            ->method('find')
            ->with(
                $this->equalTo(self::ROLE_ID),
            )
            ->willReturn(null);

        $this->entityManager->expects($this->exactly(2))
            ->method('getRepository')
            ->withConsecutive(
                [User::class],
                [Role::class],
            )
            ->willReturn($repositoryMock);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Not found role by ID');
        $this->expectExceptionCode(0);
        $this->userManager->addUser($currentUser, $data);
    }

    /**
     * @testCase - method updateUser - Exception
     * User with email address ... already exists
     *
     * @return void
     * @throws Exception
     */
    public function testUpdateUserException(): void
    {
        $currentUser = $this->createUser();
        $this->setEntityId($currentUser, self::USER_ID);

        $user = $this->createUser();
        $this->setEntityId($user, self::USER_ID);

        $data = [
            'email' => User::EMAIL_ADMIN,
            'full_name' => self::USER_FULL_NAME,
            'password' => User::PASSWORD_ADMIN,
            'gender' => User::GENDER_MALE_ID,
            'status' => User::STATUS_ACTIVE_ID,
            'access' => User::ACCESS_YES_ID,
            'date_birthday' => '2023-02-07',
            'roles' => [],
        ];

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['findOneBy'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneBy')
            ->with(
                $this->equalTo(['email' => User::EMAIL_ADMIN]),
            )
            ->willReturn($user);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [User::class],
            )
            ->willReturn($repositoryMock);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Another user with email address ' . User::EMAIL_ADMIN . ' already exists');
        $this->expectExceptionCode(0);
        $this->userManager->updateUser($user, $currentUser, $data);
    }

    /**
     * @testCase - method createAdminUserIfNotExists - Exception
     * empty adminRole - Administrator role doesn\'t exist
     *
     * @return void
     * @throws Exception
     */
    public function testCreateAdminUserIfNotExists(): void
    {
        $user = null;
        $adminRole = null;
        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->addMethods(['findOneByName'])
            ->onlyMethods(['findOneBy'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneBy')
            ->with(
                $this->equalTo([]),
            )
            ->willReturn($user);

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneByName')
            ->with(
                $this->equalTo(self::USER_ROLE_NAME_ADMINISTRATOR),
            )
            ->willReturn($adminRole);

        $this->entityManager->expects($this->exactly(2))
            ->method('getRepository')
            ->withConsecutive(
                [User::class],
                [Role::class],
            )
            ->willReturn($repositoryMock);


        $permissionManagerMock = $this->getMockBuilder(PermissionManager::class)
            ->onlyMethods(['createDefaultPermissionsIfNotExist'])
            ->disableOriginalConstructor()
            ->getMock();

        $permissionManagerMock->expects(self::once())
            ->method('createDefaultPermissionsIfNotExist');

        $this->userManager->setPermissionManager($permissionManagerMock);


        $roleManagerMock = $this->getMockBuilder(RoleManager::class)
            ->onlyMethods(['createDefaultRolesIfNotExist'])
            ->disableOriginalConstructor()
            ->getMock();

        $roleManagerMock->expects(self::once())
            ->method('createDefaultRolesIfNotExist');

        $this->userManager->setRoleManager($roleManagerMock);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Administrator role doesn\'t exist');
        $this->expectExceptionCode(0);
        $this->userManager->createAdminUserIfNotExists();
    }

    /**
     * @testCase - method generatePasswordResetToken - Exception
     * empty adminRole - Cannot generate password reset token for inactive user ...
     *
     * @return void
     * @throws Exception
     */
    public function testGeneratePasswordResetTokenException(): void
    {
        $user = $this->createUser();
        $user->setStatus(User::STATUS_DISACTIVE_ID);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Cannot generate password reset token for inactive user ' . self::USER_EMAIL);
        $this->expectExceptionCode(0);
        $this->userManager->generatePasswordResetToken($user);
    }

    /**
     * @testCase - method generatePasswordResetToken - Exception
     * getTransport()->send($mail) - Error: Message -  ...
     *
     * @return void
     * @throws Exception
     */
    public function testGeneratePasswordResetTokenExceptionSend(): void
    {
        $user = $this->createUser();
        $this->userManager->generatePasswordResetToken($user);
        $this->assertTrue(true);
    }
}
