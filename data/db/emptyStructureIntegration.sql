#  This file is part of the Simple demo web-project with REST Full API for Mobile.
#
#  This project is no longer maintained.
#  The project is written in Laminas Framework Release.
#
#  @link https://github.com/scorpion3dd
#  @copyright Copyright (c) 2016-2021 Denis Puzik <scorpion3dd@gmail.com>

SET NAMES 'utf8';
USE `laminas_mvc_demo_integration`;

CREATE TABLE IF NOT EXISTS `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(128) NOT NULL,
  `full_name` varchar(256) NOT NULL,
  `description` varchar(1024) NULL,
  `password` varchar(128) NOT NULL,
  `status` int NOT NULL,
  `access` int NOT NULL,
  `gender` int NOT NULL,
  `date_birthday` datetime NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `pwd_reset_token` varchar(132) DEFAULT NULL,
  `pwd_reset_token_creation_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
)
ENGINE = INNODB,
AUTO_INCREMENT = 2,
AVG_ROW_LENGTH = 16384,
CHARACTER SET utf8mb4,
COLLATE utf8mb4_0900_ai_ci;

ALTER TABLE `user`
ADD UNIQUE INDEX `email_idx` (`email`);



CREATE
    TRIGGER `user_AFTER_INSERT`
    AFTER INSERT ON `user`
    FOR EACH ROW
BEGIN
    DECLARE v_user_id INT DEFAULT 0;
    IF (@SESSION.user_id IS NOT NULL ) THEN
        SET v_user_id = @SESSION.user_id;
    END IF;
    INSERT INTO `user_log`
    (`user_id`,
     `action_user_id`,
     `action`,
     `changed`,
     `date_action`)
    VALUES (NEW.`id`,
            v_user_id,
            1,
            '',
            NOW());
END;

CREATE
    TRIGGER `user_AFTER_UPDATE`
    AFTER UPDATE ON `user`
    FOR EACH ROW
BEGIN
    DECLARE v_user_id INT DEFAULT 0;
    DECLARE v_changed LONGTEXT DEFAULT '';
    IF (NEW.`email` <> OLD.`email` or
        NEW.`full_name` <> OLD.`full_name` or
        NEW.`description` <> OLD.`description` or
        NEW.`password` <> OLD.`password` or
        NEW.`status` <> OLD.`status` or
        NEW.`access` <> OLD.`access` or
        NEW.`gender` <> OLD.`gender` or
        NEW.`date_birthday` <> OLD.`date_birthday` or
        NEW.`date_created` <> OLD.`date_created` or
        NEW.`pwd_reset_token` <> OLD.`pwd_reset_token` or
        NEW.`pwd_reset_token_creation_date` <> OLD.`pwd_reset_token_creation_date`
        )
    THEN
        IF (NEW.`email` <> OLD.`email`) THEN
            SET v_changed = CONCAT(v_changed, 'email = ', NEW.`email`, '; ');
        END IF;
        IF (NEW.`full_name` <> OLD.`full_name`) THEN
            SET v_changed = CONCAT(v_changed, 'full_name = ', NEW.`full_name`, '; ');
        END IF;
        IF (NEW.`description` <> OLD.`description`) THEN
            SET v_changed = CONCAT(v_changed, 'description = ', NEW.`description`, '; ');
        END IF;
        IF (NEW.`status` <> OLD.`status`) THEN
            SET v_changed = CONCAT(v_changed, 'status = ', NEW.`status`, '; ');
        END IF;
        IF (NEW.`access` <> OLD.`access`) THEN
            SET v_changed = CONCAT(v_changed, 'access = ', NEW.`access`, '; ');
        END IF;
        IF (NEW.`gender` <> OLD.`gender`) THEN
            SET v_changed = CONCAT(v_changed, 'gender = ', NEW.`gender`, '; ');
        END IF;
        IF (NEW.`date_birthday` <> OLD.`date_birthday`) THEN
            SET v_changed = CONCAT(v_changed, 'date_birthday = ', NEW.`date_birthday`, '; ');
        END IF;
        IF (NEW.`pwd_reset_token` <> OLD.`pwd_reset_token`) THEN
            SET v_changed = CONCAT(v_changed, 'pwd_reset_token = ', NEW.`pwd_reset_token`, '; ');
        END IF;
        IF (NEW.`pwd_reset_token_creation_date` <> OLD.`pwd_reset_token_creation_date`) THEN
            SET v_changed = CONCAT(v_changed, 'pwd_reset_token_creation_date = ', NEW.`pwd_reset_token_creation_date`, '; ');
        END IF;
        IF (@SESSION.user_id IS NOT NULL ) THEN
            SET v_user_id = @SESSION.user_id;
        END IF;
        INSERT INTO `user_log`
        (`user_id`,
         `action_user_id`,
         `action`,
         `changed`,
         `date_action`)
        VALUES (OLD.`id`,
                v_user_id,
                2,
                v_changed,
                NOW());
    END IF;
