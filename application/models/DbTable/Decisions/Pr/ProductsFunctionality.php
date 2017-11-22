<?php
	//VERO
	class Model_DbTable_Decisions_Pr_ProductsFunctionality extends Zend_Db_Table{
		protected $_name = 'decision_production_products_functionality';
		
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

			$productionFunctionalitiesData=$parameters['functionality_params'];

			$product_number=1;
			//var_dump($productionFunctionalitiesData);die();
			while (isset($productionFunctionalitiesData['product_number_'.$product_number])){
				$production_product=$productionFunctionalitiesData['product_number_'.$product_number];
				$functionality_number=1;
				while (isset($production_product['functionality_param_number_'.$functionality_number])){
					$production_functionality=$production_product['functionality_param_number_'.$functionality_number];
					self::insert(array('game_id'=>$game_id, 'company_id' => $company_id, 'product_number' => $product_number, 'functionality_param_number'=>$functionality_number, 'functionality_param_value'=>$production_functionality));
					$functionality_number++;
				}
				$product_number++;
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
			$productionFunctionalitiesData=$parameters['functionality_params'];

			$product_number=1;
			while (isset($productionFunctionalitiesData['product_number_'.$product_number])){
				$production_product=$productionFunctionalitiesData['product_number_'.$product_number];
				$functionality_number=1;
				while (isset($production_product['functionality_param_number_'.$functionality_number])){
					$production_functionality=$production_product['functionality_param_number_'.$functionality_number];
					self::update(array('functionality_param_value'=>$production_functionality), 'game_id = '.$game_id.' AND company_id = '.$company_id.'  AND product_number = '.$product_number.' AND functionality_param_number = '.$functionality_number);
					$functionality_number++;
				}
				$product_number++;
			}								
		}
		
		function getActiveRoundLastDecisionSaved(){
			$front = Zend_Controller_Front::getInstance();
			$game=$front->getParam('activeGame');
			$company=$front->getParam('activeCompany');
			$round=$front->getParam('activeRound');
			$array=array();
			$decisions=$this->fetchAll('game_id = '.$game['id'].
								   ' AND company_id = '.$company['id'],array('product_number ASC', 'functionality_param_number ASC'));

			$i=0;
			foreach ($decisions as $decision){
				if($product<>$decision['product_number']){
					$i=0;
					$product=$decision['product_number'];
				}
				if($decision['functionality_param_value']==1){
					$array['product_number_'.$decision['product_number']][$i]=$decision['functionality_param_number'];
					$i++;
				}
			}
			
			return $array;
		}
		
		function getDecision($game_id, $company_id){
			$array=array();
			$decisions=$this->fetchAll('game_id = '.$game_id.
								   ' AND company_id = '.$company_id,array('product_number ASC', 'functionality_param_number ASC'));

			$i=0;
			foreach ($decisions as $decision){
				if($product<>$decision['product_number']){
					$i=0;
					$product=$decision['product_number'];
				}
				if($decision['functionality_param_value']==1){
					$array['product_number_'.$decision['product_number']][$i]=$decision['functionality_param_number'];
					$i++;
				}
			}
			
			return $array;
			
		}
		function setFunctionalityParam($game_id, $company_id, $value, $product_number, $product_functionality_number){					
			//Convert to string
			$product_functionality_number="$product_functionality_number";
			self::update(array('functionality_param_value'=>$value), 'game_id = '.$game_id.' AND company_id = '.$company_id.' AND product_number = '.$product_number.' AND functionality_param_number = '.$product_functionality_number);	
		}

		function getFunctionalityByProductAndParamNumber($game_id, $company_id, $product_number, $functionality_param_number){
			$decisions=$this->fetchRow('game_id = '.$game_id.' AND company_id = '.$company_id. ' AND product_number = '.$product_number.' AND functionality_param_number = '.$functionality_param_number);
			return $decisions['functionality_param_value'];

		}
		
	}
	//VERO
?>