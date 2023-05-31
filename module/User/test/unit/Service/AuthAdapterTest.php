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
use User\Entity\User;
use User\Service\AuthAdapter;
use Laminas\Authentication\Result;

/**
 * Class AuthAdapterTest - Unit tests for AuthAdapter
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package UserTest\unit\Service
 */
class AuthAdapterTest extends AbstractMock
{
    /** @var AuthAdapter $authAdapter */
    public AuthAdapter $authAdapter;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->authAdapter = $this->serviceManager->get(AuthAdapter::class);
    }

    /**
     * @testCase - method authenticate - must be a success
     * Result::SUCCESS
     *
     * @return void
     * @throws Exception
     */
    public function testAuthenticate(): void
    {
        $identity = User::EMAIL_ADMIN;
        $user = $this->createUser();
        $this->setEntityId($user, self::USER_ID);

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['findOneBy'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneBy')
            ->with($this->equalTo(['email' => $identity]))
            ->willReturn($user);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [User::class],
            )
            ->willReturn($repositoryMock);

        $messages = ['Authenticated successfully.'];
        $resultAuthenticate = new Result(Result::SUCCESS, $identity, $messages);

        $this->authAdapter->setEmail($identity);
        $this->authAdapter->setPassword(User::PASSWORD_ADMIN);
        $result = $this->authAdapter->authenticate();
        $this->assertEquals($resultAuthenticate, $result);
    }

    /**
     * @testCase - method authenticate - must be a success
     * Result::FAILURE_CREDENTIAL_INVALID
     *
     * @return void
     * @throws Exception
     */
    public function testAuthenticateFailureCredentailInvalid(): void
    {
        $identity = User::EMAIL_ADMIN;
        $user = $this->createUser();
        $this->setEntityId($user, self::USER_ID);

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['findOneBy'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneBy')
            ->with($this->equalTo(['email' => $identity]))
            ->willReturn($user);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [User::class],
            )
            ->willReturn($repositoryMock);

        $messages = ['Invalid credentials.'];
        $resultAuthenticate = new Result(Result::FAILURE_CREDENTIAL_INVALID, null, $messages);

        $this->authAdapter->setEmail($identity);
        $this->authAdapter->setPassword(User::PASSWORD_GUEST);
        $result = $this->authAdapter->authenticate();
        $this->assertEquals($resultAuthenticate, $result);
    }

    /**
     * @testCase - method authenticate - must be a success
     * Result::FAILURE
     *
     * @return void
     * @throws Exception
     */
    public function testAuthenticateFailure(): void
    {
        $identity = User::EMAIL_ADMIN;
        $user = $this->createUser();
        $user->setStatus(User::STATUS_DISACTIVE_ID);
        $this->setEntityId($user, self::USER_ID);

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['findOneBy'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneBy')
            ->with($this->equalTo(['email' => $identity]))
            ->willReturn($user);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [User::class],
            )
            ->willReturn($repositoryMock);

        $messages = ['User is Disactived.'];
        $resultAuthenticate = new Result(Result::FAILURE, null, $messages);

        $this->authAdapter->setEmail($identity);
        $result = $this->authAdapter->authenticate();
        $this->assertEquals($resultAuthenticate, $result);
    }

    /**
     * @testCase - method authenticate - must be a success
     * Result::FAILURE_IDENTITY_NOT_FOUND
     *
     * @return void
     * @throws Exception
     */
    public function testAuthenticateFailureIdentityNotFound(): void
    {
        $identity = User::EMAIL_ADMIN;
        $user = null;

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['findOneBy'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneBy')
            ->with($this->equalTo(['email' => $identity]))
            ->willReturn($user);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [User::class],
            )
            ->willReturn($repositoryMock);

        $messages = ['Invalid credentials.'];
        $resultAuthenticate = new Result(Result::FAILURE_IDENTITY_NOT_FOUND, null, $messages);

        $this->authAdapter->setEmail($identity);
        $result = $this->authAdapter->authenticate();
        $this->assertEquals($resultAuthenticate, $result);
    }
}
