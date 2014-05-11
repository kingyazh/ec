-- phpMyAdmin SQL Dump
-- version 2.11.11.3
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2013 年 12 月 20 日 20:07
-- 服务器版本: 5.1.58
-- PHP 版本: 5.2.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- 数据库: `ecshoputf8`
--

-- --------------------------------------------------------

--
-- 表的结构 `wxch_cfg`
--

CREATE TABLE IF NOT EXISTS `wxch_cfg` (
  `cfg_id` int(3) unsigned NOT NULL AUTO_INCREMENT,
  `cfg_name` varchar(64) NOT NULL DEFAULT '',
  `cfg_value` varchar(100) NOT NULL,
  `autoload` varchar(20) NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`cfg_id`),
  UNIQUE KEY `cfg_name` (`cfg_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- 导出表中的数据 `wxch_cfg`
--

INSERT INTO `wxch_cfg` (`cfg_id`, `cfg_name`, `cfg_value`, `autoload`) VALUES
(1, 'murl', 'mobile/', 'yes'),
(2, 'baseurl', 'http://ecshoputf8.weixincaihong.com/', 'yes');

-- --------------------------------------------------------

--
-- 表的结构 `wxch_config`
--

CREATE TABLE IF NOT EXISTS `wxch_config` (
  `id` int(1) NOT NULL,
  `token` varchar(100) NOT NULL,
  `appid` char(18) NOT NULL,
  `appsecret` char(32) NOT NULL,
  `access_token` char(150) NOT NULL,
  `dateline` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 导出表中的数据 `wxch_config`
--

INSERT INTO `wxch_config` (`id`, `token`, `appid`, `appsecret`, `access_token`, `dateline`) VALUES
(1, 'weixin', '', '', '', 1386912383);

-- --------------------------------------------------------

--
-- 表的结构 `wxch_keywords`
--

CREATE TABLE IF NOT EXISTS `wxch_keywords` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `keyword` varchar(100) NOT NULL,
  `type` tinyint(1) unsigned NOT NULL,
  `contents` text NOT NULL,
  `count` int(10) unsigned NOT NULL,
  `status` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;


-- --------------------------------------------------------

--
-- 表的结构 `wxch_lang`
--

CREATE TABLE IF NOT EXISTS `wxch_lang` (
  `lang_id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `lang_name` varchar(64) NOT NULL,
  `lang_value` text NOT NULL,
  PRIMARY KEY (`lang_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `wxch_lang`
--

INSERT INTO `wxch_lang` (`lang_id`, `lang_name`, `lang_value`) VALUES
(1, 'regmsg', '欢迎关注微信彩虹');



-- --------------------------------------------------------

--
-- 表的结构 `wxch_user`
--

CREATE TABLE IF NOT EXISTS `wxch_user` (
  `uid` int(7) NOT NULL AUTO_INCREMENT,
  `subscribe` tinyint(1) unsigned NOT NULL,
  `wxid` char(28) NOT NULL,
  `nickname` varchar(200) NOT NULL,
  `sex` tinyint(1) unsigned NOT NULL,
  `city` varchar(100) NOT NULL,
  `country` varchar(100) NOT NULL,
  `province` varchar(100) NOT NULL,
  `language` varchar(50) NOT NULL,
  `headimgurl` varchar(200) NOT NULL,
  `subscribe_time` int(10) unsigned NOT NULL,
  `localimgurl` varchar(200) NOT NULL,
  `setp` smallint(2) unsigned NOT NULL,
  `uname` varchar(50) NOT NULL,
  `coupon` varchar(30) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

