<?php
	class Model_DbTable_Games_Evolution_Markets_TaxRates extends Model_DbTable_Games_Evolution_Base{
		protected $_name = 'games_evolution_markets_taxrates';

		function getEvolutionByRegionAndRound($region_id, $round_number){
			$results=$this->fetchRow('region_number = '.$region_id.' AND round_number = '.$round_number);
			return $results['evolution'];			
		}
		
		function add($parameters){		
			$this->insert($parameters);
		}
		function updateMarketTaxRateEvolution($game_id, $data){
			$this->delete('game_id = '.$game_id);
			$roundCounter=2;
			$decision=$data['marketTaxRateEvolution'];
			while (isset($decision['round_'.$roundCounter])){
				
				
					$marketIR=$decision['round_'.$roundCounter];
					$regionCounter=1;
					while (isset($marketIR['region_'.$regionCounter])){
						$evolution=$marketIR['region_'.$regionCounter];
						$this->add(array('game_id'=>$game_id, 'round_number'=>$roundCounter,'region_number'=>$regionCounter, 'evolution'=>$evolution));
						$regionCounter++;
					
				}
				$roundCounter++;
			}
		}
	}
?>