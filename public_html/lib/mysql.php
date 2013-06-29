<?php
  class mysql{
    var $queries=0;
    var $rowsf=0;
    var $rowst=0;
    var $time=0;
    var $db=0;
    function connect($host,$user,$pass) {return $this->db=new mysqli($host,$user,$pass);}
    function selectdb($dbname)          {$this->db->set_charset("latin1"); return $this->db->select_db($dbname);}

    function numrows($resultset) {
      return $resultset->num_rows;
    }

    function query($query){	
      if(0 && $_GET[sqldebug])
        print "{$this->queries} $query<br>";
      
      $start=usectime();
      if($res=@$this->db->query($query)){
        $this->queries++;
        $this->rowst+=$res->num_rows;
      }else
        print $this->db->error;

      $this->time+=usectime()-$start;
      return $res;
    }

	
	function escape($str)
	{
		return '\''.$this->db->real_escape_string($str).'\'';
	}

   function preparesql ($query, $phs = array()) {
    $phs = array_map(array($this,'escape'), $phs);

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
     return $this->query($this->preparesql($query,$phs));
   }


    function fetch($result){
      $start=usectime();

      if($result && $res=$result->fetch_assoc())
          $this->rowsf++;

      $this->time+=usectime()-$start;
      return $res;
    }

    function result($result,$row=0,$col=0){
      $start=usectime();

      if($result)
	  {
		$result->data_seek($row);
		$thisrow = array_values($result->fetch_assoc());
		$res = $thisrow[$col];
		if (isset($thisrow[$col]))
			$this->rowsf++;
	  }

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