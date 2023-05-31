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

namespace UserTest\integration\Controller;

use ApplicationTest\AbstractMock;
use Doctrine\ORM\ORMException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\Controller\RoleController;
use User\Entity\Role;
use User\Form\RoleForm;

/**
 * Class RoleControllerIntegrationNegativeTest - Integration negative tests for RoleController
 * with connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are real
 *
 * @package UserTest\integration\Controller
 */
class RoleControllerIntegrationNegativeTest extends AbstractMock
{
    public const MODULE_NAME = 'user';
    public const CONTROLLER_NAME = RoleController::class;
    public const CONTROLLER_CLASS = 'RoleController';
    public const ROUTE_URL = '/roles';
    public const ROUTE_USERS = 'roles';

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        $this->setTypeTest(self::TYPE_TEST_FUNCTIONAL);
        parent::setUp();
        $this->setEnvTest();
    }

    /**
     * @testCase - route view action - not id
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testViewActionNotId(): void
    {
        $this->setAuth();
        $this->dispatch(self::ROUTE_URL . '/view/', self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertTrue($this->assertHTML($response, false));
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route view action - empty permission
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testViewActionEmptyPermission(): void
    {
        $this->setAuth();
        $this->dispatch(self::ROUTE_URL . '/view/1000', self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertTrue($this->assertHTML($response, false));
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route add action by method POST - form is not valid
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testAddActionPostFormIsNotValid(): void
    {
        $this->setAuth();
        $this->dispatch(self::ROUTE_URL . '/add', self::METHOD_POST);
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertTrue($this->assertHTML($response, false));
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route edit action by method GET - not id
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testEditActionGetNotId(): void
    {
        $this->setAuth();
        $this->dispatch(self::ROUTE_URL . '/edit/', self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertTrue($this->assertHTML($response, false));
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route edit action by method GET - empty permission
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testEditActionGetEmptyPermission(): void
    {
        $this->setAuth();
        $this->dispatch(self::ROUTE_URL . '/edit/1000', self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertTrue($this->assertHTML($response, false));
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route edit action by method POST - form is not valid
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ORMException
     */
    public function testEditActionPostFormIsNotValid(): void
    {
        $this->setAuth();
        /** @var Role|null $role */
        $role = $this->getRole();
        $response = '';
        if (! empty($role)) {
            $form = new RoleForm('update', $this->entityManager, $role);
            $params = [
                'description' => self::USER_ROLE_DESCRIPTION . ' edited',
                'csrf' => $form->get('csrf')->getValue(),
                'submit' => 'Create',
            ];
            $this->dispatch(self::ROUTE_URL . '/edit/' . $role->getId(), self::METHOD_POST, $params);
            $this->assertResponseStatusCode(self::STATUS_CODE_200);
            $this->assertModuleName(self::MODULE_NAME);
            $this->assertControllerName(self::CONTROLLER_NAME);
            $this->assertControllerClass(self::CONTROLLER_CLASS);
            $this->assertMatchedRouteName(self::ROUTE_USERS);
            $response = $this->getResponse()->getContent();
            self::assertTrue($this->assertHTML($response, false));
        }
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route delete action by method GET - not id
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testDeleteActionGetNotId(): void
    {
        $this->setAuth();
        $this->dispatch(self::ROUTE_URL . '/delete/', self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertTrue($this->assertHTML($response, false));
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route delete action by method GET - empty permission
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testDeleteActionGetEmptyPermission(): void
    {
        $this->setAuth();
        $this->dispatch(self::ROUTE_URL . '/delete/1000', self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertTrue($this->assertHTML($response, false));
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route editPermissions action by method GET - not id
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testEditPermissionsActionGetNotId(): void
    {
        $this->setAuth();
        $this->dispatch(self::ROUTE_URL . '/edit-permissions/', self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertTrue($this->assertHTML($response, false));
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route editPermissions action by method GET - empty role
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testEditPermissionsActionGetEmptyRole(): void
    {
        $this->setAuth();
        $this->dispatch(self::ROUTE_URL . '/edit-permissions/1000', self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_USERS);
        $response = $this->getResponse()->getContent();
        self::assertTrue($this->assertHTML($response, false));
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route editPermissions action by method POST - form is not valid
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ORMException
     */
    public function testEditPermissionsActionPostFormIsNotValid(): void
    {
        $this->setAuth();
        /** @var Role|null $role */
        $role = $this->getRole();
        $response = '';
        if (! empty($role)) {
            $params = [
                'submit' => 'Create',
            ];
            $this->dispatch(self::ROUTE_URL . '/edit-permissions/' . $role->getId(), self::METHOD_POST, $params);
            $this->assertResponseStatusCode(self::STATUS_CODE_200);
            $this->assertModuleName(self::MODULE_NAME);
            $this->assertControllerName(self::CONTROLLER_NAME);
            $this->assertControllerClass(self::CONTROLLER_CLASS);
            $this->assertMatchedRouteName(self::ROUTE_USERS);
            $response = $this->getResponse()->getContent();
            self::assertTrue($this->assertHTML($response, false));
        }
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }
}
