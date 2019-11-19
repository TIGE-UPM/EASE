<?php
	class Model_DbTable_Decisions_Idi extends Zend_Db_Table{
		protected $_name = 'decision_idi_registry';
		
	function processDecision($decisionData, $game_id=null, $company_id=null, $round_number=null){
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
			//var_dump($decisionData); die();			
			$date=date( 'Y-m-d H:i:s', time());
			$idi_changes= new Model_DbTable_Decisions_Idi_Changes();
			$idi_new= new Model_DbTable_Decisions_Idi_New();
			// si no existe ya una decisión para esta ronda se crea
			if ($this->fetchRow('game_id = '.$game_id.
								' AND company_id = '.$company_id.
								' AND round_number = '.$round_number)==null){
				$idi_changes->add($decisionData, $game_id, $company_id, $round_number);
				$idi_new->add($decisionData, $game_id, $company_id, $round_number);
				$this->insert(array('game_id'=>$game_id, 
									'company_id' => $company_id, 
									'round_number' => $round_number, 
									'date'=>$date));
			}
			else{ //si ya se tomó con anterioridad se sobreescribe
				$idi_changes->updateDecision($decisionData, $game_id, $company_id, $round_number);
				$idi_new->updateDecision($decisionData, $game_id, $company_id, $round_number);
				$this->update(array('date'=>$date), 
								  'game_id = '.$game_id.
								  ' AND company_id = '.$company_id.
								  ' AND round_number = '.$round_number);
			}
		}
		
		function existsPrevious($game_id=null, $company_id=null, $round_number=null){
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
			$previous=$this->fetchRow('game_id = '.$game_id.
								' AND company_id = '.$company_id.
								' AND round_number = '.$round_number);
			if ($previous==null){ 
				return false;			
			}
			return true;
		}
		
		function getActiveRoundDecisionRegistry(){
			$front = Zend_Controller_Front::getInstance();
			$game=$front->getParam('activeGame');
			$company=$front->getParam('activeCompany');
			$round=$front->getParam('activeRound');
			return $this->fetchRow('game_id = '.$game['id'].' AND company_id = '.$company['id'].' AND round_number = '.$round['round_number']);
		}
		
		function getActiveRoundLastDecisionSaved(){
			$front = Zend_Controller_Front::getInstance();
			$game=$front->getParam('activeGame');
			$company=$front->getParam('activeCompany');
			$round=$front->getParam('activeRound');
			$idi_changes= new Model_DbTable_Decisions_Idi_Changes();
			$idi_new= new Model_DbTable_Decisions_Idi_New();
			return array('changeIdi'=>$idi_changes->getActiveRoundLastDecisionSaved(), 
						 'newIdi'=>$idi_new->getActiveRoundLastDecisionSaved());
		}
		function getDecision($game_id, $company_id, $round_number){
			return $this->fetchRow('game_id = '.$game_id.
							' AND company_id = '.$company_id.
							' AND round_number = '.$round_number);
		}
		
		function getDecisionArray($game_id, $company_id, $round_number){
			$idi_changes= new Model_DbTable_Decisions_Idi_Changes();
			$idi_new= new Model_DbTable_Decisions_Idi_New();
			return array('changeIdi'=>$idi_changes->getDecision($game_id, $company_id, $round_number), 
						 'newIdi'=>$idi_new->getDecision($game_id, $company_id, $round_number));
		}
		
		function getNewIdiProductsSolicited($game_id, $company_id, $round_number){
			$array=array();
			$idi_new= new Model_DbTable_Decisions_Idi_New();
			$decisions=$idi_new->fetchAll('game_id = '.$game_id.
								   ' AND company_id = '.$company_id.
								   ' AND round_number = '.$round_number, 'idi_product_number ASC');
			foreach ($decisions as $decision){
				$array['idiproduct_'.$decision['idi_product_number']]=$decision['idi_new_decision'];
			}
			return $array;
		}
		
		function getTotalNewIdiProductsBudget($game_id, $company_id){//, $round_number){
			$array=array();
			$idi_new= new Model_DbTable_Decisions_Idi_New();
			/*$decisions=$idi_new->fetchAll('game_id = '.$game_id.
								   ' AND company_id = '.$company_id.
								   ' AND round_number = '.$round_number, 'idi_product_number ASC');*/
			$decisions=$idi_new->fetchAll('game_id = '.$game_id.
								   ' AND company_id = '.$company_id, 'idi_product_number ASC');					   
			foreach ($decisions as $decision){
				$array['idiproduct_'.$decision['idi_product_number']]+=$decision['idi_new_budget'];
			}
			return $array;
		}

		function getRoundNewIdiProductsBudget($game_id, $company_id, $round_number){
			$array=array();
			$idi_new= new Model_DbTable_Decisions_Idi_New();
			$decisions=$idi_new->fetchAll('game_id = '.$game_id.
								   ' AND company_id = '.$company_id.
								   ' AND round_number = '.$round_number, 'idi_product_number ASC');
			foreach ($decisions as $decision){
				$array['idiproduct_'.$decision['idi_product_number']]=$decision['idi_new_budget'];
			}
			return $array;
		}
		
		function getNewIdiProductsNumber($game_id, $company_id, $round_number){
			$array=array();
			$idi_new= new Model_DbTable_Decisions_Idi_New();
			$decisions=$idi_new->fetchAll('game_id = '.$game_id.
								   ' AND company_id = '.$company_id.
								   ' AND round_number = '.$round_number, 'idi_product_number ASC');
			foreach ($decisions as $decision){
				$array['idiproduct_'.$decision['idi_product_number']]=$decision['product_number'];
			}
			return $array;
		}
		
		function getIdiChangesInProducts($game_id, $company_id){  //, $round_number){
			$array=array();
			$idi_changes= new Model_DbTable_Decisions_Idi_Changes();
			/*$decisions=$idi_changes->fetchAll('game_id = '.$game_id.
								   ' AND company_id = '.$company_id.
								   ' AND round_number = '.$round_number);*/
			/* 20191118 Eliminado $round_number para que sume las subidas de calidad de todas las rondas */
			$decisions=$idi_changes->fetchAll('game_id = '.$game_id.
								   ' AND company_id = '.$company_id);			   
			foreach ($decisions as $decision){
				$array['product_'.$decision['product_number']]['product_quality_'.$decision['product_quality']]+=$decision['changes'];
				// echo("<br/>DUMP");
				// var_dump($array);
				// echo("<br/>END DUMP<br/>");
				
			}
			return $array;
		}
	

		/* 20191118 Añadida función para que calcule los costes de cambios únicamente de la ronda actual */	
		function getIdiChangesInProductsThisRound($game_id, $company_id, $round_number){
			$array=array();
			$idi_changes= new Model_DbTable_Decisions_Idi_Changes();
			$decisions=$idi_changes->fetchAll('game_id = '.$game_id.
								   ' AND company_id = '.$company_id.
								   ' AND round_number = '.$round_number);
			foreach ($decisions as $decision){
				$array['product_'.$decision['product_number']]['product_quality_'.$decision['product_quality']]+=$decision['changes'];
			}
			return $array;
		}
	
		/* 20191118 Añadida función para que calcule los costes de cambios hasta la ronda actual. Usado por el DecisionController */	
		function getIdiChangesInProductsUpToThisRound($game_id, $company_id, $round_number){
			$array=array();
			$idi_changes= new Model_DbTable_Decisions_Idi_Changes();
			$decisions=$idi_changes->fetchAll('game_id = '.$game_id.
								   ' AND company_id = '.$company_id);
			foreach ($decisions as $decision){
				if($decision['round_number']<=$round_number){
					$array['product_'.$decision['product_number']]['product_quality_'.$decision['product_quality']]+=$decision['changes'];
				}
			}
			return $array;
		}
	
	
	}

?>