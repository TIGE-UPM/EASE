<?php
	class Model_DbTable_Games_Param_Mk_ChannelsIncomeTerms extends Model_DbTable_Games_Param_Base{
		protected $_name = 'games_param_mk_channel_incomeTerms';

		function getIncomeTimeByChannelId($channel_id){
			$results=$this->fetchRow('channel_id = '.$channel_id);
			return $results['time'];			
		}

		function add($parameters){		
			$this->insert($parameters);
		}
		function updateChannelsIncomeTerms($game_id, $data){
			$this->delete('game_id = '.$game_id);
			$channelCounter=1;
			$channelsIncomeTerms=$data['mkChannelsIncomeTerms'];
			while (isset($channelsIncomeTerms['channel_'.$channelCounter])){
				$incomeTerms=$channelsIncomeTerms['channel_'.$channelCounter];
				$this->add(array('game_id'=>$game_id, 'channel_number'=>$channelCounter, 'time'=>$incomeTerms));
				$channelCounter++;
			}
		}
	}
?>