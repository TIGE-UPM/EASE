<?php
	class Model_DbTable_Decisions_Suppliers extends Zend_Db_Table{
		protected $_name = 'decision_suppliers_registry';
		
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
			$date=date( 'Y-m-d H:i:s', time());
			$su_number= new Model_DbTable_Decisions_Su_Number();
			$su_payterms= new Model_DbTable_Decisions_Su_Payterms();
			// si no existe ya una decisión para esta ronda se crea
			if ($this->fetchRow('game_id = '.$game_id.
								' AND company_id = '.$company_id.
								' AND round_number = '.$round_number)==null){
				$su_number->add($decisionData, $game_id, $company_id, $round_number);
				$su_payterms->add($decisionData, $game_id, $company_id, $round_number);
				$this->insert(array('game_id'=>$game_id, 
									'company_id' => $company_id, 
									'round_number' => $round_number, 
									'date'=>$date));
			}
			else{ //si ya se tomó con anterioridad se sobreescribe
				$su_number->updateDecision($decisionData, $game_id, $company_id, $round_number);
				$su_payterms->updateDecision($decisionData, $game_id, $company_id, $round_number);
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
			$su_number= new Model_DbTable_Decisions_Su_Number();
			$su_payterms= new Model_DbTable_Decisions_Su_Payterms();
			return array('number'=>$su_number->getActiveRoundLastDecisionSaved(), 
						 'payterms'=>$su_payterms->getActiveRoundLastDecisionSaved());

		}
		function getDecisionArray($game_id, $company_id, $round_number){
			$su_number= new Model_DbTable_Decisions_Su_Number();
			$su_payterms= new Model_DbTable_Decisions_Su_Payterms();
			return array('number'=>$su_number->getDecision($game_id, $company_id, $round_number), 
						 'payterms'=>$su_payterms->getDecision($game_id, $company_id, $round_number));
		}
		function getDecision($game_id, $company_id, $round_number){
			return $this->fetchRow('game_id = '.$game_id.
							' AND company_id = '.$company_id.
							' AND round_number = '.$round_number);
		}
		function getNumber($game_id, $company_id, $round_number){
			$su_number= new Model_DbTable_Decisions_Su_Number();
			$results=$su_number->fetchRow('game_id = '.$game_id.
							' AND company_id = '.$company_id.
							' AND round_number = '.$round_number);
			return $results['number'];
		}
		function getPayterms($game_id, $company_id, $round_number){
			$su_payterms= new Model_DbTable_Decisions_Su_Payterms();			
			return $su_payterms->getDecision($game_id, $company_id, $round_number);
		}
	}
?>