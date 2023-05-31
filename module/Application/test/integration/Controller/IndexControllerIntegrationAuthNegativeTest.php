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

namespace ApplicationTest\integration\Controller;

use Application\Controller\IndexController;
use ApplicationTest\AbstractMock;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\Controller\UserController;
use User\Entity\User;

/**
 * Class IndexControllerIntegrationAuthNegativeTest - Integration negative tests for IndexController in Auth
 * with connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are real
 *
 * @package ApplicationTest\integration\Controller
 */
class IndexControllerIntegrationAuthNegativeTest extends AbstractMock
{
    public const MODULE_NAME = 'application';
    public const CONTROLLER_NAME = IndexController::class;
    public const CONTROLLER_CLASS = 'IndexController';
    public const ROUTE_URL = '/';
    public const ROUTE_HOME = 'home';
    public const ROUTE_APPLICATION = 'application';

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        $this->setTypeTest(self::TYPE_TEST_FUNCTIONAL);
        parent::setUp();
    }

    /**
     * @testCase - route settings action - AUTH_REQUIRED - redirect to login route
     * User Module onDispatch authManager->filterAccess result == AuthManager::AUTH_REQUIRED redirect
     *
     * @return void
     * @throws Exception
     */
    public function testSettingsActionRedirectToLogin(): void
    {
        $this->dispatch(self::ROUTE_URL . 'application/settings/' . self::USER_ID, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_302);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_APPLICATION);
        $response = $this->getResponse()->getContent();
        $expected = '';
        self::assertSame($this->trim($expected), $this->trim($response));
    }

    /**
     * @testCase - route settings action - User ACCESS_DENIED - redirect to not-authorized route
     * User Module onDispatch authManager->filterAccess result == AuthManager::ACCESS_DENIED redirect
     * authorization one user, by userId = 3 with role = Guest,
     * but view route /users/view/5 other user, with userId = 5, which views only users with role = Administrator
     *
     * @return void
     * @throws Exception
     */
    public function testSettingsActionUserRedirectToNotAauthorized(): void
    {
//        self::markTestSkipped('skiped');
        $userId = 3;
        $this->prepareSessionContainer();
        /** @phpstan-ignore-next-line */
        $this->sessionContainer->member = 'session';
        /** @phpstan-ignore-next-line */
        $this->sessionContainer->user_id = $userId;
        $this->prepareDbMySqlIntegration();
        /** @var User|null $user */
        $user = $this->entityManagerIntegration->getRepository(User::class)->findOneBy(['id' => $userId]);
        $response = 'error';
        if (! empty($user)) {
            /** @phpstan-ignore-next-line */
            $this->sessionContainer->session = $user->getEmail();
            $this->dispatch(self::ROUTE_URL . 'users/view/' . self::USER_ID, self::METHOD_GET);
            $this->assertResponseStatusCode(self::STATUS_CODE_302);
            $this->assertModuleName('user');
            $this->assertControllerName(UserController::class);
            $this->assertControllerClass('usercontroller');
            $this->assertMatchedRouteName('users');
            $response = $this->getResponse()->getContent();
        }
        $expected = '';
        self::assertSame($this->trim($expected), $this->trim($response));
    }
}
