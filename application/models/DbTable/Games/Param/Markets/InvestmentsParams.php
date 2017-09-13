<?php
	class Model_DbTable_Games_Param_Markets_InvestmentsParams extends Model_DbTable_Games_Param_Base{
		protected $_name = 'games_param_markets_investments';
		//VERO
		function getChannelNameById($id){
			$results=$this->fetchRow('id = '.$id);
			return $results['name'];			
		}
		
		function add($parameters){	
			while (isset($parameters['investment_param_'.$investmentParamCounter])){
				$investment_param=$parameters['investment_param_'.$investmentParamCounter];
				$name=$investment_param['name'];
				$limit=$investment_param['limit'];
				$investment_param_number=$investmentParamCounter;
				$average_performace=$investment_param['average_performace'];
				$this->insert(array('game_id'=>$game_id, 'investment_param_number'=>$investment_param_number, 'name'=>$name, 'average_performace'=>$average_performace,  'limit'=>$limit));
				$investmentParamCounter++;
				
			}
		}
		function updateinvestmentParams($game_id, $data){
			$this->delete('game_id = '.$game_id);
			$investmentParamCounter=1;
			while (isset($data['investment_param_'.$investmentParamCounter])){
				$investment_param=$data['investment_param_'.$investmentParamCounter];
				$name=$investment_param['name'];
				$limit=$investment_param['limit'];
				$investment_param_number=$investmentParamCounter;
				$average_performace=$investment_param['investment_param_average_performance'];
				$this->insert(array('game_id'=>$game_id, 'investment_param_number'=>$investment_param_number, 'name'=>$name, 'average_performace'=>$average_performace, 'limit'=>$limit));
				$investmentParamCounter++;
				
			}
		}
		
		function getinvestmentParamsAveragePerformace($game_id){
			$results=$this->fetchAll('game_id = '.$game_id, array('investment_param_number ASC'));

			foreach ($results as $result){
				$array['investment_param_number_'.$result['investment_param_number']]=$result['average_performace'];
			}

			return $array;
		}
		function getinvestmentParamsName($game_id){
			$results=$this->fetchAll('game_id = '.$game_id, array('investment_param_number ASC'));
			foreach ($results as $result){
				$name['investment_param_number_'.$result['investment_param_number']]=$result['name'];
			}
			return $name;
		}

		function getinvestmentParamsLimit($game_id){
			$results=$this->fetchAll('game_id = '.$game_id, array('investment_param_number ASC'));
			foreach ($results as $result){
				$limit['investment_param_number_'.$result['investment_param_number']]=$result['limit'];
			}
			return $limit;
		}

		function getinvestmentLimit($game_id, $investment_param_number){
			$limit=$this->fetchAll('game_id = '.$game_id.' AND investment_param_number ='.$investment_param_number );
			return $limit;
		}
		//VERO
	}
?>