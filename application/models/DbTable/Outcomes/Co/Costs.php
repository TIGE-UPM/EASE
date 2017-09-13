<?php
	class Model_DbTable_Outcomes_Co_Costs extends Zend_Db_Table{
		protected $_name = 'outcomes_costs';
		function getCosts($game_id, $round_number){
			$results=$this->fetchAll('game_id = '.$game_id.' AND round_number = '.$round_number);
			foreach ($results as $result){
				if ( $result['channel_number']==0){
					$costs[$result['company_id']][$result['type']]=$result['cost'];
				}
				else{
					$costs[$result['company_id']][$result['type']][$result['channel_number']]=$result['cost'];
				}
			}
			return $costs;			
		}
		
	}
?>