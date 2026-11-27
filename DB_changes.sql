-----------------------------------------------------------
-- DB Changes - If you are not injesting clean Schema -----
-----------------------------------------------------------

-- /**This is needed only to run code tests ---------------
-- ie:   php -d extension=pdo_mysql backend\tests\run-tests.php all
CREATE DATABASE BadgeApp_test CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
CREATE USER 'badgeapp_test'@'%' IDENTIFIED WITH caching_sha2_password BY '<your password here>';
GRANT ALL PRIVILEGES ON BadgeApp_test.* TO 'badgeapp_test'@'%';
-- /** Test DB **/ ----------------------------------------

-- for #313 2026-11-11
ALTER TABLE `BadgeDB`.`user`
DROP COLUMN `company`;

-- For #165  2026-11-16
ALTER TABLE `BadgeDB`.`params`
ADD COLUMN `privs` TEXT NULL DEFAULT NULL AFTER `rso_email`;

UPDATE `BadgeDB`.`params` SET `privs` = '["accounts/add-banned-guest","accounts/banned","accounts/index","accounts/view","badges/all","badges/modify","badges/photo-add","badges/photo-crop","badges/view-certifications-list","badges/view-remarks-history","badges/view-renewal-history","badges/view-subscriptions","badges/view-violations-history","badges/view-work-credits","badges/view-work-credits-log","calendar/bulkdelete","calendar/conflict","calendar/create","calendar/delete","calendar/get-event-types","calendar/inactive","calendar/index","calendar/open-range","calendar/recur","calendar/republish","calendar/shoot","calendar/update","calendar/view","clubs/badge-rosters","clubs/index","clubs/officers","clubs/officers-create","clubs/officers-delete","clubs/officers-update","clubs/view","events/add-att","events/index","events/reg","events/view","guest/update","reports/active-badges","reports/cio-report","reports/events-report","reports/guest-report","reports/index","rules/index","rules/view","violations/index","violations/report","violations/view"]' WHERE (`id` = '1');
	

ALTER TABLE `BadgeDB`.`roles`
ADD COLUMN `r_timeout` INT NOT NULL DEFAULT 5 AFTER `disp_order`,
ADD COLUMN `r_privs` TEXT NULL DEFAULT NULL AFTER `r_timeout`;

TRUNCATE `BadgeDB`.`roles`;
INSERT INTO `roles` VALUES 
	(1,'President',10,15,'["badges/view-remarks-history","badges/view-renewal-history","badges/view-subscriptions","badges/view-violations-history","badges/view-work-credits","badges/view-work-credits-log","calendar/create","calendar/index","calendar/update","calendar/view","clubs/index","clubs/officers","clubs/officers-create","clubs/officers-delete","clubs/officers-update","clubs/view","events/index","events/reg","events/view","reports/active-badges","reports/cio-report","reports/events-report","reports/guest-report","reports/index"]'),
	(2,'Vice President',20,10,'["events/index","events/reg","events/view","reports/active-badges","reports/cio-report","reports/events-report","reports/guest-report","reports/index","violations/index","violations/view"]'),
	(3,'Executive Officer',30,10,'["reports/active-badges","reports/cio-report","reports/events-report","reports/guest-report","reports/index"]'),
	(4,'Treasurer',40,10,'["reports/active-badges","reports/cio-report","reports/events-report","reports/guest-report","reports/index"]'),
	(5,'Secretary',50,10,'["events/index","events/reg","reports/active-badges","reports/cio-report","reports/events-report","reports/guest-report","reports/index"]'),
	(6,'Trustee',60,10,'["reports/active-badges","reports/cio-report","reports/events-report","reports/guest-report","reports/index"]'),
	(7,'Alternate Trustee',70,10,'["reports/active-badges","reports/cio-report","reports/events-report","reports/guest-report","reports/index"]'),
	(8,'Delegate',90,5,'[]'),
	(9,'Calendar Coordinator',75,15,'["calendar/bulkdelete","calendar/conflict","calendar/create","calendar/delete","calendar/get-event-types","calendar/inactive","calendar/index","calendar/open-range","calendar/recur","calendar/republish","calendar/update","calendar/view","events/add-att","events/index","events/reg","events/view","guest/update"]'),
	(10,'Event Director',80,10,'["events/add-att","events/index","events/reg","events/view"]');

DROP VIEW `BadgeDB`.`officers`;

CREATE OR REPLACE VIEW `view_officers` AS
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
