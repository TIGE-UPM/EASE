<?php
	class Model_DbTable_Games_Evolution_Pr_ProductionCosts extends Model_DbTable_Games_Evolution_Base{
		protected $_name = 'games_evolution_pr_production_costs';
		
		function add($parameters){		
			$this->insert($parameters);
		}
		function updateProductionCostsEvolution($game_id, $data){
			$this->delete('game_id = '.$game_id);
			$roundCounter=2;
			$productionCostsEvolution=$data['prProductionCostsEvolution'];
			while (isset($productionCostsEvolution['round_'.$roundCounter])){
				$regionCounter=1;
				while (isset($productionCostsEvolution['round_'.$roundCounter]['region_'.$regionCounter])){
					$regionCosts=$productionCostsEvolution['round_'.$roundCounter]['region_'.$regionCounter];
					$evolutionF=$regionCosts['fixed'];
					$this->add(array('game_id'=>$game_id, 'round_number'=>$roundCounter, 'region_number'=>$regionCounter, 'cost_type'=>'fixed', 'evolution'=>$evolutionF, 'product_number'=>0));			
					$evolutionU=$regionCosts['unit'];
					$productCounter=1;
					while (isset($evolutionU['product_'.$productCounter])){
						$cost=$evolutionU['product_'.$productCounter];
						$this->add(array('game_id'=>$game_id, 'round_number'=>$roundCounter, 'region_number'=>$regionCounter, 'cost_type'=>'unit', 'evolution'=>$cost, 'product_number'=>$productCounter));
						$productCounter++;
					}
					$regionCounter++;
				}
				$roundCounter++;
			}
		}
	}
?>