<?php
	class Model_DbTable_Games_Evolution_Np_NewProducts extends Zend_Db_Table{
		protected $_name = 'games_evolution_new_products';
		
		function getSuccessProbability($game_id, $company_id, $round_number, $product_number){
			$result=$this->fetchRow('game_id = '.$game_id.' AND company_id = '.$company_id.' AND round_number = '.$round_number.' AND product_number = '.$product_number);
			return $result['success_probability'];
		}
		function getActualAvailability($game_id, $company_id, $round_number, $product_number){
			$result=$this->fetchRow('game_id = '.$game_id.' AND company_id = '.$company_id.' AND round_number = '.$round_number.' AND product_number = '.$product_number);
			return $result['availability'];
		}
		function getNewReleasesRow($game_id, $company_id, $round_number, $product_number){
			$result= $this->fetchRow('game_id = '.$game_id.' AND company_id = '.$company_id.' AND round_number = '.$round_number.' AND product_number = '.$product_number);
			return $result;
		}
	}
?>