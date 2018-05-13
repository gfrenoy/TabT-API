<?php
define('DB_HOSTNAME', $GLOBALS['site_info']['db_hostname']);
define('DB_NAME',     $GLOBALS['site_info']['db_name']);
define('DB_USER',     $GLOBALS['site_info']['db_user']);
define('DB_PASSWORD', $GLOBALS['site_info']['db_password']);
define('ADMIN_MAIL',  isset($GLOBALS['site_info']['technical_admin_mail']) ? $GLOBALS['site_info']['technical_admin_mail'] : $GLOBALS['site_info']['admin_mail']);

class DB_Session extends DB_Sql {
  var $Host     = DB_HOSTNAME;
  var $Database = DB_NAME;
  var $User     = DB_USER;
  var $Password = DB_PASSWORD;

  var $admin_mail_address = ADMIN_MAIL;

  // Data file with last sent mail
  var $lastmail_filename = "../data/lastmail.dat";

  // Display an error message when a database error occurs
  function haltmsg($msg)
  {
    if (isset($GLOBALS['console_mode']) && $GLOBALS['console_mode'])
    {
      print("{$this->Error} ($this->Errno)\n");
      print("{$msg}\n");
    }
    elseif ((isset($GLOBALS['site_info']['force_show_db_error']) && $GLOBALS['site_info']['force_show_db_error']) || ($GLOBALS['perm'] && $GLOBALS['perm']->have_perm('admin')))
    {
      print("</td></tr></table>");
      if (!isset($GLOBALS['site_info']['show_db_error']) || $GLOBALS['site_info']['show_db_error'])
      {
        print("<div class=\"ErrorMsg\">".(isset($GLOBALS['str_DatabaseError'])?$GLOBALS['str_DatabaseError']:'Database Error')."<br><br>");
        print("<div style=\"background-color: #EEEEEE; margin-left: 10%; margin-bottom: 10px; width: 80%; border: 1px dotted black;\">");
        if ($this->Errno>0)
        {
          print("{$this->Error} ($this->Errno)<br><br>");
        }
        print("<pre style=\"text-align: left; width: 1024px; overflow-wrap: break-word;\">" . htmlentities($msg) . "</pre>");
        print("</div></div>");
        print("<div>Backtrace:</div>");
        print("<pre style=\"text-align: left;\">");
        debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        print("</pre>");
      }
    }

    $do_send_mail = isset($this->admin_mail_address) && $this->admin_mail_address != '';

    if ($do_send_mail && file_exists($this->lastmail_filename)) {
      // Check that we did not send the same email recently (1 hour)...
      $last_msg = file_get_contents($this->lastmail_filename);
      if ($msg == $last_msg && filemtime($this->lastmail_filename) > time() - 60*60) {
        $do_send_mail = false;
      }
    }

    if ($do_send_mail) {
        $headers = array();
        $headers[] = "From: {$GLOBALS['site_info']['notifier_from']}";

        $str = '';
        if (isset($GLOBALS['auth'])) $str .= "User   = [" . (isset($GLOBALS['auth']->auth['uname']) ? $GLOBALS['auth']->auth['uname'] : '???') . "]\n";
        if (isset($_SERVER['HTTP_REFERER'])) $str .= "Referer= [{$_SERVER['HTTP_REFERER']}]\n\n";
        if (isset($_SERVER['REMOTE_ADDR'])) {
          $str .= "IP     = [{$_SERVER['REMOTE_ADDR']}]\n";
        }
        if (isset($_SERVER['GEOIP_COUNTRY_CODE'])) $str .= "GeoIP  = [{$_SERVER['GEOIP_COUNTRY_CODE']}]-[{$_SERVER['GEOIP_COUNTRY_NAME']}]\n\n";
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
          $str .= "Agent  = [{$_SERVER['HTTP_USER_AGENT']}]\n\n";
        }
        if (isset($_SERVER['REMOTE_HOST'])) $str .= "Host   = [{$_SERVER['REMOTE_HOST']}]\n\n";
        if (isset($_SERVER['REQUEST_URI'])) {
          $str .= "Uri    = [{$_SERVER['REQUEST_URI']}]\n\n";
        }
        $str .= "{$this->Error} ($this->Errno)\n\n";
        $str .= $msg . "\n";

        @mail($this->admin_mail_address,
              'Database error',
              $str,
              implode("\r\n", $headers));

        // Save last sent message
        file_put_contents($this->lastmail_filename, $msg);
    }

  }
}

class DB_CT_Sql extends CT_Sql {
  var $database_class = 'DB_Session';          ## Which database to connect...
  var $database_table = 'active_sessions'; ## and find our session data in this table.
}

class MySession extends Session {
  var $classname = 'MySession';

  var $cookiename     = '';                ## defaults to classname
  var $magic          = 'isntthismagic';      ## ID seed
  var $mode           = 'cookie';          ## We propagate session IDs with cookies
  var $lifetime       = 0;                 ## 0 = do session cookies, else minutes
  var $that_class     = 'DB_CT_Sql';       ## name of data storage container
  var $gc_probability = 5;  


  function full_url() {
    $url  = $_SERVER['PHP_SELF'].'?'.$this->full_query();
    return urlencode($url);
  }

  function full_query() {
    global $menu, $season;

    $url = '';
    $q = get_query_string_but(array('season', 'menu', 'conf_id', 'register'));
    if ($q != '') $url .= $q.'&';
    $url .= "menu={$menu}&season={$season}";

    return urlencode($url);
  }
}

class HurriUser extends User {
  var $classname = 'HurriUser';

