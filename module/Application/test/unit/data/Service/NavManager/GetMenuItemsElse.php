<?php
/**
 * This file is part of the Simple Web Demo Free Lottery Management Application.
 *
 * This project is no longer maintained.
 * The project is written in Zend Framework 3 Release.
 *
 * @link https://github.com/scorpion3dd
 * @author Denis Puzik <scorpion3dd@gmail.com>
 * @copyright Copyright (c) 2020-2021 scorpion3dd
 */

declare(strict_types=1);

return [
    0 => [
        'id' => 'home',
        'label' => 'Home',
        'link' => '/'
    ],
    1 => [
        'id' => 'about',
        'label' => 'About',
        'link' => '/about'
    ],
    2 => [
        'id' => 'language',
        'label' => 'Language',
        'icon' => 'glyphicon-globe',
        'float' => 'right',
        'dropdown' => [
            0 => [
                'id' => 'en',
                'label' => 'English (EN)',
                'link' => '/application/language/en_US'
            ],
            1 => [
                'id' => 'ru',
                'label' => 'Russian (RU)',
                'link' => '/application/language/ru_RU'
            ],
            2 => [
                'id' => 'es',
                'label' => 'Spanish (ES)',
                'link' => '/application/language/es_ES'
            ]
        ]
    ],
    3 => [
        'id' => 'admin',
        'label' => 'Admin',
        'dropdown' => [
            0 => [
                'id' => 'users',
                'label' => 'Manage Users',
                'link' => '/users'
            ],
            1 => [
                'id' => 'permissions',
                'label' => 'Manage Permissions',
                'link' => '/permissions'
            ],
            2 => [
                'id' => 'roles',
                'label' => 'Manage Roles',
                'link' => '/roles'
            ],
            3 => [
                'id' => 'consumer',
                'label' => 'Consumer',
                'link' => '/consumer'
            ],
            4 => [
                'id' => 'logs',
                'label' => 'Logs',
                'link' => '/logs'
            ],
        ]
    ],
    4 => [
        'id' => 'logout',
        'label' => 'admin@example.com',
        'float' => 'right',
        'dropdown' => [
            0 => [
                'id' => 'settings',
                'label' => 'Settings',
                'link' => '/application/settings'
            ],
            1 => [
                'id' => 'logout',
                'label' => 'Sign out',
                'link' => '/logout'
            ]
        ]
    ],
];
