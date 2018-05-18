<?php
  class mysql{
    public $queries=0;
    public $rowsf=0;
    public $rowst=0;
    public $time=0;
    public $db=0;
	// SQL debug stuff
	public static $debug = false;
	public static $query_list = array();
	public function connect($host,$user,$pass) {
		global $config;
		self::$debug = $config['enablesqldebug'];
		return $this->db=new mysqli($host,$user,$pass);
	}
    public function selectdb($dbname)          {$this->db->set_charset("latin1"); return $this->db->select_db($dbname);}

    public function numrows($resultset) {
      return $resultset->num_rows;
    }

    public function query($query){	
     // if(0 && $_GET[sqldebug])
     //   print "{$this->queries} $query<br>";
      
		$start = microtime(true);
		$error = "";
		if ($res=@$this->db->query($query)) {
			$this->queries++;
			if ($res instanceof MySQLi) {
				$this->rowst+=$res->num_rows;
			}
		} else {
			$error = $this->db->error();
			trigger_error($error, E_USER_NOTICE);
		}
		$timetaken = microtime(true) - $start;
		$this->time += $timetaken;
	  
		if (self::$debug) {
			// Query logging
			$b = self::getbacktrace();
			self::$query_list[] = array(1, $query, $b['pfunc'], $b['file'], $b['line'], $timetaken, $error);
		}
		return $res;
    }
	
	public function error()
	{
		return $this->db->error;
	}

	
	public function escape($str)
	{
		return $this->db->real_escape_string($str);
	}
	
	public function escapeandquote($str)
	{
		return '\''.$this->escape($str).'\'';
	}

	public function preparesql ($query, $phs = array()) {
    $phs = array_map(array($this,'escapeandquote'), $phs);

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
	public function prepare ($query, $phs = array()) {
     return $this->query($this->preparesql($query,$phs));
	}


    public function fetch($result){
      $start=usectime();

      if($result && $res=$result->fetch_assoc())
          $this->rowsf++;

      $this->time+=usectime()-$start;
      return $res;
    }

    public function result($result,$row=0,$col=0){
      $start=usectime();

	  $res = null;
      if($result)
	  {
		$result->data_seek($row);
		$thisrow = $result->fetch_assoc();
		if ($thisrow)
		{
			$thisrow = array_values($thisrow);
			$res = $thisrow[$col];
			if (isset($thisrow[$col]))
				$this->rowsf++;
		}
	  }

      $this->time+=usectime()-$start;
      return $res;
    }

    public function fetchq($query,$row=0,$col=0){
      $res=$this->query($query);
      $res=$this->fetch($res);
      return $res;
    }

    public function fetchp($query,$phs,$row=0,$col=0){
      //HOSTILE DEBUGGING echo 'preparing fetch query<br>';
      return $this->fetchq($this->preparesql($query,$phs),$row,$col);
    }


    public function resultq($query,$row=0,$col=0){
      $res=$this->query($query);
      $res=$this->result($res,$row,$col);
      return $res;
    }
    public function resultp($query,$phs,$row=0,$col=0){
      return $this->resultq($this->preparesql($query,$phs),$row,$col);
    }
	
	public function insertid()
	{
		return $this->db->insert_id;
	}
	
	public function affectedrows()
	{
		return $this->db->affected_rows;
	}
	
	// PDO::FETCH_ASSOC + fetchAll
	public function getarray($query) {
		$res = $this->query($query);
		$out = array();
		while ($x = $this->fetch($res)) {
			$out[] = $x;
		}
		return $out;
	}
	
	// returns the entire row indexed by an unique field
	// similar to PDO::FETCH_UNIQUE
	public function getarraybykey($query, $key) {
		$res = $this->query($query, $hash);
		$out = array();
		while ($x = $this->fetch($res)) {
			$out[$x[$key]] = $x;
		}
		return $out;
	}
	
	// returns a one dimentional array out of a query
	// similar to PDO::FETCH_COLUMN
	public function getresults($query, $col = 0) {
		$res = $this->query($query);
		$out = array();
		$max = $this->numrows($res);
		for ($i = 0; $i < $max; ++$i) {
			$out[] = $this->result($res, $i, $col);
		}
		return $out;
	}
	
	// returns array indexed by an unique field
	// similar to PDO::FETCH_KEY_PAIR
	public function getresultsbykey($query, $key, $val) {
		$res = $this->query($query);
		$out = array();
		while ($x = $this->fetch($res)) {
			$out[$x[$key]] = $x[$val];
		}
		return $out;
	}
	
	private static function getbacktrace() {
		$backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
		
		// Loop until we have found the real location of the query
		for ($i = 1; strpos($backtrace[$i]['file'], "mysql.php"); ++$i);
		
		// Check in what function it comes from
		$backtrace[$i]['pfunc'] = (isset($backtrace[$i+1]) ? $backtrace[$i+1]['function'] : "<i>(main)</i>");	
		return $backtrace[$i];
	}

  }
?>