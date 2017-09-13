<?php
	class Model_DbTable_Games_Param_Markets_Prices extends Model_DbTable_Games_Param_Base{
		protected $_name = 'games_param_markets_prices';

		function getIdealPriceByProductAndChannel($product_id, $channel_id){
			$results=$this->fetchRow('channel_id = '.$channel_id.' AND product_id ='. $product_id);
			return $results['ideal_price'];			
		}
		
		function getMaxPriceByProductAndChannel($product_id, $channel_id){
			$results=$this->fetchRow('channel_id = '.$channel_id.' AND product_id ='. $product_id);
			return $results['max_price'];			
		}

		function add($parameters){		
			$this->insert($parameters);
		}
		function updateMarketPrices($game_id, $data){
			$this->delete('game_id = '.$game_id);
			$productCounter=1;
			$marketPrices=$data['marketProductPrices']['round_1'];
			while (isset($marketPrices['product_'.$productCounter])){
				$aux=$marketPrices['product_'.$productCounter];
				$channelCounter=1;
				while (isset($aux['channel_'.$channelCounter])){
					$decision=$aux['channel_'.$channelCounter];
					$regionCounter=1;
					while (isset($decision['region_'.$regionCounter])){
						$ideal_price=$decision['region_'.$regionCounter]['idealPrice'];
						$max_price=$decision['region_'.$regionCounter]['maxPrice'];
						$this->add(array('game_id'=>$game_id, 'round_number'=>1, 'product_number'=>$productCounter, 'channel_number'=>$channelCounter, 'region_number'=>$regionCounter, 'max_price'=>$max_price, 'ideal_price'=>$ideal_price));
						$regionCounter++;
					}
					$channelCounter++;
				}				
				$productCounter++;
			}
		}
		function setRoundMarketPrices($game_id, $round_number, $product_number, $region_number, $channel_number, $ideal_price, $max_price){
			$this->delete(array('game_id = '.$game_id, 'round_number = '.$round_number, 'product_number = '.$product_number, 'channel_number = '.$channel_number, 'region_number = '.$region_number));
			$this->add(array('game_id'=>$game_id, 'round_number'=>$round_number, 'product_number'=>$product_number, 'channel_number'=>$channel_number, 'region_number'=>$region_number, 'max_price'=>$max_price, 'ideal_price'=>$ideal_price));
		}
	}
?>