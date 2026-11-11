-- This is needed only to run code tests
-- ie:   php -d extension=pdo_mysql backend\tests\run-tests.php all
CREATE DATABASE BadgeApp_test CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
CREATE USER 'badgeapp_test'@'%' IDENTIFIED WITH caching_sha2_password BY '<your password here>';
GRANT ALL PRIVILEGES ON BadgeApp_test.* TO 'badgeapp_test'@'%';

-- for #313 2026-11-11
ALTER TABLE `BadgeDB`.`user` 
DROP COLUMN `company`;
