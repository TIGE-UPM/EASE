<?php
	class Model_DbTable_Games_Evolution_Am_Amortization extends Zend_Db_Table{
		protected $_name = 'games_evolution_amortization';
		
		function getConsAmount($game_id, $company_id, $round_number){
			$result=$this->fetchRow('game_id = '.$game_id.' AND company_id = '.$company_id.' AND round_number = '.$round_number.' AND type = "cons"');
			return $result['amount'];
		}
		
		function getExtAmount($game_id, $company_id, $round_number){
			$result=$this->fetchRow('game_id = '.$game_id.' AND company_id = '.$company_id.' AND round_number = '.$round_number.' AND type = "ext"');
			return $result['amount'];
		}
		
		function getConsTerm($game_id, $company_id, $round_number){
			$result=$this->fetchRow('game_id = '.$game_id.' AND company_id = '.$company_id.' AND round_number = '.$round_number.' AND type = "cons"');
			return $result['term'];
		}
		
		function getExtTerm($game_id, $company_id, $round_number){
			$result=$this->fetchRow('game_id = '.$game_id.' AND company_id = '.$company_id.' AND round_number = '.$round_number.' AND type = "ext"');
			return $result['term'];
		}

	}
?>