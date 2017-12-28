<?php
/**
 * This is the AcmlmBoard User object. It contains the data for users.
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

class User
{
	protected $user_data; // Everything sits in here
	
	// Post constructor. TODO: Make $args meaningful.
	function __construct ( $args )
	{
		$this->user_data = array(
			'id' => NULL, // ID in MySQL
			'name' => '', // Cool internet handle
			'email' => NULL, // Email address
			'real_name' => NULL, // "Christian name"
			'location' => NULL, // Some kind of location
			'birthday' => NULL, // User's bithday. Happy Birthday, user! Just another step closer to your eventual end...
			'homepage' => '', // 
			'timezone' => NULL, // 
			'bio' => '' // 
		);
	}
	
	// Allows friendly syntax for getting user data.
	public function __get ( $name )
	{
		switch ( $name )
		{
			case 'user_id':
				return $this->user_data['id'];
			case 'user_name':
				return $this->user_data['name'];
			case 'user_name':
				return $this->user_data['email'];
			case 'user_real_name':
				return $this->user_data['real_name'];
			case 'user_location':
				return $this->user_data['location'];
			case 'user_birthday':
				return isset( $this->user_data['birthday'] ) ? $this->user_data['birthday']->format( 'n/j/y' ) : NULL;
			case 'user_homepage':
				return $this->user_data['homepage'];
			case 'user_timezone':
				return $this->user_data['timezone'];
			case 'user_bio':
				return $this->user_data['bio'];
			default:
				if ( array_key_exists( $name, $this->user_data ) )
					{ return $this->user_data[$name]; }
				$trace = debug_backtrace();
				trigger_error( 'Undefined property via __get(): ' . $name . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'], E_USER_NOTICE );
				return NULL;
		}
	}
	
	// Allows friendly syntax for setting user data and updating MySQL.
	public function __set ( $name, $value )
	{
		switch ( $name )
		{
			case 'user_id':
				// Do we actually want to allow this?
				return $this->user_data['id'] = $value;
			case 'user_name':
				return $this->user_data['name'] = $value;
			case 'user_name':
				return $this->user_data['email'] = $value;
			case 'user_real_name':
				return $this->user_data['real_name'] = $value;
			case 'user_location':
				return $this->user_data['location'] = $value;
			case 'user_birthday':
				return isset( $this->user_data['birthday'] ) ? $this->user_data['birthday']->format( 'n/j/y' ) : NULL;
			case 'user_homepage':
				return $this->user_data['homepage'] = $value;
			case 'user_timezone':
				return $this->user_data['timezone'] = $value;
			case 'user_bio':
				return $this->user_data['bio'] = $value;
			default:
				if ( array_key_exists( $name, $this->user_data ) )
					{ return $this->user_data[$name] = $value; }
				$trace = debug_backtrace();
				trigger_error( 'Undefined property via __get(): ' . $name . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'], E_USER_NOTICE );
				return NULL;
		}
	}
	
	// Since we're using __get() and __set(), we might as well implement __isset() so everything matches up properly.
	public function __isset ( $name )
		{ return isset( $this->user_data[$name] ); }
}
?>