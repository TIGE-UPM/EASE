<?php
	class Model_DbTable_Decisions_Mk_TradeMktBudgetRegion extends Zend_Db_Table{
		protected $_name = 'decision_marketing_trademktbudget_region';
		
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
			$trademktData=$parameters['trademkt_budget_region'];
			$region_counter=1;
				while (isset($trademktData['region_'.$region_counter])){
					$trade_budget=$trademktData['region_'.$region_counter];
					self::insert(array('game_id'=>$game_id, 'company_id' => $company_id, 'round_number' => $round_number, 'region_number' => $region_counter, 'distribution'=>$trade_budget));
					$region_counter++;
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
			$trademktData=$parameters['trademkt_budget_region'];
					$region_counter=1;
				while (isset($trademktData['region_'.$region_counter])){
					$trade_budget=$trademktData['region_'.$region_counter];
					self::update(array('distribution'=>$trade_budget), 'game_id = '.$game_id.' AND company_id = '.$company_id.' AND round_number = '.$round_number.' AND region_number = '.$region_counter);
					$region_counter++;
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
				$result['region_'.$decision['region_number']]=$decision['distribution'];
				//$product_counter++;
			}
			return $result;		
		}
		function getDecisionArray($game_id, $company_id, $round_number){
			$decisions=$this->fetchAll('game_id = '.$game_id.
								   ' AND company_id = '.$company_id.
								   ' AND round_number = '.$round_number);
			//$product_counter=1;
			foreach ($decisions as $decision) {
				$result['region_'.$decision['region_number']]=$decision['distribution'];
				//$product_counter++;
			}
			return $result;
		}
		function getDecision($game_id, $company_id, $round_number, $region_number){
			$decisions=$this->fetchRow('game_id = '.$game_id.
								   ' AND company_id = '.$company_id.
								   ' AND round_number = '.$round_number.
								   ' AND region_number = '.$region_number);
			//$product_counter=1;
			$result = $decisions['distribution'];
				//$product_counter++;
			
			return $result;
		}
	}
?>