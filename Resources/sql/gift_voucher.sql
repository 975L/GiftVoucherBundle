/*
 * (c) 2018: 975l <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for gift_voucher_available
-- ----------------------------
-- DROP TABLE IF EXISTS `gift_voucher_available`;
CREATE TABLE `gift_voucher_available` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `suppressed` bit(1) NULL,
  `object` varchar(128) DEFAULT NULL,
  `slug` varchar(128) DEFAULT NULL,
  `description` blob DEFAULT NULL,
  `valid` varchar(24) DEFAULT NULL,
  `amount` mediumint(8) DEFAULT NULL,
  `currency` varchar(3) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for gift_voucher_purchased
-- ----------------------------
-- DROP TABLE IF EXISTS `gift_voucher_purchased`;
CREATE TABLE `gift_voucher_purchased` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `identifier` varchar(12) DEFAULT NULL,
  `secret` varchar(4) DEFAULT NULL,
  `object` varchar(128) DEFAULT NULL,
  `description` blob DEFAULT NULL,
  `offered_by` varchar(128) DEFAULT NULL,
  `offered_to` varchar(128) DEFAULT NULL,
  `message` blob DEFAULT NULL,
  `send_to_email` varchar(128) DEFAULT NULL,
  `purchase` datetime DEFAULT NULL,
  `valid` date DEFAULT NULL,
  `amount` mediumint(8) DEFAULT NULL,
  `currency` varchar(3) DEFAULT NULL,
  `order_id` varchar(48) NULL,
  `used` datetime DEFAULT NULL,
  `user_ip` varchar(48) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;