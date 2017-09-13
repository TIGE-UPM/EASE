<?php
class Zend_View_Helper_Short{
	function short($string){
		if (isset ($string) && strlen($string)>40){
			return substr($string, 0, 35).'[...]';
		}
		return $string;
	}
}

?>