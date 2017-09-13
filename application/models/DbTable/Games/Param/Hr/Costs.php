<?php
	class Model_DbTable_Games_Param_Hr_Costs extends Model_DbTable_Games_Param_Base{
		protected $_name = 'games_param_hr_costs';

		function getHiringCostsInRegion($region_id){
			$results=$this->fetchRow('region_id = '.$region_id);
			return $results['hiring_costs'];			
		}
		
		function getTrainingCostsInRegion($region_id){
			$results=$this->fetchRow('region_id = '.$region_id);
			return $results['training_costs'];			
		}
		
		function getWagesCostsInRegion($region_id){
			$results=$this->fetchRow('region_id = '.$region_id);
			return $results['wages_costs'];			
		}

		function getDismissalsCostsInRegion($region_id){
			$results=$this->fetchRow('region_id = '.$region_id);
			return $results['dismissals_cost'];			
		}

		function add($parameters){		
			$this->insert($parameters);
		}
		function updateHrCosts($game_id, $data){
			$this->delete('game_id = '.$game_id);
			$hrCosts=$data['hrRegionCosts']['round_1'];
			$regionCounter=1;
			while (isset($hrCosts['region_'.$regionCounter])){
				$regionCost=$hrCosts['region_'.$regionCounter];
				$this->add(array('game_id'=>$game_id, 'round_number'=>1, 'region_number'=>$regionCounter, 'hiring_cost'=>$regionCost['hiring'], 
								 'training_cost'=>$regionCost['training'],'wages_cost'=>$regionCost['wages'], 'dismissals_cost'=>$regionCost['dismissals']));
				$regionCounter++;
			}
		}
		function setRoundHumanResourcesCosts($game_id, $round_number, $region_number, $hiring_cost, $training_cost, $wages_cost, $dismissals_cost){
			$this->delete(array('game_id = '.$game_id, 'round_number = '.$round_number, 'region_number = '.$region_number));
			$this->add(array('game_id'=>$game_id, 'round_number'=>$round_number, 'region_number'=>$region_number, 'hiring_cost'=>$hiring_cost, 'training_cost'=>$training_cost, 'wages_cost'=>$wages_cost, 'dismissals_cost'=>$dismissals_cost));
		}
	}
?>