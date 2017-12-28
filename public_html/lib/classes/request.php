<?php

/**
 * A static class for accessing request variables, as well as
 * typecasting.
 */
class Request {
	
	public static function get($name, $default_value = 0, $type = 'string') {
		if (!isset($_GET[$name])) {
			return $default_value;
		}
		return self::typecast($_GET[$name], $type);
	}
	
	public static function post($name, $default_value = 0, $type = 'string') {
		if (!isset($_POST[$name])) {
			return $default_value;
		}
		return self::typecast($_POST[$name], $type);
	}
	
	public static function _request($name, $default_value = 0, $type = 'string') {
		if (!isset($_REQUEST[$name])) {
			return $default_value;
		}
		return self::typecast($_REQUEST[$name], $type);
	}
	
	public static function cookie($name, $default_value = 0, $type = 'string') {
		if (!isset($_COOKIE[$name])) {
			return $default_value;
		}
		return self::typecast($_COOKIE[$name], $type);
	}
	
	/**
	 * Typecasts the input based on a type.
	 * 
	 * <b>Supported types:</b>
	 * <ul>
	 *   <li>bool/boolean</li>
	 *   <li>int/integer</li>
	 *   <li>int16/short</li>
	 *   <li>int8/byte</li>
	 *   <li>float/double/real</li>
	 *   <li>string</li>
	 *   <li>array</li>
	 *   <li>object</li>
	 * </ul>
	 * 
	 * <b>bool/boolean has a lot of special handling</b>
	 * <ul>
	 *   <li>If the value is a string and it's found in the list of common 
	 *     true values then it returns true. </li>
	 *   <li>If the value is an integer or float and its bigger than zero
	 *     then true is returned.</li>
	 *   <li>If the value is an array with more than 0 elements or an object, true is returned.</li>
	 *   <li>Everything else returns false.</li>
	 * </ul>
	 */
	public static function typecast($input_value, $type) {
		switch($type) {
			case "bool":
			case "boolean":
				$true_values = array("1", "true", "yes", "y", "on");
				if(is_string($input_value)) {
					$clean_value = preg_replace('/\s+/', '', strtolower($input_value));
					return in_array($clean_value, $true_values);
				} elseif(is_integer($input_value) || is_float($input_value)) {
					return $input_value > 0;
				} elseif(is_array($input_value)) {
					return count($input_value) > 0;
				} elseif(is_object($input_value) || is_resource($input_value)) {
					return TRUE;
				}
				return FALSE;
			
			case "int":
			case "integer":
				return (int)$input_value;
			
			case "int16":
			case "short":
				return ((int)$input_value & 0xFFFF);
			
			case "int8":
			case "byte":
				return ((int)$input_value & 0xFF);
				
			case "float":			
			case "double":
			case "real":
				return (float)$input_value;
			
			case "string":
				return (string)$input_value;
				
			case "trimmed_string":
				return preg_replace('/\s+/', '', $input_value);
				
			case "magic_string":
				return addslashes((string)$input_value);
			
			case "base64_string":
				return base64_encode((string)$input_value);
				
			case "md5_string":
				return md5((string)$input_value);
			
			case "sha1_string":
				return sha1((string)$input_value);
				
			case "array":
				return (array)$input_value;
			
			case "object":
				return (object)$input_value;
		}
		return FALSE;
	}
}