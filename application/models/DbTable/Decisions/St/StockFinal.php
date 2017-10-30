<?php
	class Model_DbTable_Decisions_St_StockFinal extends Zend_Db_Table{
		protected $_name = 'decision_stock';
		
		function add($parameters, $game_id=null, $company_id=null, $round_number=null){	
			$front = Zend_Controller_Front::getInstance();
			if ($game_id==null){
				$game=$front->getParam('activeGame');
				$game_id=$game['id'];
			}
			if ($company_id==null){
				$company=$front->getParam('activeCompany');
				$company_id=$company['id'];
			}
			if ($round_number==null){
				$round=$front->getParam('activeRound');
				$round_number=$round['round_number'];
			}
			//$unitsData=$parameters['unitsStock'];
			$unitsData=$parameters['stock']['unitsStockResult'];
			$product_number=1;
			$unitsData['product_'.$product_number];

			while (isset($unitsData['product_'.$product_number])){
				$units_product=$unitsData['product_'.$product_number];
				$channel_number=1;
				while (isset($units_product['channel_'.$channel_number])){
					$units_channel=$units_product['channel_'.$channel_number];
					$region_number=1;
					while (isset($units_channel['region_'.$region_number])){
						$units=$units_channel['region_'.$region_number];
						self::insert(array('game_id'=>$game_id, 'company_id' => $company_id, 'round_number' => $round_number, 'product_number' => $product_number, 'channel_number'=>$channel_number, 'region_number'=>$region_number, 'units'=>$units));
						$region_number++;
					}
					$channel_number++;
				}
				$product_number++;
			}
		}

		function updateDecision($parameters, $game_id=null, $company_id=null, $round_number=null){	
			$front = Zend_Controller_Front::getInstance();
			if ($game_id==null){
				$game=$front->getParam('activeGame');
				$game_id=$game['id'];
			}
			if ($company_id==null){
				$company=$front->getParam('activeCompany');
				$company_id=$company['id'];
			}
			if ($round_number==null){
				$round=$front->getParam('activeRound');
				$round_number=$round['round_number'];
			}
			$unitsData=$parameters['stock']['unitsStockResult'];
			$product_number=1;
			while (isset($unitsData['product_'.$product_number])){
				$units_product=$unitsData['product_'.$product_number];
				$channel_number=1;
				while (isset($units_product['channel_'.$channel_number])){
					$units_channel=$units_product['channel_'.$channel_number];
					$region_number=1;
					while (isset($units_channel['region_'.$region_number])){
						$units=$units_channel['region_'.$region_number];
						self::update(array('units'=>$units), 'game_id = '.$game_id.' AND company_id = '.$company_id.' AND product_number = '.$product_number.' AND channel_number = '.$channel_number.' AND region_number = '.$region_number. ' AND round_number = '.$round_number);
						$region_number++;
					}
					$channel_number++;
				}
				$product_number++;
			}							
		}
		function getActiveRoundLastDecisionSaved(){
			$front = Zend_Controller_Front::getInstance();
			$game=$front->getParam('activeGame');
			$company=$front->getParam('activeCompany');
			$round=$front->getParam('activeRound');
			$array=array();
			$decisions=$this->fetchAll('game_id = '.$game['id'].' AND company_id = '.$company['id'].
									   ' AND round_number = '.$round['round_number'], 
									    array('product_number ASC', 'region_number ASC', 'channel_number ASC'));
			foreach ($decisions as $decision){
				$array['product_'.$decision['product_number']]
					  ['channel_'.$decision['channel_number']]
					  ['region_'.$decision['region_number']]=$decision['units'];
			}
			return $array;
		}
		function getDecision($game_id, $company_id, $round_number){
			$array=array();
			$decisions=$this->fetchAll('game_id = '.$game_id.
								   ' AND company_id = '.$company_id.
								   ' AND round_number = '.$round_number, array('product_number ASC', 'region_number ASC', 'channel_number ASC'));
			foreach ($decisions as $decision){
				$array['product_'.$decision['product_number']]
					  ['channel_'.$decision['channel_number']]
					  ['region_'.$decision['region_number']]=$decision['units'];
			}
			return $array;
		}
		function getAllDecisions($game_id, $round_number){
			$array=array();
			$decisions=$this->fetchAll('game_id = '.$game_id.' AND round_number = '.$round_number, array('company_id ASC','product_number ASC', 'region_number ASC', 'channel_number ASC'));
			foreach ($decisions as $decision){
				$array['company_id_'.$decision['company_id']]
					  ['product_'.$decision['product_number']]
					  ['channel_'.$decision['channel_number']]
					  ['region_'.$decision['region_number']]=$decision['units'];
			}
			return $array;
		}
		function getUnits ($game_id, $round_number){
			$results=$this->fetchAll('game_id = '.$game_id.' AND round_number = '.$round_number, array("company_id", "product_number", "region_number", "channel_number"));
			foreach ($results as $result){
				$units['company_'.$result['company_id']]
				['product_'.$result['product_number']]
				['region_'.$result['region_number']]
				['channel_'.$result['channel_number']]=$result['units'];
			}
			return $units;
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
	}
?>