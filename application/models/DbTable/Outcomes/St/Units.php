<?php
	class Model_DbTable_Outcomes_St_Units extends Zend_Db_Table{
		protected $_name = 'outcomes_stocks_units';
		
		function getOutcomes($game_id, $round_number){
			$results=$this->fetchAll('game_id = '.$game_id.' AND round_number = '.$round_number, array("company_id", "product_number", "region_number", "channel_number"));
			foreach ($results as $result){
				$stocks['company_'.$result['company_id']]
					   ['product_'.$result['product_number']]
					   ['region_'.$result['region_number']]
					   ['channel_'.$result['channel_number']]=$result['units'];
			}
			return $stocks;
		}
		
		function getStockByMarket($game_id, $round_number, $company_id, $product_number, $region_number, $channel_number){
			$result=$this->fetchRow('game_id = '.$game_id.' AND round_number = '.$round_number.' AND company_id = '.$company_id.
									 ' AND product_number = '.$product_number.' AND region_number = '.$region_number.' AND channel_number = '.$channel_number);
			return $result['units'];
		}
		function getStockTotalRow($game_id, $round_number, $company_id, $product_number, $region_number, $channel_number){
			$result= $this->fetchRow('game_id = '.$game_id.' AND round_number = '.$round_number.' AND company_id = '.$company_id.
									 ' AND product_number = '.$product_number.' AND region_number = '.$region_number.' AND channel_number = '.$channel_number);
			return $result;
		}

		//VERO
		function getStockByCompany($game_id, $round_number, $company_id){
			
					$results=$this->fetchAll('game_id = '.$game_id.' AND round_number = '.$round_number.' AND company_id = '.$company_id, array("product_number", "region_number", "channel_number"));


					foreach ($results as $result){
						$array['product_'.$result['product_number']]
						['channel_'.$result['channel_number']]
						['region_'.$result['region_number']]=$result['units'];

					}
			return $array;
			

		}

		function getStockByProduct($game_id, $round_number, $company_id, $product_number){
			
					$results=$this->fetchAll('game_id = '.$game_id.' AND round_number = '.$round_number.' AND company_id = '.$company_id.' AND product_number= '.$product_number, array("product_number", "region_number", "channel_number"));


					foreach ($results as $result){
						$array['product_'.$result['product_number']]
						['channel_'.$result['channel_number']]
						['region_'.$result['region_number']]=$result['units'];

					}
			return $array;
			

		}
		//VERO
	}
?>