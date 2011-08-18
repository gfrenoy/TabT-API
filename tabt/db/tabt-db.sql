/*
 ****************************************************************************
 * TabT
 *  The table tennis information manager.
 * -----------------------------------------------------------------
 * TabT database structure
 * -----------------------------------------------------------------
 * @version 0.8
 * -----------------------------------------------------------------
 * Copyright (C) 2000-2011 Gaëtan Frenoy (gaetan@frenoy.net)
 * -----------------------------------------------------------------
 * This file is part of TabT
 *
 * TabT is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with TabT.  If not, see <http://www.gnu.org/licenses/>.
 **************************************************************************
*/
CREATE TABLE `active_sessions` (
  `sid` varchar(32) NOT NULL default '',
  `name` varchar(32) NOT NULL default '',
  `val` text,
  `changed` varchar(14) NOT NULL default '',
  PRIMARY KEY  (`name`,`sid`),
  KEY `changed` (`changed`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='List of currently active user session';

CREATE TABLE `auth_user` (
  `user_id` varchar(32) NOT NULL default '',
  `username` varchar(32) NOT NULL default '',
  `password` varchar(32) NOT NULL default '',
  `perms` varchar(255) default NULL,
  `player_id` int(10) unsigned default NULL,
  `conf_id` varchar(32) default NULL,
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `k_username` (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='List of authorized users';

CREATE TABLE `calendarchanges` (
  `div_id` int(5) unsigned NOT NULL default '0',
  `week` tinyint(2) unsigned NOT NULL default '0',
  `match_nb` tinyint(1) unsigned NOT NULL default '0',
  `date` date default NULL,
  `time` time default NULL,
  `home` tinyint(2) default NULL,
  `away` tinyint(2) default NULL,
  PRIMARY KEY  (`div_id`,`week`,`match_nb`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `calendardateinfo` (
  `id` mediumint(5) unsigned NOT NULL auto_increment,
  `season` tinyint(2) NOT NULL default '0',
  `calendar_id` tinyint(2) unsigned NOT NULL default '0',
  `name` varchar(80) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=140 DEFAULT CHARSET=latin1 COMMENT='Information about calendar date tables';

CREATE TABLE `calendardates` (
  `calendardate_id` mediumint(5) unsigned NOT NULL auto_increment,
  `week` tinyint(2) unsigned NOT NULL default '0',
  `date` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`calendardate_id`,`week`),
  KEY `calendardate_id` (`calendardate_id`)
) ENGINE=MyISAM AUTO_INCREMENT=140 DEFAULT CHARSET=latin1;

CREATE TABLE `calendarinfo` (
  `calendar_id` tinyint(1) unsigned NOT NULL default '0',
  `week` tinyint(2) unsigned NOT NULL default '0',
  `match_nb` tinyint(1) unsigned NOT NULL default '0',
  `home` tinyint(2) unsigned NOT NULL default '0',
  `away` tinyint(2) unsigned NOT NULL default '0',
  PRIMARY KEY  (`calendar_id`,`week`,`match_nb`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `calendartypeinfo` (
  `id` tinyint(1) unsigned NOT NULL auto_increment,
  `name` varchar(60) NOT NULL default '',
  `nb_team` tinyint(2) unsigned NOT NULL default '0',
  `competition_id` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=68 DEFAULT CHARSET=latin1 PACK_KEYS=0 COMMENT='A calendar template for a given type of competition';

CREATE TABLE `calendarweekname` (
  `calendar_id` tinyint(1) unsigned NOT NULL default '0',
  `week` tinyint(2) unsigned NOT NULL default '0',
  `name` char(3) NOT NULL default '0',
  PRIMARY KEY  (`calendar_id`,`week`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `classementcategories` (
  `id` smallint(3) NOT NULL default '0',
  `name` varchar(100) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `classementgroupinfo` (
  `group_id` smallint(3) NOT NULL default '0',
  `classementcategory` tinyint(2) default NULL,
  `name` varchar(100) default NULL,
  PRIMARY KEY  (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `classementgroups` (
  `group_id` smallint(3) NOT NULL default '0',
  `classement_id` tinyint(2) NOT NULL default '0',
  PRIMARY KEY  (`group_id`,`classement_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `classementinfo` (
  `id` tinyint(2) unsigned NOT NULL default '0',
  `category` tinyint(2) unsigned NOT NULL default '0',
  `name` varchar(4) NOT NULL default '',
  `order` int(4) unsigned default NULL,
  `elo_value` int(4) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`,`category`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `classementtypeinfo` (
  `id` tinyint(1) unsigned NOT NULL auto_increment,
  `name` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

CREATE TABLE `clubaddressinfo` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `club_id` smallint(5) unsigned NOT NULL default '0',
  `address_id` tinyint(1) unsigned NOT NULL default '1',
  `name` varchar(100) NOT NULL default '',
  `address` varchar(150) NOT NULL default '',
  `zip` smallint(5) unsigned NOT NULL default '0',
  `town` varchar(50) NOT NULL default '',
  `phone` varchar(15) default NULL,
  `fax` varchar(15) default NULL,
  `comment` text,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `club_id` (`club_id`,`address_id`)
) ENGINE=MyISAM AUTO_INCREMENT=331 DEFAULT CHARSET=latin1;

CREATE TABLE `clubcategories` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `levels` varchar(100) NOT NULL default ':1:2:',
  `menu_order` tinyint(2) NOT NULL default '0',
  `group` varchar(100) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

CREATE TABLE `clubfines` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `season` tinyint(2) unsigned NOT NULL default '0',
  `club_id` smallint(5) unsigned NOT NULL default '0',
  `team_indice` char(1) default NULL,
  `week_name` char(3) NOT NULL default '',
  `fine_id` tinyint(3) unsigned NOT NULL default '0',
  `div_id` int(5) unsigned default NULL,
  `match_nb` tinyint(1) unsigned default NULL,
  `comment` varchar(255) NOT NULL default '',
  `value` float NOT NULL default '0',
  `linked_fine_id` int(10) unsigned default NULL,
  `linked_club_id` smallint(5) unsigned default NULL,
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `modified_by` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `club_id` (`club_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3742 DEFAULT CHARSET=latin1;

CREATE TABLE `clubresponsabilityinfo` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `lang` char(2) NOT NULL default 'nl',
  `name` varchar(40) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `modified_by` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`,`lang`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

CREATE TABLE `clubresponsibles` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `season` tinyint(2) NOT NULL default '0',
  `club_id` smallint(5) unsigned NOT NULL default '0',
  `responsability_id` smallint(5) unsigned NOT NULL default '0',
  `player_id` int(10) unsigned NOT NULL default '0',
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `modified_by` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=87 DEFAULT CHARSET=latin1;

CREATE TABLE `clubs` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `category` smallint(5) NOT NULL default '1',
  `name` varchar(255) character set utf8 NOT NULL default '',
  `short_name` varchar(15) character set utf8 default NULL,
  `indice` varchar(10) default NULL,
  `site` varchar(100) default NULL,
  `admin_name` varchar(50) default NULL,
  `admin_mail` varchar(50) default NULL,
  `is_english` tinyint(1) unsigned default '0',
  `is_french` tinyint(1) unsigned default '0',
  `is_dutch` tinyint(1) unsigned default '0',
  `is_german` tinyint(1) unsigned default '0',
  `is_dead` tinyint(1) unsigned NOT NULL default '0',
  `first_season` tinyint(2) unsigned default NULL,
  `last_season` tinyint(2) unsigned default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=746 DEFAULT CHARSET=latin1;

CREATE TABLE `clubstyles` (
  `club_id` smallint(5) unsigned NOT NULL default '0',
  `attrib_name` varchar(12) NOT NULL default '',
  `attrib_value` varchar(150) default '#FFFFFF',
  PRIMARY KEY  (`club_id`,`attrib_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `clubstylesinfo` (
  `attrib_name` varchar(10) NOT NULL default '0',
  `attrib_desc` varchar(200) NOT NULL default '',
  PRIMARY KEY  (`attrib_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `competitioninfo` (
  `id` tinyint(1) unsigned NOT NULL auto_increment,
  `name` varchar(40) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COMMENT='List of competition type';

CREATE TABLE `divisioncategories` (
  `id` tinyint(2) unsigned NOT NULL auto_increment,
  `name` varchar(30) NOT NULL default '',
  `nb_match_per_game` tinyint(2) unsigned NOT NULL default '16',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

CREATE TABLE `divisioninfo` (
  `id` int(5) unsigned NOT NULL auto_increment,
  `season` tinyint(2) NOT NULL default '0',
  `div_id` tinyint(1) unsigned NOT NULL default '1',
  `serie` char(1) NOT NULL default '?',
  `order` mediumint(2) NOT NULL default '1',
  `level` tinyint(1) unsigned NOT NULL default '1',
  `category` tinyint(1) unsigned NOT NULL default '1',
  `calendar_id` tinyint(1) unsigned NOT NULL default '1',
  `calendardate_id` mediumint(5) unsigned NOT NULL default '1',
  `first_match_nb` smallint(6) default '1',
  `type_match` tinyint(2) unsigned NOT NULL default '0',
  `top50_limit` tinyint(2) unsigned NOT NULL default '50',
  `match_value` smallint(3) unsigned NOT NULL default '100',
  `week_start_on` enum('-6','-5') NOT NULL default '-5',
  `is_youth_division` enum('Y','N') NOT NULL default 'N',
  `extra_name` varchar(30) default NULL,
  `wo_as_vict` enum('Y','N') NOT NULL default 'N',
  `match_number_scheme` varchar(100) NOT NULL default '#WEEKNAME#/#MATCHNAME#',
  `classement_type` tinyint(1) unsigned NOT NULL default '1',
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `validated_by` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`,`season`),
  KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=478 DEFAULT CHARSET=latin1 PACK_KEYS=0;

CREATE TABLE `divisionresults` (
  `div_id` int(5) unsigned NOT NULL default '0',
  `season` tinyint(2) NOT NULL default '0',
  `week` tinyint(2) unsigned NOT NULL default '0',
  `match_nb` tinyint(1) unsigned NOT NULL default '0',
  `home` tinyint(2) unsigned NOT NULL default '0',
  `away` tinyint(2) unsigned NOT NULL default '0',
  `sets_home` tinyint(2) unsigned NOT NULL default '0',
  `sets_away` tinyint(2) unsigned NOT NULL default '0',
  `match_id` int(10) unsigned NOT NULL default '0',
  `home_wo` enum('N','Y') NOT NULL default 'N',
  `away_wo` enum('N','Y') NOT NULL default 'N',
  `score_modified` enum('Y','N') NOT NULL default 'N',
  `validation_timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `validated_by` int(5) unsigned default NULL,
  PRIMARY KEY  (`div_id`,`season`,`week`,`match_nb`),
  KEY `match_id` (`match_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `divisionresultslog` (
  `div_id` int(5) unsigned NOT NULL default '0',
  `season` tinyint(2) NOT NULL default '0',
  `week` tinyint(2) unsigned NOT NULL default '0',
  `match_nb` tinyint(1) unsigned NOT NULL default '0',
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `modified_by` int(10) unsigned NOT NULL default '0',
  `modification_type` enum('S','M','HW','AW','DC','DM','DD','V','C') NOT NULL default 'S',
  `data` varchar(30) default NULL,
  PRIMARY KEY  (`div_id`,`season`,`week`,`match_nb`,`modified`,`modification_type`),
  KEY `div_id` (`div_id`,`season`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `divisionteaminfo` (
  `div_id` int(5) unsigned NOT NULL default '0',
  `season` tinyint(2) NOT NULL default '0',
  `team_id` tinyint(3) unsigned NOT NULL default '0',
  `club_id` smallint(5) unsigned default '0',
  `indice` char(1) default '?',
  `day_in_week` tinyint(3) default '0',
  `hour` time default '19:45:00',
  `address_id` tinyint(3) unsigned NOT NULL default '1',
  `is_bye` tinyint(1) unsigned NOT NULL default '0',
  `is_withdraw` enum('Y','N') NOT NULL default 'N',
  `in_classement` enum('Y','N') NOT NULL default 'Y',
  PRIMARY KEY  (`div_id`,`season`,`team_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `domaines` (
  `code` int(11) NOT NULL auto_increment,
  `domaine` char(20) NOT NULL default '',
  `description` char(50) NOT NULL default '',
  PRIMARY KEY  (`code`)
) ENGINE=MyISAM AUTO_INCREMENT=255 DEFAULT CHARSET=latin1;

CREATE TABLE `finedescription` (
  `fine_id` mediumint(4) NOT NULL default '0',
  `lang` char(2) NOT NULL default '',
  `description` varchar(255) default NULL,
  PRIMARY KEY  (`fine_id`,`lang`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `fineseasoninfo` (
  `level_id` tinyint(1) unsigned NOT NULL default '5',
  `fine_id` mediumint(4) NOT NULL default '0',
  `season` tinyint(2) unsigned NOT NULL default '0',
  `fine_nr` char(10) default NULL,
  `value` float NOT NULL default '0',
  `max_value` float default NULL,
  `order` mediumint(4) unsigned default NULL,
  PRIMARY KEY  (`level_id`,`fine_id`,`season`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Fine information per season';

CREATE TABLE `languages` (
  `id` char(2) NOT NULL default '',
  `description` varchar(50) NOT NULL default '',
  `order` tinyint(1) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `levelinfo` (
  `id` tinyint(1) unsigned NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default '',
  `order` tinyint(2) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

CREATE TABLE `matchcomments` (
  `id` int(5) unsigned NOT NULL auto_increment,
  `user_id` varchar(32) NOT NULL default '',
  `date` datetime default NULL,
  `IP` varchar(255) NOT NULL default '',
  `match_id` int(10) unsigned NOT NULL default '0',
  `message` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=67 DEFAULT CHARSET=latin1;

CREATE TABLE `matchinfo` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `season` tinyint(2) NOT NULL default '0',
  `competition_id` tinyint(1) unsigned NOT NULL default '1',
  `category` tinyint(2) unsigned NOT NULL default '0',
  `type_match` tinyint(3) unsigned NOT NULL default '0',
  `home_club` smallint(5) unsigned NOT NULL default '0',
  `home_indice` char(1) NOT NULL default '',
  `away_club` smallint(5) unsigned NOT NULL default '0',
  `away_indice` char(1) NOT NULL default '',
  `home_score` tinyint(2) unsigned NOT NULL default '0',
  `away_score` tinyint(2) unsigned NOT NULL default '0',
  `match_ok` enum('Y','N') NOT NULL default 'N',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=35037 DEFAULT CHARSET=latin1;

CREATE TABLE `matchplayer` (
  `match_id` int(10) unsigned NOT NULL default '0',
  `player_nb` tinyint(2) unsigned NOT NULL default '0',
  `home_player_id` int(10) unsigned default NULL,
  `away_player_id` int(11) default NULL,
  `home_vict` tinyint(2) unsigned NOT NULL default '0',
  `away_vict` tinyint(2) unsigned NOT NULL default '0',
  `home_wo` tinyint(1) unsigned NOT NULL default '0',
  `away_wo` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`match_id`,`player_nb`),
  KEY `home_player_id` (`home_player_id`),
  KEY `away_player_id` (`away_player_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `matchresults` (
  `match_id` int(10) unsigned NOT NULL default '0',
  `game_id` tinyint(2) unsigned NOT NULL default '0',
  `set_id` tinyint(2) unsigned NOT NULL default '0',
  `points` tinyint(2) default NULL,
  `home_wo` tinyint(1) unsigned NOT NULL default '0',
  `away_wo` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`match_id`,`game_id`,`set_id`),
  KEY `match_id` (`match_id`,`game_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `matchtypegames` (
  `id` tinyint(2) unsigned NOT NULL auto_increment,
  `game_nb` tinyint(2) unsigned NOT NULL default '0',
  `nb_players` tinyint(2) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`,`game_nb`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

CREATE TABLE `matchtypeinfo` (
  `id` tinyint(2) unsigned NOT NULL default '0',
  `name` varchar(40) NOT NULL default '',
  `nb_single` tinyint(2) unsigned NOT NULL default '4',
  `nb_double` tinyint(1) unsigned NOT NULL default '0',
  `nb_sets` tinyint(2) unsigned NOT NULL default '3',
  `nb_points` tinyint(2) unsigned NOT NULL default '11',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `matchtypeplayer` (
  `match_type_id` tinyint(2) unsigned NOT NULL default '0',
  `game_nb` tinyint(2) unsigned NOT NULL default '0',
  `player_nb` tinyint(2) unsigned NOT NULL default '0',
  `home_player` tinyint(2) unsigned NOT NULL default '0',
  `away_player` tinyint(2) unsigned NOT NULL default '0',
  PRIMARY KEY  (`match_type_id`,`game_nb`,`player_nb`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `nationalities` (
  `code` char(2) NOT NULL default '',
  `name` varchar(60) NOT NULL default '',
  PRIMARY KEY  (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Nationality list';

CREATE TABLE `news` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `creator` int(10) unsigned NOT NULL default '1',
  `categories` varchar(50) default 'NULL',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=77 DEFAULT CHARSET=latin1;

CREATE TABLE `news_text` (
  `news_id` smallint(5) unsigned NOT NULL default '0',
  `lang` char(2) NOT NULL default '',
  `message` text,
  PRIMARY KEY  (`news_id`,`lang`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `notifications` (
  `player_id` int(10) unsigned NOT NULL default '0',
  `div_id` int(5) unsigned NOT NULL default '0',
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`player_id`,`div_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='List of request mail notification on result changes';

CREATE TABLE `playercategories` (
  `id` tinyint(2) unsigned NOT NULL auto_increment,
  `name` varchar(20) NOT NULL default '',
  `sex` enum('M','F') default NULL,
  `non_strict_sex` enum('M','F') default NULL,
  `min_age` tinyint(2) unsigned default NULL,
  `non_strict_min_age` tinyint(2) unsigned default NULL,
  `max_age` tinyint(2) unsigned default NULL,
  `non_strict_max_age` tinyint(2) unsigned default NULL,
  `classementcategory` tinyint(2) unsigned NOT NULL default '1',
  `min_age_year_only` enum('Y','N') default 'Y',
  `max_age_year_only` enum('Y','N') default 'Y',
  `group` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=latin1 COMMENT='Player categories';

CREATE TABLE `playerclassement` (
  `player_id` int(10) unsigned NOT NULL default '0',
  `season` tinyint(2) NOT NULL default '0',
  `category` tinyint(2) unsigned NOT NULL default '1',
  `classement_id` tinyint(2) unsigned NOT NULL default '0',
  PRIMARY KEY  (`player_id`,`season`,`category`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `playerclub` (
  `player_id` int(10) unsigned NOT NULL default '0',
  `season` tinyint(2) NOT NULL default '0',
  `club_id` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`player_id`,`season`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `playerelo` (
  `first_season` tinyint(2) NOT NULL default '1',
  `player_id` int(10) unsigned NOT NULL default '0',
  `class_category` tinyint(2) unsigned NOT NULL default '0',
  `date` date NOT NULL default '0000-00-00',
  `points` int(10) default '500',
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`first_season`,`player_id`,`class_category`,`date`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `playerinfo` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `first_name` varchar(50) NOT NULL default '',
  `last_name` varchar(50) NOT NULL default '',
  `email` varchar(60) NOT NULL default '',
  `photo_filename` varchar(100) default NULL,
  `birthdate` date default NULL,
  `index` mediumint(4) unsigned NOT NULL default '0',
  `vttl_index` int(6) unsigned default NULL,
  `address` varchar(100) NOT NULL default '',
  `postcode` smallint(5) unsigned NOT NULL default '0',
  `sex` enum('M','F') default NULL,
  `nationality` char(2) NOT NULL default 'BE',
  `home_phone` varchar(15) default NULL,
  `office_phone` varchar(15) default NULL,
  `fax` varchar(15) default NULL,
  `gsm` varchar(15) default NULL,
  `medic_validity` date default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `vttl_index` (`vttl_index`)
) ENGINE=MyISAM AUTO_INCREMENT=37258 DEFAULT CHARSET=latin1;

CREATE TABLE `playerlastelo` (
  `player_id` int(10) unsigned NOT NULL default '0',
  `class_category` tinyint(2) unsigned NOT NULL default '0',
  `points` int(10) default '500',
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`player_id`,`class_category`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `playernewclassement` (
  `player_id` int(10) unsigned NOT NULL default '0',
  `season` tinyint(2) NOT NULL default '0',
  `category` tinyint(2) unsigned NOT NULL default '1',
  `method` tinyint(4) unsigned NOT NULL default '3',
  `classement_id` tinyint(2) unsigned NOT NULL default '0',
  `match_count` smallint(3) default '0',
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `modified_by` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`player_id`,`season`,`category`,`method`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `playerstatus` (
  `season` tinyint(4) NOT NULL default '0',
  `player_id` int(10) unsigned NOT NULL default '0',
  `status` enum('I','L','R','S','E','V','A') NOT NULL default 'I',
  `woman_on_men_playerlist` enum('N') default NULL,
  PRIMARY KEY  (`season`,`player_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `postcodes` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `postcode` smallint(4) unsigned NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2921 DEFAULT CHARSET=latin1 COMMENT='Postcode list';

CREATE TABLE `preferences` (
  `id` mediumint(3) unsigned NOT NULL auto_increment,
  `scope` enum('user','club','province','admin') NOT NULL default 'user',
  `name` varchar(18) NOT NULL default '',
  `perms` varchar(255) default NULL,
  `type` enum('string','yesno') NOT NULL default 'string',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

CREATE TABLE `preferencevalues` (
  `id` mediumint(3) unsigned NOT NULL default '0',
  `lang` char(2) default NULL,
  `key_id` int(5) default NULL,
  `value` longtext,
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `modified_by` int(5) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `seasoninfo` (
  `id` tinyint(2) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `start_date` date NOT NULL default '0000-00-00',
  `stop_date` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `teamselection` (
  `season` tinyint(2) unsigned NOT NULL default '0',
  `club_id` smallint(5) unsigned NOT NULL default '0',
  `div_id` int(5) unsigned NOT NULL default '0',
  `team_indice` char(1) NOT NULL default '',
  `week` tinyint(2) unsigned NOT NULL default '0',
  `position` smallint(2) unsigned NOT NULL default '0',
  `player_id` int(10) unsigned NOT NULL default '0',
  `status` enum('P','R','A','?','W','D') default NULL,
  `captain` enum('Y','N') NOT NULL default 'N',
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`season`,`club_id`,`div_id`,`week`,`position`,`team_indice`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Team selection';

CREATE TABLE `teamselectioncomment` (
  `season` tinyint(2) NOT NULL default '0',
  `club_id` smallint(5) NOT NULL default '0',
  `div_id` int(5) NOT NULL default '0',
  `team_indice` char(1) NOT NULL default '',
  `week` tinyint(2) NOT NULL default '0',
  `comment` text,
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`season`,`club_id`,`div_id`,`team_indice`,`week`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `top50` (
  `season` tinyint(2) NOT NULL default '0',
  `div_id` int(5) unsigned NOT NULL default '0',
  `place` tinyint(2) unsigned NOT NULL default '0',
  `player_id` int(10) unsigned default '0',
  `team_id` tinyint(3) unsigned NOT NULL default '0',
  `vict` tinyint(2) unsigned NOT NULL default '0',
  `played` tinyint(2) unsigned NOT NULL default '0',
  PRIMARY KEY  (`season`,`div_id`,`place`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tournamentplayers` (
  `id` int(5) unsigned NOT NULL auto_increment,
  `tournament_id` smallint(3) unsigned NOT NULL default '0',
  `serie_id` mediumint(5) unsigned NOT NULL default '0',
  `player_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=37680 DEFAULT CHARSET=latin1;

CREATE TABLE `tournamentresults` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `tournament_id` smallint(5) unsigned NOT NULL default '0',
  `serie_id` mediumint(5) unsigned NOT NULL default '0',
  `round_id` int(5) unsigned default NULL,
  `match_id` int(6) unsigned default NULL,
  `player_id` int(10) unsigned NOT NULL default '0',
  `opponent_id` int(10) unsigned NOT NULL default '0',
  `player_score` tinyint(1) unsigned NOT NULL default '0',
  `opponent_score` tinyint(1) unsigned NOT NULL default '0',
  `player_wo` enum('Y','N') default 'N',
  `opponent_wo` enum('Y','N') default 'N',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=65732 DEFAULT CHARSET=latin1;

CREATE TABLE `tournamentroundrobininfo` (
  `round_id` int(5) unsigned NOT NULL default '0',
  `group_id` tinyint(2) unsigned NOT NULL default '0',
  `rule` varchar(10) default NULL,
  `subscribing_id` int(5) unsigned NOT NULL default '0',
  `player_number` tinyint(2) unsigned NOT NULL default '0',
  `is_wo` enum('Y','N') NOT NULL default 'N',
  `is_qualified` enum('Y','N') default 'N',
  `is_validated` enum('Y','N') NOT NULL default 'N',
  PRIMARY KEY  (`round_id`,`group_id`,`subscribing_id`,`player_number`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tournamentrounds` (
  `tournament_id` smallint(3) unsigned NOT NULL default '0',
  `serie_id` mediumint(5) unsigned NOT NULL default '0',
  `round_number` tinyint(2) unsigned NOT NULL default '0',
  `round_id` int(5) unsigned default NULL,
  `type` enum('RR','SW','DD') default NULL,
  `name` varchar(40) default NULL,
  `nb_sets` tinyint(1) unsigned default NULL,
  `min_per_group` tinyint(1) unsigned default '3',
  `num_qualified_per_group` tinyint(1) unsigned default '1',
  `first_round_count` smallint(3) unsigned default NULL,
  PRIMARY KEY  (`tournament_id`,`serie_id`,`round_number`),
  UNIQUE KEY `round_id` (`round_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tournaments` (
  `id` smallint(3) unsigned NOT NULL auto_increment,
  `season` smallint(2) unsigned NOT NULL default '0',
  `name` varchar(85) NOT NULL default '',
  `date_from` date NOT NULL default '0000-00-00',
  `date_to` date default NULL,
  `registration_date` date default NULL,
  `authorisation_ref` varchar(20) default NULL,
  `url_rules` varchar(100) default NULL,
  `mail_notification` varchar(200) default NULL,
  `responsible_id` int(10) unsigned default NULL,
  `responsible_phone` varchar(15) default NULL,
  `responsible_fax` varchar(15) default NULL,
  `responsible_email` varchar(50) default NULL,
  `level` tinyint(1) unsigned default NULL,
  `modified` timestamp NULL default NULL,
  `validated_by` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=493 DEFAULT CHARSET=latin1 COMMENT='Tournament list';

CREATE TABLE `tournamentseries` (
  `id` mediumint(5) unsigned NOT NULL auto_increment,
  `tournament_id` smallint(3) unsigned NOT NULL default '0',
  `name` varchar(30) NOT NULL default '',
  `sex` enum('M','F','All') NOT NULL default 'All',
  `classementcategory` tinyint(2) unsigned default '0',
  `player_categories` text,
  `date` date default NULL,
  `time` time default NULL,
  `classements` text NOT NULL,
  `nb_points` tinyint(2) unsigned NOT NULL default '11',
  `nb_sets` tinyint(1) unsigned NOT NULL default '3',
  `match_value` smallint(3) unsigned NOT NULL default '100',
  `round_count` tinyint(2) default '0',
  `price` float default NULL,
  `max_players` smallint(3) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2879 DEFAULT CHARSET=latin1;

CREATE TABLE `user_link` (
  `uid` varchar(32) NOT NULL default '',
  `link_id` smallint(3) unsigned NOT NULL auto_increment,
  `name` varchar(30) NOT NULL default '',
  `link` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`uid`,`link_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
