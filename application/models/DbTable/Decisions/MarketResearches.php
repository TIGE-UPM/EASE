<?php
	class Model_DbTable_Decisions_MarketResearches extends Zend_Db_Table{
		protected $_name = 'decision_marketresearches_registry';
	
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
			$mr_channel= new Model_DbTable_Decisions_Mr_Channel();
			$mr_prices= new Model_DbTable_Decisions_Mr_Prices();
			$mr_mkt= new Model_DbTable_Decisions_Mr_Mkt();
			$mr_spected= new Model_DbTable_Decisions_Mr_Spected();
			$mr_account= new Model_DbTable_Decisions_Mr_Account();
			// si no existe ya una decisión para esta ronda se crea
			if ($this->fetchRow('game_id = '.$game_id.
								' AND company_id = '.$company_id.
								' AND round_number = '.$round_number)==null){
				$mr_channel->add($decisionData, $game_id, $company_id, $round_number);
				$mr_prices->add($decisionData, $game_id, $company_id, $round_number);
				$mr_mkt->add($decisionData, $game_id, $company_id, $round_number);
				$mr_spected->add($decisionData, $game_id, $company_id, $round_number);
				$mr_account->add($decisionData, $game_id, $company_id, $round_number);
				$this->insert(array('game_id'=>$game_id, 
									'company_id' => $company_id, 
									'round_number' => $round_number, 
									'date'=>$date));
			}
			else{ //si ya se tomó con anterioridad se sobreescribe
				$mr_channel->updateDecision($decisionData, $game_id, $company_id, $round_number);
				$mr_prices->updateDecision($decisionData, $game_id, $company_id, $round_number);
				$mr_mkt->updateDecision($decisionData, $game_id, $company_id, $round_number);
				$mr_spected->updateDecision($decisionData, $game_id, $company_id, $round_number);
				$mr_account->updateDecision($decisionData, $game_id, $company_id, $round_number);
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
			$mr_channel= new Model_DbTable_Decisions_Mr_Channel();
			$mr_prices= new Model_DbTable_Decisions_Mr_Prices();
			$mr_mkt= new Model_DbTable_Decisions_Mr_Mkt();
			$mr_spected= new Model_DbTable_Decisions_Mr_Spected();
			$mr_account= new Model_DbTable_Decisions_Mr_Account();
			return array('channelResearch'=>$mr_channel->getActiveRoundLastDecisionSaved(), 
						 'pricesResearch'=>$mr_prices->getActiveRoundLastDecisionSaved(),
						 'mktResearch'=>$mr_mkt->getActiveRoundLastDecisionSaved(),
						 'spectedResearch'=>$mr_spected->getActiveRoundLastDecisionSaved(),
			             'accountResearch'=>$mr_account->getActiveRoundLastDecisionSaved());
		}
		function getDecision($game_id, $company_id, $round_number){
			return $this->fetchRow('game_id = '.$game_id.
							' AND company_id = '.$company_id.
							' AND round_number = '.$round_number);
		}
		function getDecisionArray($game_id, $company_id, $round_number){
			$mr_channel= new Model_DbTable_Decisions_Mr_Channel();
			$mr_prices= new Model_DbTable_Decisions_Mr_Prices();
			$mr_mkt= new Model_DbTable_Decisions_Mr_Mkt();
			$mr_spected= new Model_DbTable_Decisions_Mr_Spected();
			$mr_account= new Model_DbTable_Decisions_Mr_Account();
			return array('channelResearch'=>$mr_channel->getDecision($game_id, $company_id, $round_number), 
						 'pricesResearch'=>$mr_prices->getDecision($game_id, $company_id, $round_number),
						 'mktResearch'=>$mr_mkt->getDecision($game_id, $company_id, $round_number),
						 'spectedResearch'=>$mr_spected->getDecision($game_id, $company_id, $round_number),
			             'accountResearch'=>$mr_account->getDecision($game_id, $company_id, $round_number));
		}
		
		function getMarketResearchesSolicited($game_id, $company_id, $round_number){
			$mr_channel= new Model_DbTable_Decisions_Mr_Channel();
			$mr_prices= new Model_DbTable_Decisions_Mr_Prices();
			$mr_mkt= new Model_DbTable_Decisions_Mr_Mkt();
			$mr_spected= new Model_DbTable_Decisions_Mr_Spected();
			$mr_account= new Model_DbTable_Decisions_Mr_Account();
			$solC=$mr_channel->getDecision($game_id, $company_id, $round_number);
			$solP=$mr_prices->getDecision($game_id, $company_id, $round_number);
			$solM=$mr_mkt->getDecision($game_id, $company_id, $round_number);
			$solS=$mr_spected->getDecision($game_id, $company_id, $round_number);
			$solA=$mr_account->getDecision($game_id, $company_id, $round_number);
			return array('channelResearch'=>$solC['active'], 
						 'pricesResearch'=>$solP['active'],
						 'mktResearch'=>$solM['active'],
						 'spectedResearch'=>$solS['active'],
			             'accountResearch'=>$solA['active']);
		}
		
	}
	
?>