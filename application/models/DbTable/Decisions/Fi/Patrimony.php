<?php
	class Model_DbTable_Decisions_Fi_Patrimony extends Zend_Db_Table{
		protected $_name = 'decision_finance_patrimony';
		
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
			$dividends=$parameters['patrimony'];
			self::insert(array('game_id'=>$game_id, 'company_id' => $company_id, 'round_number' => $round_number, 'dividends' => $dividends));	
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
			$dividends=$parameters['patrimony'];	
			self::update(array('dividends'=>$dividends), 'game_id = '.$game_id.' AND company_id = '.$company_id.' AND round_number = '.$round_number);	
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