<?php
	class Model_DbTable_Decisions_Idi_New extends Zend_Db_Table{
		protected $_name = 'decision_idi_new';
		
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
			$decision=$parameters['newIdi'];
			$idiP=1;
			$games=new Model_DbTable_Games();
			$numberOfProducts=$games->getNumberOfProducts($game_id);
			while(isset($decision['idiproduct_'.$idiP])){
				$product_number=0;
				$selection_aux=$decision['idiproduct_'.$idiP];
				for ($i = 1; $i <= $numberOfProducts; $i++) {
					if(isset($selection_aux['product_number_'.$i])){
					$product_number=$i;
					}
				}
				$selection=$selection_aux['product_number_'.$product_number];
				self::insert(array('game_id'=>$game_id, 'company_id' => $company_id, 'round_number' => $round_number, 'idi_product_number'=>$idiP, 'product_number'=>$product_number, 'idi_new_decision'=>$selection['newidivalue'], 'idi_new_budget'=>$selection['newidibudget']));
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
			$decision=$parameters['newIdi'];
			$idiP=1;
			$games=new Model_DbTable_Games();
			$numberOfProducts=$games->getNumberOfProducts($game_id);
			while(isset($decision['idiproduct_'.$idiP])){
				$product_number=0;
				$selection_aux=$decision['idiproduct_'.$idiP];
				for ($i = 1; $i <= $numberOfProducts; $i++) {
					if(isset($selection_aux['product_number_'.$i])){
					$product_number=$i;
					}
				}
				$selection=$selection_aux['product_number_'.$product_number];
				$new=$this->getDecisionBudget($game_id, $company_id, $round_number);
				if($new==null){
					self::insert(array('game_id'=>$game_id, 'company_id' => $company_id, 'round_number' => $round_number, 'idi_product_number'=>$idiP, 'product_number'=>$product_number, 'idi_new_decision'=>$selection['newidivalue'], 'idi_new_budget'=>$selection['newidibudget']));
				}
				else {
					self::update(array('idi_new_decision'=>$selection['newidivalue'], 'idi_new_budget'=>$selection['newidibudget']), 'game_id = '.$game_id.' AND company_id = '.$company_id.' AND round_number = '.$round_number.' AND idi_product_number = '.$idiP.' AND product_number = '.$product_number);
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
				$array['idiproduct_'.$decision['idi_product_number']]['product_number_'.$decision['product_number']]['newidivalue']=$decision['idi_new_decision'];
				$array['idiproduct_'.$decision['idi_product_number']]['product_number_'.$decision['product_number']]['newidibudget']=$decision['idi_new_budget'];
			}
			return $array;
		}
		
		function getDecision($game_id, $company_id, $round_number){
			$array=array();
			$decisions=$this->fetchAll('game_id = '.$game_id.
								   ' AND company_id = '.$company_id.
								   ' AND round_number = '.$round_number);
			foreach ($decisions as $decision){
				$array['idiproduct_'.$decision['idi_product_number']]['product_number_'.$decision['product_number']]['newidivalue']=$decision['idi_new_decision'];
				$array['idiproduct_'.$decision['idi_product_number']]['product_number_'.$decision['product_number']]['newidibudget']=$decision['idi_new_budget'];
			}
			return $array;
		}
		function getDecisionBudget($game_id, $company_id, $round_number){
			return $this->fetchRow('game_id = '.$game_id.
								   ' AND company_id = '.$company_id.
								   ' AND round_number = '.$round_number);
		}
		function getProductSolicited($game_id, $company_id, $round_number, $product_number){
			for ($round = 1; $round <= $round_number; $round++) {
				$result=$this->fetchRow('game_id = '.$game_id.
									   ' AND company_id = '.$company_id.
									   ' AND round_number = '.$round.
								   	   ' AND product_number = '.$product_number);
				if($result['idi_new_decision']==1){
					$solicited=1;
				}
			}
			return $solicited;
		}
	
	}
	
?>