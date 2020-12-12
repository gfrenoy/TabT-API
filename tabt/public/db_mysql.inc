<?php
// **************************************************************************
// Copyright (C) 2001-2020 Gaëtan Frenoy (gaetan [à] frenoy.net)
// Copyright (C) 1998-2000 NetUSE AG, Boris Erdmann, Kristian Koehntopp
// **************************************************************************
// This file is part of « TabT » (http://tabt.frenoy.net)
// a software to manage a database of a table tennis association.
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details:
//                 http://www.gnu.org/copyleft/gpl.html
// ***************************************************************************

class DB_Sql {
  
  /* public: connection parameters */
  var $Host     = "";
  var $Database = "";
  var $User     = "";
  var $Password = "";

  /* public: configuration parameters */
  var $Debug         = 0;     ## Set to 1 for debugging messages.
  var $Halt_On_Error = "yes"; ## "yes" (halt with message), "no" (ignore errors quietly), "report" (ignore errror, but spit a warning)
  var $Fetch_Style   = PDO::FETCH_BOTH;

  /* public: result array and current row number */
  var $Record   = array();
  var $Row;

  /* public: current error number and error text */
  var $Errno    = 0;
  var $Error    = "";

  /* private: link and query handles */
  var $pdo      = null;
  var $result;

  /* private : track the number of affected rows */
  var $affected_rows_tracking = false;
  var $affected_rows_count    = 0;

  /* public: constructor */
  function __construct($query = '', $fetch_style = PDO::FETCH_ASSOC) {
      $this->query($query);
      $this->Fetch_Style = $fetch_style;
  }

  /* public: connection management */
  function connect($Database = "", $Host = "", $User = "", $Password = "") {
    /* Handle defaults */
    if ("" == $Database)
      $Database = $this->Database;
    if ("" == $Host)
      $Host     = $this->Host;
    if ("" == $User)
      $User     = $this->User;
    if ("" == $Password)
      $Password = $this->Password;
      
    /* establish connection, select database */
    if (!isset($this->pdo)) {
      try {
        $this->pdo = new PDO('mysql:host=' . $Host . ';dbname=' . $Database . ';charset=utf8mb4', $User, $Password, array(
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_EMULATE_PREPARES => false,
          PDO::ATTR_PERSISTENT => true,
          PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
          PDO::MYSQL_ATTR_LOCAL_INFILE => true
        ));
      } catch (Exception $e) {
        $this->halt("Unable to connect the database, please contact the administrator.", $e);
        unset($this->pdo);
      }

      if (!isset($this->pdo) || !$this->pdo) {
        return 0;
      }

      /* makes sure to select the correct mode and use UTF */
      $res = $this->pdo->query("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '')), NAMES 'utf8' COLLATE 'utf8_unicode_ci';");
      $res->fetchAll();
      $res->closeCursor();
    }

    return $this->pdo;
  }

  /* public: discard the query result */
  function free() {
    if (isset($this->result)) {
      $this->result->closeCursor();
      unset($this->result);
    }
  }

  /* public: perform a query */
  function query($query, $input_parameters = null) {
    if ($query == "") {
      // The empty query string is passed on from the constructor,
      // when calling the class without a query, e.g. in situations
      // like these: '$db = new DB_Sql_Subclass;'
      //
      return false;
    }

    // If first parameter is an array with 2 parameters, suppose we passed input parameters as an array (like in "execute_queries")
    if (is_array($query) && isset($query['q']) && isset($query['p']) && is_array($query['p'])) {
      $input_parameters = $query['p'];
      $query = $query['q'];
    }

    if (!$this->connect()) {
      return false; // we already complained in connect() about that.
    };

    // New query, discard previous result.
    if (isset($this->result)) {
      $this->free();
    }

    if (($this->Debug || isset($_GET['debug_query'])) && isset($GLOBALS['perm']) && $GLOBALS['perm']->have_perm('admin')) {
      $stime = utime();
      printf("<pre>Debug: query = %s</pre>\n", $query);
    }

    $this->Errno = 0;
    $this->Error = '';
    try {
      $this->result = $this->pdo->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
      $this->result->execute($input_parameters);
    } catch (PDOException $e) {
      unset($this->result);
      $this->Errno = $e->getCode();
      $this->Error = $e->getMessage();
    }
    $this->Row   = 0;
    if (!isset($this->result) || !$this->result) {
      $this->halt("Invalid SQL: " . $query);
    } elseif (($this->Debug || isset($_GET['debug_query'])) && isset($GLOBALS['perm']) && $GLOBALS['perm']->have_perm('admin')) {
      $etime = utime() - $stime;
      $color = 'black';
      if ($etime > 1) $color = 'red';
      elseif ($etime > 0.3) $color = 'orange';
      printf("<span style=\"color: {$color}\">Debug took %f s<br></span>\n", $etime);
    }

    if ($this->affected_rows_tracking) {
      $this->affected_rows_count += $this->affected_rows();
    }

    # Will return nada if it fails. That's fine.
    return isset($this->result) ? $this->result : false;
  }

