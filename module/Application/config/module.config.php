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

namespace Application;

use Application\Controller\Factory\IndexControllerFactory;
use Application\Controller\Factory\LogsControllerFactory;
use Application\Controller\IndexController;
use Application\Controller\LogsController;
use Application\Service\Factory\LogServiceFactory;
use Application\Service\Factory\MailSenderFactory;
use Application\Service\Factory\NavManagerFactory;
use Application\Service\Factory\RbacAssertionManagerFactory;
use Application\Service\LogService;
use Application\Service\MailSender;
use Application\Service\NavManager;
use Application\Service\RbacAssertionManager;
use Application\View\Helper\Breadcrumbs;
use Application\View\Helper\Factory\MenuFactory;
use Application\View\Helper\Menu;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Laminas\Mail\Transport\Sendmail;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
        'routes' => [
            'home' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+'
                    ],
                    'defaults' => [
                        'controller' => IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'application' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/application[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9_-]+'
                    ],
                    'defaults' => [
                        'controller'    => IndexController::class,
                        'action'        => 'index',
                    ],
                ],
            ],
            'about' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/about',
                    'defaults' => [
                        'controller' => IndexController::class,
                        'action'     => 'about',
                    ],
                ],
            ],
            'logs' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/logs[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller'    => LogsController::class,
                        'action'        => 'index',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            IndexController::class => IndexControllerFactory::class,
            LogsController::class => LogsControllerFactory::class,
        ],
    ],
    // The 'access_filter' key is used by the User module to restrict or permit
    // access to certain controller actions for unauthorized visitors.
    'access_filter' => [
        'options' => [
            // The access filter can work in 'restrictive' (recommended) or 'permissive'
            // mode. In restrictive mode all controller actions must be explicitly listed
            // under the 'access_filter' config key, and access is denied to any not listed
            // action for not logged in users. In permissive mode, if an action is not listed
            // under the 'access_filter' key, access to it is permitted to anyone (even for
            // not logged in users. Restrictive mode is more secure and recommended to use.
            'mode' => 'restrictive'
        ],
        'controllers' => [
            IndexController::class => [
                // Allow anyone to visit "index", "view", "language" and "about" actions
                ['actions' => ['index', 'about', 'view', 'language'], 'allow' => '*'],
                // Allow authorized users to visit "settings" action
                ['actions' => ['settings'], 'allow' => '@']
            ],
            LogsController::class => [
                // Allow authorized users to visit actions
                ['actions' => '*', 'allow' => '+user.manage']
            ],
        ]
    ],
    // This key stores configuration for RBAC manager.
    'rbac_manager' => [
        'assertions' => [RbacAssertionManager::class],
    ],
    'service_manager' => [
        'factories' => [
            LogService::class => LogServiceFactory::class,
            MailSender::class => MailSenderFactory::class,
            Sendmail::class => InvokableFactory::class,
            NavManager::class => NavManagerFactory::class,
            RbacAssertionManager::class => RbacAssertionManagerFactory::class,
        ],
    ],
    'view_helpers' => [
        'factories' => [
            Menu::class => MenuFactory::class,
            Breadcrumbs::class => InvokableFactory::class,
        ],
        'aliases' => [
            'mainMenu' => Menu::class,
            'pageBreadcrumbs' => Breadcrumbs::class,
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    // The following key allows to define custom styling for FlashMessenger view helper.
    'view_helper_config' => [
        'flashmessenger' => [
            'message_open_format'      => '<div%s><ul><li>',
            'message_close_string'     => '</li></ul></div>',
            'message_separator_string' => '</li><li>'
        ]
    ],
    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_orm_driver' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/Entity']
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_orm_driver'
                ]
            ],

            __NAMESPACE__ . '_odm_driver' => [
                'class' => \Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/../src/Document'
                ],
            ],
            'odm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Document' => __NAMESPACE__ . '_odm_driver',
                ],
            ],
        ]
    ],
];
