<?php
	class Model_Simulation_Channel extends Model_Simulation_SimulationObject{
		protected $_channel_number;
		function __construct($game_id, $round_number, $channel_number){
			$this->_game_id=$game_id;
			$this->_round_number=$round_number;
			$this->_channel_number=$channel_number;
		}
		function getChannelNumber(){
			return $this->_channel_number;
		}	
	}
?>