<?php
/** \internal
 ****************************************************************************
 * TabT API
 *  A programming interface to access information managed
 *  by TabT, the table tennis information manager.
 * -----------------------------------------------------------------
 * TabT API main code
 * -----------------------------------------------------------------
 * @version 0.8
 * -----------------------------------------------------------------
 * Copyright (C) 2007-2011 Gaëtan Frenoy (gaetan@frenoy.net)
 * -----------------------------------------------------------------
 * This file is part of TabT API
 *
 * TabT API is free software: you can redistribute it and/or modify
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
 * along with TabT API.  If not, see <http://www.gnu.org/licenses/>.
 **************************************************************************/

/**
 * @mainpage TabT API Documentation
 *
 * @htmlonly
 * <h3>The programming interface (API) of TabT</h3>
 * <p>To allow developpers and creative webmasters integrating data gathered in the TabT software, TabT provides 
 * a generic programming interface (in short "API") that can be used from any language and any platform.</p>
 * <p>Everbody can consequently create his/her own application (club results management, tournament software, ...) of 
 * website (for his/her own club) based on the powerful TabT engine.</p>
 * <p><img src="http://tabt.frenoy.net/data/documents/tabt-api-overview-en.png" alt="image" border="0">
 * <h3>Quick start</h3>
 * See the list of <a href="group__TabTAPIfunctions.html">available functions</a>.
 * <h3>"SOAP" and "Web Services" technologies</h3>
 * <p>The TabT API is based on <a href="http://en.wikipedia.org/wiki/SOAP">SOAP</a> and
 * <a href="http://en.wikipedia.org/wiki/Web_service">Web service</a> technologies. Those frameworks are now available
 * for any modern programming language like PHP, C#, VB.NET or java.</p>
 * <h3>License</h3>
 * <a href="http://www.fsf.org/licensing/licenses/agpl-3.0.html">
 * <p><img align="left" margin="2" src="http://api.frenoy.net/tabtapi-doc-src/media/agplv3-88x31.png" border="0" title="GNU Affero General Public License"></a>
 * TabT API is released under the terms of version 3 of the <a href="http://www.fsf.org/licensing/licenses/agpl-3.0.html">
 * GNU Affero General Public License</a> that is is a free, copyleft license for software, specifically designed to 
 * ensure cooperation with the community in the case of network server software.  The GNU Affero GPL is designed 
 * specifically to ensure that the modified source code becomes available to the community. It requires the operator of
 * a network server to provide the source code of the modified version running there to the users of that server.
 * Therefore, public use of a modified version, on a publicly accessible server, gives the public access to the source
 * code of the modified version.</p>
 * @endhtmlonly
 */

/**
 * TabT API types definition
 */
if (!include_once('tabtapi_types.php')) {
   print('Cannot include TabT API types, please contact server administrator.');
   exit();
}

/**
 * @defgroup TabTAPIfunctions TabT API functions
 * @brief All TabT API functions
 * @{
 */

/**
 * @brief Dummy test function to verify connectivity
 *
 * Before trying anything else, you should try to call this function that will return (see ::TestResponse) some basic
 * information about the API server (like server timestamp and API version).
 * If you specify your credentials in the request (see ::TestRequest), the server will also tell if you have
 * a valid account on the server and which language you are currently using.
 *
 * @param[in] $Request TestRequest
 * @return TestResponse
 * @see TestRequest, TestResponse
 * @author Gaëtan Frenoy <gaetan@frenoy.net>
 * @version 0.6
 * @ingroup TabTAPIfunctions
 */
function Test(stdClass $Request) {
  $Credentials = $Request->Credentials;

  $permissions = _GetPermissions($Credentials);

  $res = array('Timestamp'      => date("c"),
               'ApiVersion'     => TABTAPI_VERSION,
               'IsValidAccount' => $permissions!='',
               'Language'       => $GLOBALS['lang'],
               'Database'       => $GLOBALS['site_info']['database']);

  return $res;
}

/**
 * @brief GetSeasons returns the list of seasons available in the TabT database.
 *
 * Each season is identified by a unique positive number and a name.
 * As an example, season ID of season 2007-2008 is 8.
 *
 * Here is the returned data if 2008-2009 is the current season
 * <ul>
 *  <li><code>1 | 2001-2002 | false</code></li>
 *  <li><code>2 | 2002-2003 | false</code></li>
 *  <li>(...)</li>
 *  <li><code>7 | 2007-2008 | false</code></li>
 *  <li><code>8 | 2008-2009 | true</code></li>
 * </ul>
 *
 * GetSeasons also returns the current season that is used in all other functions as
 * the default season (when not specified explicitely)
 *
 * @param[in] $Credentials Optional identification of the caller
 * @return GetSeasonsResponse
 * @since Version 0.5
 * @see CredentialsType, GetSeasonsResponse
 * @ingroup TabTAPIfunctions
 */
function GetSeasons(stdClass $Credentials) {
  $permissions = _GetPermissions($Credentials);

  $res = array();
  
  $db = new DB_Session("SELECT id, name FROM seasoninfo;");
  while ($db->next_record()) {
    $res[] = array('Season'    => $db->Record['id'],
                   'Name'      => $db->Record['name'],
                   'IsCurrent' => $db->Record['id']==$GLOBALS['site_info']['season']);
    if ($db->Record['id']==$GLOBALS['site_info']['season']) {
      $CurrentSeason     = $db->Record['id'];
      $CurrentSeasonName = $db->Record['name'];
    }
  }
  $db->free();
  unset($db);

  return array('CurrentSeason'     => $CurrentSeason,
               'CurrentSeasonName' => $CurrentSeasonName,
               'SeasonEntries'     => $res);

  return $res;
}

