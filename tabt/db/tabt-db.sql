/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
CREATE TABLE `active_sessions` (
  `sid` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `val` mediumtext COLLATE utf8_unicode_ci,
  `changed` varchar(14) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`name`,`sid`),
  KEY `changed` (`changed`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='List of currently active user session';
CREATE TABLE `apicurrentquota` (
  `id` int(10) unsigned NOT NULL,
  `site_id` tinyint(2) NOT NULL DEFAULT '1',
  `lastused` double NOT NULL,
  `consumed` int(10) unsigned NOT NULL DEFAULT '0',
  `quota` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`site_id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Current use quota per user (or ip if anonymous access)';
CREATE TABLE `apiquota` (
  `player_id` int(11) unsigned NOT NULL,
  `quota` int(11) unsigned NOT NULL,
  PRIMARY KEY (`player_id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Quota table for API use';
CREATE TABLE `apiuse` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `player_id` int(10) unsigned DEFAULT NULL,
  `ip` int(11) unsigned NOT NULL,
  `function` int(11) unsigned NOT NULL,
  `time` int(11) unsigned NOT NULL,
  `called` double NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Some statistics on API usage';
CREATE TABLE `auth_user` (
  `user_id` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `username` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `password` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `perms` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `player_id` int(10) unsigned DEFAULT NULL,
  `conf_id` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `conf_sent_on` timestamp NULL DEFAULT NULL,
  `conf_sent_to` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `restrict_to_ip` int(11) unsigned DEFAULT NULL,
  `api_only` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `k_username` (`username`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='List of authorized users';
CREATE TABLE `auth_user_log` (
  `modified_player_id` int(10) unsigned NOT NULL,
  `old_perms` varchar(255) NOT NULL,
  `new_perms` varchar(255) NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` int(10) unsigned NOT NULL,
  PRIMARY KEY (`modified_player_id`,`modified`),
  KEY `modified_by` (`modified_by`)
) DEFAULT CHARSET=utf8;
CREATE TABLE `belcategoryinfo` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `factor` float unsigned NOT NULL,
  `count_factor` tinyint(2) unsigned DEFAULT '1',
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='List of categories for the ranking system ''BEL';
CREATE TABLE `belcategorypoints` (
  `diff` int(11) DEFAULT NULL,
  `expected` int(11) DEFAULT NULL,
  `unexpected` int(11) DEFAULT NULL
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `calendarchanges` (
  `div_id` int(5) unsigned NOT NULL DEFAULT '0',
  `week` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `match_nb` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `home` tinyint(2) DEFAULT NULL,
  `away` tinyint(2) DEFAULT NULL,
  `address_club_id` smallint(5) unsigned DEFAULT NULL,
  `address_id` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`div_id`,`week`,`match_nb`)
) DEFAULT CHARSET=utf8;
CREATE TABLE `calendardateinfo` (
  `id` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
  `season` tinyint(2) NOT NULL DEFAULT '0',
  `calendar_id` int(5) unsigned NOT NULL DEFAULT '0',
  `name` varchar(80) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Information about calendar date tables';
CREATE TABLE `calendardates` (
  `calendardate_id` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
  `week` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `date` date NOT NULL,
  PRIMARY KEY (`calendardate_id`,`week`),
  KEY `calendardate_id` (`calendardate_id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `calendarinfo` (
  `calendar_id` int(5) unsigned NOT NULL DEFAULT '0',
  `week` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `match_nb` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `home` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `away` tinyint(2) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`calendar_id`,`week`,`match_nb`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `calendartypeinfo` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `nb_team` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `competition_id` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci PACK_KEYS=0 COMMENT='A calendar template for a given type of competition';
CREATE TABLE `calendarweekname` (
  `calendar_id` int(5) unsigned NOT NULL DEFAULT '0',
  `week` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `name` char(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`calendar_id`,`week`),
  KEY `name` (`name`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `classementcategories` (
  `id` smallint(3) NOT NULL DEFAULT '0',
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `classementgroupinfo` (
  `group_id` smallint(3) NOT NULL DEFAULT '0',
  `classementcategory` tinyint(2) DEFAULT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `order` int(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`group_id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `classementgroups` (
  `group_id` smallint(3) NOT NULL DEFAULT '0',
  `classement_id` tinyint(2) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`group_id`,`classement_id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `classementinfo` (
  `id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `category` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `name` varchar(4) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `order` int(4) unsigned DEFAULT NULL,
  `elo_value` int(4) unsigned NOT NULL DEFAULT '0',
  `bel_value` int(4) unsigned NOT NULL DEFAULT '0',
  `team_value` int(3) NOT NULL DEFAULT '0',
  `absolute_value` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`category`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `classementtypeinfo` (
  `id` tinyint(1) DEFAULT NULL,
  `name` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `show` varchar(9) COLLATE utf8_unicode_ci DEFAULT NULL,
  `order` double DEFAULT NULL
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `clubaddressinfo` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `club_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `address_id` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `address` varchar(150) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `zip` smallint(5) unsigned NOT NULL DEFAULT '0',
  `town` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `phone` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fax` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `comment` mediumtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `club_id` (`club_id`,`address_id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `clubcategories` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `levels` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT ':1:2:',
  `menu_order` tinyint(2) NOT NULL DEFAULT '0',
  `group` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `main_level` tinyint(2) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `clubfines` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `season` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `club_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `team_indice` char(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `week_name` char(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `fine_id` int(10) unsigned NOT NULL DEFAULT '0',
  `div_id` int(5) unsigned DEFAULT NULL,
  `match_nb` tinyint(1) unsigned DEFAULT NULL,
  `comment` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `value` float NOT NULL DEFAULT '0',
  `linked_fine_id` int(10) unsigned DEFAULT NULL,
  `linked_club_id` smallint(5) unsigned DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `club_id` (`club_id`),
  KEY `clubfines_season` (`season`,`week_name`,`div_id`,`match_nb`) USING BTREE
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `clubresponsabilityinfo` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `lang` char(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'nl',
  `name` varchar(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`lang`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `clubresponsibles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `season` tinyint(2) NOT NULL DEFAULT '0',
  `club_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `responsability_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `player_id` int(10) unsigned NOT NULL DEFAULT '0',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `clubs` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `category` smallint(5) NOT NULL DEFAULT '1',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `short_name` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `indice` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `site` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `admin_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `admin_mail` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_english` tinyint(1) unsigned DEFAULT '0',
  `is_french` tinyint(1) unsigned DEFAULT '0',
  `is_dutch` tinyint(1) unsigned DEFAULT '0',
  `is_german` tinyint(1) unsigned DEFAULT '0',
  `is_dead` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `first_season` tinyint(2) unsigned DEFAULT NULL,
  `last_season` tinyint(2) unsigned DEFAULT NULL,
  `is_category_default` enum('Y','N') COLLATE utf8_unicode_ci DEFAULT 'N',
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `competitioninfo` (
  `id` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='List of competition type';
CREATE TABLE `controlgroupinfo` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(25) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `classementcategory` tinyint(2) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `controlgrouprules` (
  `season` tinyint(2) NOT NULL,
  `control_group_id` int(5) unsigned NOT NULL,
  `rule_id` int(5) unsigned NOT NULL,
  `active` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`season`,`control_group_id`,`rule_id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `controlresultinfo` (
  `season` tinyint(2) NOT NULL,
  `control_group_id` int(5) unsigned NOT NULL,
  `wname` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  `status` enum('Initial','Running','Executed','Aborted') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Initial',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` int(5) unsigned NOT NULL,
  PRIMARY KEY (`season`,`control_group_id`,`wname`),
  KEY `FK_controltype` (`control_group_id`),
  KEY `FK_modifiedby` (`modified_by`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `controlresults` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `season` tinyint(2) NOT NULL,
  `control_group_id` int(5) unsigned NOT NULL,
  `wname` varchar(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `div_id` int(10) unsigned DEFAULT NULL,
  `match_nb` tinyint(1) unsigned DEFAULT NULL,
  `match_id` int(10) unsigned DEFAULT NULL,
  `deviation_type` int(10) unsigned DEFAULT NULL,
  `data` blob,
  PRIMARY KEY (`id`),
  KEY `FK_controltype` (`control_group_id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `divisioncategories` (
  `id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `classementcategory` tinyint(3) DEFAULT NULL,
  `playercategory` tinyint(2) NOT NULL,
  `division_name_prefix` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `first_season` tinyint(2) DEFAULT NULL,
  `last_season` tinyint(2) DEFAULT NULL,
  `order` int(2) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `divisioninfo` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `season` tinyint(2) NOT NULL DEFAULT '0',
  `div_id` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `serie` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '?',
  `order` mediumint(2) NOT NULL DEFAULT '1',
  `level` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `category` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `calendar_id` int(5) unsigned NOT NULL DEFAULT '0',
  `calendardate_id` mediumint(5) unsigned NOT NULL DEFAULT '1',
  `first_match_nb` smallint(6) DEFAULT '1',
  `match_type_id` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `top50_limit` tinyint(2) unsigned NOT NULL DEFAULT '50',
  `match_value` smallint(3) unsigned NOT NULL DEFAULT '100',
  `week_start_on` enum('-6','-5') COLLATE utf8_unicode_ci NOT NULL DEFAULT '-5',
  `is_youth_division` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `min_age_category` tinyint(2) unsigned DEFAULT NULL,
  `max_age_category` tinyint(2) unsigned DEFAULT NULL,
  `min_nb_youth_players` tinyint(2) unsigned DEFAULT NULL,
  `extra_name` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `wo_as_vict` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `wo_is_one_point` enum('Y','N') COLLATE utf8_unicode_ci DEFAULT 'N',
  `match_number_scheme` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '#WEEKNAME#/#MATCHNAME#',
  `classement_type` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bel_category` int(4) unsigned NOT NULL DEFAULT '0',
  `control_group_id` int(5) unsigned NOT NULL DEFAULT '1',
  `use_start_points` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `type` tinyint(2) unsigned NOT NULL DEFAULT '1',
  `source_category` tinyint(2) unsigned DEFAULT NULL,
  `draw_type` tinyint(4) DEFAULT '0',
  `auto_validation` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `responsible_id` int(10) unsigned DEFAULT NULL,
  `fixed_team` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `promoted_teams` tinyint(2) unsigned DEFAULT NULL,
  `relegated_teams` tinyint(2) unsigned DEFAULT NULL,
  `team_score_limit_day` tinyint(2) unsigned DEFAULT NULL,
  `team_score_limit_time` time DEFAULT NULL,
  `detailed_score_limit_day` tinyint(2) unsigned DEFAULT NULL,
  `detailed_score_limit_time` time DEFAULT NULL,
  `detailed_score_limit_method` enum('Match','Week') COLLATE utf8_unicode_ci DEFAULT NULL,
  `team_score_limit_method` enum('Match','Week') COLLATE utf8_unicode_ci DEFAULT NULL,
  `validated_by` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`,`season`),
  KEY `id` (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci PACK_KEYS=0;
CREATE TABLE `divisionresults` (
  `div_id` int(5) unsigned NOT NULL DEFAULT '0',
  `season` tinyint(2) NOT NULL DEFAULT '0',
  `week` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `match_nb` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `home` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `away` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `sets_home` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `sets_away` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `points_home` smallint(2) NOT NULL DEFAULT '0',
  `points_away` smallint(2) NOT NULL DEFAULT '0',
  `match_id` int(10) unsigned NOT NULL DEFAULT '0',
  `home_wo` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `away_wo` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `score_modified` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `validation_timestamp` timestamp NULL DEFAULT NULL,
  `validated_by` int(5) unsigned DEFAULT NULL,
  `lock_type` enum('B','H','A','N') COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'B=Both, H=Home, A=Away, N (or NULL)=None',
  `lock_timestamp` timestamp NULL DEFAULT NULL,
  `locked_by` int(5) unsigned DEFAULT NULL,
  `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`div_id`,`season`,`week`,`match_nb`),
  KEY `match_id` (`match_id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `divisionresultslog` (
  `div_id` int(5) unsigned NOT NULL DEFAULT '0',
  `season` tinyint(2) NOT NULL DEFAULT '0',
  `week` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `match_nb` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` int(10) unsigned NOT NULL DEFAULT '0',
  `modification_type` enum('S','M','HW','AW','FF','DC','DM','DD','CD','V','C','P','L','VA','UV','VR') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'S',
  `data` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`div_id`,`season`,`week`,`match_nb`,`modified`,`modification_type`),
  KEY `div_id` (`div_id`,`season`),
  KEY `season_modified` (`season`,`modified`) USING BTREE
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `divisionroundinfo` (
  `div_id` int(5) unsigned NOT NULL DEFAULT '0',
  `season` tinyint(2) NOT NULL DEFAULT '0',
  `week` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `nb_team` int(5) unsigned NOT NULL DEFAULT '0',
  `is_validated` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `check_week` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `check_count` int(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`div_id`,`season`,`week`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `divisionsynchronisation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `season` tinyint(2) NOT NULL DEFAULT '0',
  `div_id` int(5) unsigned NOT NULL DEFAULT '0',
  `type` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'af',
  `data` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `divisionteaminfo` (
  `div_id` int(5) unsigned NOT NULL DEFAULT '0',
  `season` tinyint(2) NOT NULL DEFAULT '0',
  `team_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `club_id` smallint(5) unsigned DEFAULT '0',
  `indice` char(1) COLLATE utf8_unicode_ci DEFAULT '',
  `day_in_week` tinyint(3) DEFAULT '0',
  `hour` time DEFAULT '19:45:00',
  `max_team_value` int(3) unsigned DEFAULT NULL,
  `start_points` smallint(5) DEFAULT NULL,
  `address_id` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `is_bye` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_withdraw` enum('Y','N','1','2') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `in_classement` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`div_id`,`season`,`team_id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `divisionteamplayers` (
  `div_id` int(5) unsigned NOT NULL,
  `season` tinyint(2) NOT NULL,
  `team_id` tinyint(3) unsigned NOT NULL,
  `player_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`div_id`,`season`,`team_id`,`player_id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `domaines` (
  `code` int(11) NOT NULL AUTO_INCREMENT,
  `domaine` char(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` char(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`code`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `finedescription` (
  `fine_id` mediumint(4) NOT NULL DEFAULT '0',
  `lang` char(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`fine_id`,`lang`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `finedeviations` (
  `deviation_type` int(10) unsigned NOT NULL,
  `fine_id` mediumint(4) NOT NULL
) DEFAULT CHARSET=utf8 COMMENT='List of fines to be applicable per detected deviation';
CREATE TABLE `fineseasoninfo` (
  `level_id` tinyint(1) unsigned NOT NULL DEFAULT '5',
  `fine_id` mediumint(4) NOT NULL DEFAULT '0',
  `season` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `fine_nr` char(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `value` float NOT NULL DEFAULT '0',
  `max_value` float DEFAULT NULL,
  `order` mediumint(4) unsigned DEFAULT NULL,
  PRIMARY KEY (`level_id`,`fine_id`,`season`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Fine information per season';
CREATE TABLE `languages` (
  `id` char(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `order` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `levelinfo` (
  `id` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `order` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `export_name` varchar(4) COLLATE utf8_unicode_ci DEFAULT NULL,
  `division_name_prefix` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `responsible_id` int(10) unsigned DEFAULT NULL,
  `has_match_referee` tinyint(1) DEFAULT '1',
  `has_room_responsible` tinyint(1) DEFAULT '0',
  `first_season` tinyint(2) unsigned DEFAULT NULL,
  `last_season` tinyint(2) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `matchcomments` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `date` datetime DEFAULT NULL,
  `IP` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `match_id` int(10) unsigned NOT NULL DEFAULT '0',
  `message` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `private` enum('Y','N') COLLATE utf8_unicode_ci DEFAULT 'N',
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `matchinfo` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `season` tinyint(2) NOT NULL DEFAULT '0',
  `competition_id` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `category` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `match_type_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `home_club` smallint(5) unsigned NOT NULL DEFAULT '0',
  `home_indice` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `away_club` smallint(5) unsigned NOT NULL DEFAULT '0',
  `away_indice` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `home_score` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `away_score` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `match_ok` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `home_captain_player_id` int(10) unsigned DEFAULT NULL,
  `away_captain_player_id` int(10) unsigned DEFAULT NULL,
  `referee_player_id` int(10) unsigned DEFAULT NULL,
  `room_responsible_player_id` int(10) unsigned DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `matchplayer` (
  `match_id` int(10) unsigned NOT NULL DEFAULT '0',
  `player_nb` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `team_nb` tinyint(1) NOT NULL DEFAULT '1',
  `home_player_id` int(10) unsigned DEFAULT NULL,
  `away_player_id` int(10) unsigned DEFAULT NULL,
  `home_vict` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `away_vict` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `home_def` tinyint(2) unsigned DEFAULT '0',
  `away_def` tinyint(2) unsigned DEFAULT '0',
  `home_wo` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `away_wo` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`match_id`,`player_nb`,`team_nb`),
  KEY `home_player_id` (`home_player_id`),
  KEY `away_player_id` (`away_player_id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `matchplayerexception` (
  `match_id` int(10) unsigned NOT NULL,
  `game_nb` tinyint(2) unsigned NOT NULL,
  `home_player_nb` tinyint(2) unsigned DEFAULT NULL,
  `away_player_nb` tinyint(2) unsigned DEFAULT NULL,
  PRIMARY KEY (`match_id`,`game_nb`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Adds exception to standard match schema';
CREATE TABLE `matchresults` (
  `match_id` int(10) unsigned NOT NULL DEFAULT '0',
  `game_id` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `set_id` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `points` tinyint(2) DEFAULT NULL,
  `home_wo` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `away_wo` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`match_id`,`game_id`,`set_id`),
  KEY `match_id` (`match_id`,`game_id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `matchtypegames` (
  `id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `game_nb` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `nb_players` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `show_draw_only` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`,`game_nb`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `matchtypeinfo` (
  `id` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `name` varchar(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `nb_single` tinyint(2) unsigned NOT NULL DEFAULT '4',
  `nb_double` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `nb_sets` tinyint(2) unsigned NOT NULL DEFAULT '3',
  `nb_points` tinyint(2) unsigned NOT NULL DEFAULT '11',
  `force_double_teams` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `nb_substitutes` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `nb_single_optional` tinyint(1) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `matchtypeplayer` (
  `match_type_id` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `game_nb` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `player_nb` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `home_player` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `away_player` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `game_group` int(1) unsigned DEFAULT NULL,
  `allow_substitute` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  PRIMARY KEY (`match_type_id`,`game_nb`,`player_nb`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `nationalities` (
  `code` char(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`code`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Nationality list';
CREATE TABLE `notifications` (
  `player_id` int(10) unsigned NOT NULL DEFAULT '0',
  `div_id` int(5) unsigned NOT NULL DEFAULT '0',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`player_id`,`div_id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='List of request mail notification on result changes';
CREATE TABLE `playerbel` (
  `first_season` tinyint(2) NOT NULL DEFAULT '1',
  `player_id` int(10) unsigned NOT NULL DEFAULT '0',
  `class_category` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `date` date NOT NULL,
  `points` int(10) DEFAULT '500',
  `bonus` int(10) DEFAULT '0',
  `count` int(10) unsigned NOT NULL DEFAULT '0',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `season_count` int(10) unsigned NOT NULL DEFAULT '0',
  `position` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`first_season`,`player_id`,`class_category`,`date`),
  KEY `playerbel_date` (`first_season`,`date`,`class_category`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='BEL points for all players';
CREATE TABLE `playerbeladjustments` (
  `first_season` tinyint(2) NOT NULL DEFAULT '1',
  `player_id` int(10) unsigned NOT NULL DEFAULT '0',
  `class_category` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `date` date NOT NULL,
  `points` int(10) DEFAULT '500',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `reason` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`first_season`,`player_id`,`class_category`,`date`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='BEL adjustment points';
CREATE TABLE `playerbelbonus` (
  `first_season` tinyint(2) NOT NULL DEFAULT '1',
  `player_id` int(10) unsigned NOT NULL DEFAULT '0',
  `class_category` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `date` date NOT NULL,
  `valid_until` date NOT NULL,
  `points` int(10) DEFAULT '500',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `reason` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`first_season`,`player_id`,`class_category`,`date`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='BEL bonus points';
CREATE TABLE `playercategories` (
  `id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `short_name` varchar(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT '???',
  `sex` enum('M','F') COLLATE utf8_unicode_ci DEFAULT NULL,
  `non_strict_sex` enum('M','F') COLLATE utf8_unicode_ci DEFAULT NULL,
  `min_age` tinyint(2) unsigned DEFAULT NULL,
  `non_strict_min_age` tinyint(2) unsigned DEFAULT NULL,
  `max_age` tinyint(2) unsigned DEFAULT NULL,
  `non_strict_max_age` tinyint(2) unsigned DEFAULT NULL,
  `classementcategory` tinyint(2) unsigned NOT NULL DEFAULT '1',
  `min_age_year_only` enum('Y','N') COLLATE utf8_unicode_ci DEFAULT 'Y',
  `max_age_year_only` enum('Y','N') COLLATE utf8_unicode_ci DEFAULT 'Y',
  `group` mediumtext COLLATE utf8_unicode_ci,
  `order` mediumint(3) NOT NULL DEFAULT '0',
  `show_index` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Player categories';
CREATE TABLE `playercategorystatus` (
  `season` tinyint(4) NOT NULL,
  `player_id` int(10) unsigned NOT NULL,
  `category_id` tinyint(2) unsigned NOT NULL,
  `status` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'I',
  KEY `playercategorystatus_seasonsoninfo_FK` (`season`),
  KEY `playercategorystatus_playerinfo_FK` (`player_id`),
  KEY `playercategorystatus_playercategories_FK` (`category_id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Specific status of a player for each player category';
CREATE TABLE `playerclassement` (
  `player_id` int(10) unsigned NOT NULL DEFAULT '0',
  `season` tinyint(2) NOT NULL DEFAULT '0',
  `category` tinyint(2) unsigned NOT NULL DEFAULT '1',
  `classement_id` tinyint(2) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`player_id`,`season`,`category`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `playerclub` (
  `player_id` int(10) unsigned NOT NULL DEFAULT '0',
  `season` tinyint(2) NOT NULL DEFAULT '0',
  `club_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`player_id`,`season`),
  KEY `season_club` (`season`,`club_id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `playerelo` (
  `first_season` tinyint(2) NOT NULL DEFAULT '1',
  `player_id` int(10) unsigned NOT NULL DEFAULT '0',
  `class_category` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `date` date NOT NULL,
  `points` int(10) DEFAULT '500',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`first_season`,`player_id`,`class_category`,`date`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `playerinfo` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `last_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `emailcc` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `photo_filename` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `index` mediumint(4) unsigned NOT NULL DEFAULT '0',
  `vttl_index` int(6) unsigned DEFAULT NULL,
  `national_number` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `postcode` smallint(5) unsigned NOT NULL DEFAULT '0',
  `sex` enum('M','F') COLLATE utf8_unicode_ci DEFAULT NULL,
  `nationality` char(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'BE',
  `home_phone` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `office_phone` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fax` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gsm` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `comment` text COLLATE utf8_unicode_ci,
  `medic_validity` date DEFAULT NULL,
  `is_anonymous` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`),
  UNIQUE KEY `vttl_index` (`vttl_index`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `playerlastelo` (
  `player_id` int(10) unsigned NOT NULL DEFAULT '0',
  `class_category` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `points` int(10) DEFAULT '500',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`player_id`,`class_category`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `playernewclassement` (
  `player_id` int(10) unsigned NOT NULL DEFAULT '0',
  `season` tinyint(2) NOT NULL DEFAULT '0',
  `category` tinyint(2) unsigned NOT NULL DEFAULT '1',
  `method` tinyint(4) unsigned NOT NULL DEFAULT '3',
  `classement_id` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `match_count` smallint(3) DEFAULT '0',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`player_id`,`season`,`category`,`method`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `playerstatus` (
  `season` tinyint(4) NOT NULL DEFAULT '0',
  `player_id` int(10) unsigned NOT NULL DEFAULT '0',
  `status` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'I',
  PRIMARY KEY (`season`,`player_id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `playerstatusinfo` (
  `status` char(1) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `order` tinyint(2) NOT NULL DEFAULT '1',
  `unique_index_modifier` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `is_playerlist` tinyint(1) DEFAULT '1',
  `is_matchsheet` tinyint(1) DEFAULT '1',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`status`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `playerwr` (
  `wr_id` int(10) unsigned NOT NULL,
  `position` int(10) unsigned NOT NULL,
  `player_id` int(10) unsigned NOT NULL,
  `points` int(10) unsigned NOT NULL
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='World ranking data';
CREATE TABLE `playerwrid` (
  `player_id` int(10) unsigned NOT NULL,
  `ittf_id` int(6) unsigned NOT NULL,
  KEY `playerwrid_playerinfo_FK` (`player_id`),
  CONSTRAINT `playerwrid_playerinfo_FK` FOREIGN KEY (`player_id`) REFERENCES `playerinfo` (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `playerwrinfo` (
  `wr_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `playercategory` tinyint(4) NOT NULL,
  `date` date NOT NULL,
  `name` char(7) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`wr_id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Information about the world rankings';
CREATE TABLE `postcodes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `postcode` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Postcode list';
CREATE TABLE `preferences` (
  `id` mediumint(3) unsigned NOT NULL AUTO_INCREMENT,
  `scope` enum('user','club','province','admin') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'user',
  `name` varchar(18) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `perms` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` enum('string','yesno','int') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'string',
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `preferencevalues` (
  `id` mediumint(3) unsigned NOT NULL DEFAULT '0',
  `lang` char(2) COLLATE utf8_unicode_ci DEFAULT NULL,
  `key_id` int(5) DEFAULT NULL,
  `site_id` tinyint(2) DEFAULT NULL,
  `value` longtext COLLATE utf8_unicode_ci,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` int(5) DEFAULT NULL
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `seasoninfo` (
  `id` tinyint(2) NOT NULL DEFAULT '0',
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `start_date` date NOT NULL,
  `stop_date` date NOT NULL,
  `competition_start_date` date DEFAULT NULL,
  `elo_correction` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `teamselection` (
  `season` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `club_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `div_id` int(5) unsigned NOT NULL DEFAULT '0',
  `team_indice` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `week` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `position` smallint(2) unsigned NOT NULL DEFAULT '0',
  `player_id` int(10) unsigned NOT NULL DEFAULT '0',
  `status` enum('P','R','A','?','W','D') COLLATE utf8_unicode_ci DEFAULT NULL,
  `captain` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`season`,`club_id`,`div_id`,`week`,`position`,`team_indice`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Team selection';
CREATE TABLE `teamselectioncomment` (
  `season` tinyint(2) NOT NULL DEFAULT '0',
  `club_id` smallint(5) NOT NULL DEFAULT '0',
  `div_id` int(5) NOT NULL DEFAULT '0',
  `team_indice` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `week` tinyint(2) NOT NULL DEFAULT '0',
  `comment` mediumtext COLLATE utf8_unicode_ci,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`season`,`club_id`,`div_id`,`team_indice`,`week`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `top50` (
  `season` tinyint(2) NOT NULL DEFAULT '0',
  `div_id` int(5) unsigned NOT NULL DEFAULT '0',
  `place` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `player_id` int(10) unsigned DEFAULT '0',
  `team_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `vict` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `played` tinyint(2) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`season`,`div_id`,`place`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `tournamentlog` (
  `tournament_id` int(10) unsigned NOT NULL DEFAULT '0',
  `serie_id` int(10) unsigned NOT NULL DEFAULT '0',
  `modification_id` int(10) unsigned NOT NULL DEFAULT '0',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` int(10) unsigned NOT NULL DEFAULT '0',
  `modification_type` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'S',
  `data` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`tournament_id`,`serie_id`,`modification_id`,`modified`,`modification_type`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `tournamentmanagers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tournament_id` int(10) unsigned DEFAULT '0',
  `player_id` int(10) unsigned DEFAULT '0',
  `added_by` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `tournamentplayers` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `tournament_id` int(10) unsigned NOT NULL DEFAULT '0',
  `serie_id` int(10) unsigned NOT NULL DEFAULT '0',
  `player_id` int(10) unsigned DEFAULT NULL,
  `registration_date` timestamp NULL DEFAULT NULL,
  `linked_player_id` int(10) unsigned DEFAULT NULL,
  `registered_by` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `serie_id` (`serie_id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `tournamentregularityrankinginfo` (
  `id` int(11) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COMMENT='List of configuration types for regularity rankings';
CREATE TABLE `tournamentregularityrankings` (
  `regularityranking_id` int(11) unsigned NOT NULL,
  `min_participants` int(11) unsigned DEFAULT NULL,
  `max_participants` int(11) unsigned DEFAULT NULL,
  `round_id` int(5) unsigned NOT NULL,
  `winner_points` int(11) NOT NULL,
  `loser_points` int(11) NOT NULL,
  KEY `regularityrankingid_tournamentregularityrankinginfo_FK` (`regularityranking_id`),
  KEY `roundid_tournamentstandardrounds_FK` (`round_id`)
) DEFAULT CHARSET=utf8 COMMENT='Details of the regularity ranking';
CREATE TABLE `tournamentresults` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tournament_id` int(10) unsigned NOT NULL DEFAULT '0',
  `serie_id` int(10) unsigned NOT NULL DEFAULT '0',
  `round_id` int(5) unsigned DEFAULT NULL,
  `match_id` int(6) unsigned DEFAULT NULL,
  `player_id` int(10) unsigned NOT NULL DEFAULT '0',
  `opponent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `player_score` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `opponent_score` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `player_wo` enum('Y','N') COLLATE utf8_unicode_ci DEFAULT 'N',
  `opponent_wo` enum('Y','N') COLLATE utf8_unicode_ci DEFAULT 'N',
  PRIMARY KEY (`id`),
  KEY `serie_id` (`serie_id`),
  KEY `player_id` (`player_id`) USING BTREE,
  KEY `opponent_id` (`opponent_id`) USING BTREE
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `tournamentroundrobininfo` (
  `round_id` int(5) unsigned NOT NULL DEFAULT '0',
  `group_id` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `rule` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subscribing_id` int(5) unsigned NOT NULL DEFAULT '0',
  `player_number` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `is_wo` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `is_qualified` enum('Y','N') COLLATE utf8_unicode_ci DEFAULT 'N',
  `is_validated` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  PRIMARY KEY (`round_id`,`group_id`,`subscribing_id`,`player_number`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `tournamentrounds` (
  `tournament_id` int(10) unsigned NOT NULL DEFAULT '0',
  `serie_id` int(10) unsigned NOT NULL DEFAULT '0',
  `round_number` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `round_id` int(5) unsigned DEFAULT NULL,
  `type` enum('RR','SW','DD') COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nb_sets` tinyint(1) unsigned DEFAULT NULL,
  `min_per_group` tinyint(1) unsigned DEFAULT '3',
  `num_qualified_per_group` tinyint(1) unsigned DEFAULT '1',
  `first_round_count` smallint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`tournament_id`,`serie_id`,`round_number`),
  UNIQUE KEY `round_id` (`round_id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `tournaments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `season` smallint(2) unsigned NOT NULL DEFAULT '0',
  `name` varchar(85) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `short_name` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_from` date NOT NULL,
  `date_to` date DEFAULT NULL,
  `address_venue` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address_street` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address_zip` int(10) DEFAULT NULL,
  `address_town` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `registration_date` date DEFAULT NULL,
  `authorisation_ref` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `url_rules` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `max_serie` tinyint(2) DEFAULT '0',
  `max_serie_per_day` tinyint(2) DEFAULT '0',
  `mail_notification` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `responsible_id` int(10) unsigned DEFAULT NULL,
  `responsible_phone` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `responsible_fax` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `responsible_email` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `level` tinyint(1) unsigned DEFAULT NULL,
  `comment` longtext COLLATE utf8_unicode_ci,
  `mail_comment` longtext COLLATE utf8_unicode_ci,
  `modified` timestamp NULL DEFAULT NULL,
  `validated_by` int(10) unsigned DEFAULT NULL,
  `open_registration` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
  `bel_category` int(4) unsigned NOT NULL DEFAULT '0',
  `regularity_ranking` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Tournament list';
CREATE TABLE `tournamentseries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tournament_id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `serie_type` enum('single','double','mixed') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'single',
  `internal_id` int(1) DEFAULT NULL,
  `sex` enum('M','F','All') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'All',
  `classementcategory` tinyint(2) unsigned DEFAULT '0',
  `birthyear_from` smallint(5) unsigned DEFAULT NULL,
  `birthyear_to` smallint(5) unsigned DEFAULT NULL,
  `player_categories` mediumtext COLLATE utf8_unicode_ci,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `classements` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `statutes` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nb_points` tinyint(2) unsigned NOT NULL DEFAULT '11',
  `nb_sets` tinyint(1) unsigned NOT NULL DEFAULT '3',
  `round_count` tinyint(2) DEFAULT '0',
  `price` float DEFAULT NULL,
  `max_players` smallint(3) NOT NULL DEFAULT '0',
  `on_player_card` enum('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y',
  `club_category` smallint(5) unsigned DEFAULT NULL,
  `bel_category` int(4) unsigned DEFAULT NULL,
  `regularity_ranking_type` int(11) unsigned DEFAULT NULL,
  `sync_series` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tournament_id` (`tournament_id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `tournamentstandardrounds` (
  `id` int(5) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `winner_name` varchar(100) DEFAULT NULL,
  `loser_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COMMENT='List of standard tournament rounds';
/*!40101 SET character_set_client = @saved_cs_client */;

/* Add tables relationships */
ALTER TABLE `playercategorystatus` ADD CONSTRAINT `playercategorystatus_playercategories_FK` FOREIGN KEY (`category_id`) REFERENCES `playercategories` (`id`);
ALTER TABLE `playercategorystatus` ADD CONSTRAINT `playercategorystatus_playerinfo_FK` FOREIGN KEY (`player_id`) REFERENCES `playerinfo` (`id`);
ALTER TABLE `playercategorystatus` ADD CONSTRAINT `playercategorystatus_seasonsoninfo_FK` FOREIGN KEY (`season`) REFERENCES `seasoninfo` (`id`);
ALTER TABLE `tournamentregularityrankings` ADD CONSTRAINT `regularityrankingid_tournamentregularityrankinginfo_FK` FOREIGN KEY (`regularityranking_id`) REFERENCES `tournamentregularityrankinginfo` (`id`);
ALTER TABLE `tournamentregularityrankings` ADD CONSTRAINT `roundid_tournamentstandardrounds_FK` FOREIGN KEY (`round_id`) REFERENCES `tournamentstandardrounds` (`id`);

