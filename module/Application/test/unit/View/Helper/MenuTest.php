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

namespace ApplicationTest\unit\View\Helper;

use Application\View\Helper\Menu;
use ApplicationTest\AbstractMock;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Laminas\View\Renderer\PhpRenderer;

/**
 * Class MenuTest - Unit tests for Menu
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package ApplicationTest\unit\View\Helper
 */
class MenuTest extends AbstractMock
{
    /** @var Menu $menu */
    protected Menu $menu;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $translator = $this->serviceManager->get('MvcTranslator');
        $this->menu = new Menu($translator);
    }

    /**
     * @testCase - method render - must be a success
     * empty items
     *
     * @return void
     */
    public function testRenderEmptyItems(): void
    {
        $expect = '';
        $result = $this->menu->render();
        $this->assertEquals($expect, $result);
    }

    /**
     * @testCase - method render - must be a success
     *
     * @return void
     */
    public function testRender(): void
    {
        $expected = include(__DIR__ . '/../../data/View/Helper/Menu/GetRender.php');
        $items = include(__DIR__ . '/../../data/View/Helper/Menu/GetItems.php');
        $view = new PhpRenderer();
        $result = $this->menu->setView($view)->setItems($items)->render();
        $this->assertEquals($expected, $result);
    }
}
