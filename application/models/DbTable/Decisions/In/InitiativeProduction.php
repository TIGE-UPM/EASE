<?php
	class Model_DbTable_Decisions_In_InitiativeProduction extends Zend_Db_Table{
		protected $_name = 'decision_initiatives_initiativeproduction';
		
		
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
			$initiativeData=$parameters['initiativeproduction'];
			//var_dump($initiativeslength); die();
			$production_initiative_number=1;
			while(isset($initiativeData['initiativeproduction_number_'.$production_initiative_number])){
				$active=$initiativeData['initiativeproduction_number_'.$production_initiative_number];
				self::insert(array('game_id'=>$game_id, 'company_id' => $company_id, 'round_number' => $round_number, 'initiativeproduction_number'=>$production_initiative_number, 'initiativeproduction_active'=>$active));
				$production_initiative_number++;
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
			$initiativeData=$parameters['initiativeproduction'];
			$production_initiative_number=1;
			while(isset($initiativeData['initiativeproduction_number_'.$production_initiative_number])){
				$active=$initiativeData['initiativeproduction_number_'.$production_initiative_number];
				self::update(array('initiativeproduction_active'=>$active), 'game_id = '.$game_id.' AND company_id = '.$company_id.' AND round_number = '.$round_number.' AND initiativeproduction_number= '.$production_initiative_number);
				$production_initiative_number++;
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
				$array['initiativeproduction_number_'.$decision['initiativeproduction_number']]=$decision['initiativeproduction_active'];
			}
			return $array;
		}
		
		function getDecision($game_id, $company_id, $round_number){
			$array=array();
			$decisions=$this->fetchAll('game_id = '.$game_id.
								   ' AND company_id = '.$company_id.
								   ' AND round_number = '.$round_number);
			foreach ($decisions as $decision){
				$array['initiativeproduction_number_'.$decision['initiativeproduction_number']]=$decision['initiativeproduction_active'];
			}
			return $array;
		}
	}
?>	