END;


CREATE
    TRIGGER `user_BEFORE_DELETE`
    BEFORE DELETE ON `user`
    FOR EACH ROW
BEGIN
    DECLARE v_user_id INT DEFAULT 0;
    DECLARE v_archive INT DEFAULT 3;
    IF (@SESSION.user_id IS NOT NULL) THEN
        SET v_user_id = @SESSION.user_id;
    END IF;
    IF (@SESSION.archive IS NOT NULL) THEN
        SET v_archive = @SESSION.archive;
    END IF;
    INSERT INTO `user_log`
    (`user_id`,
     `action_user_id`,
     `action`,
     `changed`,
     `date_action`)
    VALUES (OLD.`id`,
            v_user_id,
            v_archive,
            '',
            NOW());

    DELETE FROM `user_role`
    WHERE `user_id` = OLD.`id`;
END;



CREATE TABLE IF NOT EXISTS `user_log` (
    `id` int NOT NULL AUTO_INCREMENT,
    `user_id` int NOT NULL,
    `action_user_id` int NOT NULL COMMENT '0 - process generation fixtures, > 0 - user_id (administrators)',
    `action` int NOT NULL COMMENT '1 - Insert, 2 - Update, 3 - Delete, 4 - Archive, 5 - Dis-archive',
    `changed` varchar(1024) NULL COMMENT 'list of fields changed with new value',
    `date_action` datetime NOT NULL,
    PRIMARY KEY (`id`)
)
    ENGINE = INNODB,
    AUTO_INCREMENT = 2,
    AVG_ROW_LENGTH = 16384,
    CHARACTER SET utf8mb4,
    COLLATE utf8mb4_0900_ai_ci;



CREATE TABLE IF NOT EXISTS `user_archives` (
    `id` int NOT NULL AUTO_INCREMENT,
    `email` varchar(128) NOT NULL,
    `full_name` varchar(256) NOT NULL,
    `description` varchar(1024) NULL,
    `password` varchar(128) NOT NULL,
    `status` int NOT NULL,
    `access` int NOT NULL,
    `gender` int NOT NULL,
    `date_birthday` datetime NOT NULL,
    `date_created` datetime NOT NULL,
    `date_updated` datetime NOT NULL,
    `pwd_reset_token` varchar(132) DEFAULT NULL,
    `pwd_reset_token_creation_date` datetime DEFAULT NULL,
    `date_archived` datetime NOT NULL,
    `archived_user_id` int NOT NULL  COMMENT '0 - process automatic, > 0 - user_id (administrators)',
    PRIMARY KEY (`id`)
)
    ENGINE = INNODB,
    AUTO_INCREMENT = 2,
    AVG_ROW_LENGTH = 16384,
    CHARACTER SET utf8mb4,
    COLLATE utf8mb4_0900_ai_ci;

ALTER TABLE `user`
    ADD UNIQUE INDEX `email_idx_archives` (`email`);




