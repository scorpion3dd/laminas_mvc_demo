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

return [
    0 => [
        'id' => 'home',
        'label' => 'Домашняя',
        'link' => '/',
    ],
    1 => [
        'id' => 'about',
        'label' => 'О нас',
        'link' => '/about',
    ],
    2 => [
        'id' => 'language',
        'label' => 'Язык',
        'icon' => 'glyphicon-globe',
        'float' => 'right',
        'dropdown' => [
            0 => [
                'id' => 'en',
                'label' => 'Английский (EN)',
                'link' => '/application/language/en_US',
            ],
            1 => [
                'id' => 'ru',
                'label' => 'Русский (RU)',
                'link' => '/application/language/ru_RU',
            ],
            2 => [
                'id' => 'es',
                'label' => 'Испанский (ES)',
                'link' => '/application/language/es_ES',
            ]
        ],
    ]
];
