<?php
	class Model_DbTable_Outcomes_Bs_Performance extends Zend_Db_Table{
		protected $_name = 'outcomes_performance';
		
		function getValues($game_id, $round_number){
			$results=$this->fetchAll('game_id = '.$game_id.' AND round_number = '.$round_number);
			foreach ($results as $result){
					$values[$result['company_id']][$result['type']]=$result['value'];
			}
			return $values;
		}
	}
?>