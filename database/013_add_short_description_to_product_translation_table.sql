ALTER TABLE `category_translations`
    ADD COLUMN `short_description` VARCHAR(191) NULL DEFAULT NULL AFTER `description`;