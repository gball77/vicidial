<?php
/*
  Code By NKB Inc.
  For Support, please contact: "Nikko Benson" <nikko@nikkobenson.com>

  Wed Aug 13 12:30:13 CDT 2014 ... 0.9.0 ... Project Code started.

*/

define('NKB_INC_KEYBLOCK', '646f49cd956904c4c0a5246f604a94c4');

define('NKB_INC_DEBUG', false);
define('NKB_INC_CLIDISPLAY', false);

if (NKB_INC_CLIDISPLAY) {
  define('NKB_INC_PRE', '');
  define('NKB_INC_PRE_END', '');
} else {
  define('NKB_INC_PRE', '<pre>');
  define('NKB_INC_PRE_END', '</pre>');
}

// -----
function nkb_inc_mod_db_connect()
{
  require_once "dbconnect_mysqli.php";

  $nkb_inc_db_hook = new mysqli($VARDB_server, $VARDB_user, $VARDB_pass, $VARDB_database, $VARDB_port);
  if ($nkb_inc_db_hook->connect_errno) {
      printf(NKB_INC_PRE . "nkb_inc_mod_db_access - Connect failed: '%s'\n" . NKB_INC_PRE_END, $nkb_inc_db_hook->connect_error);
      exit();
  }

  return $nkb_inc_db_hook;

} // function nkb_inc_mod_db_access

// -----
function nkb_inc_mod_db_select($table_name, $where_statement = '')
{
  $nkb_inc_db_hook = nkb_inc_mod_db_connect();

  $sql_statement = sprintf("SELECT FROM %s", $table_name );
  if (! empty($where_statement) ) {
    $sql_statement = sprintf("%s WHERE %s", $sql_statement, $where_statement);
  }

  $record_set = $nkb_inc_db_hook->query($select_statement);

  if ($nkb_inc_db_hook->affected_rows == 0) {
    // that's not right
    return false;
  } else {
    while ( $one_record = $record_set->fetch_assoc() ) {
      $array_of_records[] = $one_record;
    }
    return $array_of_records;
  }
} // function nkb_inc_mod_db_select

// -----
function nkb_inc_mod_db_insert($table_name, $field_csv, $values_csv)
{
  $nkb_inc_db_hook = nkb_inc_mod_db_connect();

  $sql_statement = sprintf("INSERT INTO %s (%s) VALUES (%s) ", $table_name, $field_csv, $values_csv );

  $record_set = $nkb_inc_db_hook->query($select_statement);

  if ($nkb_inc_db_hook->affected_rows == 0) {
    // that's not right
    return false;
  } else {
    while ( $one_record = $record_set->fetch_assoc() ) {
      $array_of_records[] = $one_record;
    }
    return $array_of_records;
  }

} // function nkb_inc_mod_db_insert

// -----
function nkb_inc_mod_db_update($table_name, $update_csv, $where_statement = '')
{
  $nkb_inc_db_hook = nkb_inc_mod_db_connect();

  $sql_statement = sprintf("UPDATE %s SET %s ", $table_name, $update_csv );

  if (! empty($where_statement) ) {
    $sql_statement = sprintf("%s WHERE %s", $sql_statement, $where_statement);
  }

  $record_set = $nkb_inc_db_hook->query($select_statement);

  if ($nkb_inc_db_hook->affected_rows == 0) {
    // that's not right
    return false;
  } else {
    while ( $one_record = $record_set->fetch_assoc() ) {
      $array_of_records[] = $one_record;
    }
    return $array_of_records;
  }

} // function nkb_inc_mod_db_insert

/* ---- end of file ---- */
?>
