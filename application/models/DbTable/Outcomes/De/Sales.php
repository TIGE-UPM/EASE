<?php
	class Model_DbTable_Outcomes_De_Sales extends Zend_Db_Table{
		protected $_name = 'outcomes_demand_sales';
		function getSales($game_id, $round_number){
			$results=$this->fetchAll('game_id = '.$game_id.' AND round_number = '.$round_number, array("company_id", "product_number", "region_number", "channel_number"));
			foreach ($results as $result){
				$outcomes['company_'.$result['company_id']]
						 ['product_'.$result['product_number']]
						 ['region_'.$result['region_number']]
						 ['channel_'.$result['channel_number']]=$result['units'];
			}
			return $outcomes;
		}
		function getIncomes($game_id, $round_number){
			$results=$this->fetchAll('game_id = '.$game_id.' AND round_number = '.$round_number, array("company_id", "product_number", "region_number", "channel_number"));
			foreach ($results as $result){
				$outcomes['company_'.$result['company_id']]
						 ['product_'.$result['product_number']]
						 ['region_'.$result['region_number']]
						 ['channel_'.$result['channel_number']]=$result['incomes'];
			}
			return $outcomes;
		}
		
	}
?>