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

echo 'mysql commands:'
mysql -uroot -proot -e  "USE laminas_mvc_demo;SET GLOBAL log_bin_trust_function_creators = 1;"
mysql -uroot -proot -e  "USE laminas_mvc_demo_integration;SET GLOBAL log_bin_trust_function_creators = 1;"

echo 'composer install:'
composer install --ignore-platform-reqs

echo 'development-enable:'
composer development-enable

echo 'copy files:'
cp ./config/autoload/local.php.dist ./config/autoload/local.php
cp ./config/autoload/module.doctrine-mongo-odm.local.php.dist ./config/autoload/module.doctrine-mongo-odm.local.php
cp ./config/autoload_test/local.php.dist ./config/autoload_test/local.php
cp ./config/autoload_test/module.doctrine-mongo-odm.local.php.dist ./config/autoload_test/module.doctrine-mongo-odm.local.php
#cp ./config/autoload/laminas-developer-tools.local.php.dist ./config/autoload/laminas-developer-tools.local.php
cp ./config/development.config.php.dist ./config/development.config.php
cp ./public/.htaccess.dist ./public/.htaccess

echo 'chown folders:'
sudo chown -R www-data:www-data ./data/cache
sudo chown -R www-data:www-data ./data/cache_test
sudo chown -R www-data:www-data ./data/logs
sudo chown -R www-data:www-data ./data/DoctrineModule/cache

echo 'chmod folders:'
sudo chmod -R 777 ./data/cache
sudo chmod -R 777 ./data/cache_test
sudo chmod -R 777 ./data/logs
sudo chmod -R 777 ./data/DoctrineModule/cache
sudo chmod -R 777 ./data/DoctrineMongoODMModule
sudo chmod -R 777 ./public/img/captcha

echo 'db-drop:'
composer db-drop

echo 'project-init:'
composer project-init

echo 'db-drop-integration:'
composer db-drop-integration

echo 'project-init-integration:'
composer project-init-integration

echo 'project-check-all:'
composer project-check-all