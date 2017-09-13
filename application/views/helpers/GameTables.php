<?php
class Zend_View_Helper_GameTables{
	function gameTables($view=null, $options){
		if (! is_array($options)){
			$options=array($options);
		}
		$table=$options[0];
		switch ($table){
			case "companies":
				$this->companies=$options[1];
				$view=$options[2];
				include "tables/game/".$table.".phtml";
			break;
			default:
				include "tables/game/".$table.".phtml";
			break;
		}
	}	
}

?>