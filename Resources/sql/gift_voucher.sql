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
  `slug` varchar(128) DEFAULT NULL,
  `object` varchar(128) DEFAULT NULL,
  `description` blob,
  `valid` varchar(2) DEFAULT NULL,
  `amount` mediumint(8) DEFAULT NULL,
  `currency` varchar(3) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for gift_voucher_ordered
-- ----------------------------
-- DROP TABLE IF EXISTS `gift_voucher_ordered`;
CREATE TABLE `gift_voucher_ordered` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `number` varchar(12) DEFAULT NULL,
  `secret` varchar(4) DEFAULT NULL,
  `object` varchar(128) DEFAULT NULL,
  `description` blob,
  `offered_by` varchar(128) DEFAULT NULL,
  `offered_to` varchar(128) DEFAULT NULL,
  `message` blob,
  `send_to_email` varchar(128) DEFAULT NULL,
  `purchase` datetime DEFAULT NULL,
  `valid` date DEFAULT NULL,
  `amount` mediumint(8) DEFAULT NULL,
  `currency` varchar(3) DEFAULT NULL,
  `used` datetime DEFAULT NULL,
  `user_ip` varchar(48) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;