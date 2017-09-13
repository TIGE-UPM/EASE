<?php
	class Model_DbTable_Games_Param_Markets_Regions extends Model_DbTable_Games_Param_Base{
		protected $_name = 'games_param_markets_regions';


		function getRegionsInGame($game_id){
			$results=$this->fetchAll('game_id = '.$game_id);
			return $results;			
		}
		
		function getNameId($id){
			$region=$this->fetchRow('id = '.$id)->toArray();
			return $region['name'];
		}

		function add($parameters){		
			$this->insert($parameters);
		}
		function updateRegions($game_id, $data){
			$this->delete('game_id = '.$game_id);
			$regionCounter=1;
			while (isset($data['region_'.$regionCounter])){
				$region=$data['region_'.$regionCounter];
				$name=$region['name'];
				$region_number=$regionCounter;
				$this->add(array('game_id'=>$game_id, 'name'=>$name, 'region_number'=>$region_number));
				$regionCounter++;
			}
		}
	}
?>