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
use User\Entity\User;

/**
 * Class UserTest - Unit tests for User
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package UserTest\unit\Entity
 */
class UserTest extends AbstractMock
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
     * @testCase - function getStatusAsString - must be a success
     *
     * @return void
     */
    public function testGetStatusAsString(): void
    {
        $user = new User();
        $user->setStatus(100);
        $this->assertEquals('Unknown', $user->getStatusAsString());
    }

    /**
     * @testCase - function getAccessAsString - must be a success
     *
     * @return void
     */
    public function testGetAccessAsString(): void
    {
        $user = new User();
        $user->setAccess(100);
        $this->assertEquals('Unknown', $user->getAccessAsString());
    }

    /**
     * @testCase - function getGenderAsString - must be a success
     *
     * @return void
     */
    public function testGetGenderAsString(): void
    {
        $user = new User();
        $user->setGender(100);
        $this->assertEquals('Unknown', $user->getGenderAsString());
    }

    /**
     * @testCase - function getRolesAsString - must be a success
     *
     * @return void
     */
    public function testGetRolesAsString(): void
    {
        $user = new User();
        $role1 = $this->createRole();
        $role2 = $this->createRole(self::USER_ROLE_NAME_GUEST);
        $user->addRole($role1);
        $user->addRole($role2);
        $this->assertEquals('Administrator, Guest', $user->getRolesAsString());
    }
}
