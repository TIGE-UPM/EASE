<?php
	class Model_DbTable_Decisions_Su_Payterms extends Zend_Db_Table{
		protected $_name = 'decision_suppliers_payterms';
		
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
			$paytermsData=$parameters['payterms'];
			$channel_number=1;
			while (isset($paytermsData['channel_'.$channel_number])){
				$channel_payterm=$paytermsData['channel_'.$channel_number];
				self::insert(array('game_id'=>$game_id, 'company_id' => $company_id, 'round_number' => $round_number, 
								   'channel_number' => $channel_number, 'payterm'=>$channel_payterm));
				$channel_number++;
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
			$paytermsData_aux=$parameters['payterms'];
			$channel_number=1;
			//var_dump($paytermsData);
			$games=new Model_DbTable_Games();
			$n_channels=$games->getNumberOfChannels($game_id);
			//var_dump($n_channels);die();
			for ($index = 1; $index <= $n_channels; $index++) {
				$paytermsData['channel_'.$index]=$paytermsData_aux['channel_1'];			
			}
			while (isset($paytermsData['channel_'.$channel_number])){
				$channel_payterm=$paytermsData['channel_'.$channel_number];
				self::update(array('payterm'=>$channel_payterm), 'game_id = '.$game_id.' AND company_id = '.$company_id.' AND channel_number = '.$channel_number.' AND round_number = '.$round_number);
				$channel_number++;
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
								   ' AND round_number = '.$round['round_number'], 'channel_number ASC');
			foreach ($decisions as $decision){
				$array['channel_'.$decision['channel_number']]=$decision['payterm'];
			}
			return $array;
		}
		function getDecision($game_id, $company_id, $round_number){
			$array=array();
			$decisions=$this->fetchAll('game_id = '.$game_id.
								   ' AND company_id = '.$company_id.
								   ' AND round_number = '.$round_number, 'channel_number ASC');
			foreach ($decisions as $decision){
				$array['channel_'.$decision['channel_number']]=$decision['payterm'];
			}
			return $array;
		}
		
	}
?>