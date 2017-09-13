<?php
	class Model_DbTable_Outcomes_Rd_MarketShares extends Zend_Db_Table{
		protected $_name = 'outcomes_round_marketShares';
		
		function getPastShare($game_id, $company_id, $round_number, $product_number, $region_number, $channel_number){
			$result=$this->fetchRow('game_id = '.$game_id.' AND company_id = '.$company_id.' AND round_number = '.($round_number-1).
									' AND product_number = '.$product_number.' AND region_number = '.$region_number.' AND channel_number = '.$channel_number);
			return $result['share_model'];
		}
		function getRealShare($game_id, $company_id, $round_number, $product_number, $region_number, $channel_number){
			$result=$this->fetchRow('game_id = '.$game_id.' AND company_id = '.$company_id.' AND round_number = '.$round_number.
									' AND product_number = '.$product_number.' AND region_number = '.$region_number.' AND channel_number = '.$channel_number);
			return $result['share_real'];
		}
	}
?>