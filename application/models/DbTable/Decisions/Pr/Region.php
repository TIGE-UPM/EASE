<?php
	class Model_DbTable_Decisions_Pr_Region extends Zend_Db_Table{
		protected $_name = 'decision_production_region';
		
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
			/*$region=$parameters['factories'];
			$factory_number=1;
			while (isset($region['factory_number_'.$factory_number])){
					$region_set=$region['factory_number_'.$factory_number];
					$region_number=$region_set['region'];
					self::insert(array('game_id'=>$game_id, 'company_id' => $company_id, 
								   'region_number'=>$region_number, 'factory_number'=>$factory_number ,'round_number_created'=>$round_number));
					$factory_number++;
				}*/
			$factory_regions=$parameters['factories'];

			if($factory_regions==null) {return;}			
			foreach ($factory_regions as $fr_key=>$fr_val) {
				//echo($fr_key);die();
				//$region_set=$fr[$fr_key];
				$region_number=$fr_val['region'];
				$factory_number=ltrim($fr_key, "factory_number_");
				//echo($factory_number);die();
				$newfactory=$this->getDecisionFactory($game_id, $company_id, $factory_number);
				if($newfactory==null){
					self::insert(array('game_id'=>$game_id, 'company_id' => $company_id, 'region_number'=>$region_number, 'factory_number'=>$factory_number, 'round_number_created'=>$round_number));
				}			}
			
			/*foreach ($factory_regions as $fr) {
				$factory_number=1;
				$region_set=$fr['factory_number_'.$factory_number];
				//var_dump($fr);die();
				$region_number=$region_set['region'];
				//var_dump($region_number);die();
				$newfactory=$this->getDecisionFactory($game_id, $company_id, $factory_number);
				if($newfactory==null){
					self::insert(array('game_id'=>$game_id, 'company_id' => $company_id, 'region_number'=>$region_number, 'factory_number'=>$factory_number, 'round_number_created'=>$round_number));
				} else {
					//self::update(array('region_number'=>$region_number), 'game_id = '.$game_id.' AND company_id = '.$company_id.' AND factory_number = '.$factory_number.' AND round_number_created = '.$round_number);
				}
				$factory_number++;
				/*self::insert(array('game_id'=>$game_id, 'company_id' => $company_id, 'region_number'=>$region_number, 'factory_number'=>$factory_number ,'round_number_created'=>$round_number));*/
			/*}*/
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
			/*$region=$parameters['factories'];
			//var_dump($parameters);die();
			//die("1");
			$factory_number=1;
			//var_dump();die();		
			while($this->getDecisionFactory($game_id, $company_id, $factory_number)!=null){	
				//die($region['factory_number_'.$factory_number]);
				while (isset($region['factory_number_'.$factory_number])){
					$region_set=$region['factory_number_'.$factory_number];
					$region_number=$region_set['region'];					
					$newfactory=$this->getDecisionFactory($game_id, $company_id, $factory_number);
					//var_dump($region_number);die();
					if($newfactory==null){
					self::insert(array('game_id'=>$game_id, 'company_id' => $company_id, 'region_number'=>$region_number, 'factory_number'=>$factory_number, 'round_number_created'=>$round_number));
					//$factory_number++;
					}
					else{
					self::update(array('region_number'=>$region_number), 'game_id = '.$game_id.' AND company_id = '.$company_id.' AND factory_number = '.$factory_number.' AND round_number_created = '.$round_number);
					//$factory_number++;
					}
				}
				$factory_number++;
			}*/
			$factory_regions=$parameters['factories'];
			if($factory_regions==null) {return;}			
			foreach ($factory_regions as $fr_key=>$fr_val) {
				//echo($fr_key);die();
				//$region_set=$fr[$fr_key];
				$region_number=$fr_val['region'];//$region_set['region'];
				$factory_number=ltrim($fr_key, "factory_number_");
				//echo($factory_number);die();
				$newfactory=$this->getDecisionFactory($game_id, $company_id, $factory_number);
				if($newfactory==null){
					self::insert(array('game_id'=>$game_id, 'company_id' => $company_id, 'region_number'=>$region_number, 'factory_number'=>$factory_number, 'round_number_created'=>$round_number));
				} else {
					//self::update(array('region_number'=>$region_number), 'game_id = '.$game_id.' AND company_id = '.$company_id.' AND factory_number = '.$factory_number.' AND round_number_created = '.$round_number);
				}
			}
		}
		
		function getActiveRoundLastDecisionSaved(){
			$front = Zend_Controller_Front::getInstance();
			$game=$front->getParam('activeGame');
			$company=$front->getParam('activeCompany');
			$round=$front->getParam('activeRound');
			$decisions=$this->fetchAll('game_id = '.$game['id'].
								   ' AND company_id = '.$company['id']);
			foreach ($decisions as $decision){
				$array['factory_number_'.$decision['factory_number']]=$decision['region_number'];
			}
			return $array;
		
		}
		function getDecision($game_id, $company_id){
			$array=array();
			$decisions=$this->fetchAll('game_id = '.$game_id.
								   ' AND company_id = '.$company_id);
			foreach ($decisions as $decision){
				$array['factory_number_'.$decision['factory_number']]=$decision['region_number'];
			}
			return $array;
		}
		function getDecisionRegion($game_id, $company_id, $factory_number){
			return $this->fetchAll('game_id = '.$game_id.
								   ' AND company_id = '.$company_id.
								   ' AND factory_number = '.$factory_number);
		}
		//Para comprobar si la fabrica ya existe
		function getDecisionFactory($game_id, $company_id, $factory_number){
			return $this->fetchRow('game_id = '.$game_id.
								   ' AND company_id = '.$company_id.
								   ' AND factory_number = '.$factory_number);
		}
		//Cuenta el numero de fabricas que tiene cada compa–’a
		function countFactories($game_id, $company_id){
			$pr_factories= new Model_DbTable_Decisions_Pr_Region();
			$results=$pr_factories->fetchAll('game_id = '.$game_id.
							' AND company_id = '.$company_id);
			$counter=0;
			foreach ($results as $result){
				//$array=$result['factory_number']['region_number'];
				$counter++;
			}
			//var_dump($counter);die();
			return $counter;		
		}
	}
?>