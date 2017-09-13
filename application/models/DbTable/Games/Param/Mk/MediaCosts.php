<?php
	class Model_DbTable_Games_Param_Mk_MediaCosts extends Model_DbTable_Games_Param_Base{
		protected $_name = 'games_param_mk_costs_media';

		function getCostsByMediaId($media_id){
			$results=$this->fetchRow('media_id = '.$media_id);
			return explode(" ", $results['costs']);			
		}
		
		function add($parameters){		
			$this->insert($parameters);
		}
		function updateMediaCosts($game_id, $data){
			$this->delete('game_id = '.$game_id);
			$mediaCounter=1;
			$mediaCosts=$data['mkMediaCosts'];
			while (isset($mediaCosts['media_'.$mediaCounter])){
				$regionsCosts=$mediaCosts['media_'.$mediaCounter];
				$regionCounter=1;
				while (isset($regionsCosts['region_'.$regionCounter])){
					$costs=implode(';',$regionsCosts['region_'.$regionCounter]);				
					$this->add(array('game_id'=>$game_id, 'media_number'=>$mediaCounter, 'region_number'=>$regionCounter, 'costs'=>$costs));
					$regionCounter++;
				}
				$mediaCounter++;
			}
		}
	}
?>