CREATE TABLE `product_combo` (
                               `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                               `product_id` bigint(20) NOT NULL,
                               `group` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
                               `products` text COLLATE utf8mb4_unicode_ci,
                               `data` text COLLATE utf8mb4_unicode_ci,
                               `sort_order` int(3) NOT NULL DEFAULT '0',
                               `status` tinyint(1) NOT NULL DEFAULT '0',
                               `created_at` timestamp NULL DEFAULT NULL,
                               `updated_at` timestamp NULL DEFAULT NULL,
                               PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;