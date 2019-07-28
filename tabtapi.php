<?php
/** \internal
 * TabT API
 *
 * A programming interface to access information managed
 * by TabT, the table tennis information manager.
 *
 * @author Gaetan Frenoy <gaetan@frenoy.net>
 * @version 0.7.23
 *
 * Copyright (C) 2007-2019 Gaëtan Frenoy (gaetan@frenoy.net)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.

 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

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
 * <h3>Contact</h3>
 * <a href="https://babelut.be/@tabt" target="_blank"><img style="margin-right: 10px; border: 0; float: left;" src="mastodon.png" title="Mastodon icon"></a>
 * To keep you up to date of the latest changes or for questions and suggestions or any kind of interaction with the
 * developpers and other users of TabT API, we strongly suggest you to follow the TabT account on the
 * <a href="https://en.wikipedia.org/wiki/Fediverse">Fediverse</a> (typically <a href="https://joinmastodon.org/">Mastodon</a>) :<br/>
 * <a href="https://babelut.be/@tabt" target="_blank">@tabt@babelut.be</a>.
 * <h3>License</h3>
 * <a href="http://www.fsf.org/licensing/licenses/agpl-3.0.html">
 * <p><img style="margin-right: 10px; border: 0; float: left;" src="agplv3-88x31.png" title="GNU Affero General Public License"></a>
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
 * @version 0.7.16
 * @ingroup TabTAPIfunctions
 */
