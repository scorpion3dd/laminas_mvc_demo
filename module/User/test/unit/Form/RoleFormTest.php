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

namespace UserTest\unit\Form;

use ApplicationTest\AbstractMock;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\Form\RoleForm;

/**
 * Class RoleFormTest - Unit tests for RoleForm
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package UserTest\unit\Form
 */
class RoleFormTest extends AbstractMock
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
     * @testCase - new RoleForm - must be a success
     *
     * @return void
     */
    public function testNewRoleForm(): void
    {
        $roleForm = new RoleForm('create', $this->entityManager);
        $this->assertInstanceOf(RoleForm::class, $roleForm);
    }
}
