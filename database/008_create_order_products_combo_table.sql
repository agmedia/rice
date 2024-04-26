CREATE TABLE `order_products_combo` (
                               `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                               `order_id` bigint(20) NOT NULL,
                               `product_id` bigint(20) NOT NULL,
                               `selected` text COLLATE utf8mb4_unicode_ci,
                               `created_at` timestamp NULL DEFAULT NULL,
                               `updated_at` timestamp NULL DEFAULT NULL,
                               PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;