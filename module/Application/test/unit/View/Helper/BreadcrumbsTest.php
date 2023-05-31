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

use Application\View\Helper\Breadcrumbs;
use ApplicationTest\AbstractMock;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Laminas\View\Renderer\PhpRenderer;

/**
 * Class BreadcrumbsTest - Unit tests for Breadcrumbs
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package ApplicationTest\unit\View\Helper
 */
class BreadcrumbsTest extends AbstractMock
{
    /** @var Breadcrumbs $breadcrumbs */
    protected Breadcrumbs $breadcrumbs;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->breadcrumbs = new Breadcrumbs();
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
        $result = $this->breadcrumbs->render();
        $this->assertEquals($expect, $result);
    }

    /**
     * @testCase - method render - must be a success
     *
     * @return void
     */
    public function testRender(): void
    {
        $expect = '<ol class="breadcrumb"><li><a href="/">Домашняя</a></li><li><a href="Домашняя">Логи</a>'
            . '</li><li class="active">Просмотр данных лога</li></ol>';
        $items = [
            'Домашняя' => '/',
            'Логи' => 'Домашняя',
            'Просмотр данных лога' => '/logs/view/63e432930ae8bd25d70520a1',
        ];
        $view = new PhpRenderer();
        $result = $this->breadcrumbs->setView($view)->setItems($items)->render();
        $this->assertEquals($expect, $result);
    }
}