/**
 * @brief GetClubTeams returns a list with all the teams of a given club.
 *
 * Each club has one or more teams playing in divisions.  This function lists all
 * the teams of a given club.
 *
 * As an example, here is the returned data for VLB-295 (TTC Werchter) for season 2007-2008
 * <ul>
 *  <li><code>389-7 | A | 389 | Afdeling 1 - Prov. Vl.-B/Br. - Heren     | 1</code></li>
 *  <li><code>390-7 | B | 390 | Afdeling 2A - Prov. Vl.-B/Br. - Heren    | 1</code></li>
 *  <li>(...)</li>
 *  <li><code>397-2 | F | 397 | Afdeling 5B - Prov. Vl.-B/Br. - Heren    | 1</code></li>
 *  <li><code>396-9 | G | 396 | Afdeling 5A - Prov. Vl.-B/Br. - Heren    | 1</code></li>
 *  <li>(...)</li>
 *  <li><code>401-2 | A | 401 | Afdeling 1 - Prov. Vl.-B/Br. - Veteranen | 3</code></li>
 *  <li><code>403-2 | B | 403 | Afdeling 3 - Prov. Vl.-B/Br. - Veteranen | 3</code></li>
 * </ul>
 *
 * @param[in] Request Input parameters
 * @return GetClubTeamsResponse
 * @since Version 0.5
 * @see GetClubTeamsRequest, GetClubTeamsResponse
 * @ingroup TabTAPIfunctions
 * @version 0.7.1
 */
function GetClubTeams(stdClass $Request) {
  // Extract function arguments
  $Credentials = $Request->Credentials;
  $Club        = trim($Request->Club);
  $Season      = $Request->Season;

  // Check permissions
  $permissions = _GetPermissions($Credentials);

  // Get database connection
  $db = new DB_Session();
  
  // Check season
  if (!isset($Season) || $Season=='') {
    $Season = $db->select_one("SELECT MAX(id) FROM seasoninfo;");
  }
  if (!is_numeric($Season)) {
    throw new SoapFault('6', "Season [{$Season}] is not valid, must be numeric.");
  }
  if ($db->select_one("SELECT COUNT(*) FROM seasoninfo WHERE id={$Season}") == 0) {
    throw new SoapFault('7', "Season [{$Season}] is not valid.");
  }

  // Check club
  $Club = str_replace(array('-','/',' '), '', strtoupper($Club));
  if ($Club == '') {
    throw new SoapFault('17', "Club is not valid, cannot be empty.");
  }
    $q = "SELECT id FROM clubs AS c WHERE REPLACE(REPLACE(REPLACE(UCASE(c.indice), ' ', ''), '/', ''), '-', '')='{$Club}' AND (ISNULL(c.first_season) OR c.first_season<={$Season}) AND (ISNULL(c.last_season) OR c.last_season>{$Season})";
  list($ClubId, $ClubName) = $db->select_one_array($q);
  if (!is_numeric($ClubId) || $ClubId < 0) {
    throw new SoapFault('9', "Club [{$Club}] is not valid.");
  }
  
  $q = <<<EOQ
SELECT
  CONCAT(di.id,'-',dti.team_id) as teamid,
  dti.indice as team,
  di.id as div_id,
  di.category as divisioncategory
FROM
  divisionteaminfo dti,
  divisioninfo di
WHERE 1
  AND dti.season={$Season}
  AND dti.club_id={$ClubId}
  AND di.id=dti.div_id
ORDER BY
  di.category ASC, dti.indice ASC
EOQ;
  $db->query($q);

  $TeamEntries = array();
  
  while ($db->next_record()) {
    $TeamEntries[] = array('TeamId'           => $db->Record['teamid'],
                           'Team'             => $db->Record['team'],
                           'DivisionId'       => $db->Record['div_id'],
                           'DivisionName'     => utf8_encode(create_division_title_text(get_division_info($Season, $db->Record['div_id']), true)),
                           'DivisionCategory' => $db->Record['divisioncategory']);
  }
  $db->free();
  unset($db);

  return array('ClubName'    => $ClubName,
               'TeamCount'   => count($TeamEntries),
               'TeamEntries' => $TeamEntries);
}

/**
 * @brief Returns ranking of given division for a given week
 *
 * <p>GetDivisionRanking returns the ranking of all teams playing with a division after a given week.
 * If no week is given, the latest one will be selected automatically.  The last week means
 * the week that starts just before the current date.</p>
 * <p>With GetDivisionRanking, the webmaster can display on his/her website the ranking of the
 * division where the teams of his/her club are playing.</p>
 *
 * <p>Example for division #390 after 18 weeks of VTTL competition (season 2007-2008):</p>
 * <code>Afdeling 2A - Prov. Vl.-B/Br. - Heren</code>
 * <ul>
 *  <li><code>1 | T.T. Groot-Bijgaarden A | 18 | 13 | 2 | 3 | 196 |  92 | 641 | 378 | 29</code></li>
 *  <li><code>2 | Werchter B              | 18 | 11 | 4 | 3 | 158 | 130 | 525 | 400 | 25</code></li>
 *  <li><code>3 | Hurricane TTW C         | 17 | 10 | 2 | 5 | 167 | 104 | 610 | 440 | 25</code></li>
 *  <li><code>4 | T.T.K. Vilvo F          | 17 | 10 | 6 | 1 | 165 | 107 | 525 | 441 | 21</code></li>
 *  <li>(...)</li>
 * </ul>
 *
 * <p>And here is the <a href="http://tabt.frenoy.net/data/documents/tabt-api-getdivisionranking-werchter.png">
 * integration</a> into the <a href="http://www.ttcw.be/">Werchter TTC website</a>.</p>
 *
 * @see GetDivisionRankingRequest, GetDivisionRankingResponse
 * @return GetDivisionRankingResponse
 * @ingroup TabTAPIfunctions
 * @version 0.7.2
 */
