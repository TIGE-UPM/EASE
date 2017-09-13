<?php
	class Model_DbTable_Games_Param_Idi_Threshold extends Model_DbTable_Games_Param_Base{
		protected $_name = 'games_param_idi_threshold';

		function getProductNumberById($id){
			$results=$this->fetchRow('id = '.$id);
			return $results['product_number'];			
		}
		function getProductThreshold($game_id, $product_number){
			$results=$this->fetchRow('game_id = '.$game_id.' AND product_number = '.$product_number);
			return $results['threshold'];			
		}
		function add($parameters){					
			$this->insert($parameters);
		}
		//Para el futuro
		function updateThreshold($game_id, $data){
			$this->delete('game_id = '.$game_id);
			$productCounter=1;
			while (isset($data['product_'.$productCounter])){
				$product=$data['product_'.$productCounter];
				$product_number=$productCounter;
				$threshold=$product['threshold'];
				if($threshold==null){
					$threshold=0;
				}
				$this->add(array('game_id'=>$game_id, 'product_number'=>$product_number, 'threshold'=>$threshold));
				$productCounter++;
			}
		}
	}
?>