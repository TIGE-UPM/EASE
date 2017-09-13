<?php
	class Model_DbTable_Games_Param_Mk_TradeMediaCosts extends Model_DbTable_Games_Param_Base{
		protected $_name = 'games_param_mk_costs_trademedia';

		function getCostsByTradeMediaId($trademedia_id){
			$results=$this->fetchRow('trademedia_id = '.$trademedia_id);
			return explode(" ", $results['costs']);			
		}
		
		function add($parameters){		
			$this->insert($parameters);
		}
		function updateTradeMediaCosts($game_id, $data){
			$this->delete('game_id = '.$game_id);
			$trademediaCounter=1;
			$trademediaCosts=$data['mkTradeMediaCosts'];
			while (isset($trademediaCosts['trademedia_'.$trademediaCounter])){
				$regionsCosts=$trademediaCosts['trademedia_'.$trademediaCounter];
				$regionCounter=1;
				while (isset($regionsCosts['region_'.$regionCounter])){
					$costs=implode(';',$regionsCosts['region_'.$regionCounter]);				
					$this->add(array('game_id'=>$game_id, 'trademedia_number'=>$trademediaCounter, 'region_number'=>$regionCounter, 'costs'=>$costs));
					$regionCounter++;
				}
				$trademediaCounter++;
			}
		}
	}
?>