  function execute($query) {
    if (!isset($this->pdo)) {
      $this->connect();
    }
    return $this->pdo->exec($query);
  }

  /* public: walk result set */
  function next_record() {
    if (!isset($this->result) || !$this->result) {
      $this->halt("next_record called with no query pending.");
      return 0;
    }

    $this->Errno = 0;
    $this->Error = '';
    try {
      $this->Record = $this->result->fetch($this->Fetch_Style);
      $this->Row   += 1;
    } catch (PDOException $e) {
      $this->Errno = $e->getCode();
      $this->Error = $e->getMessage();
    }

    return is_array($this->Record);
  }

  /* public: position in result set */
  function seek($pos = 0) {
    $this->Errno = 0;
    $this->Error = '';
    try {
      // Re-execute current statement (to go back to first row)
      $this->result->execute();
      unset($this->Record);
      $this->Row = 0;
    } catch (PDOException $e) {
      $this->Errno = $e->getCode();
      $this->Error = $e->getMessage();
    }
    while ((!isset($this->Record) || $this->Record) && $this->Row < $pos) {
      try {
        $this->Record = $this->result->fetch($this->Fetch_Style);
        $this->Row   += 1;
      } catch (PDOException $e) {
        $this->Errno = $e->getCode();
        $this->Error = $e->getMessage();
      }
    }
    if ($this->Error != '') {
      $this->halt("seek($pos) failed: result has ".$this->num_rows()." rows");
      return 0;
    }

    return 1;
  }

  /* public: table locking */
  function lock($table, $mode="write") {
    $query = "lock tables ";
    if (is_array($table)) {
      while (list($key, $value) = each($table)) {
        if ($key == "read" && $key != 0) {
          $query .= "$value read, ";
        } else {
          $query .= "$value $mode, ";
        }
      }
      $query = substr($query, 0, -2);
    } else {
      $query .= "$table $mode";
    }

    $res = $this->execute($query);

    if ($res === false) {
      $this->halt("lock($table, $mode) failed.");
      return 0;
    }
    $this->free();

    return 1;
  }
  
  function unlock() {
    $res = $this->execute("unlock tables");
    if ($res === false) {
      $this->halt("unlock() failed.");
      return 0;
    }
    return 1;
  }


  /* public: evaluate the result (size, width) */
  function affected_rows() {
    return $this->result->rowCount();
  }

  function enable_affected_rows_tracking() {
    $this->affected_rows_tracking = true;
    $this->affected_rows_count    = 0;
  }
  function disable_affected_rows_tracking() {
    $this->affected_rows_tracking = false;
  }
  function get_affected_rows_count() {
    return $this->affected_rows_count;
  }

  function num_rows() {
    return $this->result->rowCount();
  }

  function num_fields() {
    return $this->result->columnCount();
  }

  /* public: shorthand notation */
  function nf() {
    return $this->num_rows();
  }

  function np() {
    print $this->num_rows();
  }

  function f($Name) {
    return isset($this->Record[$Name]) ? $this->Record[$Name] : '';
  }

  function p($Name) {
    print $this->Record[$Name];
  }

  function last_inserted_id() {
    return $this->pdo->lastInsertId();
  }

  /* public: return table metadata */
  function metadata($table='', $full=false) {
    $count = 0;
    $id    = 0;
    $res   = array();

    /*
     * Due to compatibility problems with Table we changed the behavior
     * of metadata();
     * depending on $full, metadata returns the following values:
     *
     * - full is false (default):
     * $result[]:
     *   [0]["table"]  table name
     *   [0]["name"]   field name
     *   [0]["type"]   field type
     *   [0]["len"]    field length
     *   [0]["flags"]  field flags
     *
     * - full is true
     * $result[]:
     *   ["num_fields"] number of metadata records
     *   [0]["table"]  table name
     *   [0]["name"]   field name
     *   [0]["type"]   field type
     *   [0]["len"]    field length
     *   [0]["flags"]  field flags
     *   ["meta"][field name]  index of field named "field name"
     *   The last one is used, if you have a field name, but no index.
     *   Test:  if (isset($result['meta']['myfield'])) { ...
     */

    // if no $table specified, assume that we are working with a query
    // result
    if ($table) {
      $this->connect();
      $this->query("SELECT * FROM {$table} LIMIT 0, 1");
    }

    if (!isset($this->result)) {
      $this->halt('No query specified.');
    }
 
    $count = $this->num_fields();

    // made this IF due to performance (one if is faster than $count if's)
    if (!$full) {
      for ($i=0; $i<$count; $i++) {
        $res[$i]['table'] = $this->result->getColumnMeta($i)['table'];
        $res[$i]['name']  = $this->result->getColumnMeta($i)['name'];
        $res[$i]['type']  = strtolower($this->result->getColumnMeta($i)['native_type']);
        $res[$i]['len']   = $this->result->getColumnMeta($i)['len'];
        $res[$i]['flags'] = $this->result->getColumnMeta($i)['flags'];
      }
    } else { // full
      $res['num_fields']= $count;
    
      for ($i=0; $i<$count; $i++) {
        $res[$i]['table'] = $this->result->getColumnMeta($i)['table'];
        $res[$i]['name']  = $this->result->getColumnMeta($i)['name'];
        $res[$i]['type']  = strtolower($this->result->getColumnMeta($i)['native_type']);
        $res[$i]['len']   = $this->result->getColumnMeta($i)['len'];
        $res[$i]['flags'] = $this->result->getColumnMeta($i)['flags'];
        $res['meta'][$res[$i]['name']] = $i;
      }
    }
    
    // free the result only if we were called on a table
    if ($table) {
      $this->free();
    }

    return $res;
  }

