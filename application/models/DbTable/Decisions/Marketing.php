<?php
	class Model_DbTable_Decisions_Marketing extends Zend_Db_Table{
		protected $_name = 'decision_marketing_registry';
		
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
			$date=date( 'Y-m-d H:i:s', time());
			$mk_prices= new Model_DbTable_Decisions_Mk_Prices();
			$mk_advertising_budget= new Model_DbTable_Decisions_Mk_AdvertisingBudget();
			$mk_advertising_budget_distribution= new Model_DbTable_Decisions_Mk_AdvertisingBudgetDistribution();
			$mk_advertising_percentage= new Model_DbTable_Decisions_Mk_AdvertisingPercentage();
			$mk_trademkt_budget= new Model_DbTable_Decisions_Mk_TradeMktBudget();
			$mk_trademkt_budget_distribution= new Model_DbTable_Decisions_Mk_TradeMktBudgetDistribution();
			$mk_trademkt_percentage= new Model_DbTable_Decisions_Mk_TradeMktPercentage();
			// si no existe ya una decisión para esta ronda se crea
			if ($this->fetchRow('game_id = '.$game_id.
								' AND company_id = '.$company_id.
								' AND round_number = '.$round_number)==null){
				$mk_prices->add($decisionData, $game_id, $company_id, $round_number);
				$mk_advertising_budget->add($decisionData, $game_id, $company_id, $round_number);
				$mk_advertising_budget_distribution->add($decisionData, $game_id, $company_id, $round_number);
				$mk_advertising_percentage->add($decisionData, $game_id, $company_id, $round_number);
				$mk_trademkt_budget->add($decisionData, $game_id, $company_id, $round_number);
				$mk_trademkt_budget_distribution->add($decisionData, $game_id, $company_id, $round_number);
				$mk_trademkt_percentage->add($decisionData, $game_id, $company_id, $round_number);
				$this->insert(array('game_id'=>$game_id, 
									'company_id' => $company_id, 
									'round_number' => $round_number, 
									'date'=>$date));
			}
			else{ //si ya se tomó con anterioridad se sobreescrib
				$mk_prices->updateDecision($decisionData, $game_id, $company_id, $round_number);
				$mk_advertising_budget->updateDecision($decisionData, $game_id, $company_id, $round_number);
				$mk_advertising_budget_distribution->updateDecision($decisionData, $game_id, $company_id, $round_number);
				$mk_advertising_percentage->updateDecision($decisionData, $game_id, $company_id, $round_number);
				$mk_trademkt_budget->updateDecision($decisionData, $game_id, $company_id, $round_number);
				$mk_trademkt_budget_distribution->updateDecision($decisionData, $game_id, $company_id, $round_number);
				$mk_trademkt_percentage->updateDecision($decisionData, $game_id, $company_id, $round_number);
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
			$mk_prices= new Model_DbTable_Decisions_Mk_Prices();
			$mk_advertising_budget= new Model_DbTable_Decisions_Mk_AdvertisingBudget();
			$mk_advertising_budget_distribution= new Model_DbTable_Decisions_Mk_AdvertisingBudgetDistribution();
			$mk_advertising_percentage= new Model_DbTable_Decisions_Mk_AdvertisingPercentage();
			$mk_trademkt_budget= new Model_DbTable_Decisions_Mk_TradeMktBudget();
			$mk_trademkt_budget_distribution= new Model_DbTable_Decisions_Mk_TradeMktBudgetDistribution();
			$mk_trademkt_percentage= new Model_DbTable_Decisions_Mk_TradeMktPercentage();
			return array('prices'=>$mk_prices->getActiveRoundLastDecisionSaved(),
						 'advertising_budget'=>$mk_advertising_budget->getActiveRoundLastDecisionSaved(),
						 'advertising_budget_distribution'=>$mk_advertising_budget_distribution->getActiveRoundLastDecisionSaved(), 
						 'advertising_percentage'=>$mk_advertising_percentage->getActiveRoundLastDecisionSaved(),
						 'trademkt_budget'=>$mk_trademkt_budget->getActiveRoundLastDecisionSaved(),
						 'trademkt_budget_distribution'=>$mk_trademkt_budget_distribution->getActiveRoundLastDecisionSaved(),
						 'trademkt_percentage'=>$mk_trademkt_percentage->getActiveRoundLastDecisionSaved());

		}
		function getDecision($game_id, $company_id, $round_number){
			return $this->fetchRow('game_id = '.$game_id.
							' AND company_id = '.$company_id.
							' AND round_number = '.$round_number);
		}
		function getDecisionArray($game_id, $company_id, $round_number){
			$mk_prices= new Model_DbTable_Decisions_Mk_Prices();
			$mk_advertising_budget= new Model_DbTable_Decisions_Mk_AdvertisingBudget();
			$mk_advertising_budget_distribution= new Model_DbTable_Decisions_Mk_AdvertisingBudgetDistribution();
			$mk_advertising_percentage= new Model_DbTable_Decisions_Mk_AdvertisingPercentage();
			$mk_trademkt_budget= new Model_DbTable_Decisions_Mk_TradeMktBudget();
			$mk_trademkt_budget_distribution= new Model_DbTable_Decisions_Mk_TradeMktBudgetDistribution();
			$mk_trademkt_percentage= new Model_DbTable_Decisions_Mk_TradeMktPercentage();
			return array('prices'=>$mk_prices->getDecision($game_id, $company_id, $round_number), 
						 'advertising_budget'=>$mk_advertising_budget->getDecision($game_id, $company_id, $round_number),
						 'advertising_budget_distribution'=>$mk_advertising_budget_distribution->getDecision($game_id, $company_id, $round_number),
						 'advertising_percentage'=>$mk_advertising_percentage->getDecision($game_id, $company_id, $round_number),
						 'trademkt_budget'=>$mk_trademkt_budget->getDecision($game_id, $company_id, $round_number),
					   	 'trademkt_budget_distribution'=>$mk_trademkt_budget_distribution->getDecision($game_id, $company_id, $round_number),
						 'trademkt_percentage'=>$mk_trademkt_percentage->getDecision($game_id, $company_id, $round_number));
		}
		
		function getPrices($game_id, $company_id, $round_number){
			$mk_prices=new Model_DbTable_Decisions_Mk_Prices();
			return $mk_prices->getDecision($game_id, $company_id, $round_number);
		}
		function getExportPrices($game_id, $round_number){
			$exportPrices=new Model_DbTable_Decisions_Mk_Prices();
			return $exportPrices->getPrices($game_id, $round_number);
			
		}
		function getAdvertisingsBudget($game_id, $company_id, $round_number){
			$mk_advertisings_budget=new Model_DbTable_Decisions_Mk_AdvertisingBudget();
			return $mk_advertisings_budget->getDecision($game_id, $company_id, $round_number);
		}
		function getAdvertisingBudgetDistribution($game_id, $company_id, $round_number){
			$mk_advertising_budget_distribution=new Model_DbTable_Decisions_Mk_AdvertisingBudgetDistribution();
			return $mk_advertising_budget_distribution->getDecision($game_id, $company_id, $round_number);
		}
		function getAdvertisingsPercentage($game_id, $company_id, $round_number){
			$mk_advertisings_percentage=new Model_DbTable_Decisions_Mk_AdvertisingPercentage();
			return $mk_advertisings_percentage->getDecision($game_id, $company_id, $round_number);
		}
		function getTradesMktBudget($game_id, $company_id, $round_number){
			$mk_tradesmkt_budget=new Model_DbTable_Decisions_Mk_TradeMktBudget();
			return $mk_tradesmkt_budget->getDecision($game_id, $company_id, $round_number);
		}
		function getTradeMktBudgetDistribution($game_id, $company_id, $round_number){
			$mk_tradesmkt_budget_distribution=new Model_DbTable_Decisions_Mk_TradeMktBudgetDistribution();
			return $mk_tradesmkt_budget_distribution->getDecision($game_id, $company_id, $round_number);
		}
		function getTradesMktPercentage($game_id, $company_id, $round_number){
			$mk_tradesmkt_percentage=new Model_DbTable_Decisions_Mk_TradeMktPercentage();
			return $mk_tradesmkt_percentage->getDecision($game_id, $company_id, $round_number);
		}
	}
?>