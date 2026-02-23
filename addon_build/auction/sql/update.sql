-- Add auction columns to products table (skip this file or remove from addon if columns already exist)
-- ALTER TABLE `products` ADD COLUMN `auction_start_date` INT(11) NULL DEFAULT NULL AFTER `auction_product`;
-- ALTER TABLE `products` ADD COLUMN `auction_end_date` INT(11) NULL DEFAULT NULL AFTER `auction_start_date`;
-- ALTER TABLE `products` ADD COLUMN `starting_bid` DOUBLE(20,2) NULL DEFAULT NULL AFTER `auction_end_date`;
SELECT 1;
