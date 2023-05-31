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
use User\Entity\Role;
use User\Form\RoleForm;
use User\Validator\RoleExistsValidator;

/**
 * Class RoleExistsValidatorTest - Unit tests for RoleExistsValidator
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package UserTest\unit\Validator
 */
class RoleExistsValidatorTest extends AbstractMock
{
    /** @var RoleExistsValidator $validator */
    public RoleExistsValidator $validator;

    /**
     * @return void
     * @throws ReflectionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $role = $this->createRole();
        $this->setEntityId($role, self::ROLE_ID);
        $options = [
            'entityManager' => $this->entityManager,
            'role' => $role,
            'scenario' => RoleForm::SCENARIO_CREATE,
        ];
        $this->validator = new RoleExistsValidator($options);
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
     * options role not null - options scenario CREATE - result false
     *
     * @return void
     * @throws ReflectionException
     */
    public function testIsValidCreate(): void
    {
        $value = self::USER_ROLE_NAME_ADMINISTRATOR;
        $role = $this->createRole();
        $this->setEntityId($role, self::ROLE_ID);

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->addMethods(['findOneByName'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneByName')
            ->with($this->equalTo($value))
            ->willReturn($role);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [Role::class]
            )
            ->willReturn($repositoryMock);

        $result = $this->validator->isValid($value);
        $this->assertFalse($result);
    }

    /**
     * @testCase - method isValid - must be a success
     * options role not null - options scenario UPDATE - result true
     *
     * @return void
     * @throws ReflectionException
     */
    public function testIsValidUpdate(): void
    {
        $value = self::USER_ROLE_NAME_ADMINISTRATOR;
        $role = $this->createRole();
        $this->setEntityId($role, self::ROLE_ID);

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->addMethods(['findOneByName'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneByName')
            ->with($this->equalTo($value))
            ->willReturn($role);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [Role::class]
            )
            ->willReturn($repositoryMock);

        $options = $this->validator->getOptions();
        $options['scenario'] = RoleForm::SCENARIO_UPDATE;
        $this->validator->setOptions($options);

        $result = $this->validator->isValid($value);
        $this->assertTrue($result);
    }

    /**
     * @testCase - method isValid - must be a success
     * options role null - options scenario CREATE - result true
     *
     * @return void
     * @throws ReflectionException
     */
    public function testIsValidCreateRoleNull(): void
    {
        $value = self::USER_ROLE_NAME_ADMINISTRATOR;
        $role = $this->createRole();
        $this->setEntityId($role, self::ROLE_ID);

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->addMethods(['findOneByName'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneByName')
            ->with($this->equalTo($value))
            ->willReturn($role);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [Role::class]
            )
            ->willReturn($repositoryMock);

        $options = $this->validator->getOptions();
        $options['role'] = null;
        $this->validator->setOptions($options);

        $result = $this->validator->isValid($value);
        $this->assertTrue($result);
    }

    /**
     * @testCase - method isValid - must be a success
     * options role null - options scenario UPDATE - result false
     *
     * @return void
     * @throws ReflectionException
     */
    public function testIsValidUpdateRoleNull(): void
    {
        $value = self::USER_ROLE_NAME_ADMINISTRATOR;
        $role = $this->createRole();
        $this->setEntityId($role, self::ROLE_ID);

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->addMethods(['findOneByName'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneByName')
            ->with($this->equalTo($value))
            ->willReturn($role);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [Role::class]
            )
            ->willReturn($repositoryMock);

        $options = $this->validator->getOptions();
        $options['role'] = null;
        $options['scenario'] = RoleForm::SCENARIO_UPDATE;
        $this->validator->setOptions($options);

        $result = $this->validator->isValid($value);
        $this->assertFalse($result);
    }
}
