<?php
	class Model_DbTable_Decisions_Fi_Investment extends Zend_Db_Table{
		protected $_name = 'decision_finance_investment';
		
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

			$investmentData=$parameters['investment'];
			$investment_number=1;

			while (isset($investmentData['investment_number_'.$investment_number])){
				$investment=$investmentData['investment_number_'.$investment_number];
				$amount=$investment['amount'];
				$term=$investment['term'];
				self::insert(array('game_id'=>$game_id, 'company_id' => $company_id, 'round_number' => $round_number, 'investment_number'=> $investment_number, 'amount' => $amount, 'term' => $term));
				$investment_number++;
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
			$investmentData=$parameters['investment'];
			$investment_number=1;

			while (isset($investmentData['investment_number_'.$investment_number])){
				$investment=$investmentData['investment_number_'.$investment_number];
				$amount=$investment['amount'];
				$term=$investment['term'];
				self::update(array('amount'=>$amount, 'term' => $term), 'game_id = '.$game_id.' AND company_id = '.$company_id.' AND round_number = '.$round_number.' AND investment_number= '.$investment_number);
				$investment_number++;
			}	
		}
		
		function getActiveRoundLastDecisionSaved(){
			$front = Zend_Controller_Front::getInstance();
			$game=$front->getParam('activeGame');
			$company=$front->getParam('activeCompany');
			$round=$front->getParam('activeRound');

			$array=array();
			$decisions=$this->fetchAll('game_id = '.$game['id'].
								   ' AND company_id = '.$company['id'].
								   ' AND round_number = '.$round['round_number']);
			foreach ($decisions as $decision){ 
				$array['investment_number_'.$decision['investment_number']]['amount']=$decision['amount'];
				$array['investment_number_'.$decision['investment_number']]['term']=$decision['term'];
			}
			return $array;
		}
		
		function getDecision($game_id, $company_id, $round_number){
			$array=array();
			$decisions=$this->fetchAll('game_id = '.$game_id.
								   ' AND company_id = '.$company_id.
								   ' AND round_number = '.$round_number);
			foreach ($decisions as $decision){
				$array['investment_number_'.$decision['investment_number']]['amount']=$decision['amount'];
				$array['investment_number_'.$decision['investment_number']]['term']=$decision['term'];
			}
			return $array;

		}

		function getInvestmentsCost($game_id, $company_id, $round_number){
			$decisions=$this->fetchAll('game_id = '.$game_id.
								   ' AND company_id = '.$company_id.
								   ' AND round_number = '.$round_number);
			foreach ($decisions as $decision){
				$amount_investment += $decision['amount'];
			}

			return $amount_investment;

		}
	}
?>