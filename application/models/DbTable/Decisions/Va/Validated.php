<?php
	class Model_DbTable_Decisions_Va_Validated extends Zend_Db_Table{
		protected $_name = 'decision_validate_validated';
		
		
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
			$Data=$parameters;
			//var_dump($Data);die();
			self::insert(array('game_id'=>$game_id, 'company_id' => $company_id, 'round_number' => $round_number, 'validated'=>$Data));
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
			$Data=$parameters;
			//var_dump($Data);die();
			self::update(array('validated'=>$Data), 'game_id = '.$game_id.' AND company_id = '.$company_id.' AND round_number = '.$round_number);
		}
		
		function getActiveRoundLastDecisionSaved(){
			$front = Zend_Controller_Front::getInstance();
			$game=$front->getParam('activeGame');
			$company=$front->getParam('activeCompany');
			$round=$front->getParam('activeRound');
			$decisions=$this->fetchRow('game_id = '.$game['id'].
								   ' AND company_id = '.$company['id'].
								   ' AND round_number = '.$round['round_number']);
			return $decisions;
		}
		
		function getDecision($game_id, $company_id, $round_number){
			$array=array();
			$decisions=$this->fetchRow('game_id = '.$game_id.
								   ' AND company_id = '.$company_id.
								   ' AND round_number = '.$round_number);
			return $decisions;
		}
	}
?>	