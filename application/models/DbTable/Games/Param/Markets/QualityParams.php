<?php
	class Model_DbTable_Games_Param_Markets_QualityParams extends Model_DbTable_Games_Param_Base{
		protected $_name = 'games_param_markets_qualityParams';
		
		function getChannelNameById($id){
			$results=$this->fetchRow('id = '.$id);
			return $results['name'];			
		}
		
		function add($parameters){		
			$this->insert($parameters);
		}
		function updateQualityParams($game_id, $data){
			$this->delete('game_id = '.$game_id);
			$qualityParamCounter=1;
			while (isset($data['quality_param_'.$qualityParamCounter])){
				$quality_param=$data['quality_param_'.$qualityParamCounter];
				$name=$quality_param['name'];
				$quality_param_number=$qualityParamCounter;
				$weight=$quality_param['quality_param_weight'];
				$this->add(array('game_id'=>$game_id, 'quality_param_number'=>$quality_param_number, 'name'=>$name, 'quality_param_weight'=>$weight));
				$qualityParamCounter++;
			}
		}
		
		function getWeightsArray($game_id){
			$results=$this->fetchAll('game_id = '.$game_id, array('quality_param_number ASC'));
			foreach ($results as $result){
				$array['quality_param_'.$result['quality_param_number']]=$result['quality_param_weight'];
			}
			//var_dump($array); die();
			return $array;
		}
	}
?>