ALTER TABLE `category_translations`
    ADD COLUMN `image_title` VARCHAR(191) NULL DEFAULT NULL AFTER `meta_description`
    ADD COLUMN `image_alt` VARCHAR(191) NULL DEFAULT NULL AFTER `image_title`;