  var $magic          = 'Remember to dare, always';     ## ID seed
  var $that_class     = 'DB_CT_Sql'; ## data storage container
}

class HurriAuth extends Auth {
  var $classname      = 'HurriAuth';

  var $lifetime       =  120; // 2 hours

  var $database_class = 'DB_Session';
  var $database_table = 'auth_user';

  var $magic          = 'Who is magic?';
  
  var $nobody_auth = array(
    'pid'          => 0,
    'player_name'  => '',
    'player_index' => 0
  );

  var $auth = array(
    'pid'          => 0
  );

  function auth_loginform() {
    global $sess;
    global $_PHPLIB;

    include('loginform.php');
  }

  function auth_registerform() {
    global $sess;
    global $_PHPLIB;

    $GLOBALS['lostpassword'] = 0;
    include('registerform.php');
    unset($GLOBALS['lostpassword']);
  }

  function auth_preauth() {
    if ($GLOBALS['site_info']['sso'] || (isset($_COOKIE['username']) && isset($_COOKIE['password']))) {
      return $this->auth_validatelogin();
    }
    return false;
  }

  function auth_validatelogin() {
    if (!isset($GLOBALS['season']) || $GLOBALS['season'] == '') {
      $this->db->query("SELECT MAX(id) as season FROM seasoninfo;");
      if ($this->db->next_record()) {
        $GLOBALS['season'] = $this->db->Record['season'];
      }
    }

    $where_clause = '0';
    if (!$GLOBALS['site_info']['sso'] && ((isset($_POST['username']) || isset($_COOKIE['username'])) && (isset($_POST['password']) || isset($_COOKIE['password'])))) {
      # Get user data from form (legacy login)
      $username     = isset($_POST['username']) ? $_POST['username'] : (isset($_COOKIE['username']) ? $_COOKIE['username'] : '');
      $password     = isset($_POST['password']) ? $_POST['password'] : (isset($_COOKIE['password']) ? $_COOKIE['password'] : '');
      $save_cred    = isset($_POST['save_cred']) ? $_POST['save_cred'] : '';
      $where_clause = sprintf("auth.username='%s' AND auth.password = md5('%s')", addslashes($username), addslashes($password));
      $this->auth['uname'] = stripslashes($username); ## This provides access for "loginform.ihtml"
    } else if ($GLOBALS['site_info']['sso'] && isset($GLOBALS['site_info']['sso_id'])) {
      # New SSO system
      $where_clause = sprintf('pi.vttl_index=%d', $GLOBALS['site_info']['sso_id']);
      $save_cred = false;
    }
    // Because of the sprintf below!
    $where_clause = str_replace('%', '%%', $where_clause);

    $uid = false;
    
    $q = <<<EOQ
SELECT
  auth.user_id as user_id,
  auth.perms as perms, 
  auth.player_id as player_id,
  pc.club_id as club_id,
  c.category as club_category,
  CONCAT(REPLACE(TRIM(REPLACE(ca.address, ',', ' ')), '  ', ' '),',',ca.zip,',BE') as club_address,
  CONCAT(pi.first_name, ' ', pi.last_name) as player_name,
  pi.vttl_index as player_index,
  auth.username as username
FROM
  %s as auth
LEFT JOIN
  playerclub as pc ON pc.season={$GLOBALS['season']} AND pc.player_id=auth.player_id
LEFT JOIN
  clubs as c ON c.id=pc.club_id
LEFT JOIN
  clubaddressinfo as ca ON ca.club_id=c.id AND ca.address_id=1
LEFT JOIN
  playerinfo AS pi ON pi.id=auth.player_id
WHERE 1
  AND {$where_clause}
  AND ISNULL(auth.conf_id)
;
EOQ;

    $query = sprintf($q, $this->database_table);
    $this->db->query($query);

    if ($this->db->next_record()) {
      $uid = $this->db->f('user_id');
      $this->auth['pid']          = $this->db->f('player_id');
      $this->auth['club_id']      = $this->db->f('club_id') ? $this->db->f('club_id') : 1;
      $this->auth['province']     = $this->db->f('club_category') ? $this->db->f('club_category') : 1;
      $this->auth['perm']         = $this->db->f('perms');
      $this->auth['club_address'] = $this->db->f('club_address');
      $this->auth['player_name']  = $this->db->f('player_name');
      $this->auth['player_index'] = $this->db->f('player_index');
      $this->auth['uname']        = $this->db->f('username');
      $this->auth['was_sso']      = $GLOBALS['site_info']['sso'];

      if ($save_cred) {
        // Validity for 365 days
        $expire = time()+60*60*24*365;
        setcookie('username', $username, $expire);
        setcookie('password', $password, $expire);
      }
    }

    return $uid;
  }

  // Makes sure we are fully unauthenticated
  function unauth($nobody = false) {
    $this->auth = $this->nobody_auth;

    parent::unauth($nobody);
  }

}

class HurriDefaultAuth extends HurriAuth {
  var $classname = 'HurriDefaultAuth';
  
  var $nobody    = true;
}

class HurriPerm extends Perm {
  var $classname = 'HurriPerm';
  
  var $permissions = array(
                            'user'       => 1,
                            'club'       => 2,
                            'province'   => 4,
                            'admin'      => 8,
                            'supervisor' => 16,
                            'tournament' => 32,
                            'classement' => 64,
                            'export'     => 128
                          );

  function perm_invalid($does_have, $must_have) {
    global $perm, $auth, $sess;
    global $_PHPLIB;
    
    include($_PHPLIB['libdir'] . 'perminvalid.ihtml');
  }
}
?>
