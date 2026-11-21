-- This is needed only to run code tests
-- ie:   php -d extension=pdo_mysql backend\tests\run-tests.php all
CREATE DATABASE BadgeApp_test CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
CREATE USER 'badgeapp_test'@'%' IDENTIFIED WITH caching_sha2_password BY '<your password here>';
GRANT ALL PRIVILEGES ON BadgeApp_test.* TO 'badgeapp_test'@'%';

-- for #313 2026-11-11
ALTER TABLE `BadgeDB`.`user` 
DROP COLUMN `company`;

-- For #165  2026-11-16
ALTER TABLE `BadgeDB`.`params` 
ADD COLUMN `privs` TEXT NULL DEFAULT NULL AFTER `rso_email`;

UPDATE `BadgeDB`.`params` SET `privs` = '[\"badges/club\",\"badges/modify\",\"calendar/create\",\"calendar/view\",\"clubs/index\",\"clubs/officers-update\",\"clubs/view\",\"events/index\",\"events/reg\",\"events/view\",\"reports/active-badges\",\"reports/cio-report\",\"reports/events-report\",\"reports/guest-report\",\"reports/index\",\"rules/index\",\"rules/view\",\"violations/index\",\"violations/report\",\"violations/view\"]' WHERE (`id` = '1');

ALTER TABLE `BadgeDB`.`roles` 
ADD COLUMN `r_timeout` INT NOT NULL DEFAULT 5 AFTER `disp_order`,
ADD COLUMN `r_privs` TEXT NULL DEFAULT NULL AFTER `r_timeout`;

UPDATE `BadgeDB`.`roles` SET `r_timeout` = '15', `r_privs` = '[\"badges/club\",\"calendar/create\",\"calendar/view\",\"clubs/index\",\"clubs/officers-update\",\"lubs/view\",\"events/index\",\"events/reg\",\"events/view\",\"reports/active-badges\",\"reports/cio-report\",\"reports/events-report\",\"reports/guest-report\",\"reports/index\"]' WHERE (`role_id` = '1');
UPDATE `BadgeDB`.`roles` SET `r_timeout` = '10', `r_privs` = '[\"events/index\",\"events/reg\",\"events/view\",\"reports/active-badges\",\"reports/cio-report\",\"reports/events-report\",\"reports/guest-report\",\"reports/index\",\"violations/index\",\"violations/view\"]' WHERE (`role_id` = '2');
UPDATE `BadgeDB`.`roles` SET `r_timeout` = '10', `r_privs` = '[\"reports/active-badges\",\"reports/cio-report\",\"reports/events-report\",\"reports/guest-report\",\"reports/index\"]' WHERE (`role_id` = '3');
UPDATE `BadgeDB`.`roles` SET `r_timeout` = '10', `r_privs` = '[\"reports/active-badges\",\"reports/cio-report\",\"reports/events-report\",\"reports/guest-report\",\"reports/index\"]' WHERE (`role_id` = '4');
UPDATE `BadgeDB`.`roles` SET `r_timeout` = '10', `r_privs` = '[\"reports/active-badges\",\"reports/cio-report\",\"reports/events-report\",\"reports/guest-report\",\"reports/index\"]' WHERE (`role_id` = '5');
UPDATE `BadgeDB`.`roles` SET `r_timeout` = '10', `r_privs` = '[\"reports/active-badges\",\"reports/cio-report\",\"reports/events-report\",\"reports/guest-report\",\"reports/index\"]' WHERE (`role_id` = '6');
UPDATE `BadgeDB`.`roles` SET `r_privs` = '[\"reports/active-badges\",\"reports/cio-report\",\"reports/events-report\",\"reports/guest-report\",\"reports/index\"]' WHERE (`role_id` = '7');

CREATE ALGORITHM = UNDEFINED DEFINER = `root`@`localhost` SQL SECURITY DEFINER VIEW `view_officers` AS
    SELECT `badge_to_role`.`badge_number` AS `badge_number`,
        CONCAT(`badges`.`first_name`, ' ', `badges`.`last_name`) AS `full_name`,
        `badge_to_role`.`role` AS `role`,
        `roles`.`role_name` AS `role_name`,
        `roles`.`r_timeout` as `timeout`,
        `roles`.`r_privs` as `privs`,
        `badge_to_role`.`club` AS `club`,
        `clubs`.`club_name` AS `club_name`,
        `clubs`.`short_name` AS `short_name`,
        `badges`.`email` AS `email`,
        `badges`.`email_vrfy` AS `email_vrfy`
    FROM
        (((`badge_to_role`
        JOIN `badges` ON ((`badges`.`badge_number` = `badge_to_role`.`badge_number`)))
        JOIN `roles` ON ((`roles`.`role_id` = `badge_to_role`.`role`)))
        JOIN `clubs` ON ((`clubs`.`club_id` = `badge_to_role`.`club`)));
