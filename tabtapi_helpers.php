<?php
/**
 * TabT API
 *
 * A programming interface to access information managed
 * by TabT, the table tennis information manager.
 *
 * @author Gaetan Frenoy <gaetan@frenoy.net>
 * @version 0.7.22
 *
 * Copyright (C) 2007-2018 Gaëtan Frenoy (gaetan@frenoy.net)
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

function _GetPermissions($Credentials) {
  // Establish a dummy connection to make sure "mysql_real_escape_string" works as expected
  $db = new DB_Session();
  $db->query('SELECT COUNT(*) FROM auth_user');
  $Account    = isset($Credentials->Account) ? mysql_real_escape_string($Credentials->Account) : '';
  $Password   = isset($Credentials->Password) ? mysql_real_escape_string($Credentials->Password) : '';
  $OnBehalfOf = isset($Credentials->OnBehalfOf) && is_numeric($Credentials->OnBehalfOf)? intval($Credentials->OnBehalfOf) : 0;

  if ($Account != '') {
    $q = <<<EOQ
SELECT
  a.user_id,
  a.perms,
  s.sid,
  a.player_id,
  pc.club_id,
  c.category as club_category,
  cc_reg.id as region_category,
  cc_reg.main_level as region_level,
  pi.vttl_index as unique_index
FROM
  auth_user as a
  LEFT JOIN active_sessions s ON s.sid=a.user_id
  LEFT JOIN playerclub pc ON a.player_id=pc.player_id
  LEFT JOIN clubs as c ON c.id=pc.club_id
  LEFT JOIN playerinfo as pi ON a.player_id=pi.id
  LEFT JOIN clubcategories as cc_reg ON CONCAT(',', cc_reg.group, ',') LIKE CONCAT('%%,', c.category, ',%%') AND NOT ISNULL(cc_reg.main_level)
WHERE 1
  AND a.username='{$Account}'
  AND a.password=MD5('{$Password}')
  AND ISNULL(a.conf_id)
  AND (ISNULL(a.restrict_to_ip) OR a.restrict_to_ip={$GLOBALS['api_caller_ip']})
ORDER BY pc.season DESC;
EOQ;
    list($user_id, $permissions, $session_id, $player_id, $club_id, $club_category, $region, $region_level, $unique_index) = $db->select_one_array(utf8_decode($q));
  }

  // Create dummy "Perm" & "Auth" objects for TabT functions that are using it
  $GLOBALS['perm'] = new HurriPerm();
  $GLOBALS['auth'] = new Auth();
  $GLOBALS['auth']->auth = array(
    'pid' => 0
  );

  // If valid account, try to retrieve the preferred language of
  // the connected user
  if (isset($permissions) && is_string($permissions)) {
    $session_access = new DB_CT_Sql();
    $session_access->ac_start();
    if (preg_match("#\\\$GLOBALS\['lang'\] *= *'([a-zA-Z]+)';#",
                   $session_access->ac_get_value($session_id, 'HurriUser'), $m)) {
      if ($GLOBALS['lang'] != $m[1]) {
        $GLOBALS['lang'] = $m[1];
        include($GLOBALS['site_info']['path'].'public/localization.php');
        dict_compute();
      }
    }
    unset($session_access);

    // If some permissions are allowed, update "Auth" object accordingly
    if (is_string($permissions)) {
      if ($region && is_numeric($region)) {
        $levels   = select_list("SELECT main_level FROM clubcategories cc WHERE (SELECT CONCAT(',', `group`, ',') FROM clubcategories WHERE id={$region}) LIKE CONCAT('%,', cc.id, ',%')", 'main_level');
        $levels[] = $region_level;
      } else {
        $levels = array();
        $region = 0;
        $club_id = 0;
      }

      // Populate the authorization object with information about the connected user
      $GLOBALS['auth']->auth = array(
        'uid' => $user_id,
        'perm' => $permissions,
        'pid' => $player_id,
        'club_id' => $club_id,
        'club_category' => $club_category,
        'region' => $region,
        'region_levels' => $levels,
        'unique_index' => $unique_index
      );

      // Did the user ask to act on behalf of another user ?
      if ($OnBehalfOf > 0) {
        $season = $db->select_one("SELECT max(id) FROM seasoninfo");
        $q = <<<EOQ
SELECT
  IFNULL(a.user_id, ''),
  IFNULL(a.perms, 'user'),
  pi.id,
  pc.club_id,
  c.category,
  cc_reg.id as region_category,
  cc_reg.main_level as region_level,
  pi.vttl_index as unique_index
FROM
  playerinfo as pi
  LEFT JOIN auth_user as a ON a.player_id=pi.id
  LEFT JOIN playerclub pc ON pc.season={$season} AND pc.player_id=pi.id
  LEFT JOIN clubs as c ON c.id=pc.club_id
  LEFT JOIN clubcategories as cc_reg ON CONCAT(',', cc_reg.group, ',') LIKE CONCAT('%%,', c.category, ',%%') AND NOT ISNULL(cc_reg.main_level)
WHERE 1
  AND pi.vttl_index={$OnBehalfOf}
ORDER BY pc.season DESC;
EOQ;

        list($requested_user_id, $requested_permissions, $requested_player_id, $requested_club_id, $requested_club_category, $requested_region_category, $requested_region_level, $requested_unique_index) = $db->select_one_array($q);
        if ($requested_player_id > 0) {
          $permissions_array = explode(',', $permissions);
          if (count(array_intersect($permissions_array, array('region', 'admin'))) == 0) {
            $GLOBALS['permission_error'] = array(
              'code'    => '53',
              'message' => "You don't have enough permission to act on behalf of another user."
            );
            unset($permissions);
          } elseif (count(array_intersect($permissions_array, array('region'))) == 1 && count(array_intersect($requested_permissions, array('admin'))) > 0) {
            $GLOBALS['permission_error'] = array(
              'code'    => '54',
              'message' => "You don't have enough permission to act on behalf of a admin user."
            );
            unset($permissions);
          } elseif (count(array_intersect($permissions_array, array('region'))) == 1 && count(array_intersect($permissions_array, array('admin'))) == 0 && !in_array($requested_club_category, get_club_category_array($region))) {
            $GLOBALS['permission_error'] = array(
              'code'    => '55',
              'message' => "You don't have enough permission to act on behalf of a user from another region."
            );
            unset($permissions);
          } else {
            $requested_region_levels = array();
            if ($requested_region_category && is_numeric($requested_region_category)) {
              $requested_region_levels   = select_list("SELECT main_level FROM clubcategories cc WHERE (SELECT CONCAT(',', `group`, ',') FROM clubcategories WHERE id={$requested_region_category}) LIKE CONCAT('%,', cc.id, ',%')", 'main_level');
              $requested_region_levels[] = $requested_region_level;
            }

            // Override user permissions
            if ($requested_user_id != '') {
              $GLOBALS['auth']->auth['uid'] = $requested_user_id;
            } else {
              // The requested player does not have an account
              // This likely will cause some issues but better to remove than using the one of the admin user
              // Should we automatically create a new account ?  Or should we have a "temporary" account ?
              unset($GLOBALS['auth']->auth['uid']);
            }
            $GLOBALS['auth']->auth['pid']           = $requested_player_id;
            $GLOBALS['auth']->auth['perm']          = $permissions = $requested_permissions;
            $GLOBALS['auth']->auth['club_id']       = $requested_club_id;
            $GLOBALS['auth']->auth['club_category'] = $requested_club_category;
            $GLOBALS['auth']->auth['region']        = $requested_region_category;
            $GLOBALS['auth']->auth['region_levels'] = $requested_region_levels;
            $GLOBALS['auth']->auth['unique_index']  = $requested_unique_index;
          }
        } else {
          $GLOBALS['permission_error'] = array(
            'code'    => '52',
            'message' => "Invalid unique index."
          );
          unset($permissions);
        }
      }

    } else {
      unset($permissions);
    }
  }

  unset($db);

  return !isset($permissions) || $permissions==-1 ? array() : explode(',', $permissions);
}

/** 
 * Function to call when API call is started.  It will record start time
 *  @param 
 *  @return nothing
 */
