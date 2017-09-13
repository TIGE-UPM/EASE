<?php
	class Model_DbTable_Outcomes_Pr_CapacityData extends Zend_Db_Table{
		protected $_name = 'outcomes_production_capacity_data';
		function getOutcomes($game_id, $round_number){
			$results=$this->fetchAll(array('game_id'=>$game_id, 'round_number'=>$round_number));
			foreach ($results as $result){
				$outcomes['company_'.$result['company_id']]
						 ['product_'.$result['product_number']]
						 ['channel_'.$result['channel_number']]
						 ['region_'.$result['region_number']]=$result['units'];
			}
			return $outcomes;
		}
		
	}
?>