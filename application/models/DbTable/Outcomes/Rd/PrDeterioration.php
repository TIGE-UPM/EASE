<?php
	class Model_DbTable_Outcomes_Rd_PrDeterioration extends Zend_Db_Table{
		protected $_name = 'outcomes_round_prDeterioration';
		
		function getDeterioration($game_id, $round_number, $company_id, $factory_number){
			$result=$this->fetchRow('game_id = '.$game_id.' AND round_number = '.$round_number.' AND company_id = '.$company_id.' AND factory_number = '.$factory_number);
			return $result['value'];
		}
		function getDeteriorationByCompany($game_id, $round_number, $company_id){
			$result=$this->fetchAll('game_id = '.$game_id.' AND round_number = '.$round_number.' AND company_id = '.$company_id);
			return $result;
		}
	}
?>