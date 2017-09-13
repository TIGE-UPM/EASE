<?php
	class Model_DbTable_Decisions_Mk_AdvertisingBudget extends Zend_Db_Table{
		protected $_name = 'decision_marketing_advertisingbudget';
		
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
			$advertisingData=$parameters['advertising_budget'];
			//echo("<br/> Write 1");
			//var_dump($parameters);
				if (isset($advertisingData)){
					$advertising_budget=$advertisingData;
					self::insert(array('game_id'=>$game_id, 'company_id' => $company_id, 'round_number' => $round_number, 'advertising_budget'=>$advertising_budget));
				//echo("<br/> Write 2");
				//var_dump($advertising_budget);
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
			$advertisingData=$parameters['advertising_budget'];
			//$advertisingData=$advertisingData_aux['total'];
			//echo("<br/> Update 1");
			//var_dump($advertisingData);die();
					if (isset($advertisingData)){
						$advertising_budget=$advertisingData;
						self::update(array('advertising_budget'=>$advertising_budget), 'game_id = '.$game_id.' AND company_id = '.$company_id.' AND round_number = '.$round_number);
					}							
		}
		function getActiveRoundLastDecisionSaved(){
			$front = Zend_Controller_Front::getInstance();
			$game=$front->getParam('activeGame');
			$company=$front->getParam('activeCompany');
			$round=$front->getParam('activeRound');
			$decisions=$this->fetchRow('game_id = '.$game['id'].' AND company_id = '.$company['id'].
									   ' AND round_number = '.$round['round_number']);
			
			return $decisions['advertising_budget'];		
		}
		function getDecision($game_id, $company_id, $round_number){
			$decision=$this->fetchRow('game_id = '.$game_id.
								   ' AND company_id = '.$company_id.
								   ' AND round_number = '.$round_number);

			return $decision['advertising_budget'];
		}
	}
?>