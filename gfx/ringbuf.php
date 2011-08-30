<?php

class ringbuf {
	var $size=1;
	var $fill=0;
	var $buf=array();

	function push($a)
	{
		for($i=($this->size-1);$i>=0;--$i) {
			$this->buf[$i+1]=$this->buf[$i];
		}
		$this->buf[0]=$a;
		if($this->fill<$this->size) $this->fill++;
	}

	function get()
	{
		$ret=0.0;
		for($i=0;$i<$this->fill;++$i) {
			$ret+=($this->buf[$i]/(float)$this->fill);
		}
		return $ret;
	}

	function low()
	{
		$ret=0.0;
		$buf2=$this->buf;
		sort($buf2);

		for($i=0;$i<$this->fill/2;++$i) {
			$ret+=($buf2[$i]*2/(float)$this->fill);
		}
		return $ret;
	}

	function high()
	{
		$ret=0.0;
		$buf2=$this->buf;
		sort($buf2);

		for($i=$this->fill/2;$i<$this->fill;++$i) {
			$ret+=($buf2[$i]*2/(float)$this->fill);
		}
		return $ret;
	}
}

?>
