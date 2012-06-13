<?
class db_object {
  public $is_new;

  function __construct($id=null, $elem) {
    $this->id=$id;

    if($elem) {
      $this->is_new=false;
    }
    else {
      $this->is_new=true;
      $elem=$this->default_values();
    }

    foreach($elem as $k=>$v) {
      $this->$k=$v;
    }

    $this->data=unserialize($elem['data']);
    if(!$this->data)
      $this->data=array();
  }

  function fields() {
    return array();
  }

  function default_values() {
    return array("data"=>serialize(array()));
  }

  function data() {
    $ret=$this->data;

    foreach($this->fields() as $k) {
      $ret[$k]=$this->$k;
    }
    $ret['id']=$this->id;

    return $ret;
  }

  function save_set($data) {
    $set=array();
    $fields=$this->fields();
    
    foreach($data as $k=>$v) {
      if(in_array($k, $fields)) {
	$this->$k=$v;
	$set[]=mysql_real_escape_string($k).
	  "='".mysql_real_escape_string($this->$k)."'";
      }
      else {
	$this->data[$k]=$v;
      }
    }

    $set[]="data='".mysql_real_escape_string(serialize($this->data))."'";
    $set=implode(", ", $set);

    return $set;
  }
}

/*
SQL:
create table template (
  id		varchar(32)	not null,
  field1	text		null,
  field2	int		null,
  data		text		null,
  primary key(id)
}

field1, field2 are some arbitrary values
data is an assoc. array where additional key/value pairs can be saved
from PHP. It will be (un)serialized on load/save.

PHP:
class template {
  function __construct($id, $elem=null) {
    if(!$elem) {
      $res=sql("select * from template where foo='bar'");
      $elem=mysql_fetch_assoc($res);
    }

    parent::__construct($id, $elem);

    // further intialization
  }

  function fields() {
    $fields=parent::fields();
    return array_merge($fields,
      array("field1", "field2"));
  }

  function save($data) {
    $set=$this->save_set($data);

    sql("insert into template set id='{$this->id}', $set on duplicate key update $set");
  }
}
*/
