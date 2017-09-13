<?php
	class Model_DbTable_Games_Evolution_Mk_ChannelsCosts extends Model_DbTable_Games_Evolution_Base{
		protected $_name = 'games_evolution_mk_costs_channels';

		function add($parameters){		
			$this->insert($parameters);
		}
		function updateChannelsCostsEvolution($game_id, $data){
			$this->delete('game_id = '.$game_id);
			$roundCounter=2;
			$channelsCostsEvolution=$data['mkChannelsCostsEvolution'];
			while (isset($channelsCostsEvolution['round_'.$roundCounter])){
				$channelCounter=1;
				$channelsCosts=$channelsCostsEvolution['round_'.$roundCounter];
				while (isset($channelsCosts['channel_'.$channelCounter])){
					$regionsCosts=$channelsCosts['channel_'.$channelCounter];
					$regionCounter=1;
					while (isset($regionsCosts['region_'.$regionCounter])){
						$evolution=$regionsCosts['region_'.$regionCounter];
						$this->add(array('game_id'=>$game_id, 'round_number'=>$roundCounter, 'channel_number'=>$channelCounter, 'region_number'=>$regionCounter, 
										 'evolution'=>$evolution['evolution']));
						$regionCounter++;
					}
					$channelCounter++;
				}
				$roundCounter++;
			}
		}
	}
?>