function _BeginAPI() {
  $GLOBALS['api_starttime'] = microtime(true);

  ///
  /// Get caller IP
  ///
  // Test if it is a shared client
  if (!empty($_SERVER['HTTP_CLIENT_IP'])){
    $ip = $_SERVER['HTTP_CLIENT_IP'];
  // Is it a proxy address
  } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
  } else {
    $ip = $_SERVER['REMOTE_ADDR'];
  }
  $GLOBALS['api_caller_ip'] = ip2long($ip);
  // Without further identification, use IP as user ID
  $GLOBALS['api_caller'] = $GLOBALS['api_caller_ip'];

  // If IP not correctly recognized, do not process further
  if (!($GLOBALS['api_caller_ip'] > 0)) {
    throw new SoapFault('49', "No IP address found, we cannot process your request further.");
  }

  // Default function (should be overriden by a called to _MethodAPI)
  $GLOBALS['api_function'] = 0;
}

/** 
 * Must be called by each API to register user and other stats
 */
function _MethodAPI($FunctionCode, $Credentials) {
  $GLOBALS['api_function'] = $FunctionCode;

  $permissions = _GetPermissions($Credentials);

  if (count($permissions) > 0) {
    $GLOBALS['api_caller'] = $GLOBALS['auth']->auth['pid'];
  }

  // Prepare database session
  $db = new DB_Session();

  // Retrieve current quota
  list($GLOBALS['api_consumed'], $GLOBALS['api_quota'], $GLOBALS['api_remaining_quota']) = _GetQuota();

  // Check quota
  // (more quota for identified users)
  $player_quota = $db->select_one("SELECT quota FROM apiquota WHERE player_id={$GLOBALS['api_caller']}");

  // House keeping
  unset($db);

  // Check quota
  $GLOBALS['api_quota_limit'] = count($permissions) ? ($player_quota > 0 ? $player_quota : 30000) : 8000;
  if ($GLOBALS['api_remaining_quota'] > $GLOBALS['api_quota_limit']) {
    throw new SoapFault('34', "Quota exceeded [" . round($GLOBALS['api_remaining_quota']) . " > {$GLOBALS['api_quota_limit']}], try again later or contact us to increase your quota.");
  }

  // Check error during permission processing
  if (isset($GLOBALS['permission_error'])) {
    throw new SoapFault($GLOBALS['permission_error']['code'], $GLOBALS['permission_error']['message']);
  }

  return $permissions;
}

