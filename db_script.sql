/* Dt: 12-03-22 */
ALTER TABLE `products` ADD COLUMN `manufacturer_location` varchar(500) NULL AFTER `wholesale_product`;
ALTER TABLE `products` ADD COLUMN `purchase_start_date` datetime NULL AFTER `manufacturer_location`;
ALTER TABLE `products` ADD COLUMN `purchase_end_date` datetime NULL AFTER `purchase_start_date`;


/* Dt: 22-03-22 */
INSERT INTO `sms_templates`(`id`, `identifier`, `sms_body`, `template_id`, `status`, `created_at`, `updated_at`) VALUES (7, 'order_confirm', 'Confirm Order', NULL, 1, '2022-03-22 15:34:58', '2022-03-22 15:35:04');
INSERT INTO `sms_templates`(`id`, `identifier`, `sms_body`, `template_id`, `status`, `created_at`, `updated_at`) VALUES (8, 'order_cancel', 'Cancel Order', NULL, 1, '2022-03-22 15:37:44', '2022-03-22 15:37:48');
INSERT INTO `sms_templates`(`id`, `identifier`, `sms_body`, `template_id`, `status`, `created_at`, `updated_at`) VALUES (9, 'order_shipped', 'Order Shipped', NULL, 1, '2022-03-22 15:40:14', '2022-03-22 15:40:14');


/* Dt: 28-03-22 */
ALTER TABLE `users` ADD COLUMN `joined_community_id` int(11) NULL AFTER `remaining_uploads`;
ALTER TABLE `users` MODIFY COLUMN `joined_community_id` int(11) NULL DEFAULT 0 AFTER `remaining_uploads`;


/* Dt: 01-04-22 */
ALTER TABLE `users` ADD COLUMN `referral_key` varchar(255) NULL AFTER `joined_community_id`;

/* Dt: 06-04-22 */
ALTER TABLE `products`
ADD COLUMN `secondary_unit` varchar(20) NULL AFTER `unit`,
MODIFY COLUMN `min_qty` double(20, 3) NOT NULL DEFAULT 1 AFTER `unit`;

/* Dt: 14-04-22 */
ALTER TABLE `product_stocks`
ADD COLUMN `seller_id` int(11) NULL AFTER `image`,
ADD COLUMN `est_shipping_days` int(11) NULL DEFAULT NULL AFTER `seller_id`,
ADD COLUMN `purchase_start_date` datetime(0) NULL DEFAULT NULL AFTER `est_shipping_days`,
ADD COLUMN `purchase_end_date` datetime(0) NULL DEFAULT NULL AFTER `purchase_start_date`;

/* Dt: 28-04-22 */
ALTER TABLE `carts` ADD COLUMN `product_stock_id` int(11) NULL AFTER `product_id`;
ALTER TABLE `order_details` ADD COLUMN `product_stock_id` int(11) NULL AFTER `product_id`;

/* Dt: 19-05-22 */
ALTER TABLE `products` ADD COLUMN `parent_category_id` int(11) NOT NULL AFTER `user_id`;
ALTER TABLE `products` ADD COLUMN `sub_category_id` int(11) NOT NULL AFTER `parent_category_id`;

/* Dt: 24-05-22 */
ALTER TABLE `products` ADD COLUMN `variation` varchar(255) NULL AFTER `category_id`;
ALTER TABLE `orders` ADD COLUMN `added_by_admin` int(0) NULL COMMENT '0: No; 1: Yes' AFTER `commission_calculated`;
ALTER TABLE `orders` MODIFY COLUMN `added_by_admin` int(11) NULL DEFAULT 0 COMMENT '0: No; 1: Yes' AFTER `commission_calculated`;

/* Dt: 26-05-22 */
ALTER TABLE `order_details` ADD COLUMN `is_archived` int(1) NULL DEFAULT 0 AFTER `product_referral_code`;


/* Dt: 23-06-22 */
ALTER TABLE `orders` ADD COLUMN `replaced_order_id` int(11) NULL DEFAULT 0 AFTER `added_by_admin`;
ALTER TABLE `order_details` ADD COLUMN `replaced_order_detail_id` int(11) NULL DEFAULT 0 AFTER `is_archived`;


