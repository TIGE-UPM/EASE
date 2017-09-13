<?php
	class Model_DbTable_Decisions_Mk_TradeMktBudget extends Zend_Db_Table{
		protected $_name = 'decision_marketing_trademktbudget';
		
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
			$trademktData=$parameters['trademkt_budget'];
				if (isset($trademktData)){
					$trademkt_budget=$trademktData;
					self::insert(array('game_id'=>$game_id, 'company_id' => $company_id, 'round_number' => $round_number, 'trademkt_budget'=>$trademkt_budget));
				//echo("<br/> Write 1");
				//var_dump($trademkt_budget);
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
			$trademktData=$parameters['trademkt_budget'];
					if (isset($trademktData)){
						$trademkt_budget=$trademktData;
						self::update(array('trademkt_budget'=>$trademkt_budget), 'game_id = '.$game_id.' AND company_id = '.$company_id.' AND round_number = '.$round_number);
					//echo("<br/> Update 1");
					//var_dump($trademkt_budget);
					}							
		}
		function getActiveRoundLastDecisionSaved(){
			$front = Zend_Controller_Front::getInstance();
			$game=$front->getParam('activeGame');
			$company=$front->getParam('activeCompany');
			$round=$front->getParam('activeRound');
			$decisions=$this->fetchRow('game_id = '.$game['id'].' AND company_id = '.$company['id'].
									   ' AND round_number = '.$round['round_number']);
			
			return $decisions['trademkt_budget'];		
		}
		function getDecision($game_id, $company_id, $round_number){
			$decisions=$this->fetchRow('game_id = '.$game_id.
								   ' AND company_id = '.$company_id.
								   ' AND round_number = '.$round_number);
			return $decisions['trademkt_budget'];
		}
	}
?>