/** 
 * Function to call when API call is finished.  It will record stop time and add a record to api stats file
 *  @param 
 *  @return nothing
 */
function _EndAPI() {
  // Prepare database session
  $db = new DB_Session();

  // Record usage
  // DevNote: we should track the real CPU usage instead of elapsed time
  $time = 1 + abs(round(1000 * (microtime(true) - $GLOBALS['api_starttime'])));
  $db->select_one("INSERT INTO apiuse (ip, function, time, called) VALUES ({$GLOBALS['api_caller']}, {$GLOBALS['api_function']}, {$time}, {$GLOBALS['api_starttime']});");
  
  if ($GLOBALS['api_consumed'] == 0) {
    if ($GLOBALS['api_function'] == 0) {
      // Strangely enough, no function was called ; let's still check quota
      list($GLOBALS['api_consumed'], $GLOBALS['api_quota'], $GLOBALS['api_remaining_quota']) = _GetQuota();
    }
  }

  $db->select_one("LOCK TABLES apicurrentquota WRITE");
  if ($GLOBALS['api_consumed'] == 0 && $db->select_one("SELECT COUNT(*) FROM apicurrentquota WHERE id={$GLOBALS['api_caller']}") == 0) {
    // Very first call
    $db->select_one("INSERT INTO apicurrentquota (id, lastused, consumed, quota) VALUES ({$GLOBALS['api_caller']}, {$GLOBALS['api_starttime']}, {$time}, {$time});");
  } else {
    // Returning user
    $GLOBALS['api_consumed'] += $time;
    $GLOBALS['api_quota']     = $GLOBALS['api_remaining_quota'] + $time;
    $db->select_one("UPDATE apicurrentquota SET lastused={$GLOBALS['api_starttime']}, consumed={$GLOBALS['api_consumed']}, quota={$GLOBALS['api_quota']} WHERE id={$GLOBALS['api_caller']}");
  }
  $db->select_one("UNLOCK TABLES");

  unset($db);

  if ($GLOBALS['api_function'] == 0) {
    // This cannot happen, let it fail before giving any other information
    throw new SoapFault('50', "No API consumed.");
  }
}

