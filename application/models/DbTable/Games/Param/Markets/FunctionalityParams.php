<?php
	class Model_DbTable_Games_Param_Markets_FunctionalityParams extends Model_DbTable_Games_Param_Base{
		protected $_name = 'games_param_markets_functionality';
		//VERO
		function getChannelNameById($id){
			$results=$this->fetchRow('id = '.$id);
			return $results['name'];			
		}
		
		function add($parameters){	
			while (isset($parameters['functionality_param_'.$functionalityParamCounter])){
				$functionality_param=$parameters['functionality_param_'.$functionalityParamCounter];
				$productParamCounter=1;
				while (isset($functionality_param['functionality_param_weight']['product_'.$productParamCounter])){
					$name=$functionality_param['name'];
					$cost=$functionality_param['cost'];
					$functionality_param_number=$functionalityParamCounter;
					$weight=$functionality_param['functionality_param_weight']['product_'.$productParamCounter];
					$this->insert(array('game_id'=>$game_id, 'functionality_param_number'=>$functionality_param_number, 'name'=>$name, 'functionality_param_weight'=>$weight, 'product_number'=>$productParamCounter, 'cost'=>$cost));
					$productParamCounter++;
				}
				$functionalityParamCounter++;
				
			}
		}
		function updateFunctionalityParams($game_id, $data){
			$this->delete('game_id = '.$game_id);
			$functionalityParamCounter=1;
			
			while (isset($data['functionality_param_'.$functionalityParamCounter])){
				$functionality_param=$data['functionality_param_'.$functionalityParamCounter];
				$productParamCounter=1;
				while (isset($functionality_param['functionality_param_weight']['product_'.$productParamCounter])){
					$name=$functionality_param['name'];
					$cost=$functionality_param['cost'];
					$functionality_param_number=$functionalityParamCounter;
					$weight=$functionality_param['functionality_param_weight']['product_'.$productParamCounter];
					$this->insert(array('game_id'=>$game_id, 'functionality_param_number'=>$functionality_param_number, 'name'=>$name, 'functionality_param_weight'=>$weight, 'product_number'=>$productParamCounter, 'cost'=>$cost));
					$productParamCounter++;
				}
				$functionalityParamCounter++;
				
			}
		}
		
		function getFunctionalityParamsWeight($game_id){
			$results=$this->fetchAll('game_id = '.$game_id, array('functionality_param_number ASC', 'product_number ASC'));

			foreach ($results as $result){
				$array['functionality_param_number_'.$result['functionality_param_number']]
					  ['product_number_'.$result['product_number']]=$result['functionality_param_weight'];
			}

			return $array;
		}
		function getFunctionalityParamsName($game_id){
			$results=$this->fetchAll('game_id = '.$game_id, array('functionality_param_number ASC', 'product_number ASC'));
			foreach ($results as $result){
				$name['functionality_param_number_'.$result['functionality_param_number']]=$result['name'];
			}
			return $name;
		}

		function getFunctionalityParamsCost($game_id){
			$results=$this->fetchAll('game_id = '.$game_id, array('functionality_param_number ASC', 'product_number ASC'));
			foreach ($results as $result){
				$cost['functionality_param_number_'.$result['functionality_param_number']]=$result['cost'];
			}
			return $cost;
		}

		function getFunctionalityCost($game_id, $functionality_param_number){
			$results=$this->fetchAll('game_id = '.$game_id.' AND functionality_param_number ='.$functionality_param_number , array('product_number ASC'));
			foreach ($results as $result){
				$cost=$result['cost'];
			}
			echo("El coste adicional de la funcionalidad es:" . $cost . "<br/>");
			return $cost;
		}
		//VERO
	}
?>