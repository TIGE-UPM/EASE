<?php
	class Model_DbTable_Games_Param_Markets_MarketResearches extends Model_DbTable_Games_Param_Base{
		protected $_name = 'games_param_markets_marketresearches';
		
		function add($parameters){					
			$this->insert($parameters);
		}
		
		function updateMarketResearches($game_id, $data){
			$this->delete('game_id = '.$game_id);
			$mrCounter=1;
			while (isset($data['marketResearch_'.$mrCounter])){
				$researchCost=$data['marketResearch_'.$mrCounter];
				$this->add(array('game_id'=>$game_id, 'research_number'=>$mrCounter, 'cost'=>$researchCost));
				$mrCounter++;
			}
		}
		
		function getMarketResearchesCosts($game_id){
			$array=array();
			$results=$this->fetchAll('game_id = '.$game_id, 'research_number ASC');
			$research_number=1;
			foreach ($results as $result){
				$array['marketResearch_number_'.$research_number]=$result['cost'];
				$research_number++;
			}
			return $array;
		}
	}