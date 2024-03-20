<?php

/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * NOTE: In practice, this file will typically be INCLUDED in your source
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
use Doctrine\ODM\MongoDB\Configuration;
use Laminas\Log\Logger;
use Laminas\Session\Storage\SessionArrayStorage;
use Laminas\Session\Validator\HttpUserAgent;
use Laminas\Session\Validator\RemoteAddr;
use MongoDB\Driver\Manager;
use Laminas\Cache\Storage\Adapter\Filesystem;

return [
    'session_config' => [
        'cookie_lifetime'     => 60 * 60 * 1, // Session cookie will expire in 1 hour
        'gc_maxlifetime'      => 60 * 60 * 24 * 30, // How long to store session data on server (for 1 month)
    ],
    'session_manager' => [
        // Session validators (used for security)
        'validators' => [
            RemoteAddr::class,
            HttpUserAgent::class,
        ]
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
                'cache_dir' => './data/cache',
                // Store cached data for 1 hour.
                'ttl' => 60 * 60 * 1
            ],
            'plugins' => [
                [
                    'name' => 'serializer',
                    'options' => []
                ]
            ]
        ]
    ],
    'doctrine' => [
        'migrations_configuration' => [
            'orm_default' => [
                'directory' => 'data/Migrations',
                'name'      => 'Doctrine Database Migrations',
                'namespace' => 'Migrations',
                'table'     => 'migrations',
                'column' => 'version',
                'custom_template' => null,
            ],
        ],
        'configuration' => [
            'odm_default' => [
                'metadata_cache'     => 'array',
                'driver'             => 'odm_default',

                'generate_proxies'   => true,
                'proxy_dir'          => 'data/DoctrineMongoODMModule/Proxy',
                'proxy_namespace'    => 'DoctrineMongoODMModule\Proxy',

                'generate_hydrators' => true,
                'hydrator_dir'       => 'data/DoctrineMongoODMModule/Hydrator',
                'hydrator_namespace' => 'DoctrineMongoODMModule\Hydrator',

                'generate_persistent_collections' => Configuration::AUTOGENERATE_ALWAYS,
                'persistent_collection_dir' => 'data/DoctrineMongoODMModule/PersistentCollection',
                'persistent_collection_namespace' => 'DoctrineMongoODMModule\PersistentCollection',
                'persistent_collection_factory' => null,
                'persistent_collection_generator' => null,

                'default_db'         => null,

                'filters'            => ['filterName' => 'BSON\Filter\Class'],

                'logger'             => 'DoctrineMongoODMModule\Logging\DebugStack'
            ]
        ],
        'driver' => [
            'odm_default' => [
//                'drivers' => []
            ]
        ],
        'documentmanager' => [
            'odm_default' => [
                'connection'    => 'odm_default',
                'configuration' => 'odm_default',
                'eventmanager' => 'odm_default'
            ]
        ],
        'eventmanager' => [
            'odm_default' => [
                'subscribers' => []
            ]
        ]
    ],
    'log' => [
        'LoggerGlobal' => [
            'writers' => [
                'dbwriter' => [
//                    'name' => 'mongodb',  // change it to 'name' => 'noop' to disable mongodb logging
                    'name' => 'noop',  // change it to 'name' => 'noop' to disable mongodb logging
                    'options' => [
//                        'manager' => new Manager('mongodb://127.0.0.1:27017'),
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
                    'options' => [
                        'stream' => __DIR__ . '/../../data/logs/logfile.log',
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
    'redis' => [
        'connection' => [
            'default' => [
                'params' => [
                    'host'     => getenv('REDIS_HOST'),
                ]
            ],
        ],
    ],
    'kafka' => [
        'filelog' => 'logfileKafka.log',
        'connection' => [
            'default' => [
                'params' => [
                    'host'     => getenv('KAFKA_HOST'),
                    'port'     => getenv('KAFKA_PORT'),
                    'brokerVersion'     => getenv('KAFKA_BROKER_VERSION'),
                ]
            ],
        ],
    ],
    'service_manager' => [
        'factories' => [
            CommandDbMigrations::class => CommandDbMigrationsFactory::class,
            CommandDbDrop::class => CommandDbDropFactory::class,
            CommandDbLoadingFixtures::class => CommandDbLoadingFixturesFactory::class,
            CommandExamplesWrite::class => CommandExamplesWriteFactory::class,
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
