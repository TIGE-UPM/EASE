<?php
	class Model_DbTable_Games_Param_Markets_Sizes extends Model_DbTable_Games_Param_Base{
		protected $_name = 'games_param_markets_sizes';

		function getSizeByProductAndRegionAndRound($product_id, $region_id, $round_number){
			$results=$this->fetchRow('region_number = '.$region_id.' AND product_number = '.$product_id.' AND round_number = '.$round_number);
			return $results['size'];			
		}
		
		function add($parameters){		
			$this->insert($parameters);
		}
		function updateMarketSizes($game_id, $data){
			$this->delete('game_id = '.$game_id);
			$productCounter=1;
			$decision=$data['marketSize']['round_1'];
			while (isset($decision['product_'.$productCounter])){
				$product_marketSizes=$decision['product_'.$productCounter];
				$regionCounter=1;
				while (isset($product_marketSizes['region_'.$regionCounter])){
					$marketSize=$product_marketSizes['region_'.$regionCounter];
					$this->add(array('game_id'=>$game_id, 'round_number'=>1, 'product_number'=>$productCounter, 'region_number'=>$regionCounter, 'size'=>$marketSize));
					$regionCounter++;
				}
				$productCounter++;
			}
		}
		
		function setRoundMarketSizes($game_id, $round_number, $product_number, $region_number, $market_size){
			$this->delete(array('game_id = '.$game_id, 'round_number = '.$round_number, 'product_number = '.$product_number, 'region_number = '.$region_number));
			$this->add(array('game_id'=>$game_id, 'round_number'=>$round_number, 'product_number'=>$product_number, 'region_number'=>$region_number, 'size'=>$market_size));
		}
	}
?>