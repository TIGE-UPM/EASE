<?php
	class Model_DbTable_Decisions_Production extends Zend_Db_Table{
		protected $_name = 'decision_production_registry';
		
		function processDecision($decisionData, $game_id=null, $company_id=null, $round_number=null){
			$front = Zend_Controller_Front::getInstance();
			//var_dump($game_id);die();
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
			$pr_regions= new Model_DbTable_Decisions_Pr_Region();
			$pr_capacities= new Model_DbTable_Decisions_Pr_Capacity();
			$pr_quality= new Model_DbTable_Decisions_Pr_ProductsQuality();
			//VERO
			$pr_functionality= new Model_DbTable_Decisions_Pr_ProductsFunctionality();
			//VERO
			$pr_units= new Model_DbTable_Decisions_Pr_Units();
			//JESUS 
			$pr_HQ = new Model_DbTable_Decisions_Pr_Headquarters();

			// si no existe ya una decisión para esta ronda se crea
			if ($this->fetchRow('game_id = '.$game_id.
								' AND company_id = '.$company_id.
								' AND round_number = '.$round_number)==null){
				//$pr_factories->add($decisionData, $game_id, $company_id, $round_number);
				//die("1");	
				$pr_regions->add($decisionData, $game_id, $company_id, $round_number);
				$pr_capacities->add($decisionData, $game_id, $company_id, $round_number);
				$pr_quality->add($decisionData, $game_id, $company_id, $round_number);
				//VERO
				$pr_functionality->add($decisionData, $game_id, $company_id, $round_number);
				//VERO
				$pr_units->add($decisionData, $game_id, $company_id, $round_number);
				//JESUS
				$pr_HQ->add($decisionData, $game_id, $company_id);
				$this->insert(array('game_id'=>$game_id, 
									'company_id' => $company_id, 
									'round_number' => $round_number, 
									'date'=>$date));
			}
			else{ //si ya se tomó con anterioridad se sobreescribe
				$pr_regions->updateDecision($decisionData, $game_id, $company_id, $round_number);
				//die("2");	
				$pr_capacities->updateDecision($decisionData, $game_id, $company_id, $round_number);
				$pr_quality->updateDecision($decisionData, $game_id, $company_id, $round_number);
				//VERO
				$pr_functionality->updateDecision($decisionData, $game_id, $company_id, $round_number);
				//VERO
				$pr_units->updateDecision($decisionData, $game_id, $company_id, $round_number);
				//JESUS
				$pr_HQ->updateDecision($decisionData, $game_id, $company_id);
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
			$pr_regions= new Model_DbTable_Decisions_Pr_Region();
			$pr_capacities= new Model_DbTable_Decisions_Pr_Capacity();
			$pr_quality= new Model_DbTable_Decisions_Pr_ProductsQuality();
			//VERO
			$pr_functionality= new Model_DbTable_Decisions_Pr_ProductsFunctionality();
			//VERO
			$pr_units= new Model_DbTable_Decisions_Pr_Units();
			//JESUS
			$pr_HQ = new Model_DbTable_Decisions_Pr_Headquarters();
			// $cap = $pr_capacities->getTotalMachinesExtFactory($game['id'], $company['id']);
			// $cap2 = $pr_capacities->getMachinesExtFactory($game['id'], $company['id']);
			// var_dump($cap); echo("<br/><br/>"); var_dump($cap2);die("2");
			return array('factories'=>$pr_regions->getActiveRoundLastDecisionSaved(),
						 'capacity'=>$pr_capacities->getActiveRoundLastDecisionSaved(), 
						 'qualities'=>$pr_quality->getActiveRoundLastDecisionSaved(),
						 'functionalities'=>$pr_functionality->getActiveRoundLastDecisionSaved(),
						 'units'=>$pr_units->getActiveRoundLastDecisionSaved(),
						 'headquarters'=>$pr_HQ->getActiveRoundLastDecisionSaved());
		}
		
		function getDecision($game_id, $company_id, $round_number){
			$result=$this->fetchRow('game_id = '.$game_id.
							' AND company_id = '.$company_id.
							' AND round_number = '.$round_number);
			return $result;
		}
		function getDecisionArray($game_id, $company_id, $round_number){
			$pr_regions= new Model_DbTable_Decisions_Pr_Region();
			$pr_capacities= new Model_DbTable_Decisions_Pr_Capacity();
			$pr_quality= new Model_DbTable_Decisions_Pr_ProductsQuality();
			//VERO
			$pr_functionality= new Model_DbTable_Decisions_Pr_ProductsFunctionality();
			//VERO
			$pr_units= new Model_DbTable_Decisions_Pr_Units();
				//JESUS
			$pr_HQ = new Model_DbTable_Decisions_Pr_Headquarters();
			/*var_dump(array('factories'=>$pr_regions->getDecision($game_id, $company_id),
						 'capacity'=>$pr_capacities->getDecision($game_id, $company_id), 
						 'qualities'=>$pr_quality->getDecision($game_id, $company_id),
						 'units'=>$pr_units->getDecision($game_id, $company_id, $round_number)));die();*/
			return array('factories'=>$pr_regions->getDecision($game_id, $company_id),
						 'capacity'=>$pr_capacities->getDecision($game_id, $company_id), 
						 'qualities'=>$pr_quality->getDecision($game_id, $company_id),
						 'functionalities'=>$pr_functionality->getDecision($game_id, $company_id),
						 'units'=>$pr_units->getDecision($game_id, $company_id, $round_number),
						 'headquarters'=>$pr_HQ->getDecision($game_id, $company_id));
		}
		
		//funciona perfectamente
		//devuelve los atributos de calidad guardados
		function getQualitiesArray($game_id, $company_id){
			$pr_quality= new Model_DbTable_Decisions_Pr_ProductsQuality();
			$results=$pr_quality->fetchAll('game_id = '.$game_id.
							' AND company_id = '.$company_id, array('product_number ASC', 'quality_param_number ASC'));
			foreach ($results as $result){
				$array['product_'.$result['product_number']]
					  ['quality_param_'.$result['quality_param_number']]=$result['quality_param_value'];
			}
			//var_dump($array); die();
			return $array;
		}

		//VERO
		function getFunctionalitiesArray($game_id, $company_id){
			$pr_functionality= new Model_DbTable_Decisions_Pr_ProductsFunctionality();
			$results=$pr_functionality->fetchAll('game_id = '.$game_id.
							' AND company_id = '.$company_id, array('product_number ASC', 'functionality_param_number ASC'));
			foreach ($results as $result){
				$array['product_'.$result['product_number']]
					  ['functionality_param_'.$result['functionality_param_number']]=$result['functionality_param_value'];
			}
			//var_dump($array); die();
			return $array;
		}
		//VERO
		
		//En principio no vamos a usar esta función
		function getQuality($game_id, $company_id, $product_number){
			$pr_quality= new Model_DbTable_Decisions_Pr_ProductsQuality();
			$pr_quality_product=$pr_quality->fetchRow('game_id = '.$game_id.
							' AND company_id = '.$company_id.
							' AND product_number = '.$product_number);
			$pr_quality_product_result = array("product_quality1" => $pr_quality_product['product_quality1'], "product_quality2" => $pr_quality_product['product_quality2'], "product_quality3" => $pr_quality_product['product_quality3']);
			return $pr_quality_product_result;
		}
		function getUnits($game_id, $company_id, $round_number, $product_number, $channel_number, $region_number){
			$pr_units= new Model_DbTable_Decisions_Pr_Units();
			$results= $pr_units->fetchRow('game_id = '.$game_id.
							' AND company_id = '.$company_id.
							' AND round_number = '.$round_number.
							' AND product_number = '.$product_number.
							' AND channel_number = '.$channel_number. 
							' AND region_number = '.$region_number);
			return $results['production_units'];
		}
		
		//funciona perfectamente
		//devuelve la produccion guardada
		function getUnitsArray($game_id, $company_id, $round_number){
			$pr_units= new Model_DbTable_Decisions_Pr_Units();
			$results=$pr_units->fetchAll('game_id = '.$game_id.
							' AND company_id = '.$company_id.
							' AND round_number = '.$round_number, array('factory_number ASC', 'product_number ASC', 'region_number ASC', 'channel_number ASC'));
			foreach ($results as $result){
				$array['factory_number_'.$result['factory_number']]
					  ['product_'.$result['product_number']]
					  ['region_'.$result['region_number']]
					  ['channel_'.$result['channel_number']]=$result['production_units'];
			}
			//var_dump($array); die();
			return $array;
		}
		
		//por el mometo nos da la regi�n en la que establecemos la primera f�brica
		//m�s adelante incluiremos el resto, pero por el momento podemos funcionar con
		//esta para ir probando el modelo
		function getRegion($game_id, $company_id){
			$pr_regions= new Model_DbTable_Decisions_Pr_Region();
			$first_factory=1;
			$result=$pr_regions->fetchRow('game_id = '.$game_id.
							' AND company_id = '.$company_id.
							' AND factory_number = '.$first_factory);
			//var_dump($result['region_number']);die();
			return $result['region_number'];
		}
		
		//ahora no se usa en initProdoction pero puede ser util más adelante
		//¿Puede dar problemas si sacamos primero la ronda y luego la fábrica? ¿Por qué no hacerlo por región? Revisar bien esto porque puede tener gran impacto en los bucles.
		function getFactories($game_id, $company_id, $round_number){
			$pr_factories= new Model_DbTable_Decisions_Pr_Region();
			$results=$pr_factories->fetchAll('game_id = '.$game_id.' AND company_id = '.$company_id, array('round_number_created ASC', 'factory_number ASC'));
			foreach ($results as $result){
				$array['factory_number_'.$result['factory_number']]=$result['region_number'];
			}
			//var_dump($results);die();
			return $array;
		}
		function getFactoriesObjects($game_id, $company_id, $round_number){
			$pr_factories= new Model_DbTable_Decisions_Pr_Region();
			$results=$pr_factories->fetchAll('game_id = '.$game_id.' AND company_id = '.$company_id, array('round_number_created ASC', 'factory_number ASC'));
			return $results;
		}
//JESUS
		function getCompanyHQ($game_id, $company_id){
			$pr_HQ = new Model_DbTable_Decisions_Pr_Headquarters();
			$array = $pr_HQ->fetchAll('game_id = '.$game_id.' AND company_id = '.$company_id);
			$results = $array['headquarters']->headquarters;

			return $results;


		}
	}
?>