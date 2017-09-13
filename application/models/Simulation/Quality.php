<?php
/*Convendría optimizar minimizando el número de accesos a BD, cacheando en variables inicializadas en los init*/
	class Model_Simulation_Quality extends Model_Simulation_SimulationObject{
		
		protected $_quality_param_number;
		protected $_quality_param_weight;
		
		function __construct($core, $game_id, $round_number, $product_number){
			$this->_core=$core;
			$this->_game_id=$game_id;
			$this->_round_number=$round_number;
			$this->_quality_param_number=$quality_param_number;

		}
		function getQualityParamNumber(){
			return $this->_quality_param_number;
		}
	}
?>