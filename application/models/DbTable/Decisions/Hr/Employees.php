<?php
	class Model_DbTable_Decisions_Hr_Employees extends Zend_Db_Table{
		protected $_name = 'decision_humanResources_employees';
		
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
				

			

			
			

			for ($factory_counter = 1; $factory_counter <= 4; $factory_counter++){
				//if ($parameters['factories_'.$factory_counter] == null){return;}
				$empleados = $parameters['factories_'.$factory_counter];
				$factory_number = $factory_counter;
				$employee_Counter = 1;
				while (isset($empleados['employee_'.$employee_Counter])){
			
					$employeeNumber=$empleados['employee_'.$employee_Counter]['number_employees'];
					$employeeCategory=$employee_Counter;	
					//self::insert(array('game_id'=>$game_id, 'company_id' => $company_id, 'round_number' => $round_number, 'factory_number' => $factory_number, 'employee_number' => $employeeNumber, 'employee_category' => $employeeCategory));
					self::insert(array('game_id'=>$game_id, 'company_id' => $company_id, 'round_number' => $round_number, 'factory_number' => $factory_number, 'employee_number' => $employeeNumber, 'employee_category' => $employeeCategory));		
					$employee_Counter++;
				}
				
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
					
			$employee_Counter = 1;
			
				
			for ($factory_counter = 1; $factory_counter <= 4; $factory_counter++){
				//if ($parameters['factories_'.$factory_counter] == null){return;}
				$empleados = $parameters['factories_'.$factory_counter];
				$factory_number = $factory_counter;
				$employee_Counter = 1;
				while (isset($empleados['employee_'.$employee_Counter])){
			
					$employeeNumber=$empleados['employee_'.$employee_Counter]['number_employees'];
					$employeeCategory=$employee_Counter;	
				
					self::update(array('employee_number'=>$employeeNumber), 'game_id = '.$game_id.' AND company_id = '.$company_id.' AND round_number = '.$round_number.' AND factory_number = '.$factory_number.' AND employee_category = '.$employeeCategory);
								
				
						$employee_Counter++;
		}
		
		}
		}
		function getActiveRoundLastDecisionSaved(){
			$front = Zend_Controller_Front::getInstance();
			$game=$front->getParam('activeGame');
			$company=$front->getParam('activeCompany');
			$round=$front->getParam('activeRound');
			$decisions= $this->fetchAll('game_id = '.$game['id'].
								   ' AND company_id = '.$company['id'].
								   ' AND round_number = '.$round['round_number']);

			foreach ($decisions as $decision){
				$array['factory_number_'.$decision['factory_number']]
					  ['category_'.$decision['employee_category']]=$decision['employee_number'];

			}
			return $array;
		}
		

		function getDecision($game_id, $company_id, $round_number){
			
			$decisions=$this->fetchAll('game_id = '.$game_id.' AND company_id = '.$company_id.' AND round_number = '.$round_number);


			foreach ($decisions as $decision){
				$array['factory_number_'.$decision['factory_number']]['category_'.$decision['employee_category']]=$decision['employee_number'];
			}

			return $array;
		}
	}
?> 