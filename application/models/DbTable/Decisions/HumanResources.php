<?php
	class Model_DbTable_Decisions_HumanResources extends Zend_Db_Table{
		protected $_name = 'decision_humanResources_registry';
		
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
			$hr_cuartil= new Model_DbTable_Decisions_Hr_Cuartil();
			$hr_formation= new Model_DbTable_Decisions_Hr_Formation();
			//JESUS
			$hr_employees = new Model_DbTable_Decisions_Hr_Employees();
			$hr_shifts = new Model_DbTable_Decisions_Hr_Shifts();
			// si no existe ya una decisión para esta ronda se crea
			if ($this->fetchRow('game_id = '.$game_id.
								' AND company_id = '.$company_id.
								' AND round_number = '.$round_number)==null){
				$hr_cuartil->add($decisionData, $game_id, $company_id, $round_number);
				$hr_formation->add($decisionData, $game_id, $company_id, $round_number);
				$hr_employees->add($decisionData, $game_id, $company_id, $round_number);
				$hr_shifts->add($decisionData, $game_id, $company_id, $round_number);
				$this->insert(array('game_id'=>$game_id, 
									'company_id' => $company_id, 
									'round_number' => $round_number, 
									'date'=>$date));
			}
			else{ //si ya se tomó con anterioridad se sobreescribe
				$hr_cuartil->updateDecision($decisionData, $game_id, $company_id, $round_number);
				$hr_formation->updateDecision($decisionData, $game_id, $company_id, $round_number);
				$hr_employees->updateDecision($decisionData, $game_id, $company_id, $round_number);
				$hr_shifts->updateDecision($decisionData, $game_id, $company_id, $round_number);
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
			$hr_cuartil= new Model_DbTable_Decisions_Hr_Cuartil();
			$hr_formation= new Model_DbTable_Decisions_Hr_Formation();
			//JESUS
			$hr_employees = new Model_DbTable_Decisions_Hr_Employees();
			$hr_shifts = new Model_DbTable_Decisions_Hr_Shifts();
			return array('cuartil'=>$hr_cuartil->getActiveRoundLastDecisionSaved(), 
						 'formation'=>$hr_formation->getActiveRoundLastDecisionSaved(),
						 'employees'=>$hr_employees->getActiveRoundLastDecisionSaved(),
						 'shifts'=>$hr_shifts->getActiveRoundLastDecisionSaved());

		}
		function getDecision($game_id, $company_id, $round_number){
			return $this->fetchRow('game_id = '.$game_id.
							' AND company_id = '.$company_id.
							' AND round_number = '.$round_number);
		}
		function getDecisionArray($game_id, $company_id, $round_number){
			$hr_cuartil= new Model_DbTable_Decisions_Hr_Cuartil();
			$hr_formation= new Model_DbTable_Decisions_Hr_Formation();
			//JESUS
			$hr_employees = new Model_DbTable_Decisions_Hr_Employees();
			$hr_shifts = new Model_DbTable_Decisions_Hr_Shifts();
			return array('cuartil'=>$hr_cuartil->getDecision($game_id, $company_id, $round_number), 
						 'formation'=>$hr_formation->getDecision($game_id, $company_id),
						 'employees'=>$hr_employees->getDecision($game_id, $company_id, $round_number),
						 'shifts'=>$hr_shifts->getDecision($game_id, $company_id, $round_number));
		}
		function getCuartiles($game_id, $company_id, $round_number){
			$hr_cuartil= new Model_DbTable_Decisions_Hr_Cuartil();
			return $hr_cuartil->getDecision($game_id, $company_id, $round_number);
		}
		function getFormations($game_id, $company_id){
			$hr_formation= new Model_DbTable_Decisions_Hr_Formation();
			return $hr_formation->getDecision($game_id, $company_id);
		}
		//JESUS
		function getEmployeeNumber($game_id, $company_id, $round_number){
			$hr_employees = new Model_DbTable_Decisions_Hr_Employees();
			$result = $hr_employees->getDecision($game_id, $company_id, $round_number);			
			return $result;

		}

		function getShiftNumber($game_id, $company_id, $round_number){
			$hr_shifts = new Model_DbTable_Decisions_Hr_Shifts();
			return $hr_shifts->getDecision($game_id, $company_id, $round_number);

		}
	}
?>