  /* private: error handling */
  function halt($msg, $e = null) {
    if ($e !== null) {
      $this->Error = $e->getMessage();
      $this->Errno = $e->getCode();
    }
    if ($this->Halt_On_Error == "no")
      return;

    $this->haltmsg($msg);

    if ($this->Halt_On_Error != "report")
      die("Session halted.");
  }

  function haltmsg($msg) {
    printf("</td></tr></table><b>Database error:</b> %s<br>\n", $msg);
    printf("<b>MySQL Error</b>: %s (%s)<br>\n", $this->Errno, $this->Error);
  }

  function get_next_autoindex($tbl_name) {
    $index = 0;
    $this->query("SHOW TABLE STATUS LIKE '$tbl_name';");
    $index = $this->next_record() ? $this->Record['Auto_increment'] : 0;
    $this->free();
    return $index;
  }

  function insert($query, $input_parameters = null) {
    $this->query($query, $input_parameters);
    return $this->last_inserted_id();
  }

  function select_one($query, $input_parameters = null) {
    $res = -1;
    $this->query($query, $input_parameters);
    if ($this->next_record()) {
      $res = current($this->Record);
    }
    $this->free();
    return $res;
  }

  function select_one_array($query, $input_parameters = null) {
    $res = -1;
    $was_fetch_style = $this->Fetch_Style;
    $this->Fetch_Style = PDO::FETCH_NUM;
    $this->query($query, $input_parameters);
    if ($this->Errno == 0 && $this->next_record())
    {
      $res = array();
      for ($i=0; $i<$this->num_fields(); $i++)
      {
        $res[$i] = $this->Record[$i];
      }
    }
    $this->Fetch_Style = $was_fetch_style;
    return $res;
  }

  function select_list($q, $fields = null, $input_parameters = null) {
    $res = array();
    $this->query($q, $input_parameters);
    while ($this->Errno == 0 && $this->next_record()) {
      if (is_array($fields)) {
        $f = array();
        foreach ($this->Record as $k => $v) {
          if (in_array($k, $fields, true)) $f[$k] = $v;
        }
        $res[] = $f;
      }
      elseif (!is_null($fields)) {
        if (isset($this->Record[$fields])) $res[] = $this->Record[$fields];
      } else {
        $res[] = $this->Record;
      }
    }
    $this->free();

    return $res;
  }

  function select_count($from, $where = '') {
    $this->query("SELECT COUNT(*) FROM {$from} WHERE 1" . ($where!=''?" AND {$where};":''));
    return $this->next_record() ? current($this->Record) : -1;
  }

  function table_exists($table) {
    $params = array(
      'db'    => $this->Database,
      'table' => $table
    );
    $q = "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema =:db AND table_name=:table";
    return $this->select_one($q, $params) > 0;
  }

  function is_table_locked($table) {
    $params = array(
      'db'    => $this->Database,
      'table' => $table
    );
    $q = "SHOW OPEN TABLES WHERE `Table`=:table AND `Database`=:db AND In_use > 0";
    $this->query($q, $params);
    return $this->num_rows() > 0;
  }

  function escape($value) {
    return DB_Sql::db_escape($value);
  }

  static function db_escape($value) {
    $search  = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
    $replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");
    return str_replace($search, $replace, $value);
  }

  function execute_queries($q_ary, $lock_table = null) {
    if (!is_array($q_ary)) {
      show_error('execute_queries must receive an array');
      return false;
    }
    if (!is_null($lock_table)) {
      $this->execute("LOCK TABLES {$lock_table} WRITE;");
    }

    $ok = true;
    if (!$this->pdo) {
      $this->connect();
    }
    if ($this->pdo->beginTransaction()) {
      try {
        foreach ($q_ary as $q) {
          if ($GLOBALS['debug']) {
            print_query($q);
          }
          if (is_array($q) && isset($q['q']) && isset($q['p']) && is_array($q['p'])) {
            $this->query($q['q'], $q['p']);
          } elseif (is_string($q)) {
            $this->query($q);
          } else {
            show_error('invalid query in execute_queries');
            $ok = false;
          }
        }
        $this->pdo->commit();
      } catch (PDOException $e) {
        $this->pdo->rollBack();
        $ok = false;
      }
    } else {
      show_error('error when creating transaction');
      $ok = false;
    }

    if (!is_null($lock_table)) {
      $this->execute("UNLOCK TABLES;");
    }

    return $ok;
  }
}
?>