function GetDivisionRanking(stdClass $Request)
{
  // Extract function arguments
  $Credentials   = $Request->Credentials;
  $DivisionId    = $Request->DivisionId;
  $WeekName      = trim($Request->WeekName);
  $RankingSystem = $Request->RankingSystem;

  // Check permissions
  $permissions = _GetPermissions($Credentials);

  // Create database session
  $db = new DB_Session();
  $db->Halt_On_Error = 'no';

  // Check that division is a numeric value
  if (!is_numeric($DivisionId))
  {
    throw new SoapFault('2', "DivisionId [{$DivisionId}] is not valid, must be numeric.");
  }
  // Retrieve season when this division was playerd
  $q = "SELECT season, calendar_id FROM divisioninfo WHERE id={$DivisionId};";
  list($season, $calendar_id) = $db->select_one_array($q);
  // Check season
  if (!is_numeric($season) || !($season > -1) || !is_numeric($calendar_id))
  {
    throw new SoapFault('3', "DivisionId [{$DivisionId}] is not valid.");
  }
  // Search week name
  if ($WeekName == '' || !isset($WeekName)) {
    // Week name not given, use last week (so we have the most "up-to-date" ranking)
    $q = "SELECT MAX(week) FROM calendarweekname WHERE calendar_id={$calendar_id};";
    $week = $db->select_one($q);
    $q = "SELECT name FROM calendarweekname WHERE calendar_id={$calendar_id} and week={$week};";
    $DatabaseWeekName = $db->select_one($q);
  } else {
    $WeekName = ltrim($WeekName, '0');
    $q = "SELECT week, name FROM calendarweekname WHERE calendar_id={$calendar_id} and TRIM(LEADING '0' FROM name) LIKE '{$WeekName}';";
    list($week, $DatabaseWeekName) = $db->select_one_array($q);
    if (!is_numeric($week) || !($week > -1)) {
      throw new SoapFault('4', "WeekName [{$WeekName}] is not valid for division [{$DivisionId}]");
    }
  }
  if (trim($RankingSystem) == '')
  {
    // Default ranking system
    $RankingSystem = $db->select_one("SELECT di.classement_type FROM divisioninfo di WHERE di.id={$DivisionId}");
  }
  if (!is_numeric($RankingSystem) || $db->select_one("SELECT COUNT(*) FROM classementtypeinfo WHERE id={$RankingSystem}") != 1)
  {
    throw new SoapFault('31', "Ranking system [{$RankingSystem}] is not valid.");
  }

  // Release database connection
  $db->free();
  unset($db);

  // Execute classement function
  include_once($GLOBALS['site_info']['path'].'public/calendar_fct.php');
  include_once($GLOBALS['site_info']['path'].'public/classement_fct.php');
  $entries = get_classement_for_division($RankingSystem, $season, $DivisionId, '', false, false, null, $DatabaseWeekName, 1, false);
  if (!is_array($entries))
  {
    throw new SoapFault('5', "Unable to process ranking for division [{$DivisionId}], week name [{$WeekName}].");
  }
  $res = array();
  foreach ($entries as $entry)
  {
    $res[] = array('Position'              => $entry[$GLOBALS['str_Place']],
                   'Team'                  => utf8_encode($entry[$GLOBALS['str_TeamName']]),
                   'GamesPlayed'           => $entry[$GLOBALS['str_GamesPlayed']],
                   'GamesWon'              => $entry[$GLOBALS['str_GamesWon']],
                   'GamesLost'             => $entry[$GLOBALS['str_GamesLost']],
                   'GamesDraw'             => $entry[$GLOBALS['str_GamesDraw']],
                   'IndividualMatchesWon'  => $entry[$GLOBALS['str_MatchsWon']],
                   'IndividualMatchesLost' => $entry[$GLOBALS['str_MatchsLost']],
                   'IndividualSetsWon'     => $entry[$GLOBALS['str_SetsWon']],
                   'IndividualSetsLost'    => $entry[$GLOBALS['str_SetsLost']],
                   'Points'                => $entry[$GLOBALS['str_Score']]);
  }

  return array('DivisionName'   => create_division_title_text(
                                     get_division_info($season, $DivisionId), true),
               'RankingEntries' => $res);
}

/**
 * @brief Returns list of matches and, if they are available, the match results.
 *
 * According to the parameters given in the ::GetMatchesRequest, ::GetMatches can be used to
 * extract all matches played into a division, by a club or by a given team.
 *
 * Example for team C of club VLB-225 (Hurricane TTW) for season 2006-2007
 * <ul>
 *  <li><code>01/016 | 01 | 2006-09-15 | 19:45:00 | Hurricane C | Meerdaal D  | 12-4</code></li>
 *  <li><code>02/014 | 02 | 2006-09-22 | 19:45:00 | Essenbeek B | Hurricane C | 6-10</code></li>
 *  <li><code>03/014 | 03 | 2006-09-29 | 19:45:00 | Hurricane C | V.M.S. B    | 12-4</code></li>
 *  <li><code>04/016 | 04 | 2006-10-20 | 19:45:00 | Hurricane C | Essenbeek D | 13-3</code></li>
 *  <li>(...)</li>
 * </ul>
 *
 * @param $Request GetMatchesRequest
 * @return GetMatchesResponse
 * @since Version 0.4
 * @version 0.7.1
 * @see GetMatchesRequest, GetMatchesResponse
 * @ingroup TabTAPIfunctions
 */
