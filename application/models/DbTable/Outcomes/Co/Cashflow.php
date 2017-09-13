<?php
	class Model_DbTable_Outcomes_Co_Cashflow extends Zend_Db_Table{
		protected $_name = 'outcomes_cashflow';
		function getCashflow($game_id, $round_number){
			$results=$this->fetchAll(array('game_id'=>$game_id, 'round_number'=>$round_number));
			foreach ($results as $result){
				if ($result['type']=='cash'){
					$cashflow[$result['company_id']]['cash'][$result['month']]=$result['value'];					
				}	
				else{
					if ($result['month']==0){
						if ($result['channel_number']==0){
							$cashflow[$result['company_id']][$result['type']]=$result['value'];
						}
						else{
							$cashflow[$result['company_id']][$result['type']][$result['channel_number']]=$result['value'];
						}					
					}
					else{
						if ($result['channel_number']==0){
							$cashflow[$result['company_id']][$result['type']][$result['month']]=$result['value'];
						}
						else{
							$cashflow[$result['company_id']][$result['type']][$result['month']][$result['channel_number']]=$result['value'];
						}	
					}
				}			
			}
			return $cashflow;			
		}
		
	}
?>