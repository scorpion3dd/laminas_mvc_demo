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
use Carbon\Carbon;
use DateTime;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\Entity\Role;
use User\Entity\User;
use User\Kafka\ProducerKafka;
use User\Service\PermissionManager;
use User\Service\RoleManager;
use User\Service\UserManager;
use Laminas\Mail\Transport\Smtp as SmtpTransport;
use Laminas\Paginator\Paginator;

/**
 * Class UserManagerTest - Unit tests for UserManager
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package UserTest\unit\Service
 */
class UserManagerTest extends AbstractMock
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
     * @testCase - method addUser - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testAddUser(): void
    {
        $currentUser = $this->createUser();
        $this->setEntityId($currentUser, self::USER_ID);

        $user = $this->createUser();
        $role = $this->createRole();
        $this->setEntityId($role, self::ROLE_ID);

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
            ->willReturn($role);

        $this->entityManager->expects($this->exactly(2))
            ->method('getRepository')
            ->withConsecutive(
                [User::class],
                [Role::class],
            )
            ->willReturn($repositoryMock);

        $producerKafkaMock = $this->getMockBuilder(ProducerKafka::class)
            ->onlyMethods(['send'])
            ->disableOriginalConstructor()
            ->getMock();

        $producerKafkaMock->expects($this->exactly(1))
            ->method('send');

        $this->userManager->setProducerKafka($producerKafkaMock);

        $result = $this->userManager->addUser($currentUser, $data);
        $this->assertEquals($user->getEmail(), $result->getEmail());
        $this->assertEquals($user->getFullName(), $result->getFullName());
        $this->assertEquals($user->getDescription(), $result->getDescription());
        $this->assertEquals($user->getStatus(), $result->getStatus());
        $this->assertEquals($user->getAccess(), $result->getAccess());
        $this->assertEquals($user->getGender(), $result->getGender());
    }

    /**
     * @testCase - method addUser - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testAddUser2(): void
    {
        $currentUser = $this->createUser();
        $this->setEntityId($currentUser, self::USER_ID);

        $user = $this->createUser();
        $role = $this->createRole();
        $this->setEntityId($role, self::ROLE_ID);
        $user->setStatus(User::STATUS_DISACTIVE_ID);
        $user->setAccess(User::ACCESS_YES_ID);
        $data = [
            'email' => self::USER_EMAIL,
            'full_name' => self::USER_FULL_NAME,
            'description' => self::USER_DESCRIPTION,
            'password' => User::PASSWORD_ADMIN,
            'gender' => User::GENDER_MALE_ID,
            'status' => User::STATUS_DISACTIVE_ID,
            'access' => User::ACCESS_YES_ID,
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
            ->willReturn($role);

        $this->entityManager->expects($this->exactly(2))
            ->method('getRepository')
            ->withConsecutive(
                [User::class],
                [Role::class],
            )
            ->willReturn($repositoryMock);

        $producerKafkaMock = $this->getMockBuilder(ProducerKafka::class)
            ->onlyMethods(['send'])
            ->disableOriginalConstructor()
            ->getMock();

        $producerKafkaMock->expects($this->exactly(1))
            ->method('send');

        $this->userManager->setProducerKafka($producerKafkaMock);

        $result = $this->userManager->addUser($currentUser, $data);
        $this->assertEquals($user->getEmail(), $result->getEmail());
        $this->assertEquals($user->getFullName(), $result->getFullName());
        $this->assertEquals($user->getDescription(), $result->getDescription());
        $this->assertEquals($user->getStatus(), $result->getStatus());
        $this->assertEquals($user->getAccess(), $result->getAccess());
        $this->assertEquals($user->getGender(), $result->getGender());
    }

    /**
     * @testCase - method updateUser - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testUpdateUser(): void
    {
        $currentUser = $this->createUser();
        $this->setEntityId($currentUser, self::USER_ID);

        $user = $this->createUser();
        $role = $this->createRole();
        $this->setEntityId($role, self::ROLE_ID);

        $data = [
            'email' => self::USER_EMAIL,
            'full_name' => self::USER_FULL_NAME,
            'description' => self::USER_DESCRIPTION,
            'password' => User::PASSWORD_ADMIN,
            'gender' => User::GENDER_MALE_ID,
            'status' => User::STATUS_DISACTIVE_ID,
            'access' => User::ACCESS_YES_ID,
            'date_birthday' => '2023-02-07',
            'roles' => [self::ROLE_ID],
        ];

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('find')
            ->with(
                $this->equalTo(self::ROLE_ID),
            )
            ->willReturn($role);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [Role::class],
            )
            ->willReturn($repositoryMock);

        $producerKafkaMock = $this->getMockBuilder(ProducerKafka::class)
            ->onlyMethods(['send'])
            ->disableOriginalConstructor()
            ->getMock();

        $producerKafkaMock->expects($this->exactly(2))
            ->method('send');

        $this->userManager->setProducerKafka($producerKafkaMock);

        $result = $this->userManager->updateUser($user, $currentUser, $data);
        $this->assertTrue($result);
    }

    /**
     * @testCase - method updateUser - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testUpdateUser2(): void
    {
        $currentUser = $this->createUser();
        $this->setEntityId($currentUser, self::USER_ID);

        $role = $this->createRole();
        $this->setEntityId($role, self::ROLE_ID);

        $user = $this->createUser();
        $user->setStatus(User::STATUS_DISACTIVE_ID);
        $user->setAccess(User::ACCESS_YES_ID);

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
            ->onlyMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('find')
            ->with(
                $this->equalTo(self::ROLE_ID),
            )
            ->willReturn($role);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [Role::class],
            )
            ->willReturn($repositoryMock);

        $producerKafkaMock = $this->getMockBuilder(ProducerKafka::class)
            ->onlyMethods(['send'])
            ->disableOriginalConstructor()
            ->getMock();

        $producerKafkaMock->expects($this->exactly(2))
            ->method('send');

        $this->userManager->setProducerKafka($producerKafkaMock);

        $result = $this->userManager->updateUser($user, $currentUser, $data);
        $this->assertTrue($result);
    }

    /**
     * @testCase - method createAdminUserIfNotExists - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testCreateAdminUserIfNotExists(): void
    {
        $user = null;
        $adminRole = $this->createRole();
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

        $this->userManager->createAdminUserIfNotExists();
        $this->assertTrue(true);
    }

    /**
     * @testCase - method generatePasswordResetToken - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testGeneratePasswordResetToken(): void
    {
        $user = $this->createUser();

        $transportMock = $this->getMockBuilder(SmtpTransport::class)
            ->onlyMethods(['setOptions', 'send', 'getConnection'])
            ->disableOriginalConstructor()
            ->getMock();

        $transportMock->expects(self::once())
            ->method('setOptions');

        $transportMock->expects(self::once())
            ->method('send');

        $transportMock->expects(self::exactly(0))
            ->method('getConnection');

        $this->userManager->setTransport($transportMock);

        $this->userManager->generatePasswordResetToken($user);
    }

    /**
     * @testCase - method validatePasswordResetToken - must be a success
     * empty user - return false
     *
     * @return void
     * @throws Exception
     */
    public function testValidatePasswordResetTokenEmptyUser(): void
    {
        $user = null;

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

        $result = $this->userManager->validatePasswordResetToken(self::USER_EMAIL, '');
        $this->assertFalse($result);
    }

    /**
     * @testCase - method validatePasswordResetToken - must be a success
     * bcrypt->verify - return false
     *
     * @return void
     * @throws Exception
     */
    public function testValidatePasswordResetTokenBcryptVerifyFalse(): void
    {
        $user = $this->createUser();
        $user->setPasswordResetToken('123');

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

        $result = $this->userManager->validatePasswordResetToken(self::USER_EMAIL, '456');
        $this->assertFalse($result);
    }

    /**
     * @testCase - method validatePasswordResetToken - must be a success
     * return true
     *
     * @return void
     * @throws Exception
     */
    public function testValidatePasswordResetTokenTrue(): void
    {
        $user = $this->createUser();
        $user->setPasswordResetToken('$2y$10$3jdAiktqqKuTJr1dIl7pou4UKlZqJd.44yvVpE4QG3UNwZXwN6T3K');
        $user->setPasswordResetTokenCreationDate(Carbon::now());

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

        $result = $this->userManager->validatePasswordResetToken(self::USER_EMAIL, User::PASSWORD_ADMIN);
        $this->assertTrue($result);
    }

    /**
     * @testCase - method validatePasswordResetToken - must be a success
     * expired - return false
     *
     * @return void
     * @throws Exception
     */
    public function testValidatePasswordResetTokenExpired(): void
    {
        $user = $this->createUser();
        $user->setPasswordResetToken('$2y$10$3jdAiktqqKuTJr1dIl7pou4UKlZqJd.44yvVpE4QG3UNwZXwN6T3K');
        $date = new DateTime();
        $date->modify("-10 day");
        $user->setPasswordResetTokenCreationDate($date);

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

        $result = $this->userManager->validatePasswordResetToken(self::USER_EMAIL, User::PASSWORD_ADMIN);
        $this->assertFalse($result);
    }

    /**
     * @testCase - method setNewPasswordByToken - must be a success
     * validatePasswordResetToken false - return false
     *
     * @return void
     * @throws Exception
     */
    public function testSetNewPasswordByTokenFalse(): void
    {
        $user = $this->createUser();
        $user->setPasswordResetToken('$2y$10$3jdAiktqqKuTJr1dIl7pou4UKlZqJd.44yvVpE4QG3UNwZXwN6T3K');
        $date = new DateTime();
        $date->modify("-10 day");
        $user->setPasswordResetTokenCreationDate($date);

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

        $result = $this->userManager->setNewPasswordByToken(
            self::USER_EMAIL,
            User::PASSWORD_ADMIN,
            User::PASSWORD_ADMIN . 'new'
        );
        $this->assertFalse($result);
    }

    /**
     * @testCase - method setNewPasswordByToken - must be a success
     * empty user - return false
     *
     * @return void
     * @throws Exception
     */
    public function testSetNewPasswordByTokenEmptyUserFalse(): void
    {
        $user = $this->createUser();
        $user->setPasswordResetToken('$2y$10$3jdAiktqqKuTJr1dIl7pou4UKlZqJd.44yvVpE4QG3UNwZXwN6T3K');
        $user->setPasswordResetTokenCreationDate(Carbon::now());

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['findOneBy'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(2))
            ->method('findOneBy')
            ->with(
                $this->equalTo(['email' => self::USER_EMAIL]),
            )
            ->willReturn($user, null);

        $this->entityManager->expects($this->exactly(2))
            ->method('getRepository')
            ->withConsecutive(
                [User::class],
            )
            ->willReturn($repositoryMock);

        $result = $this->userManager->setNewPasswordByToken(
            self::USER_EMAIL,
            User::PASSWORD_ADMIN,
            User::PASSWORD_ADMIN . 'new'
        );
        $this->assertFalse($result);
    }

    /**
     * @testCase - method setNewPasswordByToken - must be a success
     * return true
     *
     * @return void
     * @throws Exception
     */
    public function testSetNewPasswordByTokenTrue(): void
    {
        $user = $this->createUser();
        $user->setPasswordResetToken('$2y$10$3jdAiktqqKuTJr1dIl7pou4UKlZqJd.44yvVpE4QG3UNwZXwN6T3K');
        $user->setPasswordResetTokenCreationDate(Carbon::now());

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['findOneBy'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(2))
            ->method('findOneBy')
            ->with(
                $this->equalTo(['email' => self::USER_EMAIL]),
            )
            ->willReturn($user);

        $this->entityManager->expects($this->exactly(2))
            ->method('getRepository')
            ->withConsecutive(
                [User::class],
            )
            ->willReturn($repositoryMock);

        $result = $this->userManager->setNewPasswordByToken(
            self::USER_EMAIL,
            User::PASSWORD_ADMIN,
            User::PASSWORD_ADMIN . 'new'
        );
        $this->assertTrue($result);
    }

    /**
     * @testCase - method changePassword - must be a success
     * validatePassword false - return false
     *
     * @return void
     * @throws Exception
     */
    public function testChangePasswordValidatePasswordFalse(): void
    {
        $user = $this->createUser();
        $data = [
            'old_password' => '123',
        ];

        $result = $this->userManager->changePassword($user, $data);
        $this->assertFalse($result);
    }

    /**
     * @testCase - method changePassword - must be a success
     * strlen newPassword false - return false
     *
     * @return void
     * @throws Exception
     */
    public function testChangePasswordStrlenNewPasswordFalse(): void
    {
        $user = $this->createUser();
        $data = [
            'old_password' => User::PASSWORD_ADMIN,
            'new_password' => '123',
        ];

        $result = $this->userManager->changePassword($user, $data);
        $this->assertFalse($result);
    }

    /**
     * @testCase - method changePassword - must be a success
     * return true
     *
     * @return void
     * @throws Exception
     */
    public function testChangePasswordTrue(): void
    {
        $user = $this->createUser();
        $data = [
            'old_password' => User::PASSWORD_ADMIN,
            'new_password' => User::PASSWORD_ADMIN . 'new',
        ];

        $result = $this->userManager->changePassword($user, $data);
        $this->assertTrue($result);
    }

    /**
     * @testCase - method getUsersPaginator - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testGetUsersPaginator(): void
    {
        $query = $this->getMockBuilder(AbstractQuery::class)
            ->onlyMethods(['getOneOrNullResult'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $query->expects($this->exactly(0))
            ->method('getOneOrNullResult')
            ->with(
                $this->equalTo(Query::HYDRATE_ARRAY),
            )
            ->willReturn(null);

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->addMethods(['findUsersAccess'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findUsersAccess')
            ->willReturn($query);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [User::class],
            )
            ->willReturn($repositoryMock);

        $adapter = new DoctrineAdapter(new ORMPaginator($query, false));
        $paginator = new Paginator($adapter);

        $result = $this->userManager->getUsersPaginator(1, 10);
        $this->assertEquals($paginator, $result);
    }

    /**
     * @testCase - method getProducerKafka - must be a success
     * buildProducerKafka
     *
     * @return void
     * @throws Exception
     */
    public function testGetProducerKafka(): void
    {
        $this->userManager->getProducerKafka();
        $this->assertTrue(true);
    }
}
