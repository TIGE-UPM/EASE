<?php
	class Model_Simulation_Media extends Model_Simulation_SimulationObject{
		protected $_media_number;
		function __construct($game_id, $round_number, $media_number){
			$this->_game_id=$game_id;
			$this->_round_number=$round_number;
			$this->_media_number=$media_number;
		}
		function getMediaNumber(){
			return $this->_media_number;
		}
	
	}
?>