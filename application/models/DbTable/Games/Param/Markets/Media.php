<?php
	class Model_DbTable_Games_Param_Markets_Media extends Model_DbTable_Games_Param_Base{
		protected $_name = 'games_param_markets_media';

		function getMediaInRegion($region_id){
			$results=$this->fetchAll('region_id = '.$region_id);
			return $results;			
		}
		
		function getMediaNameById($id){
			$results=$this->fetchRow('id = '.$id);
			return $results['name'];			
		}
		
		function add($parameters){		
			$this->insert($parameters);
		}
		function updateMedia($game_id, $data){
			$this->delete('game_id = '.$game_id);
			$mediaCounter=1;
			while (isset($data['media_'.$mediaCounter])){
				$media=$data['media_'.$mediaCounter];
				$name=$media['name'];
				$media_number=$mediaCounter;
				$weight=$media['media_weight'];
				$this->add(array('game_id'=>$game_id, 'name'=>$name, 'media_number'=>$media_number, 'media_weight'=>$weight));
				$mediaCounter++;
			}
		}
	}
?>