function GetMatches(stdClass $Request)
{
  // Extract function arguments
  $Credentials      = $Request->Credentials;
  $DivisionId       = $Request->DivisionId;
  $Club             = trim($Request->Club);
  $Team             = trim(strtoupper($Request->Team));
  $DivisionCategory = $Request->DivisionCategory;
  $Season           = $Request->Season;
  $WeekName         = trim($Request->WeekName);
  $LevelId          = $Request->Level;
  $ShowDivisionName = strtolower(trim($Request->ShowDivisionName));

  // Create database session
  $db = new DB_Session();
  $db->Halt_On_Error = 'no';

  // Check permissions
  $permissions = _GetPermissions($Credentials);

  // Check club & division
  $Club = str_replace(array('-','/',' '), '', strtoupper($Club));
  if (trim($DivisionId) == '' && $Club == '' && $WeekName == '') {
    throw new SoapFault('13', "DivisionId, Club or WeekName must be given.");
  }

  // Check division
  if ($DivisionId != '' && !is_numeric($DivisionId)) {
    throw new SoapFault('2', "DivisionId [{$DivisionId}] is not valid, must be numeric.");
  }
  if (is_numeric($DivisionId) && $db->select_one("SELECT COUNT(*) FROM divisioninfo WHERE id={$DivisionId};")==0) {
    throw new SoapFault('3', "DivisionId [{$DivisionId}] is not valid.");
  }

  // Check season
  if (!isset($Season)) {
    if (is_numeric($DivisionId)) {
      $Season = $db->select_one("SELECT season FROM divisioninfo WHERE id={$DivisionId};");
    } else {
      $Season = $db->select_one("SELECT MAX(id) FROM seasoninfo;");
    }
  }
  if (!is_numeric($Season)) {
    throw new SoapFault('6', "Season [{$Season}] is not valid, must be numeric.");
  }
  if ($db->select_one("SELECT COUNT(*) FROM seasoninfo WHERE id={$Season}") == 0) {
    throw new SoapFault('7', "Season [{$Season}] is not valid.");
  }

  // Check club
  if ($Club != '') {
    $q = "SELECT id FROM clubs AS c WHERE REPLACE(REPLACE(REPLACE(UCASE(c.indice), ' ', ''), '/', ''), '-', '')='{$Club}' AND (ISNULL(c.first_season) OR c.first_season<={$Season}) AND (ISNULL(c.last_season) OR c.last_season>{$Season})";
    $ClubId = $db->select_one($q);
    if (!is_numeric($ClubId) || $ClubId < 0) {
      throw new SoapFault('9', "Club [{$Club}] is not valid.");
    }
    if (is_numeric($DivisionId)) {
      if ($db->select_one("SELECT COUNT(*) FROM divisionteaminfo AS dti WHERE dti.div_id={$DivisionId} AND dti.club_id={$ClubId};") == 0) {
        throw new SoapFault('10', "Club [{$Club}] has no team in division [{$DivisionId}]");
      }
    }
  }
  
  // Check team
  if ($Team != '' && $Club == '') {
      throw new SoapFault('12', "Parameter 'Club' has to be given if parameter 'Team' is set.");
  }
  if ($Team != '') {
    $Team = mysql_real_escape_string($Team);
    if (is_numeric($DivisionId)) {
      if ($db->select_one("SELECT COUNT(*) FROM divisionteaminfo AS dti WHERE dti.div_id={$DivisionId} AND dti.club_id={$ClubId} AND dti.indice='{$Team}';") == 0) {
        throw new SoapFault('11', "Club [{$Club}] has no team [{$Team}] in division [{$DivisionId}]");
      }
    } else {
      if ($db->select_one("SELECT COUNT(*) FROM divisionteaminfo AS dti WHERE dti.club_id={$ClubId} AND dti.indice='{$Team}';") == 0) {
        throw new SoapFault('14', "Club [{$Club}] has no team [{$Team}]");
      }
    }
  }

  // Check division category
  if ($DivisionCategory != '' && !is_numeric($DivisionCategory)) {
    throw new SoapFault('15', "DivisionCategory must be a number ([{$DivisionCategory}]).");
  }
  if ($DivisionCategory != '') {
    if ($db->select_one("SELECT COUNT(*) FROM divisioncategories WHERE id={$DivisionCategory};") == 0) {
      throw new SoapFault('16', "DivisionCategory [{$DivisionCategory}] does not exists.");
    }
  }

  // Check week name
  if ($WeekName != '') {
    $WeekName = ltrim($WeekName, '0');
    $q = "SELECT week, name FROM calendarweekname WHERE TRIM(LEADING '0' FROM name) LIKE '{$WeekName}';";
    list($week, $DatabaseWeekName) = select_one_array($q);
    if (!is_numeric($week) || !($week > -1)) {
      throw new SoapFault('20', "WeekName [{$WeekName}] is not valid.");
    }
  }

  // Check club category
  if ($LevelId != '' && !is_numeric($LevelId)) {
    throw new SoapFault('18', "Level must be a number ([{$LevelId}]).");
  }
  if ($LevelId != '') {
    if ($db->select_one("SELECT COUNT(*) FROM levelinfo WHERE id={$LevelId};") == 0) {
      throw new SoapFault('19', "LevelId [{$LevelId}] does not exists.");
    }
  }

  // Check 'ShowDivisionName' option
  if ($ShowDivisionName != '' && !in_array($ShowDivisionName, array('yes', 'no', 'short'))) {
    throw new SoapFault('21', "ShowDivisionName [{$ShowDivisionName}] is not valid.");
  }

  // More helper functions
  include_once($GLOBALS['site_info']['path'].'public/calendar_fct.php');

  $matchnum_select = get_select_matchnum('wname', 'di', 'cali', false, '', false);
  $date_select     = get_date_select('cd', 'team_home', 'team_away', 'cc', -1, '', '%Y-%m-%d');
  $hour_select     = get_hour_select('team_home', 'cc', true);
  $withdraw_select = get_withdraw_select('divr', ' ');

  $division_where_clause = is_numeric($DivisionId) ? " di.id={$DivisionId}" : '1';
  $club_where_clause = $Club!='' ? "(team_home.club_id={$ClubId} OR team_away.club_id={$ClubId})" : '1';
  $team_where_clause = $Team!='' ? "((team_home.club_id={$ClubId} and team_home.indice='{$Team}') OR (team_away.club_id={$ClubId} AND team_away.indice='{$Team}'))" : '1';
  $divcat_where_clause = is_numeric($DivisionCategory) ? "di.category={$DivisionCategory}" : '1';
  $wname_where_clause = $WeekName!='' ? "wname.name='{$DatabaseWeekName}'" : '1';
  $level_where_clause = is_numeric($LevelId) ? "di.level={$LevelId}" : '1';

  $q = <<<EOQ
SELECT
  di.id as `DivisionId`,
  {$matchnum_select} as `MatchId`,
  wname.name as `WeekName`,
  {$date_select} as `Date`,
  {$hour_select} as `Time`,
  IFNULL(club_home.indice, '-') as `HomeClub`,
  IF(ISNULL(club_home.name), 
     IF(team_home.is_bye,
        CONCAT('{$GLOBALS['str_Bye']} ', team_home.indice),
        '{$GLOBALS['str_UnknownTeam']}'), 
     CONCAT(IFNULL(club_home.short_name, club_home.name), ' ', team_home.indice)
    ) as `HomeTeam`,
  IFNULL(club_away.indice, '-') as `AwayClub`,
  IF(ISNULL(club_away.name),
     IF(team_away.is_bye,
        CONCAT('{$GLOBALS['str_Bye']} ', team_away.indice),
        '{$GLOBALS['str_UnknownTeam']}'), 
     CONCAT(IFNULL(club_away.short_name, club_away.name), ' ', team_away.indice)
    ) as `AwayTeam`,
  IF(ISNULL(club_home.name), 0, team_home.address_id) as `Venue`,
  IF(team_home.is_bye OR team_away.is_bye OR
     IFNULL(divr.home=0 AND divr.away=0 AND divr.home_wo<>'Y' AND divr.away_wo<>'Y' AND divr.score_modified<>'Y', 1),
   '-',
   CONCAT(IFNULL(divr.home,'...'), '-', IFNULL(divr.away,'...'), {$withdraw_select})
    ) as `Score`,
  divr.match_id as `MatchUniqueId`
FROM
 (divisioninfo as di,
  calendarinfo as cali,
  calendardates as cd,
  divisionteaminfo as team_home,
  divisionteaminfo as team_away)
LEFT JOIN calendarweekname as wname ON 1
  and wname.calendar_id=di.calendar_id
  and wname.week=cd.week
LEFT JOIN calendarchanges as cc ON 1
  and cc.div_id=di.id
  and cc.week=cali.week
  and cc.match_nb=cali.match_nb
LEFT JOIN clubs as club_home 
       ON club_home.id=team_home.club_id
LEFT JOIN clubs as club_away
       ON club_away.id=team_away.club_id
LEFT JOIN divisionresults as divr
       ON divr.div_id=di.id and
          divr.season=di.season and
          divr.week=cali.week and
          divr.match_nb=cali.match_nb
LEFT JOIN levelinfo as li
       ON li.id=di.level
WHERE 1
  and di.season={$Season}
  and {$division_where_clause}
  and cali.calendar_id=di.calendar_id
  and cd.calendardate_id=di.calendardate_id
  and cd.week=cali.week
  and team_home.div_id=di.id
  and IF(ISNULL(cc.home), team_home.team_id=cali.home, team_home.team_id=cc.home)
  and team_away.div_id=di.id
  and IF(ISNULL(cc.away), team_away.team_id=cali.away, team_away.team_id=cc.away)
  and {$club_where_clause}
  and {$team_where_clause}
  and {$divcat_where_clause}
  and {$wname_where_clause}
  and {$level_where_clause}
ORDER BY
  di.category, li.order, di.div_id, di.serie, di.order, cali.week, cali.match_nb
;
EOQ;

  $res = array();
  $db->query($q);
  while ($db->next_record())
  {
    switch ($ShowDivisionName)
    {
      case 'yes':
        $divisionname = create_division_title_text(get_division_info($Season, $db->Record['DivisionId']), true);
        break;
      case 'short':
        $divinfo = get_division_info($Season, $db->Record['DivisionId']);
        $divisionname  = $divinfo['div_id']>0 ? $divinfo['div_id'] : '';
        $divisionname .= ($divinfo['div_id']>0||$divinfo['serie']==''?'':$GLOBALS['str_Serie'].' ') . $divinfo['serie'];
        $divisionname .= strlen($divinfo['extra_name']) ? ($divisionname==''?'':" ") . $divinfo['extra_name'] : '';
        break;
      default:
      case 'no':
        $divisionname = '';
        break;
    }
    $res[] = array_merge(
      $divisionname=='' ?
        array() :
        array('DivisionName' => utf8_encode($divisionname)),
      array(
        'MatchId'      => $db->Record['MatchId'],
        'WeekName'     => $db->Record['WeekName']
      ),
      $db->Record['Date'] == '-' ?
        array() :
        array(
          'Date'  => $db->Record['Date'],
          'Time'  => $db->Record['Time']
        ),
      $db->Record['Date'] != '-' && is_numeric($db->Record['Venue']) && $db->Record['Venue']>=1 ?
        array('Venue' => $db->Record['Venue']) : array(),
      array(
        'HomeClub'  => $db->Record['HomeClub'],
        'HomeTeam'  => utf8_encode($db->Record['HomeTeam']),
        'AwayClub'  => $db->Record['AwayClub'],
        'AwayTeam'  => utf8_encode($db->Record['AwayTeam'])
      ),
      $db->Record['Score'] == '-' ?
        array() :
        array('Score' => $db->Record['Score']),
      is_numeric($db->Record['MatchUniqueId']) && $db->Record['MatchUniqueId']>0 ?
        array('MatchUniqueId' => $db->Record['MatchUniqueId']) :
        array()
    );
  }

  // Release database connection
  $db->free();
  unset($db);

  return array('MatchCount' => count($res), 'TeamMatchesEntries' => $res);

}

