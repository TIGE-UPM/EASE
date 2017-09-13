<?php
	class Model_DbTable_Games_Param_Markets_TradeMediaProfile extends Model_DbTable_Games_Param_Base{
		protected $_name = 'games_param_markets_tradeMedia_profile';

		function getIdealIntensityByTradeMediaAndProductAndRound($tradeMedia_id, $product_id, $round_number){
			$results=$this->fetchRow('tradeMedia_number = '.$tradeMedia_id.' AND product_number ='.$product_id.' AND round_number ='.$round_number);
			return $results['ideal_intensity'];			
		}

		function add($parameters){		
			$this->insert($parameters);
		}
		
		function updateTradeMediaProfile($game_id, $data){
			$this->delete('game_id = '.$game_id);
			$productCounter=1;
			$tradeMediaWeight=$data['marketTradeMediaWeight']['round_1'];
			
			while (isset($tradeMediaWeight['product_'.$productCounter])){
				$tradeMediaCounter=1;
				while (isset($tradeMediaWeight['product_'.$productCounter]['trade_media_'.$tradeMediaCounter])){
					$channelCounter=1;
					while (isset($tradeMediaWeight['product_'.$productCounter]['trade_media_'.$tradeMediaCounter]['channel_'.$channelCounter])){
						$ideal_intensity=$tradeMediaWeight['product_'.$productCounter]['trade_media_'.$tradeMediaCounter]['channel_'.$channelCounter];
						$this->add(array('game_id'=>$game_id, 'round_number'=>1, 'product_number'=>$productCounter, 'tradeMedia_number'=>$tradeMediaCounter, 'channel_number'=>$channelCounter, 'ideal_intensity'=>$ideal_intensity));
						$channelCounter++;
					}
					$tradeMediaCounter++;
				}				
				$productCounter++;
			}
		}
		
		function setRoundMarketTradeMktIntensity($game_id, $round_number, $product_number, $channel_number, $trademedia_number, $tradeMkt_intensity){
			$this->delete(array('game_id = '.$game_id, 'round_number = '.$round_number, 'product_number = '.$product_number, 'tradeMedia_number = '.$trademedia_number, 'channel_number = '.$channel_number));
			$this->add(array('game_id'=>$game_id, 'round_number'=>$round_number, 'product_number'=>$product_number, 'tradeMedia_number'=>$trademedia_number, 'channel_number'=>$channel_number, 'ideal_intensity'=>$tradeMkt_intensity));
		}
	}
?>