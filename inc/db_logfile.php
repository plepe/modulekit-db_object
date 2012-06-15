<?
class db_logfile {
  function __construct($id, $param, $fields=array()) {
    $this->param=$param;
    $this->fields=$fields;
    $this->fields['date']="datetime";
  }

  function log($entry) {
    $set=array();

    foreach($this->fields as $k=>$dummy) {
      if(isset($entry[$k])) {
	$set[]=mysql_real_escape_string($k).
	  "='".mysql_real_escape_string($entry[$k])."'";
	unset($entry[$k]);
      }
    }

    $set[]="data='".mysql_real_escape_string(serialize($entry))."'";
    $set=implode(", ", $set);

    mysql_query("insert into {$this->param['table']} set {$set}");
  }
}

/* Table layout
create table log_XYZ (
  date		timestamp	not null,
  data		mediumblob	not null,
  -- more columns
  index using btree(date)
);