CREATE TABLE IF NOT EXISTS `permission` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `description` varchar(1024) NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`)
)
ENGINE = INNODB,
AUTO_INCREMENT = 6,
AVG_ROW_LENGTH = 3276,
CHARACTER SET utf8mb4,
COLLATE utf8mb4_0900_ai_ci;

ALTER TABLE `permission`
ADD UNIQUE INDEX `name_idx` (`name`);


CREATE TABLE IF NOT EXISTS `role` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `description` varchar(1024) NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`)
)
ENGINE = INNODB,
AUTO_INCREMENT = 3,
AVG_ROW_LENGTH = 8192,
CHARACTER SET utf8mb4,
COLLATE utf8mb4_0900_ai_ci;

ALTER TABLE `role`
ADD UNIQUE INDEX `name_idx` (`name`);



CREATE TABLE IF NOT EXISTS `user_role` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL DEFAULT 0,
  `user_archived_id` int NOT NULL DEFAULT 0,
  `role_id` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
ENGINE = INNODB,
AUTO_INCREMENT = 2,
AVG_ROW_LENGTH = 16384,
CHARACTER SET utf8mb4,
COLLATE utf8mb4_0900_ai_ci;

ALTER TABLE `user_role`
ADD UNIQUE INDEX `user_id_user_archived_id_role_id` (`user_id`, `user_archived_id`, `role_id`);

ALTER TABLE `user_role`
ADD CONSTRAINT `user_role_role_id_fk` FOREIGN KEY (`role_id`)
REFERENCES `role` (`id`);



CREATE
    TRIGGER `user_role_AFTER_INSERT`
    AFTER INSERT ON `user_role`
    FOR EACH ROW
BEGIN
    DECLARE v_user_id INT DEFAULT 0;
    IF (@SESSION.user_id IS NOT NULL ) THEN
        SET v_user_id = @SESSION.user_id;
    END IF;
    INSERT INTO `user_role_log`
    (`user_role_id`,
     `action_user_id`,
     `action`,
     `changed`,
     `date_action`)
    VALUES (NEW.`id`,
            v_user_id,
            1,
            '',
            NOW());
END;

CREATE
    TRIGGER `user_role_AFTER_UPDATE`
    AFTER UPDATE ON `user_role`
    FOR EACH ROW
BEGIN
    DECLARE v_user_id INT DEFAULT 0;
    DECLARE v_changed LONGTEXT DEFAULT '';
    IF (NEW.`user_id` <> OLD.`user_id` OR
        NEW.`user_archived_id` <> OLD.`user_archived_id` OR
        NEW.`role_id` <> OLD.`role_id`)
    THEN
        IF (NEW.`user_id` <> OLD.`user_id`) THEN
            SET v_changed = CONCAT(v_changed, 'user_id = ', NEW.`user_id`, '; ');
        END IF;
        IF (NEW.`user_archived_id` <> OLD.`user_archived_id`) THEN
            SET v_changed = CONCAT(v_changed, 'user_archived_id = ', NEW.`user_archived_id`, '; ');
        END IF;
        IF (NEW.`role_id` <> OLD.`role_id`) THEN
            SET v_changed = CONCAT(v_changed, 'role_id = ', NEW.`role_id`, '; ');
        END IF;
        IF (@SESSION.user_id IS NOT NULL ) THEN
            SET v_user_id = @SESSION.user_id;
        END IF;
        INSERT INTO `user_role_log`
        (`user_role_id`,
         `action_user_id`,
         `action`,
         `changed`,
         `date_action`)
        VALUES (OLD.`id`,
                v_user_id,
                2,
                v_changed,
                NOW());
    END IF;
END;


CREATE
    TRIGGER `user_role_BEFORE_DELETE`
    BEFORE DELETE ON `user_role`
    FOR EACH ROW
BEGIN
    DECLARE v_user_id INT DEFAULT 0;
    IF (@SESSION.user_id IS NOT NULL ) THEN
        SET v_user_id = @SESSION.user_id;
    END IF;
    INSERT INTO `user_role_log`
    (`user_role_id`,
     `action_user_id`,
     `action`,
     `changed`,
     `date_action`)
    VALUES (OLD.`id`,
            v_user_id,
            3,
            '',
            NOW());
