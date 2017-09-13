<?php
	class Model_DbTable_Games_Evolution_Markets_Prices extends Model_DbTable_Games_Evolution_Base{
		protected $_name = 'games_evolution_markets_prices';

		function getPriceEvolutionByProductAndChannelAndRound($product_id, $channel_id, $round_number){
			$results=$this->fetchRow('channel_id = '.$channel_id.' AND product_id ='. $product_id.' AND round_number = '.$round_number);
			return $results['evolution'];			
		}

		function add($parameters){		
			$this->insert($parameters);
		}
		
		function updateMarketPricesEvolution($game_id, $data){
			$this->delete('game_id = '.$game_id);
			$marketPricesEvolution=$data['marketProductPricesEvolution'];
			$roundCounter=2;
			while (isset($marketPricesEvolution['round_'.$roundCounter])){
				$marketPrices=$marketPricesEvolution['round_'.$roundCounter];
				$productCounter=1;
				while (isset($marketPrices['product_'.$productCounter])){
					$aux=$marketPrices['product_'.$productCounter];
					$channelCounter=1;
					while (isset($aux['channel_'.$channelCounter])){
						$decision=$aux['channel_'.$channelCounter];
						$regionCounter=1;
						while (isset($decision['region_'.$regionCounter])){
							$evolution=$decision['region_'.$regionCounter]['evolution'];
							$this->add(array('game_id'=>$game_id, 'round_number'=>$roundCounter, 'product_number'=>$productCounter, 'channel_number'=>$channelCounter, 'region_number'=>$regionCounter, 'evolution'=>$evolution));
							$regionCounter++;
						}
						$channelCounter++;
					}				
					$productCounter++;
				}
				$roundCounter++;
			}
		}
	}
?>