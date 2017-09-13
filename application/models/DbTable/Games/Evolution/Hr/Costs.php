<?php
	class Model_DbTable_Games_Evolution_Hr_Costs extends Model_DbTable_Games_Evolution_Base{
		protected $_name = 'games_evolution_hr_costs';

		function getHiringCostsEvolutionInRegion($region_id){
			$results=$this->fetchRow('region_id = '.$region_id);
			return $results['hiring_costs'];			
		}
		
		function getTrainingCostsEvolutionInRegion($region_id){
			$results=$this->fetchRow('region_id = '.$region_id);
			return $results['training_costs'];			
		}
		
		function getWagesCostsEvolutionInRegion($region_id){
			$results=$this->fetchRow('region_id = '.$region_id);
			return $results['wages_costs'];			
		}

		function add($parameters){		
			$this->insert($parameters);
		}
		function updateHrCostsEvolution($game_id, $data){
			$this->delete('game_id = '.$game_id);
			$hrCostsEvolution=$data['hrRegionCostsEvolution'];
			$roundCounter=2;
			while(isset($hrCostsEvolution['round_'.$roundCounter])){
				$costsEvolution=$hrCostsEvolution['round_'.$roundCounter];
				$regionCounter=1;
				while(isset($costsEvolution['region_'.$regionCounter])){
					$regionCost=$costsEvolution['region_'.$regionCounter];
					$this->add(array('game_id'=>$game_id, 'round_number'=>$roundCounter, 'region_number'=>$regionCounter, 'hiring_cost_evolution'=>$regionCost['hiring_evolution'], 
									 'training_cost_evolution'=>$regionCost['training_evolution'],'wages_cost_evolution'=>$regionCost['wages_evolution']));
					$regionCounter++;
				}
				$roundCounter++;
			}
		}
	}
?>