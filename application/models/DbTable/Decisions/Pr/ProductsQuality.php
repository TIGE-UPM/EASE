<?php
	/*
	Actualizado el 20151104 para DAEM, de forma que guarde bien la decisión acerca de calidades de nuevos desarrollos (con el bucle While no entra al ser el índice de producto mayor que 1.
	*/
	class Model_DbTable_Decisions_Pr_ProductsQuality extends Zend_Db_Table{
		protected $_name = 'decision_production_products_quality';
		
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
			//var_dump($parameters); die();
			$productionQualities=$parameters['quality_params'];

			$auxLoop = New Model_DbTable_Games();	// Para sacar el número de productos y calidades para ejecutar los bucles guardando correctamente las decisiones de calidad en ronda > 1
			$nQual = $auxLoop->getNumberOfQualities($game_id);
			$nProd = $auxLoop->getNumberOfProducts($game_id);

			
			for ($i=1;$i<=$nQual;$i++) {				
				if (isset($productionQualities['product_quality_'.$i])){
					$productionQSet=$productionQualities['product_quality_'.$i];
					for ($j=1;$j<=$nProd;$j++){
						if (isset($productionQSet['product_'.$j])){
							$quality_param_value=$productionQSet['product_'.$j];
							self::insert(array('game_id'=>$game_id, 'company_id' => $company_id, 
								   'product_number' => $j, 'quality_param_number'=>$i, 'quality_param_value'=>$quality_param_value));
						}
					}
				}
			}
			// $product_quality_number=1;
			// while (isset($productionQualities['product_quality_'.$product_quality_number])){
				// $productionQSet=$productionQualities['product_quality_'.$product_quality_number];
				// $product_number=1;
				// while (isset($productionQSet['product_'.$product_number])){
					// $quality_param_value=$productionQSet['product_'.$product_number];
					// self::insert(array('game_id'=>$game_id, 'company_id' => $company_id, 
								   // 'product_number' => $product_number, 'quality_param_number'=>$product_quality_number, 'quality_param_value'=>$quality_param_value));
					// $product_number++;
				// }
				// $product_quality_number++;	
			// }
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
				
			$productionQualities=$parameters['quality_params'];

			$auxLoop = New Model_DbTable_Games();	// Para sacar el número de productos y calidades para ejecutar los bucles guardando correctamente las decisiones de calidad en ronda > 1
			$nQual = $auxLoop->getNumberOfQualities($game_id);
			$nProd = $auxLoop->getNumberOfProducts($game_id);
			
			for ($i=1;$i<=$nQual;$i++) {					
				if (isset($productionQualities['product_quality_'.$i])){
					$productionQSet=$productionQualities['product_quality_'.$i];
					for ($j=1;$j<=$nProd;$j++){	
						if (isset($productionQSet['product_'.$j])){
							$quality_param_value=$productionQSet['product_'.$j];
							self::update(array('quality_param_value'=>$quality_param_value), 'game_id = '.$game_id.' AND company_id = '.$company_id.' AND product_number = '.$j.' AND quality_param_number = '.$i);
						}
					}
				}
			}
			
			// while (isset($productionQualities['product_quality_'.$product_quality_number])){
				// $productionQSet=$productionQualities['product_quality_'.$product_quality_number];
				// $product_number=1;
				// while (isset($productionQSet['product_'.$product_number])){
					// die("eo");
					// $quality_param_value=$productionQSet['product_'.$product_number];
					// self::update(array('quality_param_value'=>$quality_param_value), 'game_id = '.$game_id.' AND company_id = '.$company_id.' AND product_number = '.$product_number.' AND quality_param_number = '.$product_quality_number);
					// $product_number++;
				// }
				// $product_quality_number++;	
			// }									
		}
		
		function getActiveRoundLastDecisionSaved(){
			$front = Zend_Controller_Front::getInstance();
			$game=$front->getParam('activeGame');
			$company=$front->getParam('activeCompany');
			$round=$front->getParam('activeRound');
			$array=array();
			$decisions=$this->fetchAll('game_id = '.$game['id'].
								     	' AND company_id = '.$company['id'], 'product_number ASC', 'quality_param_number ASC');
			foreach ($decisions as $decision){
				$array['product_quality_'.$decision['quality_param_number']]
					  ['product_'.$decision['product_number']]=$decision['quality_param_value'];
			}
			return $array;
		}
		
		function getDecision($game_id, $company_id){
			$array=array();
			$decisions=$this->fetchAll('game_id = '.$game_id.
								   ' AND company_id = '.$company_id,array('quality_param_number ASC', 'product_number ASC'));
			foreach ($decisions as $decision){
				$array['product_quality_'.$decision['quality_param_number']]
					  ['product_'.$decision['product_number']]=$decision['quality_param_value'];
			}
			return $array;
			
		}
		function setQualityParam($game_id, $company_id, $value, $product_number, $product_quality_number){					
			//Convert to string
			$product_quality_number="$product_quality_number";
			self::update(array('quality_param_value'=>$value), 'game_id = '.$game_id.' AND company_id = '.$company_id.' AND product_number = '.$product_number.' AND quality_param_number = '.$product_quality_number);	
		}
		
	}
?>