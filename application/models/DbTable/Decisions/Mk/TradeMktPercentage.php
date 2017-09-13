<?php
	class Model_DbTable_Decisions_Mk_TradeMktPercentage extends Zend_Db_Table{
		protected $_name = 'decision_marketing_trademktpercentage';
		
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
			$trademktData=$parameters['trademkt_percentage'];
			$product_number=1;
			while (isset($trademktData['product_'.$product_number])){
				$trademkt_product=$trademktData['product_'.$product_number];
				$trademedia_number=1;
				while (isset($trademkt_product['trademedia_'.$trademedia_number])){
					$trademkt_trademedia=$trademkt_product['trademedia_'.$trademedia_number];
					$channel_number=1;
					while (isset($trademkt_trademedia['channel_'.$channel_number])){
						$trademkt_percentage=$trademkt_trademedia['channel_'.$channel_number];
						self::insert(array('game_id'=>$game_id, 'company_id' => $company_id, 'round_number' => $round_number, 'product_number' => $product_number, 'trademedia_number'=>$trademedia_number, 'channel_number'=>$channel_number, 'trademkt_percentage'=>$trademkt_percentage));
					$channel_number++;
					//echo("<br/> channel Write 1");
					//var_dump($trademkt_percentage);
					}
				$trademedia_number++;
				//echo("<br/> TradeMedia Write 2");
				//var_dump($trademkt_trademedia);
				}
			$product_number++;
			//echo("<br/> Product Write 3");
			//var_dump($trademkt_product);
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
			$trademktData=$parameters['trademkt_percentage'];
			$product_number=1;
			while (isset($trademktData['product_'.$product_number])){
				$trademkt_product=$trademktData['product_'.$product_number];
				$trademedia_number=1;
				while (isset($trademkt_product['trademedia_'.$trademedia_number])){
					$trademkt_trademedia=$trademkt_product['trademedia_'.$trademedia_number];
					$channel_number=1;
					while (isset($trademkt_trademedia['channel_'.$channel_number])){
						$trademkt_percentage=$trademkt_trademedia['channel_'.$channel_number];
						self::update(array('trademkt_percentage'=>$trademkt_percentage), 'game_id = '.$game_id.' AND company_id = '.$company_id.' AND product_number = '.$product_number.' AND trademedia_number = '.$trademedia_number.' AND channel_number = '.$channel_number. ' AND round_number = '.$round_number);
						
					$channel_number++;
					//echo("<br/> channel Update 1");
					//var_dump($trademkt_percentage);
					}
				$trademedia_number++;
				//echo("<br/> TradeMedia Update 2");
				//var_dump($trademkt_trademedia);
				}
			$product_number++;
			//echo("<br/> RProduct Update 3");
			//var_dump($trademkt_product);
			}							
		}
		function getActiveRoundLastDecisionSaved(){
			$front = Zend_Controller_Front::getInstance();
			$game=$front->getParam('activeGame');
			$company=$front->getParam('activeCompany');
			$round=$front->getParam('activeRound');
			$array=array();
			$decisions=$this->fetchAll('game_id = '.$game['id'].' AND company_id = '.$company['id'].
									   ' AND round_number = '.$round['round_number'], 
									    array('product_number ASC', 'channel_number ASC', 'trademedia_number ASC'));
			foreach ($decisions as $decision){
				$array['product_'.$decision['product_number']]
					  ['trademedia_'.$decision['trademedia_number']]
					  ['channel_'.$decision['channel_number']]=$decision['trademkt_percentage'];
			}
			return $array;		
		}
		function getDecision($game_id, $company_id, $round_number){
			$array=array();
			$decisions=$this->fetchAll('game_id = '.$game_id.
								   ' AND company_id = '.$company_id.
								   ' AND round_number = '.$round_number, array('product_number ASC', 'channel_number ASC', 'trademedia_number ASC'));
			foreach ($decisions as $decision){
				$array['product_'.$decision['product_number']]
				      ['trademedia_'.$decision['trademedia_number']]	  
					  ['channel_'.$decision['channel_number']]=$decision['trademkt_percentage'];
			}

			return $array;
		}
	}
?>