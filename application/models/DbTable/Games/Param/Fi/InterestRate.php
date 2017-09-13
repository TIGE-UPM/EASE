<?php
	class Model_DbTable_Games_Param_Fi_InterestRate extends Model_DbTable_Games_Param_Base{
		protected $_name = 'games_param_fi_interest_rate';

		function getInterestRate($game_id){
			$results=$this->fetchRow('game_id = '.$game_id);
			return $results['interest_rate'];			
		}	

		function updateInterestRateParameters($game_id, $data){
			$this->delete('game_id = '.$game_id);
			$interestRateParameters=$data['interestRateParameters'];				
			//var_dump($interestRateParameters);die();
			$term=2;
			$this->add(array('game_id'=>$game_id, 'term'=>$term, 'interest_rate'=>$interestRateParameters['interest_rate']['two']));
			$term++;
			$this->add(array('game_id'=>$game_id, 'term'=>$term, 'interest_rate'=>$interestRateParameters['interest_rate']['tree']));
			$term++;
			$this->add(array('game_id'=>$game_id, 'term'=>$term, 'interest_rate'=>$interestRateParameters['interest_rate']['four']));		
		}

	}
?>