/**
 * @brief Returns list of members according to a search criteria (club, index or name)
 *
 * Each player belongs to a club.  ::GetMembers can return the list of all players of a given club.
 *
 * As an example, here is the member list of club VLB-225 (Hurricane TTW) for season 2007-2008:
 * <ul>
 *  <li><code>1  | 505304 |  5 | MARC      | DE DONCKER | B6</code></li>
 *  <li><code>2  | 505292 |  5 | DAVID     | WAEFELAER  | B6</code></li>
 *  <li>(...)</li>
 *  <li><code>7  | 505384 |  8 | MICHEL    | THAUVOYE   | C2</code></li>
 *  <li><code>8  | 505783 |  8 | PASCAL    | TIMMERMANS | C2</code></li>
 *  <li>(...)</li>
 *  <li><code>17 | 505290 | 20 | GAËTAN    | FRENOY     | C6</code></li>
 *  <li><code>18 | 505275 | 20 | CHRISTIAN | HOLTYZER   | C6</code></li>
 *  <li>(...)</li>
 * </ul>
 *
 * You can also query information about a specific player.  A player is identified by a unique index.
 *
 * You can also search players based on their name (first name or last name).
 *
 * @param $Request GetMembersRequest
 * @return GetMembersResponse
 * @version 0.7.7
 * @see GetMembersRequest, GetMembersResponse
 * @ingroup TabTAPIfunctions
 */
