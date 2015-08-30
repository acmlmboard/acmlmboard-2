<?php

class mysql {

	// public for now since this replicates the behavior of PHP4 class variables.
	public $query_count = 0;
	public $rows_fetched = 0; //rows fetched
	public $rowst = 0;
	public $time = 0;
	public $db = null;
	public $query_log = array();
	public $debug_mode = false; // change this to enable SQL query dumps

	public function connect($host, $user, $pass) {
		$this->db = new mysqli($host, $user, $pass);
		return $this->db;
	}

	public function select_db($dbname) {
		$this->db->set_charset("latin1");
		return $this->db->select_db($dbname);
	}

	public function num_rows($resultset) {
		return $resultset->num_rows;
	}

	public function query($query) {
		$error = '';
		$start = usectime();
		if (($res = $this->db->query($query)) !== FALSE) {
			$this->query_count++;
			if (is_object($res)) {
				$this->rowst += $res->num_rows;
			} else {
				$this->rowst += $this->db->affected_rows;
			}
		}

		if ($this->debug_mode) {
			$this->query_log[] = array(
				'index' => $this->query_count,
				'query' => $query,
				'execution_time' => usectime() - $start,
				'num_rows' => is_object($res) ? $res->num_rows : 0,
				'affected_rows' => $this->db->affected_rows,
				'error' => $this->db->error);
		}

		$this->time += usectime() - $start;
		return $res;
	}

	public function error() {
		return $this->db->error;
	}

	public function escape($str) {
		return $this->db->real_escape_string($str);
	}

	public function escapeandquote($str) {
		return '\'' . $this->escape($str) . '\'';
	}

	public function prepare_sql($query, $phs = array()) {
		$phs = array_map(array($this, 'escapeandquote'), $phs);

		$curpos = 0;
		$curph = count($phs) - 1;

		for ($i = strlen($query) - 1; $i > 0; $i--) {
			if ($query[$i] !== '?') {
				continue;
			}

			if ($curph < 0 || !isset($phs[$curph])) {
				$query = substr_replace($query, 'NULL', $i, 1);
			} else {
				$query = substr_replace($query, $phs[$curph], $i, 1);
			}

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
	public function prepare_query($query, $phs = array()) {
		return $this->query($this->prepare_sql($query, $phs));
	}

	public function fetch_assoc($result) {
		$start = usectime();

		if (!isset($result) || $result === false)
			return null;

		if ($res = $result->fetch_assoc())
			$this->rows_fetched++;

		$this->time += usectime() - $start;
		return $res;
	}

	public function result($result, $row = 0, $col = 0) {
		$start = usectime();
		$res = FALSE;
		if ($result) {
			$result->data_seek($row);
			$fetched_row = $result->fetch_array(MYSQLI_NUM);
			if ($fetched_row) {
				$res = $fetched_row[$col];
				$this->rows_fetched++;
			}
			$result->free();
		}

		$this->time+=usectime() - $start;
		return $res;
	}

	public function query_fetch($query) {
		$result = $this->query($query);
		$row = null;
		if ($result) {
			$row = $this->fetch_assoc($result);
			$result->free();
		}
		return $row;
	}

	public function query_fetch_all($query) {
		$data = array();
		$result = $this->query($query);
		if ($result) {
			while ($row = $this->fetch_assoc($result)) {
				$data[] = $row;
			}
			$result->free();
		}
		return $data;
	}

	public function prepare_query_fetch($query, $phs, $row = 0, $col = 0) {
		return $this->query_fetch($this->prepare_sql($query, $phs), $row, $col);
	}

	public function query_result($query, $row = 0, $col = 0) {
		$result = $this->query($query);
		if ($result) {
			return $this->result($result, $row, $col);
		}
		return FALSE;
	}

	public function prepare_query_result($query, $phs, $row = 0, $col = 0) {
		return $this->query_result($this->prepare_sql($query, $phs), $row, $col);
	}
	
	public function _prepare($query) {
		return $this->db->prepare($query);
	}
	
	public function _query($query, $resultmode = MYSQLI_STORE_RESULT) {
		return $this->db->query($query, $resultmode);
	}

	public function insert_id() {
		return $this->db->insert_id;
	}

	public function affected_rows() {
		return $this->db->affected_rows;
	}

}
