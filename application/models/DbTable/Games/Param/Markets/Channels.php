<?php
	class Model_DbTable_Games_Param_Markets_Channels extends Model_DbTable_Games_Param_Base{
		protected $_name = 'games_param_markets_channels';

		function getChannelsInRegion($region_id){
			$results=$this->fetchAll('region_id = '.$region_id);
			return $results;			
		}
		
		function getChannelNameById($id){
			$results=$this->fetchRow('id = '.$id);
			return $results['name'];			
		}
		
		function add($parameters){		
			$this->insert($parameters);
		}
		function updateChannels($game_id, $data){
			$this->delete('game_id = '.$game_id);
			$channelCounter=1;
			while (isset($data['channel_'.$channelCounter])){
				$channel=$data['channel_'.$channelCounter];
				$name=$channel['name'];
				$channel_number=$channelCounter;
				$weight=$channel['channel_weight'];
				$this->add(array('game_id'=>$game_id, 'name'=>$name, 'channel_number'=>$channel_number, 'channel_weight'=>$weight));
				$channelCounter++;
			}
		}
	}
?>