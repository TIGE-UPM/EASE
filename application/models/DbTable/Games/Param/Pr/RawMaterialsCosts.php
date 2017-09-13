<?php
	class Model_DbTable_Games_Param_Pr_RawMaterialsCosts extends Model_DbTable_Games_Param_Base{
		protected $_name = 'games_param_pr_rawMaterials';
		
		function add($parameters){		
			$this->insert($parameters);
		}
		function updateRawMaterialsCosts($game_id, $data){
			$this->delete('game_id = '.$game_id);
			$productCounter=1;
			$rawMaterialsCosts=$data['prRawMaterialsCosts']['round_1'];
			while (isset($rawMaterialsCosts['product_'.$productCounter])){
				$productCosts=$rawMaterialsCosts['product_'.$productCounter];
				$base=$productCosts['basePrice'];
				$increment=$productCosts['incrementPerSupplier'];
				$this->add(array('game_id'=>$game_id, 'round_number'=>1, 'product_number'=>$productCounter, 'base'=>$base, 
								 'increment_per_supplier'=>$increment));			
				$productCounter++;
			}
		}
		function setRoundRawMaterialsCosts($game_id, $round_number, $product_number, $base, $increment){
			$this->delete(array('game_id = '.$game_id, 'round_number = '.$round_number, 'product_number = '.$product_number));
			$this->add(array('game_id'=>$game_id, 'round_number'=>$round_number, 'product_number'=>$product_number, 'base'=>$base, 'increment_per_supplier'=>$increment));
		}
	}
?>