<?php
	class Model_DbTable_Decisions_Fi_Interest extends Zend_Db_Table{
		protected $_name = 'decision_finance_interest';
		
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
			$interest=$parameters['interest'];
			$counter=0;
			while(isset($interest)&&$counter==0){								
				self::insert(array('game_id'=>$game_id, 'company_id' => $company_id, 'round_number' => $round_number, 'interest' => $interest));
				$counter++;
			}		
		}
		
		function setInterestTotal($game_id, $company_id, $round_number, $interest_total){
			$this->delete(array('game_id = '.$game_id, 'company_id = '.$company_id, 'round_number = '.$round_number));
			self::insert(array('game_id'=>$game_id, 'company_id' => $company_id, 'round_number' => $round_number, 'interest' => $interest_total));
		}
		
		function getActiveRoundLastDecisionSaved(){
			$front = Zend_Controller_Front::getInstance();
			$game=$front->getParam('activeGame');
			$company=$front->getParam('activeCompany');
			$round=$front->getParam('activeRound');
			return $this->fetchRow('game_id = '.$game['id'].
								   ' AND company_id = '.$company['id'].
								   ' AND round_number = '.$round['round_number']);
		}
		
		function getDecision($game_id, $company_id, $round_number){
			return $this->fetchRow('game_id = '.$game_id.
								   ' AND company_id = '.$company_id.
								   ' AND round_number = '.$round_number);
		}
	}
?>