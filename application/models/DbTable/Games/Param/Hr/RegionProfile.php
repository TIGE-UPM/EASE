<?php
	class Model_DbTable_Games_Param_Hr_RegionProfile extends Model_DbTable_Games_Param_Base{
		protected $_name = 'games_param_hr_regionProfile';

		function getProductivityRegion($region_id){
			$results=$this->fetchRow('region_id = '.$region_id);
			return $results['productivity'];			
		}
		
		function getHiringProbabilityInRegion($region_id){
			$results=$this->fetchRow('region_id = '.$region_id);
			return $results['hiring_probability'];			
		}

		function add($parameters){		
			$this->insert($parameters);
		}
		function updateHrProfile($game_id, $data){
			$this->delete('game_id = '.$game_id);
			$hrProfile=$data['hrRegionProfile'];
			$regionCounter=1;
			while (isset($hrProfile['region_'.$regionCounter])){
				$regionProfile=$hrProfile['region_'.$regionCounter];
				$this->add(array('game_id'=>$game_id, 'region_number'=>$regionCounter, 'hiring_probability'=>$regionProfile['hiring_probability'], 
								 'productivity'=>$regionProfile['productivity']));
				$regionCounter++;
			}
		}
	}
?>