function GetMembers(stdClass $Request)
{
  // Required helpers functions
  include_once($GLOBALS['site_info']['path'].'public/players_fct.php');

  // Extract function arguments
  $Credentials              = $Request->Credentials;
  $Club                     = trim($Request->Club);
  $Season                   = $Request->Season;
  $PlayerCategory           = $Request->PlayerCategory;
  $UniqueIndex              = $Request->UniqueIndex;
  $NameSearch               = $Request->NameSearch;
  $ExtendedInformation      = $Request->ExtendedInformation ? true : false;
  $RankingPointsInformation = $Request->RankingPointsInformation ? true : false;

  // Check permissions
  $permissions = split(',', _GetPermissions($Credentials));

  // Create database session
  $db = new DB_Session();
  $db->Halt_On_Error = 'no';

  // Check season
  if (!isset($Season))
  {
    $Season = $db->select_one("SELECT MAX(id) FROM seasoninfo;");
  }
  if (!is_numeric($Season))
  {
    throw new SoapFault('6', "Season [{$Season}] is not valid, must be numeric.");
  }
  if ($db->select_one("SELECT COUNT(*) FROM seasoninfo WHERE id={$Season}") == 0)
  {
    throw new SoapFault('7', "Season [{$Season}] is not valid.");
  }

  // Check club
  $club_where_clause = '1';
  if ($Club != '')
  {
    $Club = str_replace(array('-','/',' '), '', strtoupper($Club));
    $q = "SELECT id FROM clubs AS c WHERE REPLACE(REPLACE(REPLACE(UCASE(c.indice), ' ', ''), '/', ''), '-', '')='{$Club}' AND (ISNULL(c.first_season) OR c.first_season<={$Season}) AND (ISNULL(c.last_season) OR c.last_season>{$Season})";
    $ClubId = $db->select_one($q);
    if (!is_numeric($ClubId) || $ClubId < 0)
    {
      throw new SoapFault('9', "Club [{$Club}] is not valid.");
    }
    $club_where_clause = "pclub.club_id={$ClubId}";
  }

  // Check player category
  if (!isset($PlayerCategory))
  {
    // Select the first player category
    $PlayerCategory = $db->select_one("SELECT id FROM playercategories LIMIT 0,1");
  }

  // Check player category
  if (!is_numeric($PlayerCategory)) {
    throw new SoapFault('29', "Player category [{$PlayerCategory}] is not valid, must be numeric.");
  }

  // Get ranking category
  $RankingCategory = $db->select_one("SELECT classementcategory FROM playercategories WHERE id={$PlayerCategory} LIMIT 0,1");
  if (!is_numeric($RankingCategory) || $RankingCategory<1) {
    throw new SoapFault('30', "Player category [{$PlayerCategory}] is not valid.");
  }

  // Extract club category
  $class_repartition_join_clause = '';
  $select_index_clause = '';
  if (is_numeric($ClubId))
  {
    $ClubCategory = $db->select_one("SELECT category FROM clubs WHERE id={$ClubId}");

    // Calculate table with player indexes
    $q_ary = array();
    get_classement_repartition_queries($q_ary, 
                                       'tmp_class_repartition',
                                       $Season,
                                       $ClubId,
                                       $PlayerCategory,
                                       $RankingCategory,
                                       '',
                                       '', '', '',
                                       get_pref('reservewithindex', $ClubCategory)!=0);
    foreach ($q_ary as $q)
    {
      $db->query($q);
      if ($db->Errno != 0) throw new SoapFault('8', "Unexpected database error [{$db->Error}].");
    }
    $class_repartition_join_clause = "LEFT JOIN tmp_class_repartition as crep ON crep.order<=ci.order";

    // Ranking index
    $select_index = get_index_select('si', 'pstat', 'crep', 'pclass', false, true, true);
    $select_index_clause = "{$select_index} as ranking_index,";
  }

  // Check unique index
  $index_where_clause = '1';
  if (isset($UniqueIndex))
  {
    if (!is_numeric($UniqueIndex))
    {
      throw new SoapFault('28', "Member unique index ({$UniqueIndex}) is not valid, must be numeric.");
    }
    $index_where_clause = "pi.vttl_index={$UniqueIndex}";
  }

  // Check name search
  $name_where_clause = '1';
  if (isset($NameSearch))
  {
    $NameSearch = mysql_real_escape_string($NameSearch);
    $name_where_clause = "(CONCAT(pi.first_name, ' ', pi.last_name) LIKE '%{$NameSearch}%' OR CONCAT(pi.last_name, ' ', pi.first_name) LIKE '%{$NameSearch}%')";
  }

  // Check at least one search criteria is given
  if ($Club=='' && !isset($UniqueIndex) && !isset($NameSearch))
  {
    throw new SoapFault('26', "At least one search criteria must be specified (club, index or name).");
  }

  // To see extended information, user must be identified
  if ($ExtendedInformation && count(array_intersect($permissions, array('user', 'club', 'province', 'admin')))==0) {
    throw new SoapFault('27', "You don't have permission to see extended information.");
  }

  // Prepare list of results
  $res = array();

  // Prepare player category search
  $pcat_wherecond = get_player_categories_where_cond('si', 'pi', 'pcat', 0, false, false, false, false);
  $pcat_strict_wherecond = get_player_categories_where_cond('si', 'pi', 'pcat_strict', 1, false, false, false, false);
  $category_select = get_player_category_select('pi', 'pcat_strict');

  // Retrieve all members of requested club for requested season
  $q = <<<EOQ
SELECT
  pi.id as id,
  {$select_index_clause}
  pi.vttl_index as unique_index,
  pi.first_name as first_name,
  pi.last_name as last_name,
  pi.sex as sex,
  pi.birthdate as birthdate,
  IFNULL(pstat.status, 'A') as status,
  IF(ci.name='NC', '{$GLOBALS['str_NC']}', ci.name) as classement,
  c.indice as club_indice,
  pi.medic_validity>si.start_date as medical_attestation,
  {$category_select} as player_category,
  GROUP_CONCAT(CONCAT(pelo.points, '|', pelo.modified) SEPARATOR '$-@') as player_points
FROM
  (seasoninfo as si,
   playerinfo as pi) LEFT JOIN playerstatus as pstat ON pstat.season=si.id AND pstat.player_id=pi.id LEFT JOIN playercategories as pcat ON {$pcat_wherecond} LEFT JOIN playercategories as pcat_strict ON {$pcat_strict_wherecond} LEFT JOIN playerlastelo pelo ON pelo.player_id=pi.id AND pelo.class_category=pcat.classementcategory,
  playerclub as pclub LEFT JOIN clubs as c ON c.id=pclub.club_id,
  playerclassement as pclass,
  classementinfo as ci {$class_repartition_join_clause}
WHERE 1
  AND si.id={$Season}
  AND pclub.season=si.id
  AND pclub.player_id=pi.id
  AND {$club_where_clause}
  AND (ISNULL(pstat.status) OR pstat.status IN ('A','S','L','R','V'))
  AND pclass.season=si.id
  AND pclass.player_id=pi.id
  AND pclass.category=pcat.classementcategory
  AND pcat.id={$PlayerCategory}
  AND ci.id=pclass.classement_id
  AND ci.category=pclass.category
  AND {$index_where_clause}
  AND {$name_where_clause}
GROUP BY
  pi.id
ORDER BY
  ci.order ASC, pi.last_name, pi.first_name
EOQ;

  $db->query($q);
  if ($db->Errno != 0) throw new SoapFault('8', "Unexpected database error [{$db->Error}].");
  while ($db->next_record())
  {
    $entry = array('Position'     => count($res)+1,
                   'UniqueIndex'  => $db->Record['unique_index'],
                   'RankingIndex' => $db->Record['ranking_index'],
                   'FirstName'    => utf8_encode($db->Record['first_name']),
                   'LastName'     => utf8_encode($db->Record['last_name']),
                   'Ranking'      => $db->Record['classement']
                  );
    if ($Club=='')
    {
      $entry['Club'] = $db->Record['club_indice'];
    }
    if ($ExtendedInformation)
    {
      $entry['Status']             = $db->Record['status'];
      $entry['Gender']             = $db->Record['sex'];
      $entry['Category']           = $db->Record['player_category'];
      if (count(array_intersect($permissions, array('admin')))>0) {
        $entry['BirthDate']        = $db->Record['birthdate'];
      }
      $entry['MedicalAttestation'] = $db->Record['medical_attestation'];
    }
    if ($RankingPointsInformation)
    {
      $entry['RankingPointsCount'] = 1;
    }
    $res[] = $entry;
  }

  // Release database connection
  $db->free();
  unset($db);

  return array('MemberCount'   => count($res),
               'MemberEntries' => $res);
}

