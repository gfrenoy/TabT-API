<?php
/*
 * Session Management for PHP3
 *
 * Copyright (c) 1998-2000 NetUSE AG
 *                    Boris Erdmann, Kristian Koehntopp
 *
 * $Id: db_mysql.inc,v 1.8 2009/08/16 12:09:08 gaff Exp $
 *
 */ 

class DB_Sql {
  
  /* public: connection parameters */
  var $Host     = "";
  var $Database = "";
  var $User     = "";
  var $Password = "";

  /* public: configuration parameters */
  var $Auto_Free     = 0;     ## Set to 1 for automatic mysql_free_result()
  var $Debug         = 0;     ## Set to 1 for debugging messages.
  var $Halt_On_Error = "yes"; ## "yes" (halt with message), "no" (ignore errors quietly), "report" (ignore errror, but spit a warning)
  var $Seq_Table     = "db_sequence";
  var $Result_Type   = MYSQL_BOTH;

  /* public: result array and current row number */
  var $Record   = array();
  var $Row;

  /* public: current error number and error text */
  var $Errno    = 0;
  var $Error    = "";

  /* public: this is an api revision, not a CVS revision. */
  var $type     = "mysql";
  var $revision = "1.2";

  /* private: link and query handles */
  var $Link_ID  = 0;
  var $Query_ID = 0;

  /* public: constructor */
  function __construct($query = "", $result_type = MYSQL_ASSOC) {
      $this->query($query);
      $this->Result_Type = $result_type;
  }

  /* public: some trivial reporting */
  function link_id() {
    return $this->Link_ID;
  }

  function query_id() {
    return $this->Query_ID;
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
    if ( 0 == $this->Link_ID ) {

      try {
        $this->pdo = new PDO('mysql:host=' . $Host . ';dbname=' . $Database . ';charset=utf8mb4', $User, $Password);
      } catch (Exception $e) {
        $this->halt("Unable to connect the database, please contact the administrator.", $e);
        unset($this->pdo);
      }

      if ($this->pdo) {
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
      } else {
        return 0;
      }
    }

    /* makes sure to select the correct mode */
    $this->pdo->query("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
    /* makes sure to use UTF */
    $this->pdo->query("SET NAMES 'utf8';");

    return $this->pdo;
  }

  /* public: discard the query result */
  function free() {
    unset($this->result);
  }

  /* public: perform a query */
  function query($Query_String) {
    /* No empty queries, please, since PHP4 chokes on them. */
    if ($Query_String == "")
      /* The empty query string is passed on from the constructor,
       * when calling the class without a query, e.g. in situations
       * like these: '$db = new DB_Sql_Subclass;'
       */
      return 0;

    if (!$this->connect()) {
      return 0; /* we already complained in connect() about that. */
    };

    # New query, discard previous result.
    if ($this->Query_ID) {
      $this->free();
    }

    if (($this->Debug || isset($_GET['debug_query'])) && isset($GLOBALS['perm']) && $GLOBALS['perm']->have_perm('admin'))
    {
      $stime = utime();
      printf("<pre>Debug: query = %s</pre>\n", $Query_String);
    }

    $this->Errno = 0;
    $this->Error = '';
    try {
      $this->result = $this->pdo->prepare($Query_String, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
      $this->result->execute();
    } catch (PDOException $e) {
      $this->result = false;
      $this->Errno = $e->getCode();
      $this->Error = $e->getMessage();
    }
    $this->Row   = 0;
    if (!$this->result) {
      $this->halt("Invalid SQL: ".$Query_String);
    }
    elseif (($this->Debug || isset($_GET['debug_query'])) && isset($GLOBALS['perm']) && $GLOBALS['perm']->have_perm('admin'))
    {
      $etime = utime() - $stime;
      $color = 'black';
      if ($etime > 1) $color = 'red';
      elseif ($etime > 0.3) $color = 'orange';
      printf("<span style=\"color: {$color}\">Debug took %f s with link ID = %d, query ID = %d<br></span>\n", $etime, $this->Link_ID, $this->Query_ID);
    }

    # Will return nada if it fails. That's fine.
    return $this->result;
  }

  function execute($query) {
    return $this->pdo->exec($query);
  }

  /* public: walk result set */
  function next_record() {
    if (!$this->result) {
      $this->halt("next_record called with no query pending.");
      return 0;
    }

    $this->Errno = 0;
    $this->Error = '';
    try {
      $this->Record = $this->result->fetch($this->Result_Type);
      $this->Row   += 1;
    } catch (PDOException $e) {
      $this->Errno = $e->getCode();
      $this->Error = $e->getMessage();
    }

    $stat = is_array($this->Record);
    if (!$stat && $this->Auto_Free) {
      $this->free();
    }
    return $stat;
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
        $this->Record = $this->result->fetch($this->Result_Type);
        $this->Row   += 1;
      } catch (PDOException $e) {
        $this->Errno = $e->getCode();
        $this->Error = $e->getMessage();
      }
    }
    if ($this->Error != '') {
      $this->halt("seek($pos) failed: result has ".$this->num_rows()." rows");

      //DevNote: do we really need this?
      //~ /* half assed attempt to save the day, 
       //~ * but do not consider this documented or even
       //~ * desireable behaviour.
       //~ */
      //~ @mysql_data_seek($this->Query_ID, $this->num_rows());
      //~ $this->Row = $this->num_rows;
      return 0;
    }

    return 1;
  }

