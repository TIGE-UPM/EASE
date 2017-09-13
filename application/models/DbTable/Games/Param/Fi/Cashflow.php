<?php
	class Model_DbTable_Games_Param_Fi_Cashflow extends Model_DbTable_Games_Param_Base{
		protected $_name = 'games_param_fi_cashflow';

		function getStartingCashInGame($game_id){
			$results=$this->fetchRow('game_id = '.$game_id);
			return $results['starting_cash'];			
		}
		
		function getShortTermDebtRateInGame($game_id){
			$results=$this->fetchRow('game_id = '.$game_id);
			return $results['short_term_debt_rate'];			
		}		


		function updateCashflowParameters($game_id, $data){
			$this->delete('game_id = '.$game_id);
			$cashflowParameters=$data['fiCashflowParameters'];				
			$this->add(array('game_id'=>$game_id, 'starting_cash'=>$cashflowParameters['starting_cash'], 
							 'short_term_debt_rate'=>$cashflowParameters['short_term_debt_rate']));		
		}

	}
?>