/**
 * @brief Data upload using TabT-Upload format
 *
 * @param $Request UploadRequest
 * @return UploadResponse
 * @since Version 0.6
 * @ingroup TabTAPIfunctions
 */
function Upload(stdClass $Request)
{
  // Extract function arguments
  $Credentials     = $Request->Credentials;
  $Data            = trim($Request->Data);

  // Check permissions
  $permissions = _GetPermissions($Credentials);
  if (is_null($permissions)) {
    throw new SoapFault('22', "You don't have permission to upload data.  Please contact your administrator.");
  }
  $permissions = split(',', $permissions);
  if (count(array_intersect($permissions, array('club', 'province', 'admin')))==0) {
    throw new SoapFault('22', "You don't have permission to upload data.  Please contact your administrator.");
  }

  // Check input data
  if (strlen($Data) == 0) {
    throw new SoapFault('23', "No upload data specified.");
  }

  // Log received data
  $filename = sprintf("%s/public/cache/import_backup/api-%d-%s.tabt", $GLOBALS['site_info']['path'], $GLOBALS['auth']->auth['pid'], date('Ymd-His'));
  file_put_contents($filename, $Data."\n");
  chmod($filename, 0660);

  // Do import upload data
  $errors = array();
  $processed_line_count = 0;

  include_once($GLOBALS['site_info']['path'].'public/results_import_tabt_fct.php');
  process_upload_data($Data, $errors, $processed_line_count);
  
  $res = array(
    'Result'             => count($errors)==0,
    'ProcessedLineCount' => $processed_line_count
  );
  if (!$res->Result) {
    $res['ErrorLines'] = array();
    foreach ($errors as $error) $res['ErrorLines'][] = utf8_encode($error);
  }
  return $res;
}