function Test(stdClass $Request) {

  $permissions  = _MethodAPI(1, isset($Request->Credentials) ? $Request->Credentials : (object)array());

  $res = array('Timestamp'      => date("c"),
               'ApiVersion'     => TABTAPI_VERSION,
               'IsValidAccount' => count($permissions)>0,
               'Language'       => $GLOBALS['lang'],
               'Database'       => $GLOBALS['site_info']['database'],
               'RequestorIp'    => long2ip($GLOBALS['api_caller_ip']),
               'ConsumedTicks'  => intval($GLOBALS['api_consumed']),
               'CurrentQuota'   => intval($GLOBALS['api_remaining_quota']),
               'AllowedQuota'   => intval($GLOBALS['api_quota_limit']));

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
  $permissions = _MethodAPI(2, $Credentials);

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
 * @version 0.7.8
 */
function GetClubTeams(stdClass $Request) {
  // Check permissions & quota
  $permissions = _MethodAPI(3, isset($Request->Credentials) ? $Request->Credentials : (object)array());

  // Extract function arguments
  $Club        = trim($Request->Club);
  $Season      = trim($Request->Season);

  // Prepare database session
  $db = new DB_Session();

  // Check season
  if (!isset($Season) || '' == $Season) {
    $Season = $db->select_one("SELECT MAX(id) FROM seasoninfo;");
  }
  if (!is_numeric($Season)) {
    throw new SoapFault('6', "Season [{$Season}] is not valid, must be numeric.");
  }
  $Season = intval($Season);
  if ($db->select_one("SELECT COUNT(*) FROM seasoninfo WHERE id={$Season}") == 0) {
    throw new SoapFault('7', "Season [{$Season}] is not valid.");
  }

  // Check club
  $Club = str_replace(array('-','/',' '), '', strtoupper($Club));
  if ($Club == '') {
    throw new SoapFault('17', "Club is not valid, cannot be empty.");
  }
  list($ClubId, $ClubName) = _GetClubInfo($Season, $Club);
  if (!is_numeric($ClubId) || $ClubId < 0) {
    throw new SoapFault('9', "Club [{$Club}] is not valid.");
  }
  
  $q = <<<EOQ
SELECT
  CONCAT(di.id,'-',dti.team_id) as teamid,
  dti.indice as team,
  di.id as div_id,
  di.category as divisioncategory,
  di.match_type_id as matchtype
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
                           'DivisionName'     => create_division_title_text(get_division_info($Season, $db->Record['div_id']), true),
                           'DivisionCategory' => $db->Record['divisioncategory'],
                           'MatchType'        => $db->Record['matchtype']);
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
 * @version 0.7.14
 */
function GetDivisionRanking(stdClass $Request) {
  // Check permissions & quota
  $permissions = _MethodAPI(4, isset($Request->Credentials) ? $Request->Credentials : (object)array());

  // Extract function arguments
  $DivisionId    = isset($Request->DivisionId) ? $Request->DivisionId : -1;
  $WeekName      = isset($Request->WeekName) ? trim($Request->WeekName) : '';
  $RankingSystem = isset($Request->RankingSystem) ? $Request->RankingSystem : '';

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
    $WeekName = mysql_real_escape_string(ltrim($WeekName, '0'));
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
  $entries = get_classement_for_division($RankingSystem, $season, $DivisionId, '', false, false, null, $DatabaseWeekName, 1, false, true);
  if (!is_array($entries))
  {
    throw new SoapFault('5', "Unable to process ranking for division [{$DivisionId}], week name [{$WeekName}].");
  }
  $res = array();
  foreach ($entries as $entry)
  {
    $res[] = array('Position'              => $entry[$GLOBALS['str_Place']],
                   'Team'                  => $entry[$GLOBALS['str_TeamName']],
                   'GamesPlayed'           => $entry[$GLOBALS['str_GamesPlayed']],
                   'GamesWon'              => $entry[$GLOBALS['str_GamesWon']],
                   'GamesLost'             => $entry[$GLOBALS['str_GamesLost']],
                   'GamesDraw'             => $entry[$GLOBALS['str_GamesDraw']],
                   'GamesWO'               => $entry[$GLOBALS['str_GamesWO']],
                   'IndividualMatchesWon'  => $entry[$GLOBALS['str_MatchsWon']],
                   'IndividualMatchesLost' => $entry[$GLOBALS['str_MatchsLost']],
                   'IndividualSetsWon'     => $entry[$GLOBALS['str_SetsWon']],
                   'IndividualSetsLost'    => $entry[$GLOBALS['str_SetsLost']],
                   'Points'                => $entry[$GLOBALS['str_Score']],
                   'TeamClub'              => $entry[$GLOBALS['str_Club']]);
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
 * @version 0.7.23
 * @see GetMatchesRequest, GetMatchesResponse
 * @ingroup TabTAPIfunctions
 */
function GetMatches(stdClass $Request) {
  // Check permissions & quota
  $permissions = _MethodAPI(5, isset($Request->Credentials) ? $Request->Credentials : (object)array());

  // Extract function arguments
  $DivisionId       = isset($Request->DivisionId) ? $Request->DivisionId : '';
  $Club             = isset($Request->Club) ? trim($Request->Club) : '';
  $Team             = isset($Request->Team) ? trim(strtoupper($Request->Team)) : '';
  $DivisionCategory = isset($Request->DivisionCategory) ? $Request->DivisionCategory : '';
  $Season           = isset($Request->Season) ? trim($Request->Season) : '';
  $WeekName         = isset($Request->WeekName) ? trim($Request->WeekName) : '';
  $LevelId          = isset($Request->Level) ? $Request->Level : '';
  $ShowDivisionName = isset($Request->ShowDivisionName) ? strtolower(trim($Request->ShowDivisionName)) : '';
  if (isset($Request->YearDateFrom)) $YearDateFrom = strtotime($Request->YearDateFrom);
  if (isset($Request->YearDateTo))   $YearDateTo   = strtotime($Request->YearDateTo);
  $WithDetails      = isset($Request->WithDetails) && $Request->WithDetails ? true : false;
  $MatchId          = isset($Request->MatchId) ? mysql_real_escape_string(trim($Request->MatchId)) : '';
  $MatchUniqueId    = isset($Request->MatchUniqueId) && is_numeric($Request->MatchUniqueId) && $Request->MatchUniqueId > 0 ? intval(trim($Request->MatchUniqueId)) : 0;

  // Create database session
  $db = new DB_Session();
  $db->Halt_On_Error = 'no';

  // Check club & division
  $Club = str_replace(array('-','/',' '), '', strtoupper($Club));
  if (trim($DivisionId) == '' && $Club == '' && $WeekName == '' && $MatchUniqueId == 0) {
    throw new SoapFault('13', "DivisionId, Club, WeekName or MatchUniqueId must be given.");
  }

  // Check division
  if ($DivisionId != '' && !is_numeric($DivisionId)) {
    throw new SoapFault('2', "DivisionId [{$DivisionId}] is not valid, must be numeric.");
  }
  if (is_numeric($DivisionId) && $db->select_one("SELECT COUNT(*) FROM divisioninfo WHERE id={$DivisionId};")==0) {
    throw new SoapFault('3', "DivisionId [{$DivisionId}] is not valid.");
  }

  // Check season
  if (!isset($Season) || '' == $Season) {
    unset($Season);
    if (is_numeric($DivisionId)) {
      $Season = $db->select_one("SELECT season FROM divisioninfo WHERE id={$DivisionId};");
    } elseif (is_numeric($MatchUniqueId) && $MatchUniqueId > 0) {
      $Season = $db->select_one("SELECT di.season FROM divisionresults divr, divisioninfo di WHERE divr.match_id={$MatchUniqueId} AND di.id=divr.div_id;");
      if ($Season == -1) {
        unset($Season);
      }
    } elseif (isset($YearDateFrom)) {
      $Season = $db->select_one("SELECT id FROM seasoninfo WHERE start_date < FROM_UNIXTIME({$YearDateFrom}) AND stop_date > FROM_UNIXTIME({$YearDateFrom});");
      if ($Season == -1) {
        unset($Season);
      }
    }
    if (!isset($Season)) {
      $Season = $db->select_one("SELECT MAX(id) FROM seasoninfo;");
    }
  }
  if (!is_numeric($Season)) {
    throw new SoapFault('6', "Season [{$Season}] is not valid, must be numeric.");
  }
  $Season = intval($Season);
  if ($db->select_one("SELECT COUNT(*) FROM seasoninfo WHERE id={$Season}") == 0) {
    throw new SoapFault('7', "Season [{$Season}] is not valid.");
  }

  // Check club
  if ($Club != '') {
    list($ClubId, $ClubName) = _GetClubInfo($Season, $Club);
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
    $WeekName = mysql_real_escape_string(ltrim($WeekName, '0'));
    $q = "SELECT week, name FROM calendarweekname WHERE TRIM(LEADING '0' FROM name) LIKE '{$WeekName}';";
    list($week, $DatabaseWeekName) = $db->select_one_array($q);
    if (!is_numeric($week) || !($week > -1)) {
      throw new SoapFault('20', "WeekName [{$WeekName}] is not valid.");
    }
  }

  // Check level
  if ($LevelId != '' && !is_numeric($LevelId)) {
    throw new SoapFault('18', "Level must be a number ([{$LevelId}]).");
  }
  if ($LevelId != '') {
    $LevelId = intval($LevelId);
    if ($db->select_one("SELECT COUNT(*) FROM levelinfo WHERE id={$LevelId};") == 0) {
      throw new SoapFault('19', "LevelId [{$LevelId}] does not exists.");
    }
  }

  // Check 'ShowDivisionName' option
  if ($ShowDivisionName != '' && !in_array($ShowDivisionName, array('yes', 'no', 'short'))) {
    throw new SoapFault('21', "ShowDivisionName [{$ShowDivisionName}] is not valid.");
  }

  // Check dates
  if (isset($YearDateFrom) && $YearDateFrom === false) {
    throw new SoapFault('32', "YearDateFrom is not a valid date, please use format YYYY-MM-DD.");
  }
  if (isset($YearDateTo) && $YearDateTo === false) {
    throw new SoapFault('33', "YearDateTo is not a valid date, please use format YYYY-MM-DD.");
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
  if (isset($YearDateFrom) || isset($YearDateTo)) {
    $date_field = 'UNIX_TIMESTAMP(IFNULL(cc.date, DATE_ADD(cd.date, INTERVAL team_home.day_in_week DAY)))';
    if ($YearDateFrom && $YearDateTo) {
      $date_where_clause = "({$date_field} BETWEEN {$YearDateFrom} AND {$YearDateTo})";
    } elseif ($YearDateFrom) {
      $date_where_clause = "({$date_field} >= {$YearDateFrom})";
    } elseif ($YearDateTo) {
      $date_where_clause = "({$date_field} <= {$YearDateTo})";
    }
  } else {
    $date_where_clause = '1';
  }

  // Select on a particular match id
  // by name
  $match_where_clause = '1';
  if ($MatchId != '') {
    // First try to get match number from the cache (if any)
    $MatchUniqueId = get_match_id_from_match_number($Season, $MatchId);
    if ($MatchUniqueId <= 0) {
      $match_where_clause = "{$matchnum_select} LIKE '{$MatchId}'";
    }
  }
  // by internal id
  $uniquematch_where_clause = '1';
  if ($MatchUniqueId > 0) {
    $uniquematch_where_clause = "divr.match_id={$MatchUniqueId}";
  }

  // Defines some escape separators
  $escape_separators = array(
    '§' => '%£+|!1',
    'µ' => '%£+|!2'
  );

  // More additional clauses if details are required
  $details_select_clause = '';
  $details_from_clause = '';
  $groupby_clause = '';
  if ($WithDetails) {
    $details_select_clause  = ",di.match_type_id as `MatchTypeId`";
    $details_select_clause .= ",mti.nb_single as `PlayerCount`";
    $details_select_clause .= ",mti.nb_double as `DoubleTeamCount`";
    $details_select_clause .= ",pi_hc.vttl_index as `HomeCaptain`";
    $details_select_clause .= ",pi_ac.vttl_index as `AwayCaptain`";
    $details_select_clause .= ",pi_ref.vttl_index as `Referee`";
    $details_select_clause .= ",pi_rr.vttl_index as `HallCommissioner`";
    if ($MatchUniqueId > 0) {
      $details_select_clause .= ",IF(mc.id IS NULL, '', GROUP_CONCAT(DISTINCT CONCAT(mc.id, '§', mc.date, '§', mc.modification_type, '§', pi_authors.vttl_index, '§', pi_authors.first_name, '§', pi_authors.last_name, '§', REPLACE(REPLACE(mc.message, '§', '{$escape_separators['§']}'), 'µ', '{$escape_separators['µ']}')) ORDER BY mc.date DESC SEPARATOR 'µ')) as `MatchComments`";
    }

    $details_from_clause  = " LEFT JOIN matchinfo as mi ON mi.id=divr.match_id";
    $details_from_clause .= " LEFT JOIN playerinfo as pi_hc ON mi.home_captain_player_id=pi_hc.id";
    $details_from_clause .= " LEFT JOIN playerinfo as pi_ac ON mi.away_captain_player_id=pi_ac.id";
    $details_from_clause .= " LEFT JOIN playerinfo as pi_ref ON mi.referee_player_id=pi_ref.id";
    $details_from_clause .= " LEFT JOIN playerinfo as pi_rr ON mi.room_responsible_player_id=pi_rr.id";
    $details_from_clause .= " LEFT JOIN matchtypeinfo as mti ON mti.id=mi.match_type_id";
    $details_from_clause .= " LEFT JOIN matchplayer as mp ON mp.match_id=mi.id";
    if ($MatchUniqueId > 0) {
      $match_comment_table = get_match_comment_table_query($MatchUniqueId);
      $details_from_clause .= " LEFT JOIN {$match_comment_table} as mc ON mc.match_id=mi.id";
      $details_from_clause .= " LEFT JOIN auth_user as mc_authors ON mc_authors.user_id=mc.user_id";
      $details_from_clause .= " LEFT JOIN playerinfo as pi_authors ON pi_authors.id=mc_authors.player_id";
    }

    foreach (array('home', 'away') as $homeaway) {
      $classement_select_clause = get_classement_select_clause($homeaway . '_ci');
      $details_select_clause .= ",IF(mi.id IS NULL, '', GROUP_CONCAT(DISTINCT CONCAT(mp.player_nb, '|', IF(mp.player_nb<=mti.nb_single, {$homeaway}_pi.vttl_index, mp.{$homeaway}_player_id), '|', IF(mp.player_nb<=mti.nb_single, {$homeaway}_pi.first_name, ''), '|', IF(mp.player_nb<=mti.nb_single, {$homeaway}_pi.last_name, ''), '|', IF(mp.player_nb<=mti.nb_single, {$classement_select_clause}, ''), '|', mp.{$homeaway}_vict, '|', mp.{$homeaway}_wo) ORDER BY mp.player_nb)) as `" . ucfirst($homeaway) . "Players`";

      $details_from_clause .= " LEFT JOIN playerinfo as {$homeaway}_pi ON {$homeaway}_pi.id=mp.{$homeaway}_player_id LEFT JOIN playerclassement as {$homeaway}_pcl ON {$homeaway}_pcl.season=di.season AND {$homeaway}_pcl.player_id={$homeaway}_pi.id AND {$homeaway}_pcl.category=divc.classementcategory LEFT JOIN classementinfo as {$homeaway}_ci ON {$homeaway}_ci.id={$homeaway}_pcl.classement_id AND {$homeaway}_ci.category=divc.classementcategory";
    }

    $groupby_clause = 'GROUP BY `MatchId`';
  }

  // How should we sort the results ?
  // By default, use a generic sort order (somehow same as on the website)
  $orderby_clause = "di.category, li.order, di.div_id, di.serie, di.order, cali.week, cali.match_nb";
  if ($Club != '') {
    // If a club has been given, change order so it looks more "logical" for the club (sorted by team letter)
    $orderby_clause = "di.category, li.order, IF(team_home.club_id={$ClubId}, team_home.indice, IF(team_away.club_id={$ClubId}, team_away.indice, '')), cali.week, cali.match_nb";
  }

  // Remember lock time limit (from preferences)
  $lock_limit_time = intval(get_pref('locktimelimit', 60*60*6));

  $q = <<<EOQ
SELECT
  di.id as `DivisionId`,
  di.category as `DivisionCategory`,
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
  IF(ISNULL(club_home.name), 0, cai.address_id) as `Venue`,
  cai_club.indice as `VenueClub`,
  cai.name as `VenueEntryName`,
  cai.address as `VenueEntryStreet`,
  CONCAT(cai.zip, ' ', cai.town) as `VenueEntryTown`,
  cai.phone as `VenueEntryPhone`,
  cai.comment as `VenueEntryComment`,
  IF(team_home.is_bye OR team_away.is_bye OR
     IFNULL(divr.home=0 AND divr.away=0 AND divr.home_wo<>'Y' AND divr.away_wo<>'Y' AND divr.score_modified<>'Y' AND team_home.is_withdraw='N' AND team_away.is_withdraw='N', 1),
   '-',
   CONCAT(IFNULL(divr.home,'...'), '-', IFNULL(divr.away,'...'), {$withdraw_select}, IF(divr.home_wo<>'Y' AND divr.away_wo<>'Y' AND team_home.is_withdraw<>'N' OR team_away.is_withdraw<>'N', ' ({$GLOBALS['str_GW']})', ''))
    ) as `Score`,
  divr.match_id as `MatchUniqueId`,
  next_wname.name as `NextWeekName`,
  prev_wname.name as `PrevWeekName`,
  divr.home_wo='Y' OR team_home.is_withdraw<>'N' as `HomeFF`,
  divr.away_wo='Y' OR team_away.is_withdraw<>'N' as `AwayFF`,
  team_home.is_withdraw as `HomeWithdrawn`,
  team_away.is_withdraw as `AwayWithdrawn`,
  NOT divr.validated_by IS NULL as `IsValidated`,
  divr.lock_timestamp as `LockTime`,
  NOT divr.locked_by IS NULL as `IsLocked`
  {$details_select_clause}
FROM
 (divisioninfo as di,
  calendarinfo as cali,
  calendardates as cd,
  divisionteaminfo as team_home,
  divisionteaminfo as team_away)
LEFT JOIN divisioncategories as divc
  ON divc.id=di.category
LEFT JOIN calendarweekname as wname ON 1
  and wname.calendar_id=di.calendar_id
  and wname.week=cd.week
LEFT JOIN calendarchanges as cc ON 1
  and cc.div_id=di.id
  and cc.week=cali.week
  and cc.match_nb=cali.match_nb
LEFT JOIN calendarweekname as next_wname ON 1
  and next_wname.calendar_id=di.calendar_id
  and next_wname.week=cd.week+1
LEFT JOIN calendarweekname as prev_wname ON 1
  and prev_wname.calendar_id=di.calendar_id
  and prev_wname.week=cd.week-1
LEFT JOIN clubs as club_home 
       ON club_home.id=team_home.club_id
LEFT JOIN clubs as club_away
       ON club_away.id=team_away.club_id
LEFT JOIN divisionresults as divr
       ON divr.div_id=di.id and
          divr.season=di.season and
          divr.week=cali.week and
          divr.match_nb=cali.match_nb
LEFT JOIN clubaddressinfo as cai
       ON cai.club_id=IFNULL(cc.address_club_id, team_home.club_id) and
          cai.address_id=IFNULL(cc.address_id, team_home.address_id)
LEFT JOIN clubs as cai_club
       ON cai_club.id=cai.club_id
LEFT JOIN levelinfo as li
       ON li.id=di.level
{$details_from_clause}
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
  and {$date_where_clause}
  and {$match_where_clause}
  and {$uniquematch_where_clause}
{$groupby_clause}
ORDER BY
  {$orderby_clause}
;
EOQ;

  $res = array();
  $db->query($q);
  while ($db->next_record()) {
    $divisionname = _GetDivisionName($ShowDivisionName, $Season, $db->Record['DivisionId']);
    if ($WithDetails) {
      $resDetails = array(
        'DetailsCreated' => $db->Record['MatchUniqueId']>0,
        'MatchSystem'    => $db->Record['MatchTypeId']
      );
      if ($db->Record['HomeCaptain']) {
        $resDetails['HomeCaptain'] = intval($db->Record['HomeCaptain']);
      }
      if ($db->Record['AwayCaptain']) {
        $resDetails['AwayCaptain'] = intval($db->Record['AwayCaptain']);
      }
      if ($db->Record['Referee']) {
        $resDetails['Referee'] = intval($db->Record['Referee']);
      }
      if ($db->Record['HallCommissioner']) {
        $resDetails['HallCommissioner'] = intval($db->Record['HallCommissioner']);
      }
      $resDetails['CommentCount'] = 0;
      if (isset($db->Record['MatchComments']) && strlen(trim($db->Record['MatchComments']))>0) {
        $comments = explode('µ', $db->Record['MatchComments']);
        if (!isset($resDetails['MatchComments'])) {
          $resDetails['CommentCount'] = count($comments);
          $resDetails['CommentEntries'] = array();
        }
        foreach ($comments as $comment_str) {
          list($comment_id, $timestamp, $type, $author_id, $author_first_name, $author_last_name, $comment) = explode('§', $comment_str);
          $comment = str_replace(array_keys($escape_separators), array_values($escape_separators), $comment);
          $resDetails['CommentEntries'][] = array(
            'Timestamp' => $timestamp,
            'Author'    => array(
              'UniqueIndex' => $author_id,
              'FirstName'   => $author_first_name,
              'LastName'    => $author_last_name
            ),
            'Comment'   => $comment,
            'Code'      => $type
          );
        }
      }
    }
    if ($WithDetails && $db->Record['MatchUniqueId']>0) {
      // We will need a sub-query for scores
      $db2 = new DB_Session();

      // Players information
      foreach (array('Home', 'Away') as $HomeAway) {
        $player_list = preg_split('/,/', $db->Record["{$HomeAway}Players"]);
        $resDetails["{$HomeAway}Players"] = array(
          'PlayerCount'     => $db->Record['PlayerCount'],
          'DoubleTeamCount' => $db->Record['DoubleTeamCount']
        );
        foreach ($player_list as $player) {
          $player_info = preg_split('/\|/', $player);
          if ($player_info[0] <= $db->Record['PlayerCount']) {
            if (!isset($resDetails["{$HomeAway}Players"]['Players'])) {
              $resDetails["{$HomeAway}Players"]['Players'] = array();
            }
            ${"{$HomeAway}Players"}[$player_info[0]] = $resDetails["{$HomeAway}Players"]['Players'][] = array_merge(
              array(
                'Position'    => $player_info[0],
                'UniqueIndex' => $player_info[1],
                'FirstName'   => $player_info[2],
                'LastName'    => $player_info[3],
                'Ranking'     => $player_info[4]
              ),
              $player_info[6] == 0 ? array('VictoryCount' => $player_info[5]) :  array(),
              $player_info[6] > 0 ? array('IsForfeited' => true) : array()
            );
          } else {
            if (!isset($resDetails["{$HomeAway}Players"]['DoubleTeams'])) {
              $resDetails["{$HomeAway}Players"]['DoubleTeams'] = array();
            }
            $resDetails["{$HomeAway}Players"]['DoubleTeams'][] = array(
              'Position'    => $player_info[0],
              'Team'        => $player_info[1]
            );
          }
        }
      }

      // Get scores
      $match_scores_query = <<<EOQ
SELECT
  mr.match_id,
  mr.game_id as game_id,
  IFNULL(mpe.home_player_nb, mtp.home_player) as home_position,
  IF(mtp.player_nb=1, home_pi.vttl_index, home_mp.home_player_id) as home_index,
  IFNULL(mpe.away_player_nb, mtp.away_player) as away_position,
  IF(mtp.player_nb=1, away_pi.vttl_index, away_mp.away_player_id) as away_index,
  GROUP_CONCAT(CONCAT(mr.set_id, '|', IF(mr.points={$GLOBALS['zero_for_set']}, '0', IF(mr.points=-{$GLOBALS['zero_for_set']}, '-0', mr.points))) ORDER BY mr.set_id) as scores,
  SUM(mr.points>0) as home_sets,
  SUM(mr.points<0) as away_sets,
  MAX(mr.home_wo) as home_wo,
  MAX(mr.away_wo) as away_wo,
  (SELECT COUNT(*) FROM matchresults WHERE match_id=mr.match_id AND points<>{$GLOBALS['zero_for_set']} AND points<>-{$GLOBALS['zero_for_set']})=0 as set_only,
  mtp.player_nb as player_nb
FROM
  matchresults as mr
  LEFT JOIN matchinfo as mi ON mi.id=mr.match_id
  LEFT JOIN matchtypeplayer as mtp ON mtp.match_type_id=mi.match_type_id AND mtp.game_nb=mr.game_id
  LEFT JOIN matchplayerexception mpe ON mpe.match_id=mi.id AND mpe.game_nb=mtp.game_nb
  LEFT JOIN matchplayer as home_mp ON home_mp.match_id=mr.match_id AND home_mp.player_nb=IFNULL(mpe.home_player_nb, mtp.home_player) LEFT JOIN playerinfo as home_pi ON home_pi.id=home_mp.home_player_id
  LEFT JOIN matchplayer as away_mp ON away_mp.match_id=mr.match_id AND away_mp.player_nb=IFNULL(mpe.away_player_nb, mtp.away_player) LEFT JOIN playerinfo as away_pi ON away_pi.id=away_mp.away_player_id
WHERE mr.match_id={$db->Record['MatchUniqueId']}
GROUP BY mr.game_id
EOQ;

      // Get double teams
      $count = 0;
      $DoubleTeams = array();
      for ($i=1; $i<=$db->Record['PlayerCount']; $i++) {
        for ($j=$i+1; $j<=$db->Record['PlayerCount']; $j++) {
          $DoubleTeams[++$count] = array($i, $j);
        }
      }

      $db2->query($match_scores_query);
      $HomeScore = 0;
      $AwayScore = 0;
      while ($db2->next_record()) {
        // Prepare
        switch ($db2->Record['player_nb']) {
          case 1:
          default:
            $HomePlayerMatchIndex  = $db2->Record['home_position'];
            $HomePlayerUniqueIndex = $db2->Record['home_index'];
            $AwayPlayerMatchIndex  = $db2->Record['away_position'];
            $AwayPlayerUniqueIndex = $db2->Record['away_index'];
            break;
          case 2:
            $HomePlayerMatchIndex  = $DoubleTeams[$db2->Record['home_index']];
            $AwayPlayerMatchIndex  = $DoubleTeams[$db2->Record['away_index']];
            $HomePlayerUniqueIndex = array($HomePlayers[$HomePlayerMatchIndex[0]]['UniqueIndex'], $HomePlayers[$HomePlayerMatchIndex[1]]['UniqueIndex']);
            $AwayPlayerUniqueIndex = array($AwayPlayers[$AwayPlayerMatchIndex[0]]['UniqueIndex'], $AwayPlayers[$AwayPlayerMatchIndex[1]]['UniqueIndex']);
            break;
        }

        // Build individual match results
        $IndividualMatchResult = array_merge(
          array(
            'Position'              => $db2->Record['game_id'],
            'HomePlayerMatchIndex'  => $HomePlayerMatchIndex,
            'HomePlayerUniqueIndex' => $HomePlayerUniqueIndex,
            'AwayPlayerMatchIndex'  => $AwayPlayerMatchIndex,
            'AwayPlayerUniqueIndex' => $AwayPlayerUniqueIndex
          ),
          $db2->Record['home_wo'] > 0 ? array('IsHomeForfeited' => true) : array('HomeSetCount' => $db2->Record['home_sets']),
          $db2->Record['away_wo'] > 0 ? array('IsAwayForfeited' => true) : array('AwaySetCount' => $db2->Record['away_sets'])
        );
        if ($db2->Record['home_wo'] == 0 && $db2->Record['away_wo'] == 0 && !$db2->Record['set_only']) {
          $IndividualMatchResult['Scores'] = $db2->Record['scores'];
        }
        $resDetails['IndividualMatchResults'][] = $IndividualMatchResult;

        // Calculate score
        if ($db2->Record['home_sets'] > $db2->Record['away_sets']) {
          $HomeScore++;
        }
        if ($db2->Record['away_sets'] > $db2->Record['home_sets']) {
          $AwayScore++;
        }
      }
      $db2->free();

      // Add match score
      $resDetails['HomeScore'] = $HomeScore;
      $resDetails['AwayScore'] = $AwayScore;
    }
    unset($db2);

    // Build response
    $res[] = array_merge(
      $divisionname=='' ?
        array() :
        array('DivisionName' => $divisionname),
      array(
        'DivisionId'       => $db->Record['DivisionId'],
        'DivisionCategory' => $db->Record['DivisionCategory'],
        'MatchId'          => $db->Record['MatchId'],
        'WeekName'         => $db->Record['WeekName']
      ),
      $db->Record['Date'] == '-' ?
        array() :
        array(
          'Date'  => $db->Record['Date'],
          'Time'  => $db->Record['Time']
        ),
      $db->Record['Date'] != '-' && is_numeric($db->Record['Venue']) && $db->Record['Venue']>=1 ?
        array(
          'Venue'      => $db->Record['Venue'],
          'VenueClub'  => $db->Record['VenueClub'],
          'VenueEntry' => array(
            'Name'      => $db->Record['VenueEntryName'],
            'Street'    => $db->Record['VenueEntryStreet'],
            'Town'      => $db->Record['VenueEntryTown'],
            'Phone'     => $db->Record['VenueEntryPhone'],
            'Comment'   => $db->Record['VenueEntryComment']
          )
        ) :
        array(),
      array(
        'HomeClub'  => $db->Record['HomeClub'],
        'HomeTeam'  => $db->Record['HomeTeam'],
        'AwayClub'  => $db->Record['AwayClub'],
        'AwayTeam'  => $db->Record['AwayTeam']
      ),
      $db->Record['Score'] == '-' ?
        array() :
        array('Score' => $db->Record['Score']),
      is_numeric($db->Record['MatchUniqueId']) && $db->Record['MatchUniqueId']>0 ?
        array('MatchUniqueId' => $db->Record['MatchUniqueId']) :
        array(),
      $db->Record['NextWeekName'] != '' ?
        array('NextWeekName' => $db->Record['NextWeekName']) :
        array(),
      $db->Record['PrevWeekName'] != '' ?
        array('PreviousWeekName' => $db->Record['PrevWeekName']) :
        array(),
      array(
        'IsHomeForfeited' => $db->Record['HomeFF'],
        'IsAwayForfeited' => $db->Record['AwayFF'],
        'IsHomeWithdrawn' => $db->Record['HomeWithdrawn'],
        'IsAwayWithdrawn' => $db->Record['AwayWithdrawn'],
        'IsValidated'     => $db->Record['IsValidated'],
        'IsLocked'        => $db->Record['IsValidated'] || ($db->Record['IsLocked'] && ($lock_limit_time < 0 ? true : strtotime($db->Record['LockTime']) + $lock_limit_time < time()))
      ),
      $WithDetails ?
        array('MatchDetails' => $resDetails) :
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
 * If "WithResults" is set, player results will be returned along with player details.
 *
 * @param $Request GetMembersRequest
 * @return GetMembersResponse
 * @version 0.7.21
 * @see GetMembersRequest, GetMembersResponse
 * @ingroup TabTAPIfunctions
 */
function GetMembers(stdClass $Request) {
  // Check permissions & quota
  $permissions = _MethodAPI(6, isset($Request->Credentials) ? $Request->Credentials : (object)array());

  // Required helpers functions
  include_once($GLOBALS['site_info']['path'].'public/players_fct.php');

  // Extract function arguments
  if (isset($Request->Season))         $Season         = trim($Request->Season);
  if (isset($Request->PlayerCategory)) $PlayerCategory = $Request->PlayerCategory;
  if (isset($Request->UniqueIndex))    $UniqueIndex    = $Request->UniqueIndex;
  if (isset($Request->NameSearch))     $NameSearch     = $Request->NameSearch;
  $Club                     = isset($Request->Club) ? trim($Request->Club) : '';
  $ExtendedInformation      = isset($Request->ExtendedInformation) && $Request->ExtendedInformation ? true : false;
  $RankingPointsInformation = isset($Request->RankingPointsInformation) && $Request->RankingPointsInformation ? true : false;
  $WithResults              = isset($Request->WithResults) && $Request->WithResults ? true : false;
  $OppRankingEvaluation     = isset($Request->WithOpponentRankingEvaluation) && $Request->WithOpponentRankingEvaluation ? true : false;

  // Create database session
  $db = new DB_Session();
  $db->Halt_On_Error = 'no';

  // Check season
  if (!isset($Season) || $Season == '')
  {
    $Season = $db->select_one("SELECT MAX(id) FROM seasoninfo;");
  }
  if (!is_numeric($Season))
  {
    throw new SoapFault('6', "Season [{$Season}] is not valid, must be numeric.");
  }
  $Season = intval($Season);
  if ($db->select_one("SELECT COUNT(*) FROM seasoninfo WHERE id={$Season}") == 0)
  {
    throw new SoapFault('7', "Season [{$Season}] is not valid.");
  }

  // Check that a list of club has been given (and take the first one only)
  if (is_array($Club)) {
    $Club = current($Club);
  }

  // Check club
  $club_where_clause = '1';
  $Club = trim($Club);
  if ($Club != '')
  {
    list($ClubId, $ClubName) = _GetClubInfo($Season, $Club);
    if (!is_numeric($ClubId) || $ClubId < 0)
    {
      throw new SoapFault('9', "Club [{$Club}] is not valid.");
    }
    $club_where_clause = "pclub.club_id={$ClubId}";
  }

  // Check player category
  if (!isset($PlayerCategory)) {
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
  $select_index_clause = '';
  if (isset($ClubId) && is_numeric($ClubId))
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

    // Ranking index
    $select_index = get_index_select('si', 'pstat', 'crep', 'pclass', false, true, true, true, 'ci', $ClubId);
    $select_index_clause = "{$select_index} as ranking_index,";
  }

  // Check unique index
  $index_where_clause = '1';
  if (isset($UniqueIndex)) {
    if (!is_numeric($UniqueIndex)) {
      throw new SoapFault('28', "Member unique index ({$UniqueIndex}) is not valid, must be numeric.");
    }
    $UniqueIndex = intval($UniqueIndex);
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

  // Summary and results can only be seen for one single player or single club
  if ($WithResults && !isset($UniqueIndex) && $Club=='') {
    throw new SoapFault('51', "UniqueIndex or Club must be specified to get player results.");
  }

  // Prepare list of results
  $res = array();

  // Prepare player category search
  $pcat_wherecond = get_player_categories_where_cond('si', 'pi', 'pcat', 0, false, false, false, false);
  $pcat_strict_wherecond = get_player_categories_where_cond('si', 'pi', 'pcat_strict', 1, false, false, false, false);
  $category_select = get_player_category_select('pi', 'pcat_strict');

  // Prepare additional queries for new ranking evaluation
  $new_ranking_from = '';
  $new_ranking_select = '';
  $ranking_methods = array(
    'MER'   => array('name' => 'MER',   'code' => 'mer',   'id' => 6),
    'VLB'   => array('name' => 'VLB',   'code' => 'vlb',   'id' => 3),
    'GOF'   => array('name' => 'GOF',   'code' => 'gof',   'id' => 4),
    'RES'   => array('name' => 'RES',   'code' => 'res',   'id' => 5),
    'L/K'   => array('name' => 'L/K',   'code' => 'lk',    'id' => 7),
    'BOO'   => array('name' => 'BOO',   'code' => 'boost', 'id' => 8),
    'A_F'   => array('name' => 'A_F',   'code' => 'af',    'id' => 9),
    'VLB2'  => array('name' => 'VLB#2', 'code' => 'vlb2',  'id' => 203),
    'RES2'  => array('name' => 'RES#2', 'code' => 'res2',  'id' => 205),
    'LK2'   => array('name' => 'L/K#2', 'code' => 'lk2',   'id' => 207)
  );
  if ($RankingPointsInformation) {
    foreach ($ranking_methods as $ranking_method) {
      $new_ranking_from   .= $new_ranking_from != '' ? ' ' : '';
      $new_ranking_from   .= "LEFT JOIN playernewclassement pnc_{$ranking_method['code']} ON pnc_{$ranking_method['code']}.player_id=pi.id AND pnc_{$ranking_method['code']}.season=si.id AND pnc_{$ranking_method['code']}.category={$PlayerCategory} and pnc_{$ranking_method['code']}.method={$ranking_method['id']}";
      $new_ranking_from   .= ' ';
      $new_ranking_from   .= "LEFT JOIN classementinfo as ci_{$ranking_method['code']} ON ci_{$ranking_method['code']}.id=pnc_{$ranking_method['code']}.classement_id AND ci_{$ranking_method['code']}.category=pnc_{$ranking_method['code']}.category";
      $new_ranking_select .= ",IFNULL(IF(pnc_{$ranking_method['code']}.classement_id=0, '??', IF(ci_{$ranking_method['code']}.name='NC', '{$GLOBALS['str_NC']}', ci_{$ranking_method['code']}.name)), 'ERR') as new_classement_{$ranking_method['code']}";
      $new_ranking_select .= ",pnc_{$ranking_method['code']}.modified as new_classement_{$ranking_method['code']}_modified";
    }
  }
  $results_join_clause = '';
  $results_select = '';
  $results_where_clause = '1';
  if ($WithResults) {
    // To make sure GROUP_CONCAT will contain all results
    $db->query("SET SESSION group_concat_max_len = 2000000;");
    $results_join_clause  =  "LEFT JOIN tmp_resultsraw as raw ON raw.player_id=pi.id AND raw.season=si.id AND match_value>0 AND raw.res IN ('D', 'V') and raw.category IN (SELECT id FROM divisioncategories WHERE classementcategory={$RankingCategory})";
    $results_join_clause .= " LEFT JOIN playerinfo as opp_pi ON opp_pi.id=raw.opp_id";
    $results_join_clause .= " LEFT JOIN classementinfo as ci_raw ON ci_raw.id=raw.opp_cl AND ci_raw.category={$RankingCategory}";
    $results_join_clause .= " LEFT JOIN playerclub as opp_pclub ON opp_pclub.season={$Season} AND opp_pclub.player_id=raw.opp_id";
    $results_join_clause .= " LEFT JOIN clubs as opp_club ON opp_club.id=opp_pclub.club_id";
    $results_join_clause .= " LEFT JOIN tournamentseries as ts ON ts.id=raw.match_id";
    $results_join_clause .= " LEFT JOIN tournaments as t ON t.id=ts.tournament_id";
    $results_join_clause .= " LEFT JOIN divisionresults as divr ON divr.match_id=raw.match_id";
    $results_join_clause .= " LEFT JOIN tmp_matchnum as mnum ON mnum.div_id=raw.div_id and mnum.week=divr.week and mnum.match_nb=divr.match_nb";

    // Information about opponent new rankings (only for authorized users)
    $opp_ranking_results_select = "''";
    if ($OppRankingEvaluation && count(array_intersect($permissions, array('admin', 'classement'))) > 0) {
      $opp_ranking_results_select = array();
      foreach ($ranking_methods as $ranking_method) {
        $results_join_clause .= " LEFT JOIN playernewclassement opp_pnc_{$ranking_method['code']} ON opp_pnc_{$ranking_method['code']}.player_id=pi.id AND opp_pnc_{$ranking_method['code']}.season=si.id AND opp_pnc_{$ranking_method['code']}.category={$PlayerCategory} and opp_pnc_{$ranking_method['code']}.method={$ranking_method['id']}";
        $results_join_clause .= " LEFT JOIN classementinfo as opp_ci_{$ranking_method['code']} ON opp_ci_{$ranking_method['code']}.id=opp_pnc_{$ranking_method['code']}.classement_id AND opp_ci_{$ranking_method['code']}.category=opp_pnc_{$ranking_method['code']}.category";
        $opp_ranking_results_select[] = (count($opp_ranking_results_select) > 0 ? "'|'," : '') . "CONCAT('{$ranking_method['name']}=', IFNULL(IF(opp_pnc_{$ranking_method['code']}.classement_id=0, '??', IF(opp_ci_{$ranking_method['code']}.name='NC', '{$GLOBALS['str_NC']}', opp_ci_{$ranking_method['code']}.name)), 'ERR'))";
      }
      $opp_ranking_results_select = "CONCAT(" . join(',', $opp_ranking_results_select) . ")";
    }

    $results_select = ",GROUP_CONCAT(DISTINCT CONCAT(raw.match_date, '§', opp_pi.vttl_index, '§', opp_pi.first_name, '§', opp_pi.last_name, '§', IF(ci_raw.name='NC', '{$GLOBALS['str_NC']}', ci_raw.name), '§', raw.res, '§', IFNULL(IF(raw.match_type='T' AND raw.home='N',raw.set_against,raw.set_for), ''), '§', IFNULL(IF(raw.match_type='T' AND raw.home='N',raw.set_for,raw.set_against), ''), '§', raw.match_id, '§', raw.game_id, '§', raw.match_type, '§', IFNULL(IF(TRIM(opp_club.short_name)='', NULL, opp_club.short_name), opp_club.name), '§', IFNULL(t.name, ''), '§', IFNULL(ts.name, ''), '§', IFNULL(mnum.matchnum, ''), '§', {$opp_ranking_results_select}) ORDER BY raw.match_date SEPARATOR 'µ') as results";
  }

  // Status field
  $status_field = 'IFNULL(pcs.status, pstat.status)';

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
  IFNULL({$status_field}, 'A') as status,
  IF(ci.name='NC', '{$GLOBALS['str_NC']}', ci.name) as classement,
  c.indice as club_indice,
  pi.medic_validity>si.start_date as medical_attestation,
  {$category_select} as player_category,
  IFNULL(pelo.points, -1) as player_elo_points,
  pelo.modified as player_elo_points_modified,
  pi.email as email,
  pi.home_phone as phone_home,
  pi.office_phone as phone_work,
  pi.fax as phone_fax,
  pi.gsm as phone_mobile,
  pi.national_number as national_number,
  pi.address as address_line1,
  z.postcode as address_zip,
  z.name as address_town
  {$new_ranking_select}
  {$results_select}
FROM
  (seasoninfo as si, playerinfo as pi)
  {$new_ranking_from}
  {$results_join_clause}
  LEFT JOIN playercategories as pcat ON {$pcat_wherecond}
  LEFT JOIN playerstatus as pstat ON pstat.season=si.id AND pstat.player_id=pi.id
  LEFT JOIN playercategorystatus as pcs ON pcs.season=si.id AND pcs.player_id=pi.id AND pcs.category_id=pcat.id
  LEFT JOIN playercategories as pcat_strict ON {$pcat_strict_wherecond}
  LEFT JOIN playerlastelo pelo ON pelo.player_id=pi.id AND pelo.class_category=pcat.classementcategory
  LEFT JOIN postcodes z ON z.id=pi.postcode,
  playerclub as pclub LEFT JOIN clubs as c ON c.id=pclub.club_id,
  playerclassement as pclass,
  classementinfo as ci
WHERE 1
  AND si.id={$Season}
  AND pclub.season=si.id
  AND pclub.player_id=pi.id
  AND {$club_where_clause}
  AND IFNULL({$status_field}, 'A') IN ('A','S','E','L','R','V','D','T','M')
  AND pclass.season=si.id
  AND pclass.player_id=pi.id
  AND pclass.category=pcat.classementcategory
  AND (pcat.id={$PlayerCategory} OR IFNULL({$status_field}, 'A')='E')
  AND ci.id=pclass.classement_id
  AND ci.category=pclass.category
  AND {$index_where_clause}
  AND {$name_where_clause}
  AND {$results_where_clause}
GROUP BY
  pi.id
ORDER BY
  ci.order ASC, pi.last_name, pi.first_name
EOQ;

  $position = 0;
  $db->query($q);
  if ($db->Errno != 0) throw new SoapFault('8', "Unexpected database error [{$db->Error}].");
  while ($db->next_record())
  {
    // Note: Non playing members do not have a ranking index and position (they cannot play any match)
    // All other players should receive a position and an index
    $entry = array_merge(
      1||$db->Record['status'] != 'L' ? array('Position' => ++$position) : array(),
      array('UniqueIndex' => $db->Record['unique_index']),
      1||$db->Record['status'] != 'L' ? array('RankingIndex' => isset($db->Record['ranking_index']) ? $db->Record['ranking_index'] : '') : array(),
      array(
        'FirstName' => $db->Record['first_name'],
        'LastName'  => $db->Record['last_name'],
        'Ranking'   => $db->Record['classement']
      )
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
        $entry['NationalNumber']   = $db->Record['national_number'];
      }
      $entry['MedicalAttestation'] = $db->Record['medical_attestation'];
    }
    if ($RankingPointsInformation) {
      $entry['RankingPointsCount'] = 0;
      $entry['RankingPointsEntries'] = array();

      if ($db->Record['player_elo_points']>=0) {
        $entry['RankingPointsCount']++;
        $entry['RankingPointsEntries'][] = array(
          'MethodName'   => 'ELO',
          'Value'        => $db->Record['player_elo_points'],
          'LastModified' => $db->Record['player_elo_points_modified']
        );
      }

      // Only admins and users with the 'classement' permission can access to all evaluations
      $allow_ranking_info = count(array_intersect($permissions, array('admin', 'classement'))) > 0;

      // If you are not an admin, if flag 'allow_own_ranking_info' is set to true in the site configuration, one can access his/her own ranking evaluation
      // If allow_own_ranking_info gives an array, the user's club category must be in that array to be allowed
      if (!$allow_ranking_info && isset($GLOBALS['site_info']['allow_own_ranking_info']) && $GLOBALS['site_info']['allow_own_ranking_info'] !== false && $GLOBALS['auth']->auth['unique_index'] === $entry['UniqueIndex']) {
        if ($GLOBALS['site_info']['allow_own_ranking_info'] === true) {
          $allow_ranking_info = true;
        } elseif (is_array($GLOBALS['site_info']['allow_own_ranking_info']) && in_array($GLOBALS['auth']->auth['club_category'], $GLOBALS['site_info']['allow_own_ranking_info'])) {
          $allow_ranking_info = true;
        }
      }

      // Add results if allowed
      if ($allow_ranking_info) {
        foreach ($ranking_methods as $ranking_method) {
          $entry['RankingPointsCount']++;
          $entry['RankingPointsEntries'][] = array(
            'MethodName'   => $ranking_method['name'],
            'Value'        => $db->Record["new_classement_{$ranking_method['code']}"],
            'LastModified' => $db->Record["new_classement_{$ranking_method['code']}_modified"]
          );
        }
      }
    }
    if ($ExtendedInformation && count(array_intersect($permissions, array('admin')))>0)
    {
      if (trim($db->Record['email']) != '') $entry['Email'] = $db->Record['email'];
      $entry['Phone'] = array();
      if (trim($db->Record['phone_home']) != '')   $entry['Phone']['Home']   = _GetPhone($db->Record['phone_home']) ? _GetPhone($db->Record['phone_home']) : $db->Record['phone_home']; 
      if (trim($db->Record['phone_work']) != '')   $entry['Phone']['Work']   = _GetPhone($db->Record['phone_work']) ? _GetPhone($db->Record['phone_work']) : $db->Record['phone_work'];
      if (trim($db->Record['phone_fax']) != '')    $entry['Phone']['Fax']    = _GetPhone($db->Record['phone_fax']) ? _GetPhone($db->Record['phone_fax']) : $db->Record['phone_fax'];
      if (trim($db->Record['phone_mobile']) != '') $entry['Phone']['Mobile'] = _GetPhone($db->Record['phone_mobile']) ? _GetPhone($db->Record['phone_mobile']) : $db->Record['phone_mobile'];
      if (count($entry['Phone'])==0) unset($entry['Phone']);
      $entry['Address'] = array();
      if (trim($db->Record['address_line1']) != '' && trim($db->Record['address_town']) != '-') {
        if (trim($db->Record['address_line1']) != '') $entry['Address']['Line1'] = $db->Record['address_line1'];
        $entry['Address']['ZipCode'] = $db->Record['address_zip'];
        if (trim($db->Record['address_town']) != '-')  $entry['Address']['Town'] = $db->Record['address_town'];
      }
      if (count($entry['Address'])==0) unset($entry['Address']);
    }
    if ($WithResults && isset($db->Record['results'])) {
      $results = array();
      foreach (explode('µ', $db->Record['results']) as $result_string) {
        $result = explode('§', $result_string);
        $current_result = array(
          'Date'             => $result[0],
          'UniqueIndex'      => $result[1],
          'FirstName'        => $result[2],
          'LastName'         => $result[3],
          'Ranking'          => $result[4],
          'Result'           => $result[5],
          'SetFor'           => $result[6],
          'SetAgainst'       => $result[7],
          'CompetitionType'  => $result[10],
          'Club'             => $result[11]
        );
        if ($current_result['CompetitionType'] == 'T') {
          $current_result['TournamentName'] = $result[12];
          $current_result['TournamentSerieName'] = $result[13];
        }
        if ($current_result['CompetitionType'] == 'C') {
          $current_result['MatchId'] = $result[14];
        }
        if (strlen($result[15]) > 0) {
          $OppRankingEvaluationEntries = array();
          foreach (explode('|', $result[15]) as $OppEvaluation) {
            list($OppEvaluationType, $OppEvaluationValue) = explode('=', $OppEvaluation);
            $OppRankingEvaluationEntries[] = array(
              'EvaluationType'  => $OppEvaluationType,
              'EvaluationValue' => $OppEvaluationValue
            );
          }
          $current_result['RankingEvaluationCount']   = count($OppRankingEvaluationEntries);
          $current_result['RankingEvaluationEntries'] = $OppRankingEvaluationEntries;
        }
        $results[] = $current_result;
      }
      $entry['ResultCount'] = count($results);
      $entry['ResultEntries'] = $results;
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
function Upload(stdClass $Request) {
  // Check permissions & quota
  $permissions = _MethodAPI(7, isset($Request->Credentials) ? $Request->Credentials : (object)array());

  // Extract function arguments
  $Data        = trim($Request->Data);

  // Check permissions
  if (count(array_intersect($permissions, array('club', 'province', 'region', 'admin')))==0) {
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
  if (!$res['Result']) {
    $res['ErrorLines'] = array();
    foreach ($errors as $error) $res['ErrorLines'][] = $error;
  }
  return $res;
}

/**
 * @brief Retrieve club list according to a given category
 *
 * @param $Request GetClubsRequest
 * @return GetClubsResponse
 * @since Version 0.6
 * @version 0.7.12
 */
function GetClubs(stdClass $Request) {
  // Check permissions & quota
  $permissions = _MethodAPI(8, isset($Request->Credentials) ? $Request->Credentials : (object)array());

  // Extract function arguments
  if (isset($Request->Season))       $Season          = trim($Request->Season);
  if (isset($Request->ClubCategory)) $ClubCategoryId  = $Request->ClubCategory;
  if (isset($Request->Club))         $Club            = $Request->Club;

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
  $Season = intval($Season);
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
  $clubcategory_where_clause = isset($ClubCategoryId) && is_numeric($ClubCategoryId) ? "c.category={$ClubCategoryId}" : '1';

  // Check club
  $ClubId = 0;
  $Club = isset($Club) ? str_replace(array('-','/',' '), '', strtoupper($Club)) : '';
  if ($Club != '') {
    $q = "SELECT id FROM clubs AS c WHERE REPLACE(REPLACE(REPLACE(UCASE(c.indice), ' ', ''), '/', ''), '-', '')='{$Club}' AND (ISNULL(c.first_season) OR c.first_season<={$Season}) AND (ISNULL(c.last_season) OR c.last_season>={$Season})";
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
  IFNULL(IF(TRIM(c.short_name)='', NULL, c.short_name), c.name) as `club_name`,
  c.name as `club_long_name`,
  c.category as `club_category`,
  ccat.name as `club_category_name`,
  GROUP_CONCAT(CONCAT(ca.id, '|', ca.address_id, '|', IFNULL(ca.name, ''), '|', IFNULL(ca.address, ''), '|', IFNULL(ca.zip, ''), ' ', IFNULL(ca.town, ''), '|', IFNULL(ca.phone, ''), '|', IFNULL(ca.comment, '')) SEPARATOR '#-@-$') as addresses
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
  while ($db->next_record()) {
    $venues = array();
    foreach (explode('#-@-$', $db->Record['addresses']) as $db_address) {
      $db_venue = explode('|', $db_address);
      if (is_numeric($db_venue[0]) && $db_venue[0]>0) {
        $venues[] = array(
          'Id'        => $db_venue[0],
          'ClubVenue' => $db_venue[1],
          'Name'      => $db_venue[2],
          'Street'    => $db_venue[3],
          'Town'      => $db_venue[4],
          'Phone'     => $db_venue[5],
          'Comment'   => $db_venue[6]
        );
      }
    }
    $clubEntry = array(
      'UniqueIndex'  => $db->Record['unique_index'],
      'Name'         => $db->Record['club_name'],
      'LongName'     => $db->Record['club_long_name'],
      'Category'     => $db->Record['club_category'],
      'CategoryName' => $db->Record['club_category_name'],
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
 * @brief Retrieve division list
 *
 * @param $Request GetDivisionsRequest
 * @return GetDivisionsResponse
 * @since Version 0.7.16
 * @version 0.7.16
 */
function GetDivisions(stdClass $Request) {
  // Check permissions & quota
  $permissions = _MethodAPI(9, isset($Request->Credentials) ? $Request->Credentials : (object)array());

  // Extract function arguments
  if (isset($Request->Season))           $Season           = trim($Request->Season);
  if (isset($Request->Level))            $Levels           = $Request->Level;
  if (isset($Request->ShowDivisionName)) $ShowDivisionName = strtolower(trim($Request->ShowDivisionName));

  // Create database session
  $db = new DB_Session();
  $db->Halt_On_Error = 'no';

  //
  // Check season
  //
  if (!isset($Season) || '' == $Season) {
    // If no season given, get the last one
    $Season = select_one("SELECT MAX(id) FROM seasoninfo;");
  }
  // If season is given, it must be a numeric value
  if (!is_numeric($Season)) {
    throw new SoapFault('6', "Season [{$Season}] is not valid, must be numeric.");
  }
  // If season is given, it must exist
  $Season = intval($Season);
  if (select_one("SELECT COUNT(*) FROM seasoninfo WHERE id={$Season}") == 0) {
    throw new SoapFault('7', "Season [{$Season}] is not valid.");
  }

  //
  // Check level
  //
  if (!isset($Levels)) {
    $Levels = array();
  }
  if (!is_array($Levels)) {
    $Levels = array($Levels);
  }
  foreach ($Levels as $k => $LevelId) {
    if ($LevelId != '' && !is_numeric($LevelId)) {
      throw new SoapFault('18', "Level must be a number ([{$LevelId}]).");
    }
    if ($LevelId != '') {
      $LevelId = intval($LevelId);
      if ($db->select_one("SELECT COUNT(*) FROM levelinfo WHERE id={$LevelId};") == 0) {
        throw new SoapFault('19', "LevelId [{$LevelId}] does not exists.");
      }
    }
    // Validated level
    $Levels[$k] = $LevelId;
  }

  //
  // Check 'ShowDivisionName' option
  //
  if (!isset($ShowDivisionName)) {
    $ShowDivisionName = 'yes';
  }
  if ($ShowDivisionName != '' && !in_array($ShowDivisionName, array('yes', 'no', 'short'))) {
    throw new SoapFault('21', "ShowDivisionName [{$ShowDivisionName}] is not valid.");
  }

  // Prepare some clause to filter the list
  $level_where_clause = isset($LevelId) && is_numeric($LevelId) ? "di.level IN (" . implode(',', $Levels) . ")" : '1';

  // Prepare query to retrieve all divisions matching the given criteria
  $q = <<<EOQ
SELECT
  di.id as div_id,
  di.category as category,
  di.level as level,
  di.match_type_id as match_type
FROM
  divisioninfo di
  LEFT JOIN levelinfo li ON li.id=di.level
WHERE 1
  AND di.season={$Season}
  AND {$level_where_clause}
ORDER BY
  li.order ASC
EOQ;

  // Prepare list of results
  $res = array();
  $db->query($q);
  while ($db->next_record()) {
    $divisionname = _GetDivisionName($ShowDivisionName, $Season, $db->Record['div_id']);

    // Prepare entry
    $divisionEntry = array_merge(
      array(
        'DivisionId'       => $db->Record['div_id']
      ),
      $divisionname == '' ?
        array() :
        array('DivisionName' => $divisionname),
      array(
        'DivisionCategory' => $db->Record['category'],
        'Level'            => $db->Record['level'],
        'MatchType'        => $db->Record['match_type']
      )
    );
    $res[] = $divisionEntry;
  }

  // Release database connection
  $db->free();
  unset($db);

  return array('DivisionCount'   => count($res),
               'DivisionEntries' => $res);
}

/**
 * @brief Retrieve tournament list
 * 
 * A tournament is a table tennis event where individual matches are played during one or several days.
 * 
 * Matches are played inside "series" that typically group players by category (age or ranking).
 *
 * If a specific tournament is given, results of the selected tournament can be given.
 *
 * @param $Request GetTournamentsRequest
 * @return GetTournamentsResponse
 * @since Version 0.7.16
 * @version 0.7.23
 */
function GetTournaments(stdClass $Request) {

  $logger = Logger::getLogger('GetTournaments');
  $logger->debug('request is:'.json_encode($Request) );
  

  // Check permissions & quota
  $permissions = _MethodAPI(10, isset($Request->Credentials) ? $Request->Credentials : (object)array());

  // Extract arguments
  if (isset($Request->Season))                 $Season          = trim($Request->Season);
  if (isset($Request->TournamentUniqueIndex))  $TournamentId    = trim($Request->TournamentUniqueIndex);
  $WithResults = isset($Request->WithResults) && $Request->WithResults ? true : false;
  $WithRegistrations = isset($Request->WithRegistrations) && $Request->WithRegistrations ? true : false;

  $logger->debug('WithResults='.$WithResults);  
  $logger->debug('WithRegistrations='.$WithRegistrations);  
 

  // Create database session
  $db = new DB_Session();
  $db->Halt_On_Error = 'no';

  // Check season
  if (!isset($Season) || '' == $Season) {
    // If no season given, get the last one
    $Season = $db->select_one("SELECT MAX(id) FROM seasoninfo;");
  }
  // If season is given, it must be a numeric value
  if (!is_numeric($Season)) {
    throw new SoapFault('6', "Season [{$Season}] is not valid, must be numeric.");
  }
  // If season is given, it must exist
  $Season = intval($Season);
  if ($db->select_one("SELECT COUNT(*) FROM seasoninfo WHERE id={$Season}") == 0) {
    throw new SoapFault('7', "Season [{$Season}] is not valid.");
  }

  // Validate tournament (if given)
  $tournament_where_clause = '1';
  if (isset($TournamentId) && !is_numeric($TournamentId)) {
    throw new SoapFault('49', "Tournament unique index ({$TournamentId}) is not valid, must be numeric.");
  } elseif (isset($TournamentId)) {
    $TournamentId = intval($TournamentId);
    if ($db->select_one("SELECT COUNT(*) FROM tournaments WHERE id={$TournamentId}") <= 0) {
      throw new SoapFault('50', "Tournament unique index ({$TournamentId}) is not valid.");
    }
  }
  if (isset($TournamentId) && is_numeric($TournamentId)) {
    $tournament_where_clause = "t.id={$TournamentId}";
  }

  // Tournament results can only be called by tournament
  if ($WithResults && !isset($TournamentId)) {
    throw new SoapFault('51', "TournamentUniqueIndex must be specified to get tournament results.");
  }

  // Tournament registrations can only be called by tournament
  if ($WithRegistrations && !isset($TournamentId)) {
    throw new SoapFault('51', "TournamentUniqueIndex must be specified to get tournament registrations.");
  }

  // Prepare additional clauses to extract results
  $results_select_clause = '';
  $results_from_clause = '';
  $results_where_clause = '1';
  if ($WithResults) {
    $results_from_clause = <<<EOC
LEFT JOIN tournamentresults tr ON tr.tournament_id=t.id
LEFT JOIN playerinfo player_pi ON player_pi.id=tr.player_id
LEFT JOIN playerinfo opponent_pi ON opponent_pi.id=tr.opponent_id AND tr.serie_id=ts.id
LEFT JOIN playerclassement player_pc ON player_pc.season={$Season} AND player_pc.player_id=player_pi.id AND player_pc.category=ts.classementcategory
LEFT JOIN playerclassement opponent_pc ON opponent_pc.season={$Season} AND opponent_pc.player_id=opponent_pi.id AND opponent_pc.category=ts.classementcategory
LEFT JOIN classementinfo player_ci ON player_ci.id=player_pc.classement_id AND player_ci.category=player_pc.category
LEFT JOIN classementinfo opponent_ci ON opponent_ci.id=opponent_pc.classement_id AND opponent_ci.category=opponent_pc.category
EOC;
    $results_select_clause = ",GROUP_CONCAT(CONCAT(tr.serie_id, '§', tr.id, '§', player_pi.vttl_index, '§', opponent_pi.vttl_index, '§', player_pi.first_name, '§', opponent_pi.first_name, '§', player_pi.last_name, '§', opponent_pi.last_name, '§', player_ci.name, '§', opponent_ci.name, '§', tr.player_score, '§', tr.opponent_score, '§', tr.player_wo, '§', tr.opponent_wo) SEPARATOR 'µ') as tournament_results";
  }

  // Prepare query to retrieve all tournaments matching the given criteria
  $q = <<<EOQ
SELECT
  t.id as unique_index,
  t.name as tournament_name,
  t.level as tournament_level,
  t.authorisation_ref as external_index,
  t.date_from as tournament_date_from,
  t.date_to as tournament_date_to,
  t.registration_date as tournament_date_registration,
  t.address_venue as tournament_venue_name,
  t.address_street as tournament_venue_street,
  CONCAT(t.address_zip, ' ', t.address_town) as tournament_venue_town,
  GROUP_CONCAT(DISTINCT CONCAT(ts.id, '§', ts.name) SEPARATOR 'µ') as tournament_series
  {$results_select_clause}
FROM
  tournaments t
  LEFT JOIN tournamentseries ts ON ts.tournament_id=t.id
  {$results_from_clause}
WHERE 1
  AND t.season={$Season}
  AND {$tournament_where_clause}
  AND {$results_where_clause}
GROUP BY
  t.id
EOQ;

  // Prepare list of results
  $res = array();
  $db->query("SET SESSION group_concat_max_len = 2000000;");
  $db->query($q);
  while ($db->next_record()) {
    $tournamentEntry = array(
      'UniqueIndex'   => $db->Record['unique_index'],
      'Name'          => $db->Record['tournament_name'],
      'Level'         => $db->Record['tournament_level'],
      'ExternalIndex' => $db->Record['external_index'],
      'DateFrom'      => $db->Record['tournament_date_from']
    );
    if ($db->Record['tournament_date_to']) {
      $tournamentEntry['DateTo'] = $db->Record['tournament_date_to'];
    }
    if ($db->Record['tournament_date_registration']) {
      $tournamentEntry['RegistrationDate'] = $db->Record['tournament_date_registration'];
    }
    if ($db->Record['tournament_venue_name']) {
      $tournamentEntry['Venue'] = array(
        'Name'   => $db->Record['tournament_venue_name'],
        'Street' => $db->Record['tournament_venue_street'],
        'Town'   => $db->Record['tournament_venue_town']
      );
    }
    $results = array();
    if ($WithResults && $db->Record['tournament_results']) {
      foreach (explode('µ', $db->Record['tournament_results']) as $results_serie) {
        list($serie_id, $result_id, $player_id, $opponent_id, $player_first_name, $opponent_first_name, $player_last_name, $opponent_last_name, $player_ranking, $opponent_ranking, $player_score, $opponent_score) = explode('§', $results_serie);
        if (!isset($results[$serie_id])) {
          $results[$serie_id] = array();
        }
        $results[$serie_id][] = array(
          'HomePlayer'    => array(
            'UniqueIndex' => $player_id,
            'FirstName'   => $player_first_name,
            'LastName'    => $player_last_name,
            'Ranking'     => $player_ranking
          ),
          'AwayPlayer'    => array(
            'UniqueIndex' => $opponent_id,
            'FirstName'   => $opponent_first_name,
            'LastName'    => $opponent_last_name,
            'Ranking'     => $opponent_ranking
          ),
          'HomeSetCount'  => $player_score,
          'AwaySetCount'  => $opponent_score,
        );
      }
    }
    if ($db->Record['tournament_series']) {
      $series = explode('µ', $db->Record['tournament_series']);
      $tournamentEntry['SerieCount'] = count($series);
      $tournamentEntry['SerieEntries'] = array();

      $db_2 = null;
      if( $WithRegistrations ) {
        $db_2 = new DB_Session();
        $db_2->Halt_On_Error = 'no';
      }

      foreach ($series as $serie) {
        list($serie_id, $serie_name) = explode('§', $serie);
        $SerieEntry = array(
          'UniqueIndex' => $serie_id,
          'Name'        => $serie_name
        );
        if ($WithResults) {
          $SerieEntry['ResultCount'] = isset($results[$serie_id]) ? count($results[$serie_id]) : 0;
          if ($SerieEntry['ResultCount'] > 0) {
            $SerieEntry['ResultEntries'] = $results[$serie_id];
          }
        }

        // Adding registration infos for each serie
        if( $WithRegistrations ) {
          $SerieEntry['RegistrationCount']=0;

          $logger->debug('Loading registrations for tournament:'.$TournamentId);  

          // evaluate registration count for that serie and that tournament
          $sqlRegistrationCountForSerie="select count(*) as cnt from tournamentplayers where serie_id={$serie_id}";

          $count=$db_2->select_one($sqlRegistrationCountForSerie); 
          $logger->debug('Loading registrations for tournament count:'.$count);  

          $SerieEntry['RegistrationCount']=$count;

          // identify each registration for that serie and that tournament (if any)
          if( $count > 0 ) {

            $registrations=array();

            $sqlFindRegitrationsForSerie="
select tp.id as UniqueIndex,
  pi.id as PlayerUniqueIndex, pi.first_name as FirstName, pi.last_name as LastName,
  ci.name as Ranking,
  cb.id as clubUniqueIndex, cb.name as ClubName, cb.short_name as ClubShortName, cb.indice as ClubIndice, 
  cc.id as ClubCategory, cc.name as ClubCategoryName
from tournamentplayers tp
  inner join playerinfo pi on pi.id=tp.player_id
  inner join tournaments t on t.id=tp.tournament_id
  inner join tournamentseries s on s.id=tp.serie_id
  inner join playerclassement pc on pc.season=t.season and pc.player_id=tp.player_id 
    and pc.category=s.classementcategory
  inner join classementinfo ci on ci.id=pc.classement_id and ci.category=s.classementcategory
  inner join playerclub pcb on pcb.player_id=tp.player_id and pcb.season=t.season
  inner join clubs cb on cb.id=pcb.club_id
  inner join clubcategories cc on cc.id=cb.category
where tp.tournament_id={$TournamentId}
and tp.serie_id={$serie_id}
";
            $q_id=$db_2->query($sqlFindRegitrationsForSerie);

            while ($db_2->next_record()) {

              $d=array(
                'RegistrationUniqueIndex'=> $db_2->Record['UniqueIndex'],
                
                'Member' => array( 
                    'UniqueIndex'=> $db_2->Record['PlayerUniqueIndex'],
                    'FirstName'=> $db_2->Record['FirstName'],
                    'LastName'=> $db_2->Record['LastName'],
                    'Ranking'=> $db_2->Record['Ranking'],
                  ),
                'Club' => array(
                    'UniqueIndex'=> $db_2->Record['clubUniqueIndex'],
                    'LongName'=> $db_2->Record['ClubName'],
                    'Name'=> $db_2->Record['ClubShortName'],
                    'Category'=> $db_2->Record['ClubCategory'],
                    'CategoryName'=> $db_2->Record['ClubCategoryName'],
                    'VenueCount'=> -1, // noop here ... Prefer to put '-1' in place of '0', as '0' means 'nothing'
                )
                
              );

              $logger->debug('Loading registrations for tournament: registered='.json_encode($d));  
              $registrations[]=$d;
              
            }

            $SerieEntry['RegistrationEntries'] = $registrations;
            
          }
        }

        $tournamentEntry['SerieEntries'][] = $SerieEntry;
        
      }

      if( $WithRegistrations && $db_2!=null)
      {
        // Release database connection
        $db_2->free();
        unset($db_2);
      }
    } else {
      $tournamentEntry['SerieCount'] = 0;
    }
    $res[] = $tournamentEntry;
  }

  // Release database connection
  $db->free();
  unset($db);

  if( $WithRegistrations ) {
    $logger->debug('Tournament entry: '.json_encode($tournamentEntry));  
  }

  return array('TournamentCount'   => count($res),
               'TournamentEntries' => $res);
}

/**
 * @brief Retrieve list (individual) match systems
 *
 * @param $Request GetMatchSystemsRequest
 * @return GetMatchSystemsResponse
 * @since Version 0.7.17
 * @version 0.7.19
 */
function GetMatchSystems(stdClass $Request) {
  // Check permissions & quota
  $permissions = _MethodAPI(10, isset($Request->Credentials) ? $Request->Credentials : (object)array());

  // Extract function arguments
  if (isset($Request->UniqueIndex))  $UniqueIndex     = trim($Request->UniqueIndex);

  // Check unique index
  $index_where_clause = '1';
  if (isset($UniqueIndex)) {
    if (!is_numeric($UniqueIndex)) {
      throw new SoapFault('28', "Match system unique index ({$UniqueIndex}) is not valid, must be numeric.");
    }
    $UniqueIndex = intval($UniqueIndex);
    $index_where_clause = "mti.id={$UniqueIndex}";
  }

  // Create database session
  $db = new DB_Session();
  $db->Halt_On_Error = 'no';

  // Prepare query to retrieve all tournaments matching the given criteria
  $q = <<<EOQ
SELECT
  mti.id as unique_index,
  mti.name as system_name,
  mti.nb_single as nb_single,
  mti.nb_double as nb_double,
  mti.nb_sets as nb_sets,
  mti.nb_points as nb_points,
  mti.force_double_teams as force_double_teams,
  mti.nb_substitutes as nb_substitutes,
  GROUP_CONCAT(CONCAT(mtp.game_nb, '|', mtp.player_nb, '|', mtp.home_player, '|', mtp.away_player, '|', mtp.allow_substitute) ORDER BY mtp.game_nb) as game_list
FROM
  matchtypeinfo mti
  LEFT JOIN matchtypeplayer mtp ON mtp.match_type_id=mti.id
WHERE 1
  AND {$index_where_clause}
GROUP BY
  mti.id
EOQ;

  // Prepare list of results
  $res = array();
  $db->query($q);
  while ($db->next_record()) {
    $defEntries = array();
    foreach (explode(',', $db->Record['game_list']) as $k => $data) {
      if (trim($data) != '') {
        list($Position, $Type, $HomePlayerIndex, $AwayPlayerIndex, $AllowSubstitute) = explode('|', $data);
        $defEntries[] = array_merge(
          array(
            'Position'=> $Position,
            'Type'    => $Type
          ),
          $HomePlayerIndex > 0 && $Type == 1 ? array('HomePlayerIndex' => $HomePlayerIndex) : array(),
          $AwayPlayerIndex > 0 && $Type == 1 ? array('AwayPlayerIndex' => $AwayPlayerIndex) : array(),
          $db->Record['nb_substitutes'] > 0 ? array('AllowSubstitute' => $AllowSubstitute == 'Y') : array()
        );
      }
    }

    $matchSystemEntry = array(
      'UniqueIndex'                => $db->Record['unique_index'],
      'Name'                       => $db->Record['system_name'],
      'SingleMatchCount'           => $db->Record['nb_single'],
      'DoubleMatchCount'           => $db->Record['nb_double'],
      'SetCount'                   => $db->Record['nb_sets'],
      'PointCount'                 => $db->Record['nb_points'],
      'ForcedDoubleTeams'          => $db->Record['force_double_teams'],
      'SubstituteCount'            => $db->Record['nb_substitutes'],
      'TeamMatchCount'             => count($defEntries),
      'TeamMatchDefinitionEntries' => $defEntries
    );
    $res[] = $matchSystemEntry;
  }

  // Release database connection
  $db->free();
  unset($db);

  return array('MatchSystemCount'   => count($res),
               'MatchSystemEntries' => $res);
}

/**
 * @brief Register player to existing tournaments
 *
 * @param $Request TournamentRegister
 * @return TournamentRegisterResponse
 * @since Version 0.7.20
 * @version 0.7.20
 */
function TournamentRegister(stdClass $Request) {
  // Check permissions & quota
  $permissions = _MethodAPI(11, isset($Request->Credentials) ? $Request->Credentials : (object)array());

  // Remember the request status
  $success = true;

  // Extract function arguments
  if (isset($Request->TournamentUniqueIndex))       $TournamentId      = trim($Request->TournamentUniqueIndex);
  if (isset($Request->SerieUniqueIndex))            $SerieId           = trim($Request->SerieUniqueIndex);
  if (isset($Request->PlayerUniqueIndex))           $PlayerList        = is_array($Request->PlayerUniqueIndex) ? $Request->PlayerUniqueIndex : array(trim($Request->PlayerUniqueIndex));
  if (isset($Request->Unregister))                  $Unregister        = trim($Request->Unregister);
  if (isset($Request->NotifyPlayer))                $NotifyPlayer      = trim($Request->NotifyPlayer);

  // Create database session
  $db = new DB_Session();
  $db->Halt_On_Error = 'no';

  // Validate access rights
  if (count(array_intersect($permissions, array('tournament', 'admin'))) == 0) {
    throw new SoapFault('47', "You don't have permission to register tournament players.");
  }

  // Validate serie
  // (must be an integer matching an existing serie)
  if (!isset($SerieId)) {
    throw new SoapFault('35', "A tournament serie unique index is required.");
  }
  if (!is_numeric($SerieId)) {
    throw new SoapFault('36', "Tournament serie unique index ({$SerieId}) is not valid, must be numeric.");
  }
  $SerieId = intval($SerieId);
  if ($db->select_one("SELECT COUNT(*) FROM tournamentseries WHERE id={$SerieId}") <= 0) {
    throw new SoapFault('37', "Tournament serie unique index ({$SerieId}) is not valid.");
  }

  // Validate tournament (if given)
  // (must be an integer matching an existing tournament and serie must be part of this tournament)
  if (!isset($TournamentId)) {
    // If not given, we get it from the serie
    $TournamentId = $db->select_one("SELECT tournament_id FROM tournamentseries WHERE id={$SerieId}");
  }
  if (!is_numeric($TournamentId)) {
    throw new SoapFault('39', "Tournament unique index ({$TournamentId}) is not valid, must be numeric.");
  }
  $TournamentId = intval($TournamentId);
  if ($db->select_one("SELECT COUNT(*) FROM tournamentseries WHERE id={$SerieId} AND tournament_id={$TournamentId}") <= 0) {
    throw new SoapFault('40', "Tournament unique index ({$TournamentId}) is not valid.");
  }

  // At least one player must be given
  if (count($PlayerList) == 0) {
    throw new SoapFault('41', "At least one player unique index must be given.");
  }
  if (count($PlayerList) > 2) {
    throw new SoapFault('44', "More than two players does not make sense.");
  }

  // Validate list of players
  // (all given players must an integer matching an existing player)
  $InternalPlayerList = array();
  foreach ($PlayerList as $k => $PlayerId) {
    if (!is_numeric($PlayerId)) {
      throw new SoapFault('42', "Player unique index ({$PlayerId}) is not valid, must be numeric.");
    }
    $PlayerList[$k] = $PlayerId = intval($PlayerId);
    if (($InternalPlayerList[$k] = $PlayerId = $db->select_one("SELECT id FROM playerinfo WHERE vttl_index={$PlayerId}")) <= 0) {
      throw new SoapFault('43', "Player unique index ({$PlayerId}) is not valid.");
    }
  }
  if (count($InternalPlayerList) > 1 && $InternalPlayerList[0] == $InternalPlayerList[1]) {
    throw new SoapFault('45', "Two times the same player does not make sense.");
  }
  if (count($InternalPlayerList) == 1) {
    $InternalPlayerList[1] = null;
  }

  // Register or Unregister?
  // (default is register)
  $Register = true;
  if (isset($Unregister)) {
    $Register = $Unregister != 1;
  }

  // Do send email to player
  // (default is yes)
  if (!isset($NotifyPlayer)) {
    $NotifyPlayer = true;
  }

  // Prepare error message (if any)
  $PlayerStr = count($PlayerList) > 1 ? "Player or team ({$PlayerList[0]}/{$PlayerList[1]})" : "Player ({$PlayerList[0]})";

  // Load tournament helper functions
  include_once($GLOBALS['site_info']['path'].'public/tournament_localization.php');
  include_once($GLOBALS['site_info']['path'].'public/tournament_fct.php');

  // Remember message while registering
  $messages = array();

  // Remember mail messages to be sent
  $mail_notification_info = array();

  // And to whom should the mail be sent to
  $mail_notifications_destinees = array();
  if ($NotifyPlayer) {
    $mail_notifications_destinees = array($InternalPlayerList[0]);
    if (count($PlayerList) > 1) {
      $mail_notifications_destinees[] = $InternalPlayerList[1];
    }
  }

  $already_registered = tournament_is_player_registered($SerieId, $InternalPlayerList[0], $InternalPlayerList[1]);
  if ($Register) {
    // You cannot register twice
    if ($already_registered) {
      throw new SoapFault('46', "{$PlayerStr} is already registered for tournanent ($TournamentId), serie ($SerieId).");
    }
    // Do register!
    $success = tournament_register_player($SerieId, $InternalPlayerList[0], $messages, $InternalPlayerList[1]);
    if ($success) {
      $mail_notification_info[] = array('action' => 'R', 'serie' => $SerieId);
    }
  } else {
    // You cannot unregister if not yet registered
    if (!$already_registered) {
      throw new SoapFault('48', "{$PlayerStr} is not registered for tournanent ($TournamentId), serie ($SerieId).");
    }
    // Do unregister!
    $success = tournament_unregister_player($SerieId, $InternalPlayerList[0], $messages, $InternalPlayerList[1]);
    if ($success) {
      $mail_notification_info[] = array('action' => 'U', 'serie' => $SerieId);
    }
  }

  // Let the people concerned know
  if (count($mail_notifications_destinees) > 0) {
    tournament_mail_notification($mail_notifications_destinees, $mail_notification_info, $messages);
  }

  // Release database connection
  $db->free();
  unset($db);

  // Tell the world we did it
  return array(
    'Success'        => $success,
    'MessageCount'   => count($messages),
    'MessageEntries' => array_map(function ($item) { return is_array($item) ? $item['text'] : $item; }, $messages)
  );
}

  /**
  * @}
  */
?>
