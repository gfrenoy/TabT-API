<?php

##
## Copyright (c) 1998-2000 NetUSE AG
##                    Boris Erdmann, Kristian Koehntopp
##
## Copyright (c) 1998-2000 Sascha Schumann <sascha@schumann.cx>
## 
## $Id: ct_sql.inc,v 1.2 2008/04/01 13:08:34 gaff Exp $
##
## PHPLIB Data Storage Container using a SQL database
##

// DevNote: temporary solution to avoid PHP warning with PHP 7
if (!defined('MYSQL_BOTH')) {
  define('MYSQL_ASSOC', PDO::FETCH_ASSOC);
  define('MYSQL_BOTH', PDO::FETCH_BOTH);
  define('MYSQL_NUM', PDO::FETCH_NUM);
}

class CT_Sql {
  ##
  ## Define these parameters by overwriting or by
  ## deriving your own class from it (recommened)
  ##
    
  var $database_table = "active_sessions";
  var $database_class = "DB_Sql";
  var $database_lock_semaphore = "";

  var $encoding_mode = "base64";

  ## end of configuration

  var $db;

  function ac_start() {
    $name = $this->database_class;
    $this->db = new $name;
    $this->db->Result_Type = MYSQL_BOTH;
  }

  function ac_get_lock() {
    if ( "" != $this->database_lock_semaphore ) {
      $query = sprintf("SELECT get_lock('%s')", $this->database_lock_semaphore);
      while ( ! $this->db->query($query)) {
        $t = 1 + time(); while ( $t > time() ) { ; }
      }
    }
  }

  function ac_release_lock() {
    if ( "" != $this->database_lock_semaphore ) {
      $query = sprintf("SELECT release_lock('%s')", $this->database_lock_semaphore);
      $this->db->query($query);
    }
  }

  function ac_gc($gc_time, $name) {
    $timeout = time();
    $sqldate = date("YmdHis", $timeout - ($gc_time * 60));
    $this->db->query(sprintf("DELETE FROM %s WHERE changed < '%s' AND name = '%s'",
                    $this->database_table, 
                    $sqldate,
                    addslashes($name)));
    }

  function ac_store($id, $name, $str) {
    $ret = true;

    switch ( $this->encoding_mode ) {
      case "slashes":
        $str = addslashes($name . ":" . $str);
      break;

      case "base64":
      default:
        $str = base64_encode($name . ":" . $str);
    };

    $name = addslashes($name);

    ## update duration of visit
    global $HTTP_REFERER, $HTTP_USER_AGENT, $REMOTE_ADDR;

    $now = date("YmdHis", time());
    $uquery = sprintf("update %s set val='%s', changed='%s' where sid='%s' and name='%s'",
      $this->database_table,
      $str,
      $now,
      $id,
      $name);
    $squery = sprintf("select count(*) from %s where val='%s' and changed='%s' and sid='%s' and name='%s'",
      $this->database_table,
      $str,
      $now,
      $id,
      $name);
    $iquery = sprintf("insert into %s ( sid, name, val, changed ) values ('%s', '%s', '%s', '%s')",
      $this->database_table,
      $id,
      $name,
      $str,
      $now);

    # Do not stop process if an error occurs during query processing
    $was_Halt_On_Error = $this->db->Halt_On_Error;
    $this->db->Halt_On_Error = false;

    # Lock the table (read/write) to avoid two requests at the same time
    $this->db->execute("LOCK TABLES {$this->database_table} WRITE");
    
    try
    {
      # FIRST test to see if any rows were affected.
      #   Zero rows affected could mean either there were no matching rows
      #   whatsoever, OR that the update statement did match a row but made
      #   no changes to the table data (i.e. UPDATE tbl SET col = 'x', when
      #   "col" is _already_ set to 'x') so then,
      $affected_rows = $this->db->execute($uquery);

      # SECOND, query(SELECT...) on the sid to determine if the row is in
      #   fact there,
      # THIRD, verify that there is at least one row present, and if there
      #   is not, then
      # FOURTH, insert the row as we've determined that it does not exist.
      if ( $affected_rows == 0
          && $this->db->query($squery) && $this->db->next_record() && $this->db->f(0) == 0
          && !$this->db->query($iquery)) {

          $ret = false;
      }
    }
    catch (Exception $e)
    {
    }
    $this->db->execute("UNLOCK TABLES");

    $this->db->Halt_On_Error = $was_Halt_On_Error;
    return $ret;
  }

  function ac_delete($id, $name) {
    $this->db->query(sprintf("delete from %s where name = '%s' and sid = '%s'",
      $this->database_table,
      addslashes($name),
      $id));
  }

  function ac_get_value($id, $name) {
    $this->db->query(sprintf("select val from %s where sid  = '%s' and name = '%s'",
      $this->database_table,
      $this->db->escape($id),
      $this->db->escape($name)));
    if ($this->db->next_record()) {
      $str  = $this->db->f("val");
      $str2 = base64_decode( $str );

      if ( preg_match("/^".$name.":.*/", $str2) ) {
         $str = preg_replace("/^".$name.":/", "", $str2 );
      } else {

        $str3 = stripslashes( $str );

        if ( preg_match("/^".$name.":.*/", $str3) ) {
          $str = preg_replace("/^".$name.":/", "", $str3 );
        } else {

          switch ( $this->encoding_mode ) {
            case "slashes":
              $str = stripslashes($str);
            break;

            case "base64":
            default:
              $str = base64_decode($str);
          }
        }
      };
      return $str;
    };
    return "";
  }

  function ac_newid($str, $name) {
    return $str;
  }

  function ac_halt($s) {
    $this->db->halt($s);
  }
}
?>
