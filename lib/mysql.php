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

    function resultq($query,$row=0,$col=0){
      $res=$this->query($query);
      $res=$this->result($res,$row,$col);
      return $res;
    }
  }
?>
