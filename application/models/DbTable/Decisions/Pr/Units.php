<?php
	class Model_DbTable_Decisions_Pr_Units extends Zend_Db_Table{
		protected $_name = 'decision_production_units';
		
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
			$productionData=$parameters['production_units'];
			$factory_number=1;
			while (isset($productionData['factory_number_'.$factory_number])){
				$production_factory=$productionData['factory_number_'.$factory_number];
				$product_number=1;
				while (isset($production_factory['product_'.$product_number])){
					$production_product=$production_factory['product_'.$product_number];
					$channel_number=1;
					while (isset($production_product['channel_'.$channel_number])){
						$production_channel=$production_product['channel_'.$channel_number];
						$region_number=1;
						while (isset($production_channel['region_'.$region_number])){
							$units=$production_channel['region_'.$region_number];
							self::insert(array('game_id'=>$game_id, 'company_id' => $company_id, 'round_number' => $round_number, 'factory_number' => $factory_number, 'product_number' => $product_number, 'channel_number'=>$channel_number, 'region_number'=>$region_number, 'production_units'=>$units));
							$region_number++;
						}
						$channel_number++;
					}
					$product_number++;
				}
				$factory_number++;
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
			$productionData=$parameters['production_units'];
			$factory_number=1;
			while (isset($productionData['factory_number_'.$factory_number])){
				$production_factory=$productionData['factory_number_'.$factory_number];
				$product_number=1;
				if(($this->ExistsFactory($game_id, $company_id, $factory_number))==null){
					$this->add($parameters, $game_id, $company_id, $round_number);
				}
				else {
					while (isset($production_factory['product_'.$product_number])){
						$production_product=$production_factory['product_'.$product_number];
						$channel_number=1;
						while (isset($production_product['channel_'.$channel_number])){
							$production_channel=$production_product['channel_'.$channel_number];
							$region_number=1;
							while (isset($production_channel['region_'.$region_number])){
								$units=$production_channel['region_'.$region_number];
								self::update(array('production_units'=>$units), 'game_id = '.$game_id.' AND company_id = '.$company_id. ' AND round_number = '.$round_number. ' AND factory_number = '.$factory_number.' AND product_number = '.$product_number.' AND channel_number = '.$channel_number.' AND region_number = '.$region_number);
								
								$region_number++;
							}
							$channel_number++;
						}
						$product_number++;
					}
				}
				$factory_number++;
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
									    array('factory_number ASC', 'product_number ASC', 'region_number ASC', 'channel_number ASC'));
			foreach ($decisions as $decision){
				$array['factory_number_'.$decision['factory_number']]
					  ['product_'.$decision['product_number']]
					  ['channel_'.$decision['channel_number']]
					  ['region_'.$decision['region_number']]=$decision['production_units'];
			}
			
			return $array;
		
		}
		function getDecision($game_id, $company_id, $round_number){
			$array=array();
			$decisions=$this->fetchAll('game_id = '.$game_id.
								   ' AND company_id = '.$company_id.
								   ' AND round_number = '.$round_number, array('factory_number ASC', 'product_number ASC', 'region_number ASC', 'channel_number ASC'));
			foreach ($decisions as $decision){
				$array['factory_number_'.$decision['factory_number']]
					  ['product_'.$decision['product_number']]
					  ['channel_'.$decision['channel_number']]
					  ['region_'.$decision['region_number']]=$decision['production_units'];
			}
			return $array;
		}
		function ExistsFactory($game_id, $company_id, $factory_number){
			$decisions=$this->fetchRow('game_id = '.$game_id.' AND company_id = '.$company_id.' AND factory_number = '.$factory_number);
			return $decisions;
		}
	}
?>