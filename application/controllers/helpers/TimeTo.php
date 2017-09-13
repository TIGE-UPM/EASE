<?php
class Zend_Controller_Action_Helper_TimeTo extends Zend_Controller_Action_Helper_Abstract{
	function timeTo($fromTime, $toTime = 0){
		    $distanceInSeconds = round(abs($toTime - $fromTime));
		    $distanceInMinutes = round($distanceInSeconds / 60);
			if ($ditanceInMinutes>60){
				return 'más de una hora';
			}
			else{
				if ($distanceInMinutes>1){
					return $distanceInMinutes.' minutos';
				}
				else{
					return $distanceInSeconds.' segundos';
				}
			}
	}	
}

?>