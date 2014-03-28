<?php
class regex {

	var $safe_array  = array();
	var $error_array = array();
	
	# Purpose: Checks that variable contains only text
	# Usage  : regex_textonly(array('key' => $variable1,'key2' => variable2));
	# Input  : Named array of variables to check.
	# Return : sets _SESSION if true, or returns Key (name) of wrong element
	function regex_textonly($array) {
		foreach ($array as $key => $value) {
			# Null is a unique failour
			if ($value == "") {
				#$error_array[$key] = $key." cannot be blank<br>";
			} else {
				if (preg_match("/^[A-Z0-9`',.\s\_\-]{1,100}$/i", $value)) {
					$this->safe_array[$key] = $value;
				} else {
					$error_array[$key] = $key. " contains ilegal charactors<br>";
				}					
			}
		}
		return $error_array;
	}

	# Purpose: Returns private safe variables
	# Usage  : return("Value");
	# Input	 : Single value;
	# Return : Value requested, or null if it doesn't exist.
	function get($variable) {
		return $this->safe_array[$variable];
	}
}
?>
