ALTER TABLE `products`
    ADD COLUMN `size_value` INT NULL DEFAULT '0' AFTER `quantity`;

ALTER TABLE `products`
    ADD COLUMN `size_type` VARCHAR(2) NULL DEFAULT '' AFTER `quantity`;
