<?php
	class Model_DbTable_Games_Evolution_Markets_TradeMediaProfile extends Model_DbTable_Games_Evolution_Base{
		protected $_name = 'games_evolution_markets_tradeMedia_profile';

		function getIdealIntensityByTradeMediaAndProductAndRound($tradeMedia_id, $product_id, $round_number){
			$results=$this->fetchRow('tradeMedia_number = '.$tradeMedia_id.' AND product_number ='.$product_id.' AND round_number ='.$round_number);
			return $results['evolution'];			
		}

		function add($parameters){		
			$this->insert($parameters);
		}
		
		function updateTradeMediaProfileEvolution($game_id, $data){
			$this->delete('game_id = '.$game_id);
			$roundCounter=2;
			$tradeMediaWeightEvolution=$data['marketTradeMediaWeightEvolution'];
			while (isset($tradeMediaWeightEvolution['round_'.$roundCounter])){
				$productCounter=1;
				while (isset($tradeMediaWeightEvolution['round_'.$roundCounter]['product_'.$productCounter])){
					$tradeMediaCounter=1;
					while (isset($tradeMediaWeightEvolution['round_'.$roundCounter]['product_'.$productCounter]['trade_media_'.$tradeMediaCounter])){
						$channelCounter=1;
						while (isset($tradeMediaWeightEvolution['round_'.$roundCounter]['product_'.$productCounter]['trade_media_'.$tradeMediaCounter]['channel_'.$channelCounter])){
							$evolution=$tradeMediaWeightEvolution['round_'.$roundCounter]['product_'.$productCounter]['trade_media_'.$tradeMediaCounter]['channel_'.$channelCounter];
							$this->add(array('game_id'=>$game_id, 'round_number'=>$roundCounter, 'product_number'=>$productCounter, 'tradeMedia_number'=>$tradeMediaCounter, 'channel_number'=>$channelCounter, 'evolution'=>$evolution));
							$channelCounter++;
						}
						$tradeMediaCounter++;
					}
					$productCounter++;
				}
				$roundCounter++;
			}
		}
	}
?>