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

namespace UserTest\unit\Validator;

use ApplicationTest\AbstractMock;
use Doctrine\ORM\EntityRepository;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionException;
use User\Entity\User;
use User\Form\UserForm;
use User\Validator\UserExistsValidator;

/**
 * Class UserExistsValidatorTest - Unit tests for UserExistsValidator
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package UserTest\unit\Validator
 */
class UserExistsValidatorTest extends AbstractMock
{
    /** @var UserExistsValidator $validator */
    public UserExistsValidator $validator;

    /**
     * @return void
     * @throws ReflectionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $user = $this->createUser();
        $this->setEntityId($user, self::USER_ID);
        $options = [
            'entityManager' => $this->entityManager,
            'user' => $user,
            'scenario' => UserForm::SCENARIO_CREATE,
        ];
        $this->validator = new UserExistsValidator($options);
    }

    /**
     * @testCase - method isValid - must be a success
     * ! is_scalar - false
     *
     * @return void
     */
    public function testIsValidCreateIsScalarFalse(): void
    {
        $value = null;
        $result = $this->validator->isValid($value);
        $this->assertFalse($result);
    }

    /**
     * @testCase - method isValid - must be a success
     * options user not null - options scenario CREATE - result false
     *
     * @return void
     * @throws ReflectionException
     */
    public function testIsValidCreate(): void
    {
        $value = self::USER_EMAIL;
        $user = $this->createUser();
        $this->setEntityId($user, self::USER_ID);

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['findOneBy'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneBy')
            ->with($this->equalTo(['email' => self::USER_EMAIL]))
            ->willReturn($user);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [User::class]
            )
            ->willReturn($repositoryMock);

        $result = $this->validator->isValid($value);
        $this->assertFalse($result);
    }

    /**
     * @testCase - method isValid - must be a success
     * options user not null - options scenario UPDATE - result true
     *
     * @return void
     * @throws ReflectionException
     */
    public function testIsValidUpdate(): void
    {
        $value = self::USER_EMAIL;
        $user = $this->createUser();
        $this->setEntityId($user, self::USER_ID);

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['findOneBy'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneBy')
            ->with($this->equalTo(['email' => self::USER_EMAIL]))
            ->willReturn($user);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [User::class]
            )
            ->willReturn($repositoryMock);

        $options = $this->validator->getOptions();
        $options['scenario'] = UserForm::SCENARIO_UPDATE;
        $this->validator->setOptions($options);

        $result = $this->validator->isValid($value);
        $this->assertTrue($result);
    }

    /**
     * @testCase - method isValid - must be a success
     * options user null - options scenario CREATE - result true
     *
     * @return void
     * @throws ReflectionException
     */
    public function testIsValidCreateUserNull(): void
    {
        $value = self::USER_EMAIL;
        $user = $this->createUser();
        $this->setEntityId($user, self::USER_ID);

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['findOneBy'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneBy')
            ->with($this->equalTo(['email' => self::USER_EMAIL]))
            ->willReturn($user);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [User::class]
            )
            ->willReturn($repositoryMock);

        $options = $this->validator->getOptions();
        $options['user'] = null;
        $this->validator->setOptions($options);

        $result = $this->validator->isValid($value);
        $this->assertTrue($result);
    }

    /**
     * @testCase - method isValid - must be a success
     * options user null - options scenario UPDATE - result false
     *
     * @return void
     * @throws ReflectionException
     */
    public function testIsValidUpdateUserNull(): void
    {
        $value = self::USER_EMAIL;
        $user = $this->createUser();
        $this->setEntityId($user, self::USER_ID);

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['findOneBy'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneBy')
            ->with($this->equalTo(['email' => self::USER_EMAIL]))
            ->willReturn($user);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [User::class]
            )
            ->willReturn($repositoryMock);

        $options = $this->validator->getOptions();
        $options['user'] = null;
        $options['scenario'] = UserForm::SCENARIO_UPDATE;
        $this->validator->setOptions($options);

        $result = $this->validator->isValid($value);
        $this->assertFalse($result);
    }
}
