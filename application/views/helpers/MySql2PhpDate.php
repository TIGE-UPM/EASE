<?php
class Zend_View_Helper_MySql2PhpDate{
	function mySql2PhpDate($date){
		$date_time=explode(' ', $date);
		$unformatted_day_array=explode('-', $date_time[0]);
		$formatted_date=$unformatted_day_array[2].'-'.$unformatted_day_array[1].'-'$unformatted_day_array[0];
		return $formatted_date;
	}
}

?>