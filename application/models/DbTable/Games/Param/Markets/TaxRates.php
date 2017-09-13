<?php
	class Model_DbTable_Games_Param_Markets_TaxRates extends Model_DbTable_Games_Param_Base{
		protected $_name = 'games_param_markets_taxrates';

		function getTaxRatebyRegionAndRound($game_id, $round_number, $region_id){
			$results=$this->fetchRow('game_id = '.$game_id.' AND region_number = '.$region_id.' AND round_number = '.$round_number);
			return $results['tax_rate'];	
			
		}
		
		function add($parameters){		
			$this->insert($parameters);
		}
		function updateMarketTaxRates($game_id, $data){
			$this->delete('game_id = '.$game_id);
			
			$decision=$data['marketTaxRate']['round_1'];
				
				$regionCounter=1;
				while (isset($decision['region_'.$regionCounter])){
					$marketIR=$decision['region_'.$regionCounter];
					$this->add(array('game_id'=>$game_id, 'round_number'=>1, 'region_number'=>$regionCounter, 'tax_rate'=>$marketIR));
					$regionCounter++;
				}
				
			
		}
		
		function setRoundMarketTaxRates($game_id, $round_number, $region_number, $market_tr){
			$this->delete(array('game_id = '.$game_id, 'round_number = '.$round_number, 'region_number = '.$region_number));
			$this->add(array('game_id'=>$game_id, 'round_number'=>$round_number, 'region_number'=>$region_number, 'tax_rate'=>$market_tr));
		}
	}
?>