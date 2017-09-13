<?php
	class Model_DbTable_Decisions_Pr_Capacity extends Zend_Db_Table{
		protected $_name = 'decision_production_capacity';
		
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
			$capacity=$parameters['capacity'];
			$factory_number=1;
			//var_dump($capacity);die();
			while (isset($capacity['factory_number_'.$factory_number])){
					$capacity_added=$capacity['factory_number_'.$factory_number];
					self::insert(array('game_id'=>$game_id, 'company_id' => $company_id,'factory_number'=>$factory_number, 'capacity_added'=>$capacity_added, 'round_number_created'=>$round_number,
										'factory_text'=>"factory_number_".$factory_number));
					$factory_number++;
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
			$capacity=$parameters['capacity'];
			$factory_number=1;
			//var_dump($capacity);die();
			while (isset($capacity['factory_number_'.$factory_number])){
					$capacity_added=$capacity['factory_number_'.$factory_number];
					$addCapacity=$this->getDecisionCapacity($game_id, $round_number, $company_id, $factory_number);			
					if($addCapacity==null){
						if ($capacity_added>0) {			
							self::insert(array('game_id'=>$game_id, 'company_id' => $company_id,'factory_number'=>$factory_number, 'capacity_added'=>$capacity_added, 'round_number_created'=>$round_number,
												'factory_text'=>"factory_number_".$factory_number));
						}
					}
					else {
						if ($capacity_added==0) {	//OJO: ACTUALIZAR PARA QUE NO SE PUEDAN DESMANTELAR MÁQUINAS... ¿O SÍ EN EL FUTURO?						
							self::delete(array('game_id = '.$game_id.' AND company_id = '.$company_id.' AND factory_number = '.$factory_number.' AND round_number_created = '.$round_number,
												'factory_text'=>"factory_number_".$factory_number));
						} else {
							self::update(array('capacity_added'=>$capacity_added), 'game_id = '.$game_id.' AND company_id = '.$company_id.' AND factory_number = '.$factory_number.' AND round_number_created = '.
												$round_number.' AND factory_text = "factory_number_'.$factory_number.'"');
						}
					}
					$factory_number++;
				}
		}
		function getActiveRoundLastDecisionSaved(){
			$front = Zend_Controller_Front::getInstance();
			$game=$front->getParam('activeGame');
			$company=$front->getParam('activeCompany');
			$round=$front->getParam('activeRound');
			$decisions=$this->fetchAll('game_id = '.$game['id'].
								   ' AND company_id = '.$company['id'].
								   ' AND round_number_created = '.$round['round_number']);
			foreach ($decisions as $decision){
				$array['factory_number_'.$decision['factory_number']]=$decision['capacity_added'];
			}
			return $array;
		
		}
		function getDecision($game_id, $company_id){
			$array=array();
			$decisions=$this->fetchAll('game_id = '.$game_id.
								   ' AND company_id = '.$company_id);
			foreach ($decisions as $decision){
				$array['factory_number_'.$decision['factory_number']]=$decision['capacity_added'];
			}
			return $array;
		}
		function getDecisionRegion($game_id, $company_id, $factory_number){
			return $this->fetchAll('game_id = '.$game_id.
								   ' AND company_id = '.$company_id.
								   ' AND factory_number = '.$factory_number);
		}
		function getDecisionCapacity($game_id, $round_number, $company_id, $factory_number){
			return $this->fetchRow('game_id = '.$game_id.
								   ' AND company_id = '.$company_id.
								   ' AND factory_number = '.$factory_number.
								   ' AND round_number_created = '.$round_number);
		}
		function getNumberOfMachinesExtension($game_id, $round_number, $company_id, $factory_number){
			$results=$this->fetchRow('game_id = '.$game_id.
								   ' AND company_id = '.$company_id.
								   ' AND factory_number = '.$factory_number.
								   ' AND round_number_created = '.$round_number);
			return $results['capacity_added'];
		}	
		function getExtensionWasCreated($game_id, $company_id, $factory_number){
			$results=$this->fetchAll('game_id = '.$game_id.' AND company_id = '.$company_id, 'round_number_created ASC');
			foreach ($results as $result){
				//if ($result['capacity_added'] != 0) { 
					$array['factory_number_'.$result['factory_number']]['capacity_'.$result['round_number_created']]=$result['capacity_added'];
					$array['factory_number_'.$result['factory_number']]['round_number_created_'.$result['round_number_created']]=$result['round_number_created'];
				//}
			}
			echo("<br/><br/>");
			//var_dump($array);			
			return $array;
		}
		function getExtensionTotalArray($game_id, $company_id){
			$results=$this->fetchAll('game_id = '.$game_id.' AND company_id = '.$company_id, 'factory_number ASC');
			 $results=$this->fetchAll('game_id = '.$game_id.' AND company_id = '.$company_id, 'round_number_created ASC');
			 foreach ($results as $result){
				$array['factory_number_'.$result['factory_number']]['capacity_'.$result['round_number_created']]=$result['capacity_added'];
				$array['factory_number_'.$result['factory_number']]['round_number_created_'.$result['round_number_created']]=$result['round_number_created'];
			}
			return $array;
		}		
		
		// Esta función extrae el total agregado de máquinas que se han añadido en cada fábrica
		function getTotalMachinesExtFactory($game_id, $company_id){
			//$results=$this->fetchAll('game_id = '.$game_id.' AND company_id = '.$company_id, 'factory_number ASC');
			$sql = "SELECT dpc.* FROM decision_production_capacity dpc 
						INNER JOIN (
							SELECT factory_number, MAX( round_number_created ) AS MaxRNC
							FROM decision_production_capacity
							WHERE game_id = ".$game_id." AND company_id = ".$company_id."
							GROUP BY factory_number
						) groupeddpc
						ON dpc.factory_number = groupeddpc.factory_number
						AND dpc.round_number_created = groupeddpc.MaxRNC
						AND dpc.game_id = ".$game_id."
						AND dpc.company_id = ".$company_id."
						ORDER BY factory_number ASC";
			$db = Zend_Db_Table::getDefaultAdapter();
			$query = $db->query($sql);
			//$test=$this->query($sql);
			$machinesExtFactory = $query->fetchAll();
			if(count($machinesExtFactory)>0){
				return $machinesExtFactory;
			}			
		}
		

		// TODO: ***
		// Esta función extrae el número de máquinas por fábrica y ronda
		// Devuelve un array de dos elementos:
		// El primero es el número total acumulado de máquinas en cada ronda
		function getMachinesExtFactory($game_id, $company_id){
			$resultAgg = $this->fetchAll('game_id = '.$game_id.' AND company_id = '.$company_id, array('round_number_created DESC', 'factory_number ASC'));
			$resultAgg = $resultAgg->toArray();
			//$resultAgg = array_unique($resultAgg);
			$resultR = array();
			foreach ($resultAgg as $reg=>$val){
				
			}
			if(count($resultAgg)>0){
				return $resultAgg;
			}			
		}
	}
?>