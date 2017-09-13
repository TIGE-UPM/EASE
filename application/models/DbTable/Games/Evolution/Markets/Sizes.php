<?php
	class Model_DbTable_Games_Evolution_Markets_Sizes extends Model_DbTable_Games_Evolution_Base{
		protected $_name = 'games_evolution_markets_sizes';

		function getEvolutionByProductAndRegionAndRound($product_id, $region_id, $round_number){
			$results=$this->fetchRow('region_number = '.$region_id.' AND product_number = '.$product_id.' AND round_number = '.$round_number);
			return $results['evolution'];			
		}
		
		function add($parameters){		
			$this->insert($parameters);
		}
		function updateMarketSizesEvolution($game_id, $data){
			$this->delete('game_id = '.$game_id);
			$roundCounter=2;
			$decision=$data['marketSizeEvolution'];
			while (isset($decision['round_'.$roundCounter])){
				$aux=$decision['round_'.$roundCounter];
				$productCounter=1;
				while (isset($aux['product_'.$productCounter])){
					$product_marketSizes=$aux['product_'.$productCounter];
					$regionCounter=1;
					while (isset($product_marketSizes['region_'.$regionCounter])){
						$evolution=$product_marketSizes['region_'.$regionCounter];
						$this->add(array('game_id'=>$game_id, 'round_number'=>$roundCounter, 'product_number'=>$productCounter, 'region_number'=>$regionCounter, 'evolution'=>$evolution));
						$regionCounter++;
					}
					$productCounter++;
				}
				$roundCounter++;
			}
		}
	}
?>