#  This file is part of the Simple demo web-project with REST Full API for Mobile.
#
#  This project is no longer maintained.
#  The project is written in Laminas Framework Release.
#
#  @link https://github.com/scorpion3dd
#  @copyright Copyright (c) 2016-2021 Denis Puzik <scorpion3dd@gmail.com>

SET NAMES 'utf8';
USE `laminas_mvc_demo`;

DROP TABLE IF EXISTS `role_hierarchy`;
DROP TABLE IF EXISTS `role_permission`;
DROP TABLE IF EXISTS `user_role`;
DROP TABLE IF EXISTS `permission`;
DROP TABLE IF EXISTS `role`;
DROP TABLE IF EXISTS `user`;
DROP TABLE IF EXISTS `user_log`;
DROP TABLE IF EXISTS `user_role_log`;
DROP TABLE IF EXISTS `user_archives`;
DROP TABLE IF EXISTS `migrations`;
DROP FUNCTION IF EXISTS `randomInt`;
DROP PROCEDURE IF EXISTS `setUsersAccesses`;
DROP PROCEDURE IF EXISTS `setUsersNotAccesses`;
DROP PROCEDURE IF EXISTS `setUsersArchives`;
DROP PROCEDURE IF EXISTS `moveUsersArchives`;
DROP EVENT IF EXISTS `setAccesses`;
DROP EVENT IF EXISTS `setNotAccesses`;
DROP EVENT IF EXISTS `setArchives`;
DROP EVENT IF EXISTS `moveArchives`;