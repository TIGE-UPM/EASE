<?php
	class Model_DbTable_Outcomes_Pr_Messages extends Zend_Db_Table{
		protected $_name = 'outcomes_production_messages';
		
		function getMessages($game_id, $round_number){
			$results=$this->fetchAll(array('game_id'=>$game_id, 'round_number'=>$round_number));
			foreach ($results as $result){
				$outcomes['company_'.$result['company_id']]=explode(';', $result['messages']);
			}
			return $outcomes;			
		}
	}
?>