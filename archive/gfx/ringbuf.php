<?php

class ringbuf {

	public $size = 1;
	public $fill = 0;
	public $buf = array();

	function push($a) {
		/*
		// what the hell is this crap?
		for ($i = ($this->size - 1); $i >= 0;  --$i) {
			$this->buf[$i + 1] = $this->buf[$i];
		}
		$this->buf[0] = $a;
		*/
		array_unshift($this->buf, $a);
		var_dump($this->buf);
		if ($this->fill < $this->size)
			$this->fill++;
	}

	function get() {
		$ret = 0.0;
		for ($i = 0; $i < $this->fill;  ++$i) {
			$ret+=($this->buf[$i] / (float) $this->fill);
		}
		return $ret;
	}

	function low() {
		$ret = 0.0;
		$buf2 = $this->buf;
		sort($buf2);

		for ($i = 0; $i < $this->fill / 2;  ++$i) {
			$ret+=($buf2[$i] * 2 / (float) $this->fill);
		}
		return $ret;
	}

	function high() {
		$ret = 0.0;
		$buf2 = $this->buf;
		sort($buf2);

		for ($i = $this->fill / 2; $i < $this->fill;  ++$i) {
			$ret+=($buf2[$i] * 2 / (float) $this->fill);
		}
		return $ret;
	}

}