/** 
 * Returns the currently consumed quota for current caller
 *  @return int consumed quota
 */
function _GetQuota() {
  $db = new DB_Session();
  $a = $db->select_one_array("SELECT consumed, quota, GREATEST(0, quota - 200*({$GLOBALS['api_starttime']}-lastused)) FROM apicurrentquota WHERE id={$GLOBALS['api_caller']}");
  if (is_null($a)) {
    $a = array(0, 0, 0);
  }
  unset($db);
  return $a;
}

/** 
 * Checks if the provided phone number is formally valid
 *  @param string $str phone number to validate
 *  @return true formatted valid phone number if validation succeeded, false otherwise
 */
function _GetPhone($str) {
  $str = str_replace(array(' ', '.', '-', '/', '+'), '', $str);
  // Reject if not only containing numbers (and not empty)
  if (!preg_match('/^[0-9]+$/', $str)) return FALSE;
  // Consider numbers starting with only one zero as Belgian numbers
  if (preg_match('/^0([1-9][0-9]+)/', $str, $matches)) $str = '32'.$matches[1];
  // Remove numbers starting with two zeros
  if (preg_match('/^00([0-9]+)/', $str, $matches)) $str = $matches[1];
  // Reject if longer than 20 characters
  if (strlen($str)>20) return FALSE;
  // Reject if smaller than 9 characters
  if (strlen($str)<9) return FALSE;

  return $str;
}

/**
 * Return the name of a division
 *
 *  @param string $Show yes = display the full name of the division, short = division a short version of the division name, no = no name
 *  @param int $Season the season consider
 *  @param string $DivisionId the divisionid to consider
 *  @return string the name of the division (or empty if $Show is no) 
 */
function _GetDivisionName($Show, $Season, $DivisionId) {
  $divisionname = '';

  switch ($Show) {
    case 'yes':
      $divisionname = create_division_title_text(get_division_info($Season, $DivisionId), true);
      break;
    case 'short':
      $divinfo = get_division_info($Season, $DivisionId);
      $divisionname  = $divinfo['div_id']>0 ? $divinfo['div_id'] : '';
      $divisionname .= ($divinfo['div_id']>0||$divinfo['serie']==''?'':$GLOBALS['str_Serie'].' ') . $divinfo['serie'];
      $divisionname .= strlen($divinfo['extra_name']) ? ($divisionname==''?'':" ") . $divinfo['extra_name'] : '';
      break;
    default:
    case 'no':
      break;
  }
  return $divisionname;
}

/**
 * Return the name of a division
 *
 *  @param int $Season the season consider
 *  @param string $Club the club indice we're looking for
 *  @return array ID and name of the club, -1 otherwise
 */
function _GetClubInfo($Season, $Club) {
  $db = new DB_Session();

  // Clean up user input
  $Club = str_replace(array('-','/',' ', '\'', '"'), '', strtoupper($Club));

  // Query database
  $q = "SELECT id, name FROM clubs AS c WHERE REPLACE(REPLACE(REPLACE(UCASE(c.indice), ' ', ''), '/', ''), '-', '')='{$Club}' AND (ISNULL(c.first_season) OR c.first_season<={$Season}) AND (ISNULL(c.last_season) OR c.last_season>={$Season})";
  $response = $db->select_one_array($q);

  // Housekeeping
  $db->free();
  unset($db);

  return $response;
}

?>
