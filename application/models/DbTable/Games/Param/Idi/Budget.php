<?php
	class Model_DbTable_Games_Param_Idi_Budget extends Model_DbTable_Games_Param_Base{
		protected $_name = 'games_param_idi_budget';

		function getProductNumberById($id){
			$results=$this->fetchRow('id = '.$id);
			return $results['product_number'];			
		}
		function getProductBudget($game_id, $product_number){
			$results=$this->fetchRow('game_id = '.$game_id.' AND product_number = '.$product_number);
			return $results['budget'];			
		}
		function add($parameters){					
			$this->insert($parameters);
		}
		function updateBudgets($game_id, $data){
			$this->delete('game_id = '.$game_id);
			$productCounter=1;
			while (isset($data['product_'.$productCounter])){
				$product=$data['product_'.$productCounter];
				$product_number=$productCounter;
				$budget=$product['budget'];
				if($budget==null){
					$budget=0;
				}
				$this->add(array('game_id'=>$game_id, 'product_number'=>$product_number, 'budget'=>$budget));
				$productCounter++;
			}
		}
	}
?>