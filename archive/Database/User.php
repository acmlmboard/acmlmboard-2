<?php

require_once('lib/Database/DBRecord.php');
require_once('lib/Database/Group.php');

class User extends DBRecord
{    
  //names
  static protected $_table = "users";
  //normal fields
  //foreign keys
  protected $_group_id, $_group;
  protected $_secondaryGroups = array();

  public function setGroup_id($value) {
    $this->_group_id = $value;
    unset($this->_group); //invalidate
    $this->setDirty(true);
  }
  public function group_id() {
    return $this->_group_id;
  }
  public function group() {
    if (!isset($this->_group)) $this->_group = Group::fetchByID($this->_group_id);
    return $this->_group; //cached
  }
  public function setGroup(Group $group) {
    if ($group->id() != $this->_group_id) {
      $this->_group = $group;
      $this->syncForeign();
    }
  }

  private function syncForeign() {
      $this->_group_id = $this->_group->id();    
  }

  public function commit() {
    //make sure group id matches
    $this->syncForeign();


    if ($res) return true;
    else return false;


  }
  public static function createFromRow($row) {
    $r = new self();
    $r->_id = $row['id'];    
    $r->_group_id = $row['group_id'];
    return $r;
  }

}
?>