END;



CREATE TABLE IF NOT EXISTS `user_role_log` (
    `id` int NOT NULL AUTO_INCREMENT,
    `user_role_id` int NOT NULL,
    `action_user_id` int NOT NULL COMMENT '0 - process generation fixtures, > 0 - user_id (administrators)',
    `action` int NOT NULL COMMENT '1 - Insert, 2 - Update, 3 - Delete',
    `changed` varchar(1024) NULL COMMENT 'list of fields changed with new value',
    `date_action` datetime NOT NULL,
    PRIMARY KEY (`id`)
)
    ENGINE = INNODB,
    AUTO_INCREMENT = 2,
    AVG_ROW_LENGTH = 16384,
    CHARACTER SET utf8mb4,
    COLLATE utf8mb4_0900_ai_ci;




CREATE TABLE IF NOT EXISTS `role_permission` (
  `id` int NOT NULL AUTO_INCREMENT,
  `role_id` int NOT NULL,
  `permission_id` int NOT NULL,
  PRIMARY KEY (`id`)
)
ENGINE = INNODB,
AUTO_INCREMENT = 6,
AVG_ROW_LENGTH = 3276,
CHARACTER SET utf8mb4,
COLLATE utf8mb4_0900_ai_ci;

ALTER TABLE `role_permission`
ADD UNIQUE INDEX `role_id_permission_id` (`role_id`, `permission_id`);

ALTER TABLE `role_permission`
ADD CONSTRAINT `role_permission_permission_id_fk` FOREIGN KEY (`permission_id`)
REFERENCES `permission` (`id`);

ALTER TABLE `role_permission`
ADD CONSTRAINT `role_permission_role_id_fk` FOREIGN KEY (`role_id`)
REFERENCES `role` (`id`);


CREATE TABLE IF NOT EXISTS `role_hierarchy` (
  `id` int NOT NULL AUTO_INCREMENT,
  `parent_role_id` int NOT NULL,
  `child_role_id` int NOT NULL,
  PRIMARY KEY (`id`)
)
ENGINE = INNODB,
CHARACTER SET utf8mb4,
COLLATE utf8mb4_0900_ai_ci;

ALTER TABLE `role_hierarchy`
ADD CONSTRAINT `role_role_child_role_id_fk` FOREIGN KEY (`child_role_id`)
REFERENCES `role` (`id`);

ALTER TABLE `role_hierarchy`
ADD CONSTRAINT `role_role_parent_role_id_fk` FOREIGN KEY (`parent_role_id`)
REFERENCES `role` (`id`);



DROP PROCEDURE IF EXISTS `setUsersNotAccesses`;
CREATE PROCEDURE `setUsersNotAccesses`()
BEGIN
    UPDATE `user`
        SET `access` = 2,
            `date_updated` = NOW()
    WHERE `access` = 1;
END;


DROP PROCEDURE IF EXISTS `setUsersAccesses`;
CREATE PROCEDURE `setUsersAccesses`()
BEGIN
    DECLARE v_max_id INT DEFAULT 0;
    SELECT MAX(id) FROM `user` INTO v_max_id;
    UPDATE `user` u
        INNER JOIN user_role ur on u.`id` = ur.`user_id`
        INNER JOIN role r on r.`id` = ur.`role_id`
    SET u.`access` = 1,
        u.`date_updated` = NOW()
    WHERE u.`status` = 1 AND r.`name` = 'Guest'
        AND u.`id` = randomInt(v_max_id);
END;

DROP FUNCTION IF EXISTS `randomInt`;
CREATE FUNCTION `randomInt`(count INT) RETURNS TINYINT(4)
BEGIN
    DECLARE vResult INT DEFAULT 0;
    SELECT FLOOR((RAND() * 100)) INTO vResult;

    RETURN vResult;
END;



