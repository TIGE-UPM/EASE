<?php
	class Model_DbTable_Decision extends Zend_Db_Table{
		function getDecision($company_id, $turn){
			return $this->fetchRow('company_id = '.$company_id.' AND turn = '.$turn)->toArray();
		}

		function addDecision($decision_data){
			$this->insert($decision_data);
		}
	}
?>