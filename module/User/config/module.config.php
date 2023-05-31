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

namespace User;

use User\Controller\AuthController;
use User\Controller\ConsumerController;
use User\Controller\Factory\AuthControllerFactory;
use User\Controller\Factory\ConsumerControllerFactory;
use User\Controller\Factory\PermissionControllerFactory;
use User\Controller\Factory\RoleControllerFactory;
use User\Controller\Factory\UserControllerFactory;
use User\Controller\PermissionController;
use User\Controller\Plugin\AccessPlugin;
use User\Controller\Plugin\CurrentUserPlugin;
use User\Controller\Plugin\Factory\AccessPluginFactory;
use User\Controller\Plugin\Factory\CurrentUserPluginFactory;
use User\Controller\RoleController;
use User\Controller\UserController;
use User\Kafka\ConsumerKafka;
use User\Kafka\Factory\ConsumerKafkaFactory;
use User\Service\AuthAdapter;
use User\Service\AuthManager;
use User\Service\Factory\AuthAdapterFactory;
use User\Service\Factory\AuthenticationServiceFactory;
use User\Service\Factory\AuthManagerFactory;
use User\Service\Factory\PermissionManagerFactory;
use User\Service\Factory\RbacManagerFactory;
use User\Service\Factory\RoleManagerFactory;
use User\Service\Factory\UserManagerFactory;
use User\Service\PermissionManager;
use User\Service\RbacManager;
use User\Service\RoleManager;
use User\Service\UserManager;
use User\View\Helper\Access;
use User\View\Helper\CurrentUser;
use User\View\Helper\Factory\AccessFactory;
use User\View\Helper\Factory\CurrentUserFactory;
use Laminas\Authentication\AuthenticationService;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

return [
    'router' => [
        'routes' => [
            'login' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/login',
                    'defaults' => [
                        'controller' => AuthController::class,
                        'action'     => 'login',
                    ],
                ],
            ],
            'logout' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/logout',
                    'defaults' => [
                        'controller' => AuthController::class,
                        'action'     => 'logout',
                    ],
                ],
            ],
            'not-authorized' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/not-authorized',
                    'defaults' => [
                        'controller' => AuthController::class,
                        'action'     => 'notAuthorized',
                    ],
                ],
            ],
            'reset-password' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/reset-password',
                    'defaults' => [
                        'controller' => UserController::class,
                        'action'     => 'resetPassword',
                    ],
                ],
            ],
            'set-password' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/set-password',
                    'defaults' => [
                        'controller' => UserController::class,
                        'action'     => 'setPassword',
                    ],
                ],
            ],
            'users' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/users[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller'    => UserController::class,
                        'action'        => 'index',
                    ],
                ],
            ],
            'roles' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/roles[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller'    => RoleController::class,
                        'action'        => 'index',
                    ],
                ],
            ],
            'permissions' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/permissions[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller'    => PermissionController::class,
                        'action'        => 'index',
                    ],
                ],
            ],
            'consumer' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/consumer',
                    'defaults' => [
                        'controller'    => ConsumerController::class,
                        'action'        => 'index',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            AuthController::class => AuthControllerFactory::class,
            PermissionController::class => PermissionControllerFactory::class,
            RoleController::class => RoleControllerFactory::class,
            UserController::class => UserControllerFactory::class,
            ConsumerController::class => ConsumerControllerFactory::class,
        ],
    ],
    'controller_plugins' => [
        'factories' => [
            AccessPlugin::class => AccessPluginFactory::class,
            CurrentUserPlugin::class => CurrentUserPluginFactory::class,
        ],
        'aliases' => [
            'access' => AccessPlugin::class,
            'currentUser' => CurrentUserPlugin::class,
        ],
    ],
    // The 'access_filter' key is used by the User module to restrict or permit
    // access to certain controller actions for unauthorized visitors.
    'access_filter' => [
        'controllers' => [
            ConsumerController::class => [
                // Give access to all actions to anyone.
                ['actions' => '*', 'allow' => '+user.manage'],
            ],
            UserController::class => [
                // Give access to "resetPassword", "message" and "setPassword" actions
                // to anyone.
                ['actions' => ['resetPassword', 'message', 'setPassword'], 'allow' => '*'],
                // Give access to "index", "add", "edit", "view", "changePassword" actions to users having the "user.manage" permission.
                ['actions' => ['index', 'add', 'edit', 'view', 'changePassword'], 'allow' => '+user.manage']
            ],
            RoleController::class => [
                // Allow access to authenticated users having the "role.manage" permission.
                ['actions' => '*', 'allow' => '+role.manage']
            ],
            PermissionController::class => [
                // Allow access to authenticated users having "permission.manage" permission.
                ['actions' => '*', 'allow' => '+permission.manage']
            ],
        ]
    ],
    'service_manager' => [
        'factories' => [
            AuthenticationService::class => AuthenticationServiceFactory::class,
            AuthAdapter::class => AuthAdapterFactory::class,
            AuthManager::class => AuthManagerFactory::class,
            PermissionManager::class => PermissionManagerFactory::class,
            RbacManager::class => RbacManagerFactory::class,
            RoleManager::class => RoleManagerFactory::class,
            UserManager::class => UserManagerFactory::class,
            ConsumerKafka::class => ConsumerKafkaFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'view_helpers' => [
        'factories' => [
            Access::class => AccessFactory::class,
            CurrentUser::class => CurrentUserFactory::class,
        ],
        'aliases' => [
            'access' => Access::class,
            'currentUser' => CurrentUser::class,
        ],
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
