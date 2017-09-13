<?php
	class Model_DbTable_Decisions_Finance extends Zend_Db_Table{
		protected $_name = 'decision_finance_registry';
		
		function processDecision($decisionData, $game_id=null, $company_id=null, $round_number=null){
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
			//var_dump($decisionData); die();
			$date=date( 'Y-m-d H:i:s', time());
			$fi_patrimony= new Model_DbTable_Decisions_Fi_Patrimony();
			$fi_term= new Model_DbTable_Decisions_Fi_Term();
			$fi_amount= new Model_DbTable_Decisions_Fi_Amount();
			// si no existe ya una decisión para esta ronda se crea
			if ($this->fetchRow('game_id = '.$game_id.
								' AND company_id = '.$company_id.
								' AND round_number = '.$round_number)==null){
				$fi_patrimony->add($decisionData, $game_id, $company_id, $round_number);
				$fi_term->add($decisionData, $game_id, $company_id, $round_number);
				$fi_amount->add($decisionData, $game_id, $company_id, $round_number);
				$this->insert(array('game_id'=>$game_id, 
									'company_id' => $company_id, 
									'round_number' => $round_number, 
									'date'=>$date));
			}
			else{ //si ya se tomó con anterioridad se sobreescribe
				$fi_patrimony->updateDecision($decisionData, $game_id, $company_id, $round_number);
				$fi_term->updateDecision($decisionData, $game_id, $company_id, $round_number);
				$fi_amount->updateDecision($decisionData, $game_id, $company_id, $round_number);
				$this->update(array('date'=>$date), 
							  'game_id = '.$game_id.
							  ' AND company_id = '.$company_id.
							  ' AND round_number = '.$round_number);
			}
		}
		
		function existsPrevious($game_id=null, $company_id=null, $round_number=null){
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
			$previous=$this->fetchRow('game_id = '.$game_id.
								' AND company_id = '.$company_id.
								' AND round_number = '.$round_number);
			if ($previous==null){ 
				return false;			
			}
			return true;
		}
		function getActiveRoundDecisionRegistry(){
			$front = Zend_Controller_Front::getInstance();
			$game=$front->getParam('activeGame');
			$company=$front->getParam('activeCompany');
			$round=$front->getParam('activeRound');
			return $this->fetchRow('game_id = '.$game['id'].' AND company_id = '.$company['id'].' AND round_number = '.$round['round_number']);		
		}
		function getActiveRoundLastDecisionSaved(){
			$front = Zend_Controller_Front::getInstance();
			$game=$front->getParam('activeGame');
			$company=$front->getParam('activeCompany');
			$round=$front->getParam('activeRound');	
			$fi_term= new Model_DbTable_Decisions_Fi_Term();
			$fi_amount= new Model_DbTable_Decisions_Fi_Amount();
			$fi_patrimony= new Model_DbTable_Decisions_Fi_Patrimony();
			return array('amount'=>$fi_amount->getActiveRoundLastDecisionSaved(),
			             'term'=>$fi_term->getActiveRoundLastDecisionSaved(),
						 'patrimony'=>$fi_patrimony->getActiveRoundLastDecisionSaved());
		}
		
		function getDecision($game_id, $company_id, $round_number){
			return $this->fetchRow('game_id = '.$game_id.
							' AND company_id = '.$company_id.
							' AND round_number = '.$round_number);
		}
		
		function getDecisionArray($game_id, $company_id, $round_number){
			$fi_term= new Model_DbTable_Decisions_Fi_Term();
			$fi_amount= new Model_DbTable_Decisions_Fi_Amount();
			$fi_patrimony= new Model_DbTable_Decisions_Fi_Patrimony();
			return array('amount'=>$fi_amount->getDecision($game_id, $company_id, $round_number),
			             'term'=>$fi_term->getDecision($game_id, $company_id, $round_number),
						 'patrimony'=>$fi_patrimony->getDecision($game_id, $company_id, $round_number));
		}
		
		function getDividends($game_id, $company_id, $round_number){
			$fi_patrimony= new Model_DbTable_Decisions_Fi_Patrimony();
			$decision=$fi_patrimony->getDecision($game_id, $company_id, $round_number);
			return $decision['dividends'];
		}
		
		function getTerm($game_id, $company_id, $round_number){
			$fi_term= new Model_DbTable_Decisions_Fi_Term();
			$decision=$fi_term->getDecision($game_id, $company_id, $round_number);
			return $decision['term'];
		}
		
		function getAmount($game_id, $company_id, $round_number){
			$fi_amount= new Model_DbTable_Decisions_Fi_Amount();
			$decision=$fi_amount->getDecision($game_id, $company_id, $round_number);
			return $decision['amount'];
		}
	}
?>