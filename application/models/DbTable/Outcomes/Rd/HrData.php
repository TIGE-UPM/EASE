<?php
	class Model_DbTable_Outcomes_Rd_HrData extends Zend_Db_Table{
		protected $_name = 'outcomes_round_hrData';
		
		function getHrDataCompanyAtmosphere($game_id, $round_number, $company_id){
			$result=$this->fetchRow('game_id = '.$game_id.' AND round_number = '.$round_number.' AND company_id = '.$company_id.' AND type = "hr_atmosphere"');	
		return $result;
		}
		
		function getHrDataCompanyCualification($game_id, $round_number, $company_id){
			$result=$this->fetchRow('game_id = '.$game_id.' AND round_number = '.$round_number.' AND company_id = '.$company_id.' AND type = "hr_cualification"');	
		return $result;
		}
	}
?>