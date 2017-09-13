<?php
	class Model_DbTable_Games_Param_Markets_ChannelsWeights extends Model_DbTable_Games_Param_Base{
		protected $_name = 'games_param_markets_channels_weights';
		
		function add($parameters){		
			$this->insert($parameters);
		}
		function updateChannelWeights($game_id, $data){
			$this->delete('game_id = '.$game_id);
			$channelWeights=$data['channelWeight'];
			$roundCounter=1;
			while (isset($channelWeights['round_'.$roundCounter])){
				$productCounter=1;
				while (isset($channelWeights['round_'.$roundCounter]['product_'.$productCounter])){
					$channelCounter=1;
					while (isset($channelWeights['round_'.$roundCounter]['product_'.$productCounter]['channel_'.$channelCounter])){
						$regionCounter=1;
						while (isset($channelWeights['round_'.$roundCounter]['product_'.$productCounter]['channel_'.$channelCounter]['region_'.$regionCounter])){
							$weight=$channelWeights['round_'.$roundCounter]['product_'.$productCounter]['channel_'.$channelCounter]['region_'.$regionCounter];
							$this->add(array('game_id'=>$game_id, 'round_number'=>$roundCounter, 'product_number'=>$productCounter, 'channel_number'=>$channelCounter, 'region_number'=>$regionCounter, 'weight'=>$weight));
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