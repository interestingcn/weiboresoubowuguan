# Host: localhost  (Version: 5.5.53)
# Date: 2020-07-27 13:20:11
# Generator: MySQL-Front 5.3  (Build 4.234)

/*!40101 SET NAMES utf8 */;

#
# Structure for table "access_log"
#

CREATE TABLE `access_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  `system` varchar(255) DEFAULT NULL,
  `browser` varchar(255) DEFAULT NULL,
  `ua` varchar(255) DEFAULT NULL,
  `request` varchar(255) DEFAULT NULL,
  `refer` varchar(255) DEFAULT NULL,
  `ip` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ip` (`ip`),
  KEY `id` (`id`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM AUTO_INCREMENT=35766 DEFAULT CHARSET=utf8;

#
# Structure for table "auth"
#

CREATE TABLE `auth` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `pwd` varchar(255) DEFAULT NULL,
  `sec_code` int(8) DEFAULT NULL,
  `session` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

#
# Structure for table "ban_ip"
#

CREATE TABLE `ban_ip` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` bigint(20) NOT NULL,
  `add_time` int(11) DEFAULT NULL,
  `expire` int(11) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `admin_add` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ip` (`ip`),
  KEY `expire` (`expire`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

#
# Structure for table "contributing"
#

CREATE TABLE `contributing` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `price` varchar(6) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;

#
# Structure for table "spider_data"
#

CREATE TABLE `spider_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `rank` int(10) unsigned DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `href` varchar(255) DEFAULT NULL,
  `star` int(10) unsigned DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `group` varchar(12) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `group` (`group`),
  KEY `searchnum` (`title`,`group`) USING BTREE,
  FULLTEXT KEY `title` (`title`),
  FULLTEXT KEY `search` (`title`,`group`) COMMENT '搜索功能索引'
) ENGINE=MyISAM AUTO_INCREMENT=1683102 DEFAULT CHARSET=utf8;

#
# Structure for table "system"
#

CREATE TABLE `system` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `item` varchar(255) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `item` (`item`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

#
# Structure for table "time"
#

CREATE TABLE `time` (
  `time_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title_id` int(11) DEFAULT NULL,
  `time` varchar(255) DEFAULT NULL,
  `rank` int(11) DEFAULT NULL,
  `star` int(11) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`time_id`),
  KEY `timeid` (`time_id`) USING BTREE,
  KEY `titleid` (`title_id`),
  KEY `time` (`time`)
) ENGINE=MyISAM AUTO_INCREMENT=12409165 DEFAULT CHARSET=utf8;

#
# Structure for table "title"
#

CREATE TABLE `title` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `createTime` varchar(255) DEFAULT NULL,
  `maxRank` int(3) DEFAULT NULL,
  `maxStar` int(11) DEFAULT NULL,
  `check` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `time` (`createTime`),
  KEY `maxRank` (`maxRank`),
  KEY `maxStar` (`maxStar`),
  FULLTEXT KEY `title` (`title`)
) ENGINE=MyISAM AUTO_INCREMENT=37218 DEFAULT CHARSET=utf8;

#
# Structure for table "user"
#

CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `openid` varchar(255) DEFAULT NULL,
  `lave` int(8) DEFAULT NULL,
  `all` int(8) DEFAULT NULL,
  `session` varchar(255) DEFAULT NULL,
  `status` int(1) DEFAULT NULL,
  `ban_expire` int(11) DEFAULT NULL,
  `last_use` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `openid` (`openid`),
  KEY `session` (`session`)
) ENGINE=MyISAM AUTO_INCREMENT=1015 DEFAULT CHARSET=utf8;
