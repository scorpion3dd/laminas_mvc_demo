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

namespace Application\View\Helper;

use Laminas\Mvc\I18n\Translator;
use Laminas\View\Helper\AbstractHelper;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\Renderer\PhpRenderer;

/**
 * This view helper class displays a menu bar
 * Class Menu
 * @package Application\View\Helper
 */
class Menu extends AbstractHelper
{
    /** @var Translator $translator */
    protected Translator $translator;

    /** @var array $items */
    protected array $items = [];

    /** @var string $activeItemId */
    protected string $activeItemId = '';

    /**
     * Menu constructor
     * @param Translator $translator
     * @param array $items - Menu items
     */
    public function __construct(Translator $translator, array $items = [])
    {
        $this->translator = $translator;
        $this->items = $items;
    }

    /**
     * @param array $items - Menu items
     *
     * @return $this
     */
    public function setItems(array $items): Menu
    {
        $this->items = $items;

        return $this;
    }

    /**
     * @param string $activeItemId
     *
     * @return $this
     */
    public function setActiveItemId(string $activeItemId): Menu
    {
        $this->activeItemId = $activeItemId;

        return $this;
    }

    /**
     * @return string - HTML code of the menu
     */
    public function render(): string
    {
        if (count($this->items) == 0) {
            return '';
        }
        $result = '<nav class="navbar navbar-default" role="navigation">';
        $result .= '<div class="navbar-header">';
        $result .= '<button type="button" class="navbar-toggle" data-toggle="collapse" ';
        $result .= 'data-target=".navbar-ex1-collapse">';
        $result .= '<span class="sr-only">' . $this->translator->translate('Toggle navigation') . '</span>';
        $result .= '<span class="icon-bar"></span>';
        $result .= '<span class="icon-bar"></span>';
        $result .= '<span class="icon-bar"></span>';
        $result .= '</button>';
        $result .= '</div>';
        $result .= '<div class="collapse navbar-collapse navbar-ex1-collapse">';
        $result .= '<ul class="nav navbar-nav">';
        foreach ($this->items as $item) {
            if (! isset($item['float']) || $item['float'] == 'left') {
                $result .= $this->renderItem($item);
            }
        }
        $result .= '</ul>';
        $result .= '<ul class="nav navbar-nav navbar-right">';
        foreach ($this->items as $item) {
            if (isset($item['float']) && $item['float'] == 'right') {
                $result .= $this->renderItem($item);
            }
        }
        $result .= '</ul>';
        $result .= '</div>';
        $result .= '</nav>';

        return $result;
    }

    /**
     * @param array $item - The menu item info
     * @return string - HTML code of the item
     */
    protected function renderItem(array $item): string
    {
        $id = isset($item['id']) ? $item['id'] : '';
        $isActive = ($id == $this->activeItemId);
        $label = isset($item['label']) ? $item['label'] : '';
        $result = '';
        /** @var PhpRenderer $view */
        $view = $this->getView();
        /** @var EscapeHtml $escapeHtml */
        $escapeHtml = $view->plugin('escapeHtml');
        if (isset($item['dropdown'])) {
            $dropdownItems = $item['dropdown'];
            $result .= '<li class="dropdown ' . ($isActive ? 'active' : '') . '">';
            $result .= '<a href="#" class="dropdown-toggle" data-toggle="dropdown">';
            $result .= $escapeHtml($label) . ' <b class="caret"></b>';
            $result .= '</a>';
            $result .= '<ul class="dropdown-menu">';
            foreach ($dropdownItems as $item) {
                $link = isset($item['link']) ? $item['link'] : '#';
                $label = isset($item['label']) ? $item['label'] : '';
                $result .= '<li>';
                $result .= '<a href="'.$escapeHtml($link).'">'.$escapeHtml($label).'</a>';
                $result .= '</li>';
            }
            $result .= '</ul>';
            $result .= '</li>';
        } else {
            $link = isset($item['link']) ? $item['link'] : '#';
            $result .= $isActive ? '<li class="active">' : '<li>';
            $result .= '<a href="' . $escapeHtml($link) . '">' . $escapeHtml($label) . '</a>';
            $result .= '</li>';
        }

        return $result;
    }
}
