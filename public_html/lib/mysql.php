<?php
  class mysql{
    var $queries=0;
    var $rowsf=0;
    var $rowst=0;
    var $time=0;
    var $conid=0;
    function connect($host,$user,$pass) {return $this->conid=mysql_connect($host,$user,$pass);}
    function selectdb($dbname)          {mysql_set_charset("latin1"); return mysql_select_db($dbname,$this->conid);}

    function numrows($resultset) {
      return @mysql_num_rows($resultset);
    }

    function query($query){	
      if(0 && $_GET[sqldebug])
        print "$query<br>";
      
      $start=usectime();
      if($res=mysql_query($query,$this->conid)){
        $this->queries++;
        $this->rowst+=@mysql_num_rows($res,$this->conid);
      }else
        print mysql_error($this->conid);

      $this->time+=usectime()-$start;
      return $res;
    }


   static function preparesql ($query, $phs = array()) {
    $phs = array_map(create_function('$ph',
                     'return "\'".mysql_real_escape_string($ph)."\'";'), $phs);

    $curpos = 0;
    $curph  = count($phs)-1;

    for ($i=strlen($query)-1; $i>0; $i--) {

      if ($query[$i] !== '?')  continue;
      if ($curph < 0 || !isset($phs[$curph]))
    $query = substr_replace($query, 'NULL', $i, 1);
      else
    $query = substr_replace($query, $phs[$curph], $i, 1);

      $curph--;
    }
    unset($curpos, $curph, $phs);
    //HOSTILE DEBUGGING echo ($query)."<br>";
    return $query;
   }

   // mysql_query() wrapper. takes two arguments. first
   // is the query with '?' placeholders in it. second argument
   // is an array containing the values to substitute in place
   // of the placeholders (in order, of course).
   // Pass NULL constant in array to get unquoted word NULL
   function prepare ($query, $phs = array()) {
     return $this->query(self::preparesql($query,$phs));
   }


    function fetch($result){
      $start=usectime();

      if($result && $res=mysql_fetch_array($result))
          $this->rowsf++;

      $this->time+=usectime()-$start;
      return $res;
    }

    function result($result,$row=0,$col=0){
      $start=usectime();

      if($result && $res=@mysql_result($result,$row,$col))
        $this->rowsf++;

      $this->time+=usectime()-$start;
      return $res;
    }

    function fetchq($query,$row=0,$col=0){
      $res=$this->query($query);
      $res=$this->fetch($res);
      return $res;
    }

    function fetchp($query,$phs,$row=0,$col=0){
      //HOSTILE DEBUGGING echo 'preparing fetch query<br>';
      return $this->fetchq(self::preparesql($query,$phs),$row,$col);
    }


    function resultq($query,$row=0,$col=0){
      $res=$this->query($query);
      $res=$this->result($res,$row,$col);
      return $res;
    }
    function resultp($query,$phs,$row=0,$col=0){
      return $this->resultq(self::preparesql($query,$phs),$row,$col);
    }

  }
?>