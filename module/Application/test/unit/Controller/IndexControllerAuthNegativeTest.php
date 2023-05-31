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

namespace ApplicationTest\unit\Controller;

use Application\Controller\IndexController;
use ApplicationTest\AbstractMock;
use Exception;
use User\Service\AuthManager;

/**
 * Class IndexControllerAuthNegativeTest - Unit negative tests for IndexController in Auth
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package ApplicationTest\unit\Controller
 */
class IndexControllerAuthNegativeTest extends AbstractMock
{
    public const MODULE_NAME = 'application';
    public const CONTROLLER_NAME = IndexController::class;
    public const CONTROLLER_CLASS = 'IndexController';
    public const ROUTE_URL = '/';
    public const ROUTE_HOME = 'home';
    public const ROUTE_APPLICATION = 'application';

    /**
     * @testCase - route settings action - AUTH_REQUIRED - redirect to login route
     * User Module onDispatch authManager->filterAccess result == AuthManager::AUTH_REQUIRED redirect
     *
     * @return void
     * @throws Exception
     */
    public function testSettingsActionRedirectToLogin(): void
    {
        $this->dispatch(self::ROUTE_URL . 'application/settings', self::METHOD_GET);
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
     *
     * @return void
     * @throws Exception
     */
    public function testSettingsActionUserRedirectToNotAauthorized(): void
    {
        $this->setAuthMock(
            self::CONTROLLER_NAME,
            'settings',
            AuthManager::ACCESS_DENIED
        );

        $this->dispatch(self::ROUTE_URL . 'application/settings', self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_302);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_APPLICATION);
        $response = $this->getResponse()->getContent();
        $expected = '';
        self::assertSame($this->trim($expected), $this->trim($response));
    }
}
