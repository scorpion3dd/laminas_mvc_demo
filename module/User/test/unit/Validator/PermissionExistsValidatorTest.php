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
use User\Entity\Permission;
use User\Form\PermissionForm;
use User\Validator\PermissionExistsValidator;

/**
 * Class PermissionExistsValidatorTest - Unit tests for PermissionExistsValidator
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package UserTest\unit\Validator
 */
class PermissionExistsValidatorTest extends AbstractMock
{
    /** @var PermissionExistsValidator $validator */
    public PermissionExistsValidator $validator;

    /**
     * @return void
     * @throws ReflectionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $permission = $this->createPermission();
        $this->setEntityId($permission, self::PERMISSION_ID);
        $options = [
            'entityManager' => $this->entityManager,
            'permission' => $permission,
            'scenario' => PermissionForm::SCENARIO_CREATE,
        ];
        $this->validator = new PermissionExistsValidator($options);
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
     * options permission not null - options scenario CREATE - result false
     *
     * @return void
     * @throws ReflectionException
     */
    public function testIsValidCreate(): void
    {
        $value = self::USER_PERMISSION_PROFILE_ANY_VIEW;
        $permission = $this->createPermission();
        $this->setEntityId($permission, self::PERMISSION_ID);

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->addMethods(['findOneByName'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneByName')
            ->with($this->equalTo($value))
            ->willReturn($permission);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [Permission::class]
            )
            ->willReturn($repositoryMock);

        $result = $this->validator->isValid($value);
        $this->assertFalse($result);
    }

    /**
     * @testCase - method isValid - must be a success
     * options permission not null - options scenario UPDATE - result true
     *
     * @return void
     * @throws ReflectionException
     */
    public function testIsValidUpdate(): void
    {
        $value = self::USER_PERMISSION_PROFILE_ANY_VIEW;
        $permission = $this->createPermission();
        $this->setEntityId($permission, self::PERMISSION_ID);

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->addMethods(['findOneByName'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneByName')
            ->with($this->equalTo($value))
            ->willReturn($permission);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [Permission::class]
            )
            ->willReturn($repositoryMock);

        $options = $this->validator->getOptions();
        $options['scenario'] = PermissionForm::SCENARIO_UPDATE;
        $this->validator->setOptions($options);

        $result = $this->validator->isValid($value);
        $this->assertTrue($result);
    }

    /**
     * @testCase - method isValid - must be a success
     * options permission null - options scenario CREATE - result true
     *
     * @return void
     * @throws ReflectionException
     */
    public function testIsValidCreatePermissionNull(): void
    {
        $value = self::USER_PERMISSION_PROFILE_ANY_VIEW;
        $permission = $this->createPermission();
        $this->setEntityId($permission, self::PERMISSION_ID);

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->addMethods(['findOneByName'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneByName')
            ->with($this->equalTo($value))
            ->willReturn($permission);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [Permission::class]
            )
            ->willReturn($repositoryMock);

        $options = $this->validator->getOptions();
        $options['permission'] = null;
        $this->validator->setOptions($options);

        $result = $this->validator->isValid($value);
        $this->assertTrue($result);
    }

    /**
     * @testCase - method isValid - must be a success
     * options permission null - options scenario UPDATE - result false
     *
     * @return void
     * @throws ReflectionException
     */
    public function testIsValidUpdatePermissionNull(): void
    {
        $value = self::USER_PERMISSION_PROFILE_ANY_VIEW;
        $permission = $this->createPermission();
        $this->setEntityId($permission, self::PERMISSION_ID);

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->addMethods(['findOneByName'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneByName')
            ->with($this->equalTo($value))
            ->willReturn($permission);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [Permission::class]
            )
            ->willReturn($repositoryMock);

        $options = $this->validator->getOptions();
        $options['permission'] = null;
        $options['scenario'] = PermissionForm::SCENARIO_UPDATE;
        $this->validator->setOptions($options);

        $result = $this->validator->isValid($value);
        $this->assertFalse($result);
    }
}
