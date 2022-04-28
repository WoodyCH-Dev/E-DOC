/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 100422
 Source Host           : localhost:3306
 Source Schema         : wdev_e-doc

 Target Server Type    : MySQL
 Target Server Version : 100422
 File Encoding         : 65001

 Date: 29/04/2022 02:21:48
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for config
-- ----------------------------
DROP TABLE IF EXISTS `config`;
CREATE TABLE `config`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `config` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of config
-- ----------------------------
INSERT INTO `config` VALUES (1, 'year_config', '1');

-- ----------------------------
-- Table structure for document_category
-- ----------------------------
DROP TABLE IF EXISTS `document_category`;
CREATE TABLE `document_category`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `group_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of document_category
-- ----------------------------
INSERT INTO `document_category` VALUES (1, 'วิชาการ');
INSERT INTO `document_category` VALUES (2, 'ทั่วไป');
INSERT INTO `document_category` VALUES (3, 'การเงิน');
INSERT INTO `document_category` VALUES (4, 'กิจการนักเรียน');

-- ----------------------------
-- Table structure for document_file
-- ----------------------------
DROP TABLE IF EXISTS `document_file`;
CREATE TABLE `document_file`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `document_id` int NULL DEFAULT NULL,
  `file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `document_stage_id` int NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `document_file_ibfk_1`(`document_id` ASC) USING BTREE,
  INDEX `document_file_ibfk_2`(`document_stage_id` ASC) USING BTREE,
  CONSTRAINT `document_file_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `document_file_ibfk_2` FOREIGN KEY (`document_stage_id`) REFERENCES `document_stage` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 255 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of document_file
-- ----------------------------

-- ----------------------------
-- Table structure for document_stage
-- ----------------------------
DROP TABLE IF EXISTS `document_stage`;
CREATE TABLE `document_stage`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `stage` int NULL DEFAULT NULL COMMENT '1 2 3 4 5 6 ...',
  `document_id` int NULL DEFAULT NULL,
  `sender_user_id` int NULL DEFAULT NULL,
  `sender_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'group or user',
  `to` int NULL DEFAULT NULL COMMENT 'user_id or group_id',
  `status` int NULL DEFAULT NULL COMMENT '0 = wait 1 = read',
  `created_timestamp` datetime NULL DEFAULT NULL,
  `read_timestamp` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `document_stage_ibfk_1`(`document_id` ASC) USING BTREE,
  CONSTRAINT `document_stage_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 146 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of document_stage
-- ----------------------------

-- ----------------------------
-- Table structure for documents
-- ----------------------------
DROP TABLE IF EXISTS `documents`;
CREATE TABLE `documents`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `document_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `document_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `document_category_id` int NULL DEFAULT NULL,
  `document_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `document_priority` int NULL DEFAULT NULL COMMENT '0 ทั่วไป 1 ด่วน 2 ด่วนที่สุด',
  `document_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '0 เอกสารดำเนินการได้ปกติ 1 เอกสารถูกยกเลิก 2 เอกสารถูกลบ',
  `user_id` int NULL DEFAULT NULL,
  `year_id` int NULL DEFAULT NULL,
  `timestamp` datetime NULL DEFAULT NULL,
  `sign_timestamp` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `document_category_id`(`document_category_id` ASC) USING BTREE,
  INDEX `year_id`(`year_id` ASC) USING BTREE,
  CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`document_category_id`) REFERENCES `document_category` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `documents_ibfk_2` FOREIGN KEY (`year_id`) REFERENCES `year_list` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 38 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of documents
-- ----------------------------

-- ----------------------------
-- Table structure for user_group
-- ----------------------------
DROP TABLE IF EXISTS `user_group`;
CREATE TABLE `user_group`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `group_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of user_group
-- ----------------------------
INSERT INTO `user_group` VALUES (1, 'ทดสอบ');
INSERT INTO `user_group` VALUES (2, 'ผู้พัฒนาระบบ');

-- ----------------------------
-- Table structure for user_ingroup
-- ----------------------------
DROP TABLE IF EXISTS `user_ingroup`;
CREATE TABLE `user_ingroup`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NULL DEFAULT NULL,
  `group_id` int NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `user_ingroup_ibfk_1`(`user_id` ASC) USING BTREE,
  INDEX `user_ingroup_ibfk_2`(`group_id` ASC) USING BTREE,
  CONSTRAINT `user_ingroup_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_ingroup_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `user_group` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 15 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of user_ingroup
-- ----------------------------
INSERT INTO `user_ingroup` VALUES (6, 11, 1);
INSERT INTO `user_ingroup` VALUES (13, 1, 2);
INSERT INTO `user_ingroup` VALUES (14, 1, 1);

-- ----------------------------
-- Table structure for user_permission
-- ----------------------------
DROP TABLE IF EXISTS `user_permission`;
CREATE TABLE `user_permission`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NULL DEFAULT NULL,
  `permission_id` int NULL DEFAULT NULL COMMENT '0 = user , 1 = sender , 2 = admin',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `user_permission_ibfk_1`(`user_id` ASC) USING BTREE,
  CONSTRAINT `user_permission_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 50 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of user_permission
-- ----------------------------
INSERT INTO `user_permission` VALUES (1, 1, 0);
INSERT INTO `user_permission` VALUES (2, 1, 1);
INSERT INTO `user_permission` VALUES (3, 1, 2);
INSERT INTO `user_permission` VALUES (46, 11, 0);
INSERT INTO `user_permission` VALUES (48, 12, 0);
INSERT INTO `user_permission` VALUES (49, 11, 1);

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `google_uid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `users_email_unique`(`email` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (1, 'Wutthiphon', 'Tassana', 'woodychgamer5588@gmail.com', '$2a$12$MgrsMK8zpv/gbvyQfWL70uDVlddXJhUXJf8H9V3s3p.5JApCaWixO', '103141999773948035980');
INSERT INTO `users` VALUES (11, 'Test', 'Test23', 'hacker23@gg.com', '$2y$10$w1EYhxNHs0Ws4Vd/hrtAr.aV0gaY8aIfapaHN9zzMPOMlogOP6gje', NULL);
INSERT INTO `users` VALUES (12, 'W', 'WWWW', 'www@ww.com', '$2y$10$3qMq1Y3KW3qBD6wLXQXDfO7ZgSbs8aIe08AyVQbNP0.e5c0nLy4MK', NULL);

-- ----------------------------
-- Table structure for year_list
-- ----------------------------
DROP TABLE IF EXISTS `year_list`;
CREATE TABLE `year_list`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `year` year NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of year_list
-- ----------------------------
INSERT INTO `year_list` VALUES (1, 2022);

SET FOREIGN_KEY_CHECKS = 1;
