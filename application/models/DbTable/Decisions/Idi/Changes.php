<?php
	class Model_DbTable_Decisions_Idi_Changes extends Zend_Db_Table{
		protected $_name = 'decision_idi_changes';
		
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
			$decision=$parameters['changeIdi'];
			$idiP=1;
			while(isset($decision['product_'.$idiP])){
				$selection=$decision['product_'.$idiP];
				$idiQ=1;
				while(isset($selection['product_quality_'.$idiQ])){
					$changes=$selection['product_quality_'.$idiQ];
					self::insert(array('game_id'=>$game_id, 'company_id' => $company_id, 'round_number' => $round_number, 'product_number'=>$idiP, 'product_quality'=>$idiQ, 'changes'=>$changes));
					$idiQ++;
				}
				$idiP++;
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
			$decision=$parameters['changeIdi'];
			$idiP=1;
			while(isset($decision['product_'.$idiP])){
				$selection=$decision['product_'.$idiP];
				$idiQ=1;
				while(isset($selection['product_quality_'.$idiQ])){
					$changes=$selection['product_quality_'.$idiQ];
					self::update(array('changes'=>$changes), 'game_id = '.$game_id.' AND company_id = '.$company_id.' AND round_number = '.$round_number.' AND product_number = '.$idiP.' AND product_quality = '.$idiQ);
					$idiQ++;
				}
				$idiP++;
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
				$array['product_'.$decision['product_number']]['product_quality_'.$decision['product_quality']]=$decision['changes'];
			}
			return $array;
		}
		
		function getDecision($game_id, $company_id, $round_number){
			$array=array();
			$decisions=$this->fetchAll('game_id = '.$game_id.
								   ' AND company_id = '.$company_id.
								   ' AND round_number = '.$round_number);
			foreach ($decisions as $decision){
				$array['product_'.$decision['product_number']]['product_quality_'.$decision['product_quality']]=$decision['changes'];
			}
			return $array;
		}
	
	}
	
?>