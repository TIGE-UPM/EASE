<?php
	class Model_Simulation_Region extends Model_Simulation_SimulationObject{
		protected $_region_number;
		protected $_sizes;
		function __construct($core, $game_id, $round_number, $region_number){
			$this->_core=$core;
			$this->_game_id=$game_id;
			$this->_round_number=$round_number;
			$this->_region_number=$region_number;
		}
		function getRegionNumber(){
			return $this->_region_number;
		}
		
		function setProductionCosts($round_number, $cost_type, $product_number, $cost){
			$prod_costs=new Model_DbTable_Games_Param_Pr_ProductionCosts();
			$prod_costs->setRoundProductionCosts($this->_game_id, $round_number, $cost_type, $this->_region_number, $product_number, $cost);
		}
		function setHumanResourcesCosts($round_number, $hiring_cost, $training_cost, $wages_cost){
			$hr_costs=new Model_DbTable_Games_Param_Hr_Costs();
			$hr_costs->setRoundHumanResourcesCosts($this->_game_id, $round_number, $this->_region_number, $hiring_cost, $training_cost, $wages_cost);
		}
		function setChannelsCosts($round_number, $channel_number, $fixed_cost, $fare_cost){
			$channels_costs=new Model_DbTable_Games_Param_Mk_ChannelsCosts();
			$channels_costs->setRoundChannelsCosts($this->_game_id, $round_number, $channel_number, $this->_region_number, $fixed_cost, $fare_cost);
		}
	}
?>