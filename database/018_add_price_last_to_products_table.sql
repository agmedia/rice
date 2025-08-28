-- Product price history (effective-dated)
CREATE TABLE rice.product_price_history (
                                            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                                            `product_id` BIGINT UNSIGNED NOT NULL,
                                            `kind` ENUM('regular','sale') NOT NULL DEFAULT 'regular',
                                            `price` DECIMAL(15,4) NOT NULL,
                                            `currency` CHAR(3) NOT NULL DEFAULT 'EUR',

    -- closed-open interval; ended_at NULL means "still active"
                                            `effective_at` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
                                            `ended_at`     DATETIME(6) NULL,

                                            `created_at` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
                                            `updated_at` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6)
                               ON UPDATE CURRENT_TIMESTAMP(6),

                                            PRIMARY KEY (`id`),
                                            CONSTRAINT `fk_pph_product`
                                                FOREIGN KEY (`product_id`) REFERENCES `products`(`id`)
                                                    ON DELETE CASCADE ON UPDATE CASCADE,

    -- lookups for "as of date" & rolling windows
                                            KEY `idx_pph_product_kind_start` (`product_id`,`kind`,`effective_at`),
                                            KEY `idx_pph_product_window`     (`product_id`,`effective_at`,`ended_at`),
                                            KEY `idx_pph_active`             (`product_id`,`kind`,`ended_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


ALTER TABLE rice.products
    ADD COLUMN `lowest_price_30d` DECIMAL(12,2) NULL AFTER `price`,
  ADD COLUMN `lowest_price_30d_since` DATE NULL AFTER `lowest_price_30d`,
  ADD KEY `idx_products_lowest_30d` (`lowest_price_30d`);
