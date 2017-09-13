<?php
	class Model_DbTable_Games_Evolution_Pr_RawMaterialsCosts extends Model_DbTable_Games_Evolution_Base{
		protected $_name = 'games_evolution_pr_rawMaterials';
		
		function add($parameters){		
			$this->insert($parameters);
		}
		function updateRawMaterialsCostsEvolution($game_id, $data){
			$this->delete('game_id = '.$game_id);
			$rawMaterialsCostsEvolution=$data['prRawMaterialsCostsEvolution'];
			$roundCounter=2;
			while (isset($rawMaterialsCostsEvolution['round_'.$roundCounter])){
				$rawMaterialsRound=$rawMaterialsCostsEvolution['round_'.$roundCounter];
				$productCounter=1;
				while (isset($rawMaterialsRound['product_'.$productCounter])){
					$productCosts=$rawMaterialsRound['product_'.$productCounter];
					$evolution=$productCosts['evolution'];
					$this->add(array('game_id'=>$game_id, 'round_number'=>$roundCounter, 'product_number'=>$productCounter, 'evolution'=>$evolution));			
					$productCounter++;
				}
				$roundCounter++;
			}
		}
	}
?>