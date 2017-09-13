<?php
	class Model_DbTable_Decisions_In_InitiativeDeterioration extends Zend_Db_Table{
		protected $_name = 'decision_initiatives_initiativedeterioration';
		
		
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
			$initiativeData=$parameters['initiativedeterioration'];
			$initiativeData_aux=$initiativeData['initiativedeterioration_number_1'];
			$factory_number=1;
			while(isset($initiativeData_aux['factory_number_'.$factory_number])){
				$active=$initiativeData_aux['factory_number_'.$factory_number];
				self::insert(array('game_id'=>$game_id, 'company_id' => $company_id, 'round_number' => $round_number, 'factory_number'=>$factory_number, 'initiativedeterioration_active'=>$active));
				$factory_number++;
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
			$initiativeData=$parameters['initiativedeterioration'];
			$initiativeData_aux=$initiativeData['initiativedeterioration_number_1'];
			$factory_number=1;
			while(isset($initiativeData_aux['factory_number_'.$factory_number])){
				$active=$initiativeData_aux['factory_number_'.$factory_number];
				self::update(array('initiativedeterioration_active'=>$active), 'game_id = '.$game_id.' AND company_id = '.$company_id.' AND round_number = '.$round_number.' AND factory_number = '.$factory_number);
				$factory_number++;
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
				$array['factory_number_'.$decision['factory_number']]=$decision['initiativedeterioration_active'];
			}
			return $array;
		}
		
		function getDecision($game_id, $company_id, $round_number){
			$array=array();
			$decisions=$this->fetchAll('game_id = '.$game_id.
								   ' AND company_id = '.$company_id.
								   ' AND round_number = '.$round_number);
			foreach ($decisions as $decision){
				$array['factory_number_'.$decision['factory_number']]=$decision['initiativedeterioration_active'];
			}
			return $array;
		}
	}
?>	