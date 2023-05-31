<?php
/**
 * This file is part of the Simple Web Demo Free Lottery Management Application.
 *
 * This project is no longer maintained.
 * The project is written in Zend Framework 3 Release.
 *
 * @link https://github.com/scorpion3dd
 * @author Denis Puzik <scorpion3dd@gmail.com>
 * @copyright Copyright (c) 2020-2021 scorpion3dd
 */

declare(strict_types=1);

namespace UserTest\unit\Entity;

use ApplicationTest\AbstractMock;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionException;
use User\Entity\Role;

/**
 * Class RoleTest - Unit tests for Role
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package UserTest\unit\Entity
 */
class RoleTest extends AbstractMock
{
    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @testCase - function addParent - must be a success
     * return false first
     *
     * @return void
     * @throws ReflectionException
     */
    public function testAddParentFalse1(): void
    {
        $role1 = $this->createRole();
        $role = new Role();
        $this->setEntityId($role, 1);
        $this->setEntityId($role1, 1);

        $this->assertFalse($role->addParent($role1));
    }

    /**
     * @testCase - function addParent - must be a success
     * return false second
     *
     * @return void
     * @throws ReflectionException
     */
    public function testAddParentFalse2(): void
    {
        $role1 = $this->createRole();
        $role = new Role();
        $this->setEntityId($role, 1);
        $this->setEntityId($role1, 2);
        $role->addParent($role1);

        $this->assertFalse($role->addParent($role1));
    }
}
