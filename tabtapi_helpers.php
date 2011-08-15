<?php
/** \internal
 ****************************************************************************
 * TabT API
 *  A programming interface to access information managed
 *  by TabT, the table tennis information manager.
 * -----------------------------------------------------------------
 * TabT API helper functions
 * -----------------------------------------------------------------
 * @version 0.7.7
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

function _GetPermissions($Credentials) {
  // Establish a dummy connection to make sure "mysql_real_escape_string" works as expected
  $db = new DB_Session();
  $db->query('SELECT COUNT(*) FROM auth_user');
  $Account  = isset($Credentials->Account) ? mysql_real_escape_string($Credentials->Account) : '';
  $Password = isset($Credentials->Password) ? mysql_real_escape_string($Credentials->Password) : '';
  unset($db);
  if ($Account != '') {
    $q = "SELECT a.perms, s.sid, a.player_id, pc.club_id FROM auth_user as a LEFT JOIN active_sessions s ON s.sid=a.user_id LEFT JOIN playerclub pc ON a.player_id=pc.player_id WHERE a.username='{$Account}' AND a.password=MD5('{$Password}') AND ISNULL(a.conf_id) ORDER BY pc.season DESC; ";
    list($permissions, $session_id, $player_id, $club_id) = select_one_array(utf8_decode($q));
  }

  // If valid account, try to retrieve the preferred language of
  // the connected user
  if (isset($permissions) && is_string($permissions))
  {
    $session_access = new DB_CT_Sql();
    $session_access->ac_start();
    if (preg_match("#\\\$GLOBALS\['lang'\] *= *'([a-zA-Z]+)';#",
                   $session_access->ac_get_value($session_id, 'HurriUser'), $m)) {
      if ($GLOBALS['lang'] != $m[1]) {
        $GLOBALS['lang'] = $m[1];
        include($GLOBALS['site_info']['path'].'public/localization.php');
      }
    }
    unset($session_access);

    // Create dummy "Perm" object for TabT functions that are using it
    if (is_string($permissions)) {
      $GLOBALS['perm'] = new HurriPerm();
      $GLOBALS['auth'] = new Auth();
      $GLOBALS['auth']->auth = array('perm' => $permissions, 'pid' => $player_id, 'club_id' => $club_id);
    }

  }

 return !isset($permissions) || $permissions==-1 ? '' : $permissions;
}
?>
