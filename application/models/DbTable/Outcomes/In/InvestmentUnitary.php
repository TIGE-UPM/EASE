<?php
	class Model_DbTable_Outcomes_In_InvestmentUnitary extends Zend_Db_Table{
		protected $_name = 'outcomes_investment_unitary';
		
		function getInvestment($game_id, $company_id, $round_number, $investment_number){
			$result=$this->fetchRow('game_id = '.$game_id.' AND company_id = '.$company_id.' AND round_number = '.$round_number.' AND investment_number = '.$investment_number);
			return $result['result'];
		}

		function setInvestment($game_id, $company_id, $round_number, $investment_number, $result){
			$this->insert(array('game_id'=>$game_id, 'company_id'=>$company_id, 'round_number'=>$round_number, 'investment_number'=>$investment_number, 'result'=>$result));

		}
	}
?>