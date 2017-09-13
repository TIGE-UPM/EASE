<?php
	class Model_DbTable_Games_Param_Markets_QualityParams extends Model_DbTable_Games_Param_Base{
		protected $_name = 'games_param_markets_qualityParams';
		
		function getChannelNameById($id){
			$results=$this->fetchRow('id = '.$id);
			return $results['name'];			
		}
		
		function add($parameters){	
			//VERO
			//$this->insert($parameters); 
			while (isset($parameters['quality_param_'.$qualityParamCounter])){
				$quality_param=$parameters['quality_param_'.$qualityParamCounter];
				$productParamCounter=1;
				while (isset($quality_param['quality_param_weight']['product_'.$productParamCounter])){
					$name=$quality_param['name'];
					$quality_param_number=$qualityParamCounter;
					$weight=$quality_param['quality_param_weight']['product_'.$productParamCounter];
					$this->insert(array('game_id'=>$game_id, 'quality_param_number'=>$quality_param_number, 'name'=>$name, 'quality_param_weight'=>$weight, 'product_number'=>$productParamCounter));
					$productParamCounter++;
				}
				$qualityParamCounter++;
				
			}
			//VERO
		}
		function updateQualityParams($game_id, $data){
			$this->delete('game_id = '.$game_id);
			$qualityParamCounter=1;
			//VERO
			while (isset($data['quality_param_'.$qualityParamCounter])){
				$quality_param=$data['quality_param_'.$qualityParamCounter];
				$productParamCounter=1;
				while (isset($quality_param['quality_param_weight']['product_'.$productParamCounter])){
					$name=$quality_param['name'];
					$quality_param_number=$qualityParamCounter;
					$weight=$quality_param['quality_param_weight']['product_'.$productParamCounter];
					$this->insert(array('game_id'=>$game_id, 'quality_param_number'=>$quality_param_number, 'name'=>$name, 'quality_param_weight'=>$weight, 'product_number'=>$productParamCounter));
					$productParamCounter++;
				}
				$qualityParamCounter++;
				
			}
			//VERO
		}
		
		function getWeightsArray($game_id){
			$results=$this->fetchAll('game_id = '.$game_id, array('quality_param_number ASC'));
			foreach ($results as $result){
				$array['quality_param_'.$result['quality_param_number']]=$result['quality_param_weight'];
			}
			//var_dump($array); die();
			return $array;
		}
		//VERO
		function getQualityParamsWeight($game_id){
			$results=$this->fetchAll('game_id = '.$game_id, array('quality_param_number ASC', 'product_number ASC'));

			foreach ($results as $result){
				$array['quality_param_number_'.$result['quality_param_number']]
					  ['product_number_'.$result['product_number']]=$result['quality_param_weight'];
			}

			return $array;
		}
		function getQualityParamsName($game_id){
			//VERO
			$results=$this->fetchAll('game_id = '.$game_id, array('quality_param_number ASC', 'product_number ASC'));
			foreach ($results as $result){
				$name['quality_param_number_'.$result['quality_param_number']]=$result['name'];
			}
			return $name;
		}
		//VERO
	}
?>