-- ----------------------------
-- Table structure for archive_orders Dt: 29-6-22
-- ----------------------------
DROP TABLE IF EXISTS `archive_orders`;
CREATE TABLE `archive_orders`  (
   `id` int(11) NOT NULL,
   `combined_order_id` int(11) NULL DEFAULT NULL,
   `user_id` int(11) NULL DEFAULT NULL,
   `guest_id` int(11) NULL DEFAULT NULL,
   `seller_id` int(11) NULL DEFAULT NULL,
   `shipping_address` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL,
   `shipping_type` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
   `pickup_point_id` int(11) NOT NULL DEFAULT 0,
   `delivery_status` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT 'pending',
   `payment_type` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
   `payment_status` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT 'unpaid',
   `payment_details` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL,
   `grand_total` double(20, 2) NULL DEFAULT NULL,
  `coupon_discount` double(20, 2) NOT NULL DEFAULT 0.00,
  `code` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL,
  `tracking_code` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `date` int(11) NOT NULL,
  `viewed` int(11) NOT NULL DEFAULT 0,
  `delivery_viewed` int(11) NOT NULL DEFAULT 1,
  `payment_status_viewed` int(11) NULL DEFAULT 1,
  `commission_calculated` int(11) NOT NULL DEFAULT 0,
  `added_by_admin` int(11) NULL DEFAULT 0 COMMENT '0: No; 1: Yes',
  `replaced_order_id` int(11) NULL DEFAULT 0,
  `created_at` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_at` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0)
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = DYNAMIC;

SET FOREIGN_KEY_CHECKS = 1;



-- ----------------------------
-- Table structure for archive_order_details Dt: 29-6-22
-- ----------------------------
DROP TABLE IF EXISTS `archive_order_details`;
CREATE TABLE `archive_order_details`  (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `seller_id` int(11) NULL DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `product_stock_id` int(11) NULL DEFAULT NULL,
  `variation` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL,
  `price` double(20, 2) NULL DEFAULT NULL,
  `tax` double(20, 2) NOT NULL DEFAULT 0.00,
  `shipping_cost` double(20, 2) NOT NULL DEFAULT 0.00,
  `quantity` int(11) NULL DEFAULT NULL,
  `payment_status` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'unpaid',
  `delivery_status` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT 'pending',
  `shipping_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `pickup_point_id` int(11) NULL DEFAULT NULL,
  `product_referral_code` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `is_archived` int(1) NULL DEFAULT 0,
  `replaced_order_detail_id` int(11) NULL DEFAULT 0,
  `created_at` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_at` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0)
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = DYNAMIC;

SET FOREIGN_KEY_CHECKS = 1;



-- ----------------------------
-- Table structure for archive_product_stocks Dt: 29-6-22
-- ----------------------------
DROP TABLE IF EXISTS `archive_product_stocks`;
CREATE TABLE `archive_product_stocks`  (
   `id` int(11) NOT NULL,
   `product_id` int(11) NOT NULL,
   `variant` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
   `sku` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
   `price` double(20, 2) NOT NULL DEFAULT 0.00,
  `qty` int(11) NOT NULL DEFAULT 0,
  `image` int(11) NULL DEFAULT NULL,
  `seller_id` int(11) NULL DEFAULT NULL,
  `est_shipping_days` int(11) NULL DEFAULT NULL,
  `purchase_start_date` datetime(0) NULL DEFAULT NULL,
  `purchase_end_date` datetime(0) NULL DEFAULT NULL,
  `created_at` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_at` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0)
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = DYNAMIC;

SET FOREIGN_KEY_CHECKS = 1;



-- ----------------------------
-- Field aded for Best Selling products Dt: 29-6-22
-- ----------------------------
ALTER TABLE `product_stocks`
ADD COLUMN `is_best_selling` int(1) NULL DEFAULT 0 AFTER `est_shipping_days`;

-- Category slug updated
UPDATE `categories` SET `slug` = 'flowers' WHERE `categories`.`id` = 24;


-- ----------------------------
-- Dt: 2-02-23
-- ----------------------------
ALTER TABLE `orders` ADD COLUMN `razorpay_payment_link` varchar(500) NULL AFTER `replaced_order_id`;
ALTER TABLE `order_details` ADD COLUMN `custom_price` decimal(20, 2) NULL DEFAULT 0 AFTER `updated_at`;


-- ----------------------------
-- Dt: 6-02-23
-- ----------------------------
ALTER TABLE `archive_product_stocks` ADD COLUMN `is_best_selling` int(1) NULL DEFAULT 0 AFTER `est_shipping_days`;
ALTER TABLE `archive_orders` ADD COLUMN `razorpay_payment_link` varchar(500) NULL AFTER `replaced_order_id`;
ALTER TABLE `users` ADD COLUMN `pending_bill_url` varchar(255) NULL AFTER `referral_key`;
ALTER TABLE `users` ADD COLUMN `pending_url_amt` decimal(20, 3) NULL AFTER `pending_bill_url`;


-- ----------------------------
-- Dt: 8-02-23
-- ----------------------------
ALTER TABLE `order_details` MODIFY COLUMN `custom_price` decimal(20, 2) NULL DEFAULT NULL AFTER `updated_at`;
ALTER TABLE `carts` ADD COLUMN `custom_price` decimal(10, 2) NULL DEFAULT NULL AFTER `quantity`;


-- ----------------------------
-- Dt: 15-02-23 Table structure for payment_webhooks
-- ----------------------------
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `payment_webhooks`;
CREATE TABLE `payment_webhooks`  (
         `id` int(11) NOT NULL AUTO_INCREMENT,
         `type` enum('web','payment_link') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
         `webhook_data` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL,
         `created_at` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
         `updated_at` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
         PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = DYNAMIC;

SET FOREIGN_KEY_CHECKS = 1;


-- ----------------------------
-- Dt: 16-02-23
-- ----------------------------
ALTER TABLE `payment_webhooks` ADD COLUMN `is_active` int(1) NULL DEFAULT 1 AFTER `updated_at`;


-- ----------------------------
-- Dt: 24-04-23
-- ----------------------------
INSERT INTO `safequ_prime`.`business_settings` (`type`, `value`, `lang`, `created_at`, `updated_at`) VALUES ('ship_cost_min_price', '299', NULL, '2023-04-24 15:38:10', '2023-04-24 17:49:31');
