<?php
	class Model_DbTable_Games_Evolution_Fi_Overdraft extends Zend_Db_Table{
		protected $_name = 'games_evolution_fi_overdraft';
		
		function getOverdraft($game_id, $company_id, $round_number){
			$result=$this->fetchRow('game_id = '.$game_id.' AND company_id = '.$company_id.' AND round_number = '.$round_number);
			return $result['overdraft'];
		}

		function getOverdraftRow($game_id, $company_id, $round_number){
			$result= $this->fetchRow('game_id = '.$game_id.' AND company_id = '.$company_id.' AND round_number = '.$round_number);
			return $result;
		}
	}
?>