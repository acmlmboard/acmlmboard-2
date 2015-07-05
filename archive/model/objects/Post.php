<?php
/**
 * This is the AcmlmBoard Post object. It contains the data for posts. It's really great, okay?
 *
 * TODO:
 * - Implement MySQL stuff
 * - Add support for global variables that don't exist yet (like the current user, etc)
 * - Stick action hooks into the texty flesh
 * - Use some kind of configurable date/time formatting. Maybe some time zones. Y'know.
 * - Finish whatever else
 * - Improve aerodynamics and commenting
 *
 * VERSION HISTORY:
 * 2012-01-12: Shroomy lovingly sows the first seeds of the humble flexible AcmlmBoard model.
 *
 */

// Kill on direct access
if ( count( get_included_files() ) == 1 ) { include 'index.php'; die(); }

class Post
{
	protected $post_data; // Everything sits in here
	
	// Post constructor. TODO: Make $args meaningful.
	function __construct ( $args )
	{
		$this->post_data = array(
			'id' => NULL, // ID in MySQL
			'body' => '', // Text in post body
			'date' => new DateTime(), // DateTime representation of date/time
			'user' => NULL, // ID of user who created the post
			'thread' => NULL, // ID of thread this post belongs to
			'edit_date' => NULL, // DateTime representation of last edit date/time
			'edit_version' => 0, // Number of times the post has beenedited
			'edit_user' => NULL // ID of user who last edited the post
		);
	}
	
	// Allows friendly syntax for getting post data, like $post->post_body
	public function __get ( $name )
	{
		switch ( $name )
		{
			case 'post_id':
				return $this->post_data['id'];
			case 'post_body':
				return $this->post_data['body'];
			case 'post_date':
				return isset( $this->post_data['date'] ) ? $this->post_data['date']->format( 'n/j/y h:i:s a' ) : NULL;
			case 'user_id':
				return $this->post_data['user'];
			case 'thread_id':
				return $this->post_data['thread'];
			case 'edit_date':
				return isset( $this->post_data['edit_date'] ) ? $this->post_data['edit_date']->format( 'n/j/y h:i:s a' ) : NULL;
			case 'edit_version':
				return $this->post_data['edit_version'];
			case 'edit_user_id':
				return $this->post_data['edit_user'];
			default:
				if ( array_key_exists( $name, $this->post_data ) )
					{ return $this->post_data[$name]; }
				$trace = debug_backtrace();
				trigger_error( 'Undefined property via __get(): ' . $name . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'], E_USER_NOTICE );
				return NULL;
		}
	}
	
	// Allows friendly syntax for setting post data and updating MySQL, like $post->post_body = 'Here is the new post body!'
	public function __set ( $name, $value )
	{
		echo 'set ' . $name . ' to '. $value . "<br/>\n";
		switch ( $name )
		{
			case 'post_id':
				// Do we actually want to allow this?
				return $this->post_data['id'] = $value;
			case 'post_body':
				return $this->set_body( $value );
			case 'post_date':
				return isset( $this->post_data['date'] ) ? $this->post_data['date']->format( 'n/j/y h:i:s a' ) : NULL;
			case 'user_id':
				return $this->post_data['user'];
			case 'thread_id':
				return $this->post_data['thread'];
			case 'edit_date':
				return isset( $this->post_data['edit_date'] ) ? $this->post_data['edit_date']->format( 'n/j/y h:i:s a' ) : NULL;
			case 'edit_version':
				return $this->post_data['edit_version'];
			case 'edit_user_id':
				return $this->post_data['edit_user'];
			default:
				if ( array_key_exists( $name, $this->post_data ) )
					{ return $this->post_data[$name] = $value; }
				$trace = debug_backtrace();
				trigger_error( 'Undefined property via __get(): ' . $name . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'], E_USER_NOTICE );
				return NULL;
		}
	}
	
	// Since we're using __get() and __set(), we might as well implement __isset() so everything matches up properly.
	public function __isset ( $name )
		{ return isset( $this->post_data[$name] ); }
	
	// Set the post body, optionally marking it as an edit
	public function set_body ( $body, $is_edit = FALSE )
	{
		$this->post_data['body'] = $body;
		
		if ( $is_edit )
		{
			$this->post_data['edit_date'] = new DateTime();
			$this->post_data['edit_version']++;
			//$this->post_data['edit_user'] = $user->id;
		}
		
		// TODO: Update post in MySQL
		
		return $this->post_body;
	}
	
	// Set the post body as an edit
	public function edit_body ( $body )
		{ return set_body( $body, TRUE ); }
	
	// Dump the contents of the post as a string-like substance.
	public function __toString ()
	{
		$result  = "\n    [post_id] => {$this->post_id}";
		$result .= "\n    [post_body] => '{$this->post_body}'";
		$result .= "\n    [post_date] => {$this->post_date}";
		$result .= "\n    [user_id] => {$this->user_id}";
		$result .= "\n    [thread_id] => {$this->thread_id}";
		$result .= "\n    [edit_date] => {$this->edit_date}";
		$result .= "\n    [edit_version] => {$this->edit_version}";
		$result .= "\n    [edit_user_id] => {$this->edit_user_id}";
		$result = "Post Object\n(" . $result . "\n)\n";
		return $result;
	}
	
	// Oh, cool. That's everything.
}
?>