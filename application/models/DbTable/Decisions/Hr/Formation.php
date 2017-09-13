<?php
	class Model_DbTable_Decisions_Hr_Formation extends Zend_Db_Table{
		protected $_name = 'decision_humanResources_formation';
		
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
			$formation=$parameters['formation'];
			$counter=0;	
			while (isset($formation)&&$counter==0){			
				self::insert(array('game_id'=>$game_id, 'company_id' => $company_id, 'formation'=>$formation));
				$counter++;
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
			$formation=$parameters['formation'];
			$counter=0;
			while (isset($formation)&&$counter==0){				
				self::update(array('formation'=>$formation), 'game_id = '.$game_id.' AND company_id = '.$company_id);
				$counter++;
			}
		}
		
		function getActiveRoundLastDecisionSaved(){
			$front = Zend_Controller_Front::getInstance();
			$game=$front->getParam('activeGame');
			$company=$front->getParam('activeCompany');
			$round=$front->getParam('activeRound');
			return $this->fetchRow('game_id = '.$game['id'].
								   ' AND company_id = '.$company['id']);
		}
		
		function getDecision($game_id, $company_id){
			$decision=$this->fetchRow('game_id = '.$game_id.
								   ' AND company_id = '.$company_id);
			return $decision['formation'];
		}
	}
?>