<?php
	class Model_DbTable_Games_Param_Pr_ProductionTimes extends Model_DbTable_Games_Param_Base{
		protected $_name = 'games_param_pr_times';
		
		function add($parameters){		
			$this->insert($parameters);
		}
		function updateProductionTimes($game_id, $data){
			$this->delete('game_id = '.$game_id);
			$productCounter=1;
			$productionTimes=$data['prProductionTimes'];
			while (isset($productionTimes['product_'.$productCounter])){
				$productTimes=implode(";",$productionTimes['product_'.$productCounter]);
				//var_dump($productTimes);die();
				$this->add(array('game_id'=>$game_id, 'product_number'=>$productCounter, 'times'=>$productTimes));			
				$productCounter++;
			}
		}
	}
?>