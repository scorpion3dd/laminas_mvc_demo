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

/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

use Application\Command\CommandDbDrop;
use Application\Command\CommandDbMigrations;
use Application\Command\CommandExamplesWrite;
use Application\Command\CommandDbLoadingFixtures;
use Application\Command\Factory\CommandDbDropFactory;
use Application\Command\Factory\CommandDbMigrationsFactory;
use Application\Command\Factory\CommandExamplesWriteFactory;
use Application\Command\Factory\CommandDbLoadingFixturesFactory;
use Laminas\Cache\Storage\Adapter\Filesystem;
use Laminas\Log\Logger;
use Laminas\Session\Storage\SessionArrayStorage;
use MongoDB\Driver\Manager;

return [
    'session_config' => [
//        'cookie_lifetime'     => 60 * 60 * 1, // Session cookie will expire in 1 hour
//        'gc_maxlifetime'      => 60 * 60 * 24 * 30, // How long to store session data on server (for 1 month)
    ],
    'session_storage' => [
        'type' => SessionArrayStorage::class
    ],
    'session_containers' => [
        'Demo_Auth',
        'I18nSessionContainer',
    ],
    'caches' => [
        'FilesystemCache' => [
            'adapter' => Filesystem::class,
            'options' => [
                // Store cached data in this directory.
                'cache_dir' => __DIR__ . '/../../data/cache',
                // Store cached data for 1 milliseconds require for tests
                'ttl' => 1
            ],
            'plugins' => [
                [
                    'name' => 'serializer',
                    'options' => []
                ]
            ]
        ]
    ],
    'service_manager' => [
        'factories' => [
            'Laminas\Db\Adapter\Adapter' => 'Laminas\Db\Adapter\AdapterServiceFactory',
            CommandDbDrop::class => CommandDbDropFactory::class,
            CommandDbMigrations::class => CommandDbMigrationsFactory::class,
            CommandDbLoadingFixtures::class => CommandDbLoadingFixturesFactory::class,
            CommandExamplesWrite::class => CommandExamplesWriteFactory::class,
        ],
    ],
    'log' => [
        'LoggerGlobal' => [
            'writers' => [
                'dbwriter' => [
                    'name' => 'mongodb',  // change it to 'name' => 'noop' to disable mongodb logging
                    'options' => [
                        'manager' => new Manager('mongodb://'
                            . getenv('MONGO_CONNECT_USER')
                            . ':'
                            . getenv('MONGO_CONNECT_PASSWORD')
                            . '@'
                            . getenv('MONGO_HOST')
                            . ':'
                            . getenv('MONGO_PORT')
                            . '/'
                            . getenv('MONGO_CONNECT_DB')
                            . '?directConnection=true'),
                        'collection'   => getenv('MONGO_CONNECT_COLLECTION'),
                        'database'     => getenv('MONGO_CONNECT_DB'),
                        'formatter'    => 'db',

                    ],
                ],
                'filewriter' => [
                    'name' => 'stream',
                    'priority' => Logger::DEBUG,
                    'options' => [
                        'stream' => __DIR__ . '/../../data/logs/logfile_tests.log',
                        'filters' => [
                            'priority' => [
                                'name' => 'priority',
                                'options' => [
                                    'operator' => '<=',
                                    'priority' => Logger::DEBUG,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'laminas-cli' => [
        'commands' => [
            'app:db:migrations-migrate-integration' => CommandDbMigrations::class,
            'app:db:drop' => CommandDbDrop::class,
            'app:db:loading-fixtures' => CommandDbLoadingFixtures::class,
            'app:examples-write' => CommandExamplesWrite::class,
        ],
    ],
];