  /* public: table locking */
  function lock($table, $mode="write") {
    $this->connect();
    
    $query="lock tables ";
    if (is_array($table)) {
      while (list($key,$value)=each($table)) {
        if ($key=="read" && $key!=0) {
          $query.="$value read, ";
        } else {
          $query.="$value $mode, ";
        }
      }
      $query=substr($query,0,-2);
    } else {
      $query.="$table $mode";
    }
    $res = @mysql_query($query, $this->Link_ID);
    if (!$res) {
      $this->halt("lock($table, $mode) failed.");
      return 0;
    }
    return $res;
  }
  
  function unlock() {
    $this->connect();

    $res = @mysql_query("unlock tables", $this->Link_ID);
    if (!$res) {
      $this->halt("unlock() failed.");
      return 0;
    }
    return $res;
  }


  /* public: evaluate the result (size, width) */
  function affected_rows() {
    return @mysql_affected_rows($this->Link_ID);
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

  /* public: sequence numbers */
  function nextid($seq_name) {
    $this->connect();
    
    if ($this->lock($this->Seq_Table)) {
      /* get sequence number (locked) and increment */
      $q  = sprintf("select nextid from %s where seq_name = '%s'",
                $this->Seq_Table,
                $seq_name);
      $id  = @mysql_query($q, $this->Link_ID);
      $res = @mysql_fetch_array($id);
      
      /* No current value, make one */
      if (!is_array($res)) {
        $currentid = 0;
        $q = sprintf("insert into %s values('%s', %s)",
                 $this->Seq_Table,
                 $seq_name,
                 $currentid);
        $id = @mysql_query($q, $this->Link_ID);
      } else {
        $currentid = $res["nextid"];
      }
      $nextid = $currentid + 1;
      $q = sprintf("update %s set nextid = '%s' where seq_name = '%s'",
               $this->Seq_Table,
               $nextid,
               $seq_name);
      $id = @mysql_query($q, $this->Link_ID);
      $this->unlock();
    } else {
      $this->halt("cannot lock ".$this->Seq_Table." - has it been created?");
      return 0;
    }
    return $nextid;
  }

  function last_inserted_id()
  {
    return @mysql_insert_id($this->Link_ID);
  }

  /* public: return table metadata */
  function metadata($table='',$full=false) {
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
      $id = @mysql_list_fields($this->Database, $table);
      if (!$id)
        $this->halt("Metadata query failed.");
    } else {
      $id = $this->Query_ID; 
      if (!$id)
        $this->halt("No query specified.");
    }
 
    $count = @mysql_num_fields($id);

    // made this IF due to performance (one if is faster than $count if's)
    if (!$full) {
      for ($i=0; $i<$count; $i++) {
        $res[$i]["table"] = @mysql_field_table ($id, $i);
        $res[$i]["name"]  = @mysql_field_name  ($id, $i);
        $res[$i]["type"]  = @mysql_field_type  ($id, $i);
        $res[$i]["len"]   = @mysql_field_len   ($id, $i);
        $res[$i]["flags"] = @mysql_field_flags ($id, $i);
      }
    } else { // full
      $res["num_fields"]= $count;
    
      for ($i=0; $i<$count; $i++) {
        $res[$i]["table"] = @mysql_field_table ($id, $i);
        $res[$i]["name"]  = @mysql_field_name  ($id, $i);
        $res[$i]["type"]  = @mysql_field_type  ($id, $i);
        $res[$i]["len"]   = @mysql_field_len   ($id, $i);
        $res[$i]["flags"] = @mysql_field_flags ($id, $i);
        $res["meta"][$res[$i]["name"]] = $i;
      }
    }
    
    // free the result only if we were called on a table
    if ($table) @mysql_free_result($id);
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

  function table_names() {
    $this->query("SHOW TABLES");
    $i=0;
    while ($info=mysql_fetch_row($this->Query_ID))
     {
      $return[$i]["table_name"]= $info[0];
      $return[$i]["tablespace_name"]=$this->Database;
      $return[$i]["database"]=$this->Database;
      $i++;
     }
   return $return;
  }

  function select_one($query) {
    $res = -1;
    $this->query($query);
    if ($this->next_record())
    {
      $res = current($this->Record);
    }
    return $res;
  }

  function select_one_array($query) {
    $res = -1;
    $was_result_type = $this->Result_Type;
    $this->Result_Type = MYSQL_NUM;
    $this->query($query);
    if ($this->Errno == 0 && $this->next_record())
    {
      $res = array();
      for ($i=0; $i<$this->num_fields(); $i++)
      {
        $res[$i] = $this->Record[$i];
      }
    }
    $this->Result_Type = $was_result_type;
    return $res;
  }

  function select_count($from, $where = '') {
    $this->query("SELECT COUNT(*) FROM {$from} WHERE 1" . ($where!=''?" AND {$where};":''));
    return $this->next_record() ? current($this->Record) : -1;
  }

  function table_exists($table) {
    return $this->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '" . $this->Database . "' AND table_name = '" . $table . "';") > 0;
  }

  function is_table_locked($table) {
    $this->query("SHOW OPEN TABLES WHERE `Table`='{$table}' AND `Database`='{$this->Database}' AND In_use > 0;");
    return $this->num_rows() > 0;
  }

  function escape($value) {
    return DB_Sql::db_escape($value);
  }

  static function db_escape($value) {
    $return = '';
    for($i = 0; $i < strlen($value); ++$i) {
        $char = $value[$i];
        $ord = ord($char);
        if($char !== "'" && $char !== "\"" && $char !== '\\' && $ord >= 32 && $ord <= 126)
            $return .= $char;
        else
            $return .= '\\x' . dechex($ord);
    }
    return $return;
  }

  function execute_queries($q_ary, $lock_table = null) {
    if (!is_array($q_ary)) {
      show_error('execute_queries must receive an array');
      return;
    }
    if (!is_null($lock_table)) {
      $this->query("LOCK TABLES {$lock_table} WRITE;");
    }
    foreach ($q_ary as $q) {
      if ($GLOBALS['debug']) {
        print_query($q);
      }
      $this->query($q);
    }
    if (!is_null($lock_table)) {
      $this->query("UNLOCK TABLES;");
    }
  }
}
?>
