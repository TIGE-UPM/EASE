<?php
	class Model_DbTable_Decisions_Pr_Headquarters extends Zend_Db_Table{
		protected $_name = 'decision_production_HQ';
		
		function add($parameters, $game_id=null, $company_id=null){	
			$front = Zend_Controller_Front::getInstance();
			if ($game_id==null){
				$game=$front->getParam('activeGame');
				$game_id=$game['id'];
			}
			if ($company_id==null){
				$company=$front->getParam('activeCompany');
				$company_id=$company['id'];
			}
			
			
			$HQ_regions=$parameters['companyHQ'];
			
			if($HQ_regions==null) {return;}			
			
			self::insert(array('game_id'=>$game_id, 'company_id' => $company_id, 'headquarters' => $HQ_regions));
					
		}

		
		function getActiveRoundLastDecisionSaved(){
			$front = Zend_Controller_Front::getInstance();
			$game=$front->getParam('activeGame');
			$company=$front->getParam('activeCompany');
			$round=$front->getParam('activeRound');
			$decisions=$this->fetchAll('game_id = '.$game['id'].
								   ' AND company_id = '.$company['id']);
			foreach ($decisions as $decision){
				$array['headquarters']=$decision['headquarters'];
			}
			return $array;
		
		}
		function getDecision($game_id, $company_id){
			$array=array();
			$decisions=$this->fetchAll('game_id = '.$game_id.
								   ' AND company_id = '.$company_id);
		
			
			return $decisions['headquarters'];
		}
		function updateDecision($parameters, $game_id=null, $company_id=null){	
			$front = Zend_Controller_Front::getInstance();
			if ($game_id==null){
				$game=$front->getParam('activeGame');
				$game_id=$game['id'];
			}
			if ($company_id==null){
				$company=$front->getParam('activeCompany');
				$company_id=$company['id'];
			}
			
			$HQ_regions=$parameters['companyHQ'];


			if($HQ_regions==null) {return;}		


					
			self::update(array('headquarters'=>$HQ_regions), 'game_id = '.$game_id.' AND company_id = '.$company_id);
				
				}							
		}
		
	
?>