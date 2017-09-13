<?php
	class Model_DbTable_Decisions_Mk_AdvertisingPercentage extends Zend_Db_Table{
		protected $_name = 'decision_marketing_advertisingpercentage';
		
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
			$advertisingData=$parameters['advertising_percentage'];
			$product_number=1;
			while (isset($advertisingData['product_'.$product_number])){
				$advertising_product=$advertisingData['product_'.$product_number];
				$media_number=1;
				while (isset($advertising_product['media_'.$media_number])){
					$advertising_media=$advertising_product['media_'.$media_number];
					$region_number=1;
					while (isset($advertising_media['region_'.$region_number])){
						$advertising_percentage=$advertising_media['region_'.$region_number];
						self::insert(array('game_id'=>$game_id, 'company_id' => $company_id, 'round_number' => $round_number, 'product_number' => $product_number, 'media_number'=>$media_number, 'region_number'=>$region_number, 'advertising_percentage'=>$advertising_percentage));
					$region_number++;
					//echo("<br/> Region Write 1");
					//var_dump($advertising_percentage);
					}
				$media_number++;
				//echo("<br/> Media Write 2");
				//var_dump($advertising_media);
				}
			$product_number++;
			//echo("<br/> Product Write 3");
			//var_dump($advertising_product);
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
			$advertisingData=$parameters['advertising_percentage'];
			$product_number=1;
			while (isset($advertisingData['product_'.$product_number])){
				$advertising_product=$advertisingData['product_'.$product_number];
				$media_number=1;
				while (isset($advertising_product['media_'.$media_number])){
					$advertising_media=$advertising_product['media_'.$media_number];
					$region_number=1;
					while (isset($advertising_media['region_'.$region_number])){
						$advertising_percentage=$advertising_media['region_'.$region_number];
						self::update(array('advertising_percentage'=>$advertising_percentage), 'game_id = '.$game_id.' AND company_id = '.$company_id.' AND product_number = '.$product_number.' AND media_number = '.$media_number.' AND region_number = '.$region_number. ' AND round_number = '.$round_number);
						
					$region_number++;
					//echo("<br/> Region Update 1");
					//var_dump($advertising_percentage);
					}
				$media_number++;
			//echo("<br/> Media Update 2");
			//var_dump($advertising_media);
				}
			$product_number++;
			//echo("<br/> Product Update 3");
			//var_dump($advertising_product);
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
									    array('product_number ASC', 'region_number ASC', 'media_number ASC'));
			foreach ($decisions as $decision){
				$array['product_'.$decision['product_number']]
					  ['media_'.$decision['media_number']]
					  ['region_'.$decision['region_number']]=$decision['advertising_percentage'];
			}
			return $array;		
		}
		function getDecision($game_id, $company_id, $round_number){
			$array=array();
			$decisions=$this->fetchAll('game_id = '.$game_id.
								   ' AND company_id = '.$company_id.
								   ' AND round_number = '.$round_number, array('product_number ASC', 'region_number ASC', 'media_number ASC'));
			foreach ($decisions as $decision){
				$array['product_'.$decision['product_number']]
					  ['media_'.$decision['media_number']]	  
					  ['region_'.$decision['region_number']]=$decision['advertising_percentage'];
			}

			return $array;
		}
	}
?>