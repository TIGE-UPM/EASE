<?php
	class Model_DbTable_Games_Evolution_St_Stocks extends Zend_Db_Table{
		protected $_name = 'games_evolution_stocks';
		
		function getStockClasified($game_id, $company_id, $round_number, $product_number, $region_number, $channel_number){
			$result=$this->fetchRow('game_id = '.$game_id.' AND company_id = '.$company_id.' AND round_number = '.$round_number.
									 ' AND product_number = '.$product_number.' AND region_number = '.$region_number.' AND channel_number = '.$channel_number);
			//var_dump($result);
			return $result['units'];
		}
		function getStockPrCost($game_id, $company_id, $round_number, $product_number, $region_number, $channel_number){
			$result=$this->fetchRow('game_id = '.$game_id.' AND company_id = '.$company_id.' AND round_number = '.$round_number.
									 ' AND product_number = '.$product_number.' AND region_number = '.$region_number.' AND channel_number = '.$channel_number);
			//var_dump($result);
			return $result['pr_cost'];
		}
		function getStockRow($game_id, $company_id, $round_number, $product_number, $region_number, $channel_number){
			$result= $this->fetchRow('game_id = '.$game_id.' AND company_id = '.$company_id.' AND round_number = '.$round_number.
									 ' AND product_number = '.$product_number.' AND region_number = '.$region_number.' AND channel_number = '.$channel_number);
			//var_dump($result);
			return $result;
		}
	}
?>