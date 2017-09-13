<?php
	class Model_DbTable_Decisions_Initiatives extends Zend_Db_Table{
		protected $_name = 'decision_initiatives_registry';
		
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
			$in_production= new Model_DbTable_Decisions_In_InitiativeProduction();
			$in_marketing= new Model_DbTable_Decisions_In_InitiativeMarketing();
			$in_humanresources= new Model_DbTable_Decisions_In_InitiativeHumanresources();
			$in_deterioration= new Model_DbTable_Decisions_In_InitiativeDeterioration();
			// si no existe ya una decisión para esta ronda se crea
			//var_dump($decisionData); die();
			if ($this->fetchRow('game_id = '.$game_id.
								' AND company_id = '.$company_id.
								' AND round_number = '.$round_number)==null){
				$in_production->add($decisionData, $game_id, $company_id, $round_number);
				$in_marketing->add($decisionData, $game_id, $company_id, $round_number);
				$in_humanresources->add($decisionData, $game_id, $company_id, $round_number);
				$in_deterioration->add($decisionData, $game_id, $company_id, $round_number);
				$this->insert(array('game_id'=>$game_id, 
									'company_id' => $company_id, 
									'round_number' => $round_number, 
									'date'=>$date));
			}
			else{ //si ya se tomó con anterioridad se sobreescribe
				$in_production->updateDecision($decisionData, $game_id, $company_id, $round_number);
				$in_marketing->updateDecision($decisionData, $game_id, $company_id, $round_number);
				$in_humanresources->updateDecision($decisionData, $game_id, $company_id, $round_number);
				$in_deterioration->updateDecision($decisionData, $game_id, $company_id, $round_number);
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
			$in_production= new Model_DbTable_Decisions_In_InitiativeProduction();
			$in_marketing= new Model_DbTable_Decisions_In_InitiativeMarketing();
			$in_humanresources= new Model_DbTable_Decisions_In_InitiativeHumanresources();
			$in_deterioration= new Model_DbTable_Decisions_In_InitiativeDeterioration();
			return array('initiativeproduction'=>$in_production->getActiveRoundLastDecisionSaved(),
						 'initiativemarketing'=>$in_marketing->getActiveRoundLastDecisionSaved(),
				 		 'initiativehumanresources'=>$in_humanresources->getActiveRoundLastDecisionSaved(),
					 	 'initiativedeterioration'=>$in_deterioration->getActiveRoundLastDecisionSaved());
		}
		function getDecision($game_id, $company_id, $round_number){
			return $this->fetchRow('game_id = '.$game_id.
							' AND company_id = '.$company_id.
							' AND round_number = '.$round_number);
		}
		function getDecisionArray($game_id, $company_id, $round_number){
			$in_production= new Model_DbTable_Decisions_In_InitiativeProduction();
			$in_marketing= new Model_DbTable_Decisions_In_InitiativeMarketing();
			$in_humanresources= new Model_DbTable_Decisions_In_InitiativeHumanresources();
			$in_deterioration= new Model_DbTable_Decisions_In_InitiativeDeterioration();
			return array('initiativeproduction'=>$in_production->getDecision($game_id, $company_id, $round_number),
						 'initiativemarketing'=>$in_marketing->getDecision($game_id, $company_id, $round_number),
				 		 'initiativehumanresources'=>$in_humanresources->getDecision($game_id, $company_id, $round_number),
						 'initiativedeterioration'=>$in_deterioration->getDecision($game_id, $company_id, $round_number));
		}
		function getInitiativesProduction($game_id, $company_id, $round_number){
			$in_production= new Model_DbTable_Decisions_In_InitiativeProduction();
			return $in_production->getDecision($game_id, $company_id, $round_number);
		}
		function getInitiativesMarketing($game_id, $company_id, $round_number){
			$in_marketing= new Model_DbTable_Decisions_In_InitiativeMarketing();
			return $in_marketing->getDecision($game_id, $company_id, $round_number);
		}
		function getInitiativesHumanresources($game_id, $company_id, $round_number){
			$in_humanresources= new Model_DbTable_Decisions_In_InitiativeHumanresources();
			return $in_humanresources->getDecision($game_id, $company_id, $round_number);
		}
		function getInitiativesDeterioration($game_id, $company_id, $round_number){
			$in_deterioration= new Model_DbTable_Decisions_In_InitiativeDeterioration();
			return $in_deterioration->getDecision($game_id, $company_id, $round_number);
		}
		function getInitiativesChosen($game_id, $company_id, $round_number){
			for ($round = 2; $round < $round_number; $round++) {
				$result['initiative_production']['round_'.$round]=$this->getInitiativesProduction($game_id, $company_id, $round);
				$result['initiative_marketing']['round_'.$round]=$this->getInitiativesMarketing($game_id, $company_id, $round);
				$result['initiative_humanresources']['round_'.$round]=$this->getInitiativesHumanresources($game_id, $company_id, $round);
				$result['initiative_deterioration']['round_'.$round]=$this->getInitiativesDeterioration($game_id, $company_id, $round);
			}
			return $result;
		}
	}
?>