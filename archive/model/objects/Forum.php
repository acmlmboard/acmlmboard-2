<?php
/**
 * This is the AcmlmBoard Forum object. It contains the data for threads.
 *
 * TODO:
 * - Pretty much everything.
 *
 * VERSION HISTORY:
 * 2012-01-12: Shroomy phones it in with a few copy/pastes.
 *
 */

// Kill on direct access
if ( count( get_included_files() ) == 1 ) { include 'index.php'; die(); }

class Forum
{
	protected $forum_data; // Everything sits in here
	
	// Post constructor. TODO: Make $args meaningful.
	function __construct ( $args )
	{
		$this->forum_data = array(
			'id' => NULL, // ID in MySQL
			'title' => '', // Title of the thread
			'whatever' => NULL // Here's where I gave up
		);
	}
	
	// Allows friendly syntax for getting forum data.
	public function __get ( $name )
	{
		switch ( $name )
		{
			case 'forum_id':
			default:
				if ( array_key_exists( $name, $this->forum_data ) )
					{ return $this->forum_data[$name]; }
				$trace = debug_backtrace();
				trigger_error( 'Undefined property via __get(): ' . $name . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'], E_USER_NOTICE );
				return NULL;
		}
	}
	
	// Allows friendly syntax for setting forum data and updating MySQL.
	public function __set ( $name, $value )
	{
		switch ( $name )
		{
			case 'forum_id':
				// Do we actually want to allow this?
				return $this->forum_data['id'] = $value;
			case 'forum_title':
				return $this->forum_data['title'] = $value;
			default:
				if ( array_key_exists( $name, $this->forum_data ) )
					{ return $this->forum_data[$name] = $value; }
				$trace = debug_backtrace();
				trigger_error( 'Undefined property via __get(): ' . $name . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'], E_USER_NOTICE );
				return NULL;
		}
	}
	
	// Since we're using __get() and __set(), we might as well implement __isset() so everything matches up properly.
	public function __isset ( $name )
		{ return isset( $this->forum_data[$name] ); }
}
?>