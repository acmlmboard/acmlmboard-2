<?php
class DBRecord
{
  //names
  static protected $_table = "";
  static protected $_db;
  //internal
  protected $_dirty;
  protected $_id;

  public static function setDB(mysql $db) {
    DBRecord::$_db = $db;
  }

  public function db() {
    return DBRecord::$_db;
  }

  public function id() {
    return $this->_id;
  }

  public function setDirty($value) {
    $this->_dirty = $value;
  }

  protected static function fetch($q,$p) {
//        echo "$q\n$p[0]\n";
    return DBRecord::$_db->fetchp($q,$p);
  }

  protected static function query($q,$p) {
//        echo "$q\n$p[0]\n";
    return DBRecord::$_db->queryp($q,$p);
  }
  public static function idExists($id) {
    $res = self::query(
        "SELECT id FROM ".static::$_table." WHERE id=?",
        array($id));
    if ($res) return true;
    else return false;
  }
  public static function fetchByID($id) {
    $row = static::fetch(
        "SELECT * FROM `".static::$_table."` WHERE id=?",
        array($id)
      );
    return static::createFromRow($row);
  }
  
  public static function createFromRow($row) {
    return NULL;
  }

}
?>
