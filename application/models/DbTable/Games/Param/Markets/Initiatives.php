<?php
	class Model_DbTable_Games_Param_Markets_Initiatives extends Model_DbTable_Games_Param_Base{
		protected $_name = 'games_param_markets_initiatives';
		
		function add($parameters){					
			$this->insert($parameters);
		}
		
		function updateInitiatives($game_id, $data){
			$this->delete('game_id = '.$game_id);
			$initiativeCounter=1;
			while (isset($data['initiative_'.$initiativeCounter])){
				$initiative=$data['initiative_'.$initiativeCounter];
				$name=$initiative['name'];						
				$initiative_number=$initiativeCounter;
				$area=$initiative['area'];
				$weight=$initiative['weight'];
				$cost=$initiative['cost'];
				$this->add(array('game_id'=>$game_id, 'initiative_number'=>$initiative_number, 'name'=>$name, 'area'=>$area, 'weight'=>$weight, 'cost'=>$cost));
				$initiativeCounter++;
			}
		}
		
		function getInitiatives($game_id){
			$results=$this->fetchRow('game_id = '.$game_id);
			//var_dump($results); die();
			return $results['name'];
		}
		
		function getPrInitiativesWeights($game_id){
			$array=array();
			$p=0;
			$results=$this->fetchAll('game_id = '.$game_id.' AND area ='.$p, 'initiative_number ASC');
			$initiative_pr_number=1;
			foreach ($results as $result){
				$array['initiativeproduction_number_'.$initiative_pr_number]=$result['weight'];
				$initiative_pr_number++;
			}
			return $array;
		}
		
		function getHrInitiativesWeights($game_id){
			$array=array();
			$h=1;
			$results=$this->fetchAll('game_id = '.$game_id.' AND area ='.$h, 'initiative_number ASC');
			$initiative_hr_number=1;
			foreach ($results as $result){
				$array['initiativehumanresources_number_'.$initiative_hr_number]=$result['weight'];
				$initiative_hr_number++;
			}
			return $array;
		}
		
		function getMkInitiativesWeights($game_id){
			$array=array();
			$m=2;
			$results=$this->fetchAll('game_id = '.$game_id.' AND area ='.$m, 'initiative_number ASC');
			$initiative_mk_number=1;
			foreach ($results as $result){
				$array['initiativemarketing_number_'.$initiative_mk_number]=$result['weight'];
				$initiative_mk_number++;
			}
			return $array;
		}
		
		function getDetInitiativesWeights($game_id){
			$array=array();
			$m=3;
			$results=$this->fetchAll('game_id = '.$game_id.' AND area ='.$m, 'initiative_number ASC');
			$initiative_dt_number=1;
			foreach ($results as $result){
				$array['initiativedeterioration_number_'.$initiative_dt_number]=$result['weight'];
				$initiative_dt_number++;
			}
			return $array;
		}
		
		function getPrInitiativesCosts($game_id){
			$array=array();
			$p=0;
			$results=$this->fetchAll('game_id = '.$game_id.' AND area ='.$p, 'initiative_number ASC');
			$initiative_pr_number=1;
			foreach ($results as $result){
				$array['initiativeproduction_number_'.$initiative_pr_number]=$result['cost'];
				$initiative_pr_number++;
			}
			return $array;
		}
		
		function getHrInitiativesCosts($game_id){
			$array=array();
			$h=1;
			$results=$this->fetchAll('game_id = '.$game_id.' AND area ='.$h, 'initiative_number ASC');
			$initiative_hr_number=1;
			foreach ($results as $result){
				$array['initiativehumanresources_number_'.$initiative_hr_number]=$result['cost'];
				$initiative_hr_number++;
			}
			return $array;
		}
		
		function getMkInitiativesCosts($game_id){
			$array=array();
			$m=2;
			$results=$this->fetchAll('game_id = '.$game_id.' AND area ='.$m, 'initiative_number ASC');
			$initiative_mk_number=1;
			foreach ($results as $result){
				$array['initiativemarketing_number_'.$initiative_mk_number]=$result['cost'];
				$initiative_mk_number++;
			}
			return $array;
		}
		function getDetInitiativesCosts($game_id){
			$array=array();
			$m=3;
			$results=$this->fetchAll('game_id = '.$game_id.' AND area ='.$m, 'initiative_number ASC');
			$initiative_dt_number=1;
			foreach ($results as $result){
				$array['initiativedeterioration_number_'.$initiative_dt_number]=$result['cost'];
				$initiative_dt_number++;
			}
			return $array;
		}
	}
?>