<?php

require_once('lib/Database/DBRecord.php');
require_once('lib/Database/PermCat.php');

class Perm extends DBRecord
{    
  //names
  static protected $_table = "perm";
  //normal fields
  protected $_title, $_description;
  //foreign keys
  protected $_permcat_id, $_permCat;

  public function appliesToUser($id) {

  }

  public function appliesToGroup($id) {
    
  }

  public function setId($value) {
    if (self::idExists($value)) {
      return false;
    }
    else {
      $this->_id = $value;
      $this->setDirty(true);
    }
  }
  public function title() {
    return $this->_title;
  }
  public function setTitle($value) {
    $this->_title = $value;
    $this->setDirty(true);
  }
  public function description() {
    return $this->_description;
  }
  public function setDescription($value) {
    $this->_description = $value;
    $this->setDirty(true);
  }
  public function setPermcat_id($value) {
    $this->_permcat_id = $value;
    unset($this->_permCat); //invalidate
    $this->setDirty(true);
  }
  public function permcat_id() {
    return $this->_permcat_id;
  }
  public function permCat() {
    if (!isset($this->_permCat)) $this->_permCat = PermCat::fetchByID($this->_permcat_id);
    return $this->_permCat; //cached
  }
  public function setPermCat(PermCat $permCat) {
    if ($permCat->id() != $this->_permcat_id) {
      $this->_permCat = $permCat;
      $this->syncForeign();
    }
  }

  private function syncForeign() {
      $this->_permcat_id = $permCat->id();    
  }

  public function commit() {
    //make sure permcat id matches
    $this->syncForeign();

    $res = $this->query("INSERT OR REPLACE INTO ".self::$_table." (id,title,description,permcat_id) VALUES (?,?,?,?)", array(
      $this->_id,
      $this->_title,
      $this->_description,
      $this->_permcat_id
    ));

    if ($res) return true;
    else return false;


  }

  public static function createFromRow($row) {
    $r = new self();
    $r->_id = $row['id'];    
    $r->_title = $row['title'];
    $r->_description = $row['description'];
    $r->_permcat_id = $row['permcat_id'];
    return $r;
  }

}
?>