DROP PROCEDURE IF EXISTS `setUsersArchives`;
CREATE PROCEDURE `setUsersArchives`()
BEGIN
    DECLARE v_max_id INT DEFAULT 0;
    SELECT MAX(id) FROM `user` INTO v_max_id;
    UPDATE `user` u
        INNER JOIN user_role ur on u.`id` = ur.`user_id`
        INNER JOIN role r on r.`id` = ur.`role_id`
    SET u.`status` = 2,
        u.`date_updated` = NOW()
    WHERE u.`status` = 1 AND r.`name` = 'Guest'
      AND u.`id` = randomInt(v_max_id);
END;



DROP PROCEDURE IF EXISTS `moveUsersArchives`;
CREATE PROCEDURE `moveUsersArchives`()
BEGIN
    DECLARE v_user_id_archived INT DEFAULT 0;
    DECLARE v_user_id INT DEFAULT 0;
    DECLARE v_done integer DEFAULT 0;
    DECLARE v_id decimal(20, 0) DEFAULT 0;
    DECLARE v_email varchar(128) DEFAULT '';
    DECLARE v_full_name varchar(256) DEFAULT '';
    DECLARE v_description varchar(1024) DEFAULT '';
    DECLARE v_password varchar(128) DEFAULT '';
    DECLARE v_status integer DEFAULT 0;
    DECLARE v_access integer DEFAULT 0;
    DECLARE v_gender integer DEFAULT 0;
    DECLARE v_date_birthday DATETIME;
    DECLARE v_date_created DATETIME;
    DECLARE v_date_updated DATETIME;
    DECLARE v_pwd_reset_token varchar(132) DEFAULT '';
    DECLARE v_pwd_reset_token_creation_date DATETIME;

    DECLARE v_users_cursor CURSOR FOR
        SELECT
            u.`id`,
            u.`email`,
            u.`full_name`,
            u.`description`,
            u.`password`,
            u.`status`,
            u.`access`,
            u.`gender`,
            u.`date_birthday`,
            u.`date_created`,
            u.`date_updated`,
            u.`pwd_reset_token`,
            u.`pwd_reset_token_creation_date`
        FROM `user` u
            INNER JOIN user_role ur on u.`id` = ur.`user_id`
            INNER JOIN role r on r.`id` = ur.`role_id`
        WHERE u.`status` = 2 AND r.`name` = 'Guest';

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_done = 1;

    IF (@SESSION.user_id IS NOT NULL ) THEN
        SET v_user_id = @SESSION.user_id;
    END IF;

    OPEN v_users_cursor;

    users_loop:
    LOOP
        FETCH v_users_cursor INTO v_id, v_email, v_full_name, v_description, v_password,
            v_status, v_access, v_gender, v_date_birthday, v_date_created, v_date_updated,
            v_pwd_reset_token, v_pwd_reset_token_creation_date;

        IF v_done = 1 THEN
            LEAVE users_loop;
        END IF;

        INSERT INTO `user_archives`
        (`email`,
         `full_name`,
         `description`,
         `password`,
         `status`,
         `access`,
         `gender`,
         `date_birthday`,
         `date_created`,
         `date_updated`,
         `pwd_reset_token`,
         `pwd_reset_token_creation_date`,
         `date_archived`,
         `archived_user_id`)
        VALUES (v_email,
                v_full_name,
                v_description,
                v_password,
                v_status,
                v_access,
                v_gender,
                v_date_birthday,
                v_date_created,
                v_date_updated,
                v_pwd_reset_token,
                v_pwd_reset_token_creation_date,
                NOW(),
                v_user_id);
        SET v_user_id_archived = LAST_INSERT_ID();

        UPDATE `user_role`
            SET `user_id` = 0,
                `user_archived_id` = v_user_id_archived
        WHERE `user_id` = v_id;

        SET @SESSION.archive = 4;
        DELETE FROM `user` WHERE `id` = v_id;
    END LOOP users_loop;
    CLOSE v_users_cursor;
END;