/**
 * @brief Retrieve club list according to a given category
 *
 * @param $Request GetClubsRequest
 * @return GetClubsResponse
 * @since Version 0.6
 * @version 0.7.6
 */
function GetClubs(stdClass $Request)
{
  // Extract function arguments
  if (isset($Request->Credentials))  $Credentials     = $Request->Credentials;
  if (isset($Request->Season))       $Season          = $Request->Season;
  if (isset($Request->ClubCategory)) $ClubCategoryId  = $Request->ClubCategory;
  if (isset($Request->Club))         $Club            = $Request->Club;

  // Create database session
  $db = new DB_Session();
  $db->Halt_On_Error = 'no';

  // Check permissions
  $permissions = isset($Credentials) ? _GetPermissions($Credentials) : '';

  // Check season
  if (!isset($Season))
  {
    $Season = $db->select_one("SELECT MAX(id) FROM seasoninfo;");
  }
  if (!is_numeric($Season))
  {
    throw new SoapFault('6', "Season [{$Season}] is not valid, must be numeric.");
  }
  if ($db->select_one("SELECT COUNT(*) FROM seasoninfo WHERE id={$Season}") == 0)
  {
    throw new SoapFault('7', "Season [{$Season}] is not valid.");
  }

  // Check club category
  if (isset($ClubCategoryId))
  {
    if (!is_numeric($ClubCategoryId))
    {
      throw new SoapFault('24', "Club category [{$ClubCategoryId}] is not valid, must be numeric.");
    }
    if ($db->select_one("SELECT COUNT(*) FROM clubcategories WHERE id={$ClubCategoryId}") == 0)
    {
      throw new SoapFault('25', "Club category [{$ClubCategoryId}] is not valid.");
    }
  }
  $clubcategory_where_clause = is_numeric($ClubCategoryId) ? "c.category={$ClubCategoryId}" : '1';

  // Check club
  $ClubId = 0;
  $Club = isset($Club) ? str_replace(array('-','/',' '), '', strtoupper($Club)) : '';
  if ($Club != '') {
    $q = "SELECT id FROM clubs AS c WHERE REPLACE(REPLACE(REPLACE(UCASE(c.indice), ' ', ''), '/', ''), '-', '')='{$Club}' AND (ISNULL(c.first_season) OR c.first_season<={$Season}) AND (ISNULL(c.last_season) OR c.last_season>{$Season})";
    $ClubId = $db->select_one($q);
    if (!is_numeric($ClubId) || $ClubId < 0) {
      throw new SoapFault('9', "Club [{$Club}] is not valid.");
    }
  }
  $club_where_clause = is_numeric($ClubId) && $ClubId>0 ? "c.id={$ClubId}" : '1';

  // Retrieve all clubs of requested category and still active in request season
  $q = <<<EOQ
SELECT
  IF(c.indice='' OR ISNULL(c.indice), c.id, c.indice) as `unique_index`,
  IFNULL(c.short_name, c.name) as `club_name`,
  c.name as `club_long_name`,
  c.category as `club_category`,
  ccat.name as `club_category_name`,
  GROUP_CONCAT(CONCAT(ca.id, '|', ca.address_id, '|', ca.name, '|', ca.address, '|', ca.zip, ' ', ca.town, '|', ca.phone, '|', ca.comment) SEPARATOR '#-@-$') as addresses
FROM
  clubs c
LEFT JOIN clubcategories ccat ON c.category=ccat.id
LEFT JOIN clubaddressinfo ca ON ca.club_id=c.id
WHERE 1
  AND (ISNULL(c.first_season) OR c.first_season<={$Season})
  AND (ISNULL(c.last_season) OR c.last_season>={$Season})
  AND {$clubcategory_where_clause}
  AND {$club_where_clause}
GROUP BY
  c.id
ORDER BY
  c.indice ASC
EOQ;

  $db->query($q);
  if ($db->Errno != 0) throw new SoapFault('8', "Unexpected database error [{$db->Error}].");

  // Prepare list of results
  $res = array();
  while ($db->next_record())
  {
    $venues = array();
    foreach (explode('#-@-$', $db->Record['addresses']) as $db_address) {
      $db_venue = explode('|', $db_address);
      if (is_numeric($db_venue[0]) && $db_venue[0]>0) {
        $venues[] = array(
          'Id'        => $db_venue[0],
          'ClubVenue' => $db_venue[1],
          'Name'      => utf8_encode($db_venue[2]),
          'Street'    => utf8_encode($db_venue[3]),
          'Town'      => utf8_encode($db_venue[4]),
          'Phone'     => $db_venue[5],
          'Comment'   => utf8_encode($db_venue[6])
        );
      }
    }
    $clubEntry = array(
      'UniqueIndex'  => $db->Record['unique_index'],
      'Name'         => utf8_encode($db->Record['club_name']),
      'LongName'     => utf8_encode($db->Record['club_long_name']),
      'Category'     => $db->Record['club_category'],
      'CategoryName' => utf8_encode($db->Record['club_category_name']),
      'VenueCount'   => count($venues)
    );
    if (count($venues)>0) {
      $clubEntry['VenueEntries'] = $venues;
    }
    $res[] = $clubEntry;
  }

  // Release database connection
  $db->free();
  unset($db);

  return array('ClubCount'   => count($res),
               'ClubEntries' => $res);
}

 /**
  * @}
  */
?>
