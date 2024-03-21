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

/**
 * Class IndexControllerIntegrationTest - Integration tests for IndexController
 * with connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are real
 *
 * @package ApplicationTest\integration\Controller
 */
class IndexControllerIntegrationTest extends AbstractMock
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
     * @testCase - route index action must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testIndexAction(): void
    {
        $this->dispatch(self::ROUTE_URL, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_HOME);
        $response = $this->getResponse()->getContent();
        self::assertTrue($this->assertHTML($response, false));
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route view action must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testViewAction(): void
    {
        $this->dispatch(self::ROUTE_URL . 'application/view/' . self::USER_ID, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_APPLICATION);
        $response = $this->getResponse()->getContent();
        self::assertTrue($this->assertHTML($response, false));
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route about action must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testAboutAction(): void
    {
        $this->dispatch(self::ROUTE_URL . 'application/about', self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_APPLICATION);
        $response = $this->getResponse()->getContent();
        self::assertTrue($this->assertHTML($response, false));
        $expected = file_get_contents(
            __DIR__ . '/../data/Controller/Index/GetAboutAction.html'
        );
        self::assertStringStartsWith($this->trim($expected), $this->trim($response));
    }

    /**
     * @testCase - route about action must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testSettingsAction(): void
    {
        $this->setAuth();
        $this->dispatch(self::ROUTE_URL . 'application/settings/' . self::USER_ID, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_200);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_APPLICATION);
        $response = $this->getResponse()->getContent();
        self::assertTrue($this->assertHTML($response, false));
        self::assertStringStartsWith(self::HTML_START_WITH, $this->trim($response));
    }

    /**
     * @testCase - route language action - can be redirected to route "/application/settings"
     *
     * @return void
     * @throws Exception
     */
    public function testLanguageAction(): void
    {
        $_SERVER['HTTP_REFERER'] = 'http://zf3.os/application/settings';

        $this->dispatch(self::ROUTE_URL . 'application/language/ru_RU', self::METHOD_GET);
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
     * @testCase - route language action - redirect url empty can be redirected to route "home"
     *
     * @return void
     * @throws Exception
     */
    public function testLanguageActionRedirectUrlEmpty(): void
    {
        $_SERVER['HTTP_REFERER'] = '';

        $this->dispatch(self::ROUTE_URL . 'application/language/ru_RU', self::METHOD_GET);
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
