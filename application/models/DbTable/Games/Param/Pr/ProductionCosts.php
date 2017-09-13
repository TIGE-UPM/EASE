<?php
	class Model_DbTable_Games_Param_Pr_ProductionCosts extends Model_DbTable_Games_Param_Base{
		protected $_name = 'games_param_pr_production_costs';
		
		function add($parameters){		
			$this->insert($parameters);
		}
		function updateProductionCosts($game_id, $data){
			$this->delete('game_id = '.$game_id);
			$regionCounter=1;
			$productionCosts=$data['prProductionCosts']['round_1'];
			while (isset($productionCosts['region_'.$regionCounter])){
				$regionCosts=$productionCosts['region_'.$regionCounter];
				$fixed=$regionCosts['fixed'];
				$this->add(array('game_id'=>$game_id, 'round_number'=>1, 'region_number'=>$regionCounter, 'cost_type'=>'fixed', 'cost'=>$fixed, 'product_number'=>0));			
				$unit=$regionCosts['unit'];
				$productCounter=1;
				while (isset($unit['product_'.$productCounter])){
					$cost=$unit['product_'.$productCounter];
					$this->add(array('game_id'=>$game_id, 'round_number'=>1, 'region_number'=>$regionCounter, 'cost_type'=>'unit', 'cost'=>$cost, 'product_number'=>$productCounter));
					$productCounter++;
				}
				$regionCounter++;
			}
		}
		
		function setRoundProductionCosts($game_id, $round_number, $cost_type, $region_number, $product_number, $cost){
			$this->delete(array('game_id = '.$game_id, 'round_number = '.$round_number, 'region_number = '.$region_number, 'product_number = '.$product_number));
			$this->add(array('game_id'=>$game_id, 'round_number'=>$round_number, 'cost_type'=>$cost_type, 'region_number'=>$region_number, 'product_number'=>$product_number, 'cost'=>$cost));
		}
	}
?>