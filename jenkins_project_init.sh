#!/bin/bash

# This is a build configuration for PHP.
#
# This file is part of the Simple Web Demo Free Lottery Management Application.
#
# This project is no longer maintained.
# The project is written in Laminas Framework Release.
#
# @link https://github.com/scorpion3dd
# @author Denis Puzik <scorpion3dd@gmail.com>
# @copyright Copyright (c) 2021-2022 scorpion3dd

echo 'copy files:'
cp ./config/autoload/local.php.dist ./config/autoload/local.php
cp ./config/autoload/module.doctrine-mongo-odm.local.php.dist ./config/autoload/module.doctrine-mongo-odm.local.php
cp ./config/autoload_test/local.php.dist ./config/autoload_test/local.php
cp ./config/autoload_test/module.doctrine-mongo-odm.local.php.dist ./config/autoload_test/module.doctrine-mongo-odm.local.php
#cp ./config/autoload/laminas-developer-tools.local.php.dist ./config/autoload/laminas-developer-tools.local.php
cp ./config/development.config.php.dist ./config/development.config.php

echo 'chown folders:'
chown -R www-data:www-data ./data/cache
chown -R www-data:www-data ./data/cache_test
chown -R www-data:www-data ./data/logs
chown -R www-data:www-data ./data/DoctrineModule/cache

echo 'chmod folders:'
chmod -R 777 ./data/cache
chmod -R 777 ./data/cache_test
chmod -R 777 ./data/logs
chmod -R 777 ./data/DoctrineModule/cache
chmod -R 777 ./data/DoctrineMongoODMModule
chmod -R 777 ./public/img/captcha