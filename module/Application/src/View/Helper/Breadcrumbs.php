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

use Laminas\View\Helper\AbstractHelper;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\Renderer\PhpRenderer;

/**
 * This view helper class displays breadcrumbs
 * Class Breadcrumbs
 * @package Application\View\Helper
 */
class Breadcrumbs extends AbstractHelper
{
    /** @var array $items */
    private array $items = [];

    /**
     * Breadcrumbs constructor
     * @param array $items - Array of items (optional)
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * @param array $items - Menu items
     *
     * @return $this
     */
    public function setItems(array $items): Breadcrumbs
    {
        $this->items = $items;

        return $this;
    }

    /**
     * @return string - HTML code of the breadcrumbs
     */
    public function render(): string
    {
        if (count($this->items) == 0) {
            return '';
        }
        $result = '<ol class="breadcrumb">';
        $itemCount = count($this->items);
        $itemNum = 1;
        foreach ($this->items as $label => $link) {
            $isActive = ($itemNum == $itemCount);
            $result .= $this->renderItem($label, $link, $isActive);
            $itemNum++;
        }
        $result .= '</ol>';

        return $result;
    }

    /**
     * @param string $label
     * @param string $link
     * @param bool $isActive
     *
     * @return string HTML code of the item.
     */
    protected function renderItem(string $label, string $link, bool $isActive): string
    {
        /** @var PhpRenderer $view */
        $view = $this->getView();
        /** @var EscapeHtml $escapeHtml */
        $escapeHtml = $view->plugin('escapeHtml');
        $result = $isActive ? '<li class="active">' : '<li>';
        if (! $isActive) {
            $result .= '<a href="' . $escapeHtml($link) . '">' . $escapeHtml($label) . '</a>';
        } else {
            $result .= $escapeHtml($label);
        }
        $result .= '</li>';

        return $result;
    }
}
