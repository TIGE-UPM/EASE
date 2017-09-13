<?php
	class Model_DbTable_Games_Param_Mk_ChannelsCosts extends Model_DbTable_Games_Param_Base{
		protected $_name = 'games_param_mk_costs_channels';

		function getFixedCostByChannelId($channel_id){
			$results=$this->fetchRow('channel_id = '.$channel_id);
			return $results['fixed_cost'];			
		}
		
		function getFareCostByChannelId($channel_id){
			$results=$this->fetchRow('channel_id = '.$channel_id);
			return $results['fare_cost'];			
		}
		
		function add($parameters){		
			$this->insert($parameters);
		}
		function updateChannelsCosts($game_id, $data){
			$this->delete('game_id = '.$game_id);
			$channelCounter=1;
			$channelsCosts=$data['mkChannelsCosts']['round_1'];
			while (isset($channelsCosts['channel_'.$channelCounter])){
				$regionsCosts=$channelsCosts['channel_'.$channelCounter];
				$regionCounter=1;
				while (isset($regionsCosts['region_'.$regionCounter])){
					$costs=$regionsCosts['region_'.$regionCounter];
					$this->add(array('game_id'=>$game_id, 'round_number'=>1, 'channel_number'=>$channelCounter, 'region_number'=>$regionCounter, 
									 'fixed_cost'=>$costs['fixed'], 'fare_cost'=>$costs['variable']));
					$regionCounter++;
				}
				$channelCounter++;
			}
		}
		function setRoundChannelsCosts($game_id, $round_number, $channel_number, $region_number, $fixed_cost, $fare_cost){
			$this->delete(array('game_id = '.$game_id, 'round_number = '.$round_number, 'channel_number = '.$channel_number, 'region_number = '.$region_number));
			$this->add(array('game_id'=>$game_id, 'round_number'=>$round_number, 'channel_number'=>$channel_number, 'region_number'=>$region_number, 'fixed_cost'=>$fixed_cost, 'fare_cost'=>$fare_cost));
		}
	}
?>