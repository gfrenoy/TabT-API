<?php
/** \internal
 ****************************************************************************
 * TabT API
 *  A programming interface to access information managed
 *  by TabT, the table tennis information manager.
 * -----------------------------------------------------------------
 * TabT API helper functions
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
 * Database abstraction class
 */
class DB_Session {
  var $Record = array();

  var $dbh = null;
  var $dbst = null;

  function __construct($q) {
    $this->dbh = new PDO("mysql:host={$GLOBALS['site_info']['db_hostname']};dbname={$GLOBALS['site_info']['db_name']}", $GLOBALS['site_info']['db_user'], $GLOBALS['site_info']['db_password']);
    if (isset($q)) {
      $this->query($q);
    }
  }
  
  function __destruct() {
    $this->free();
  }

  public function query($q) {
    return $this->dbst = $this->dbh->query($q);
  }

  public function select_one($q) {
    return $this->dbh->query($q)->fetchColumn(0);
  }

  public function select_one_array($q) {
    return $this->dbh->query($q)->fetch(PDO::FETCH_NUM);
  }

  public function next_record() {
    return $this->Record = $this->dbst->fetch(PDO::FETCH_ASSOC);
  }

  public function free() {
    $this->dbh = null;
  }

}

function _GetPermissions($Credentials) {
  // Get database connection
  $db = new DB_Session();

  // Get credentials (if any)
  $Account  = isset($Credentials->Account) ? utf8_decode(addslashes($Credentials->Account)) : '';
  if ($Account != '') {
    $Password = isset($Credentials->Password) ? utf8_decode(addslashes($Credentials->Password)) : '';
    $q = "SELECT a.perms, s.sid, a.player_id, pc.club_id FROM auth_user as a LEFT JOIN active_sessions s ON s.sid=a.user_id LEFT JOIN playerclub pc ON a.player_id=pc.player_id WHERE a.username='{$Account}' AND a.password=MD5('{$Password}') AND ISNULL(a.conf_id) ORDER BY pc.season DESC; ";
    list($permissions, $session_id, $player_id, $club_id) = $db->select_one_array($q);
  }

  // If valid account, try to retrieve the preferred language of
  // the connected user
  if (isset($permissions) && is_string($permissions)) {
    $val = base64_decode($db->select_one("SELECT val FROM active_sessions WHERE name='HurriUser' AND sid='{$session_id}'"));
    if (preg_match("#\\\$GLOBALS\['lang'\] *= *'([a-zA-Z]+)';#", $val, $m)) {
      if ($GLOBALS['lang'] != $m[1]) {
        $GLOBALS['lang'] = $m[1];
        include($GLOBALS['site_info']['path'].'public/localization.php');
      }
    }
    unset($session_access);

    // Create dummy "Perm" object for TabT functions that are using it
    if (is_string($permissions)) {
      $GLOBALS['perm'] = new stdClass();
      $GLOBALS['auth'] = new stdClass();
      $GLOBALS['auth']->auth = array('perm' => $permissions, 'pid' => $player_id, 'club_id' => $club_id);
    }
  }

  unset($db);

  return !isset($permissions) || $permissions==-1 ? '' : $permissions;
}
?>
