<?php
	class Model_DbTable_Decisions_Stock extends Zend_Db_Table{
		protected $_name = 'decision_stock_registry';
		
		function processDecision($decisionData, $game_id=null, $company_id=null, $round_number=null){
			$st_decisions=new Model_DbTable_Decisions_Stock();
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
			$date=date( 'Y-m-d H:i:s', time());
			$st_final_stock= new Model_DbTable_Decisions_St_StockFinal();
			$st_distribution= new Model_DbTable_Decisions_St_Distribution();
			// si no existe ya una decisión para esta ronda se crea
			if ($this->fetchRow('game_id = '.$game_id.
								' AND company_id = '.$company_id.
								' AND round_number = '.$round_number)==null){
				$st_final_stock->add($decisionData, $game_id, $company_id, $round_number);
				$st_distribution->add($decisionData, $game_id, $company_id, $round_number);
				$this->insert(array('game_id'=>$game_id, 
									'company_id' => $company_id, 
									'round_number' => $round_number, 
									'date'=>$date));
			}
			else{ //si ya se tomó con anterioridad se sobreescrib
				$st_final_stock->updateDecision($decisionData, $game_id, $company_id, $round_number);
				$st_distribution->updateDecision($decisionData, $game_id, $company_id, $round_number);
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
			$st_final_stock= new Model_DbTable_Decisions_St_StockFinal();
			$st_distribution= new Model_DbTable_Decisions_St_Distribution();
			return array('unitsStock'=>$st_final_stock->getActiveRoundLastDecisionSaved(),
						'distributionStock'=>$st_distribution->getActiveRoundLastDecisionSaved());
		}
		function getDecision($game_id, $company_id, $round_number){
			return $this->fetchRow('game_id = '.$game_id.
							' AND company_id = '.$company_id.
							' AND round_number = '.$round_number);
		}
		function getDecisionArray($game_id, $company_id, $round_number){
			$st_final_stock= new Model_DbTable_Decisions_St_StockFinal();
			$st_distribution= new Model_DbTable_Decisions_St_Distribution();
			return array('unitsStock'=>$st_final_stock->getDecision($game_id, $company_id, $round_number),
				'distributionStock'=>$st_distribution->getDecision($game_id, $company_id, $round_number));
		}
		
		function getUnits($game_id, $company_id, $round_number){
			$st_final_stock= new Model_DbTable_Decisions_St_StockFinal();
			return $st_final_stock->getDecision($game_id, $company_id, $round_number);
		}
		function getStockByMarket($game_id, $round_number, $company_id, $product_number, $region_number, $channel_number){
			$result=$this->fetchRow('game_id = '.$game_id.' AND round_number = '.$round_number.' AND company_id = '.$company_id.
									 ' AND product_number = '.$product_number.' AND region_number = '.$region_number.' AND channel_number = '.$channel_number);
			return $result['units'];
		}
		function getStockTotalRow($game_id, $round_number, $company_id, $product_number, $region_number, $channel_number){
			$result= $this->fetchRow('game_id = '.$game_id.' AND round_number = '.$round_number.' AND company_id = '.$company_id.
									 ' AND product_number = '.$product_number.' AND region_number = '.$region_number.' AND channel_number = '.$channel_number);
			return $result;
		}


	}
?>