-- Adminer 4.1.0 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `chat`;
CREATE TABLE `chat` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `from_uid` int(10) unsigned NOT NULL,
  `to_uid` int(10) unsigned NOT NULL,
  `message` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `send_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `fd_tmp`;
CREATE TABLE `fd_tmp` (
  `fd` int(10) unsigned NOT NULL,
  `uid` int(10) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='FD值与用户ID绑定';


DROP TABLE IF EXISTS `friend`;
CREATE TABLE `friend` (
  `from_uid` int(10) unsigned DEFAULT NULL,
  `to_uid` int(10) unsigned NOT NULL,
  `nickname` varchar(45) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='好友列表';


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `nickname` varchar(45) COLLATE utf8_unicode_ci NOT NULL COMMENT '昵称',
  `username` varchar(45) COLLATE utf8_unicode_ci NOT NULL COMMENT '登陆名称',
  `password` char(32) COLLATE utf8_unicode_ci NOT NULL COMMENT '登陆密码',
  `login_time` datetime NOT NULL COMMENT '最后登陆时间',
  `login_num` int(10) unsigned DEFAULT '0' COMMENT '登陆次数',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='用户列表';


-- 2018-03-27 10:05:35
