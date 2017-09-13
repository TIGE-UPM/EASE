<?php
	class Model_DbTable_Decisions_Mk_AdvertisingBudgetDistribution extends Zend_Db_Table{
		protected $_name = 'decision_marketing_advertisingbudget_distribution';
		
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
			$advertisingData=$parameters['advertising_budget_distribution'];
			//echo("<br/> Write 1");
			$product_counter=1;
				while (isset($advertisingData['product_'.$product_counter])){
					$advertising_budget=$advertisingData['product_'.$product_counter];
					self::insert(array('game_id'=>$game_id, 'company_id' => $company_id, 'round_number' => $round_number, 'product_number' => $product_counter, 'distribution'=>$advertising_budget));
					$product_counter++;
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
			$advertisingData=$parameters['advertising_budget_distribution'];
			//$advertisingData=$advertisingData_aux['total'];
			//echo("<br/> Update 1");
			//var_dump($advertisingData);die();
			$product_counter=1;
				while (isset($advertisingData['product_'.$product_counter])){
					$advertising_budget=$advertisingData['product_'.$product_counter];
					self::update(array('distribution'=>$advertising_budget), 'game_id = '.$game_id.' AND company_id = '.$company_id.' AND round_number = '.$round_number.' AND product_number = '.$product_counter);
					$product_counter++;
				}
					
		}
		function getActiveRoundLastDecisionSaved(){
			$front = Zend_Controller_Front::getInstance();
			$game=$front->getParam('activeGame');
			$company=$front->getParam('activeCompany');
			$round=$front->getParam('activeRound');
			$decisions=$this->fetchAll('game_id = '.$game['id'].' AND company_id = '.$company['id'].
									   ' AND round_number = '.$round['round_number']);
			//$product_counter=1;
			foreach ($decisions as $decision) {
				$result['product_'.$decision['product_number']]=$decision['distribution'];
				//$product_counter++;
			}
			return $result;		
		}
		function getDecision($game_id, $company_id, $round_number){
			$decisions=$this->fetchAll('game_id = '.$game_id.
								   ' AND company_id = '.$company_id.
								   ' AND round_number = '.$round_number);

			//$product_counter=1;
			foreach ($decisions as $decision) {
				$result['product_'.$decision['product_number']]=$decision['distribution'];
				//$product_counter++;
			}
			return $result;	
		}
	}
?>