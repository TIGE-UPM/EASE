<?php
	class Model_DbTable_Games_Param_Mk_TradeMediaRegion extends Model_DbTable_Games_Param_Base{
		protected $_name = 'games_param_mk_trademediaregion';

		function getRegionDistribution($game_id, $region_number){
			$decisions=$this->fetchRow('game_id = '.$game_id.						   
								      ' AND region_number = '.$region_number);
			
			return $decisions['region_weight'];			
		}

		function add($parameters){		
			$this->insert($parameters);
		}
		function updateRegionDistribution($game_id, $data){
			$this->delete('game_id = '.$game_id);
			$regionCounter=1;
			
			while (isset($data['region_'.$regionCounter])){
				$region=$data['region_'.$regionCounter];
				$regionDis=$region['region_weight'];
				$region_number=$regionCounter;
				$this->add(array('game_id'=>$game_id, 'region_number'=>$region_number, 'region_weight'=>$regionDis));
				$regionCounter++;
			}
			
		}
	}
?>