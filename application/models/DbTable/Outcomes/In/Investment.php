<?php
	class Model_DbTable_Outcomes_In_Investment extends Zend_Db_Table{
		protected $_name = 'outcomes_investment';
		function getInvestment($game_id, $round_number){
			$results=$this->fetchAll('game_id = '.$game_id.' AND round_number = '.$round_number);
			foreach ($results as $result){
				$investment[$result['company_id']][$result['type']]=$result['investment'];
			}
			return $investment;			
		}

		function setInvestment($game_id, $round_number, $company_id, $type, $result){
			$this->insert(array('game_id'=>$game_id,
								 'company_id'=>$company_id,	  
								 'round_number'=>$round_number,																
								 'type'=>$type,
								 'investment'=>$result));
		}

		function getInvestmentByCompany($game_id, $round_number, $company_id){
			$results=$this->fetchAll('game_id = '.$game_id.' AND company_id = '.$company_id.' AND round_number = '.$round_number);
			foreach ($results as $result){
				$investment[$result['type']]=$result['investment'];
			}
			return $investment;			
		}
		
	}
?>