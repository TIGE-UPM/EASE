<?php
	class Model_DbTable_Games_Config_GameRounds extends Zend_Db_Table{
		protected $_name = 'games_config_rounds';
		
		function getActiveRound($game_id){
			$rounds=$this->fetchAll('game_id = '.$game_id, 'opening_date ASC');
			$now=time();
			foreach ($rounds as $round){
				$opening=strtotime($round['opening_date']);
				$closing=strtotime($round['closing_date']);
				if ($opening<$now && $now<=$closing) return $round;
			}
			return null;
		}
		function getNextRounds($game_id){
			$rounds=$this->fetchAll('game_id = '.$game_id, 'opening_date ASC');
			$now=time();
			foreach ($rounds as $round){
				$opening=strtotime($round['opening_date']);
				if ($opening>$now) $roundsArray[]=$round;
			}
			return isset($roundsArray)?$roundsArray:null;
		}
		
		function getPastRounds($game_id){
			$rounds=$this->fetchAll('game_id = '.$game_id, 'opening_date ASC');
			$now=time();
			foreach ($rounds as $round){
				$closing=strtotime($round['closing_date']);
				if ($closing<$now) $roundsArray[]=$round;
			}
			return isset($roundsArray)?$roundsArray:null;
		}
		
		function addRound($data){
			return $this->insert($data);
		}

		function updateRounds($game_id, $data){
			$this->delete('game_id = '.$game_id);
			$roundCounter=1;
			while (isset($data['round_'.$roundCounter.'_opening'])){
				$opening_string=$data['round_'.$roundCounter.'_opening'];
				$closing_string=$data['round_'.$roundCounter.'_closing'];
				
				$opening_hour=$data['round_'.$roundCounter.'_opening_hour'];
				$opening_minute=$data['round_'.$roundCounter.'_opening_minute'];
				$opening_second=$data['round_'.$roundCounter.'_opening_second'];
				$closing_hour=$data['round_'.$roundCounter.'_closing_hour'];
				$closing_minute=$data['round_'.$roundCounter.'_closing_minute'];
				$closing_second=$data['round_'.$roundCounter.'_closing_second'];
				//parsing
				$opening_day=substr($opening_string, 0, strpos($opening_string, '/'));
				$opening_string=substr($opening_string, 3);
				$opening_month=substr($opening_string, 0, strpos($opening_string, '/'));
				$opening_string=substr($opening_string, 3);
				$opening_year=$opening_string;
				
				$closing_day=substr($closing_string, 0, strpos($closing_string, '/'));
				$closing_string=substr($closing_string, 3);
				$closing_month=substr($closing_string, 0, strpos($closing_string, '/'));
				$closing_string=substr($closing_string, 3);
				$closing_year=$closing_string;
				//creating datetimes
				$opening_time=date( 'Y-m-d H:i:s', mktime($opening_hour,$opening_minute,$opening_second,intVal($opening_month),intVal($opening_day),intVal($opening_year)));
				$closing_time=date( 'Y-m-d H:i:s', mktime($closing_hour,$closing_minute,$closing_second,intVal($closing_month),intVal($closing_day),intVal($closing_year)));
				$this->addRound(array('game_id'=>$game_id, 'round_number'=>$roundCounter, 
									   'opening_date'=>$opening_time, 'closing_date'=>$closing_time));
				$roundCounter++;
			}
		}
		function applyTemplate($game_id, $template_id){
			if ($template_id!=0){
				$this->delete('game_id = '.$game_id);
				$result=$this->fetchAll('game_id = '.$template_id);
				foreach ($result as $row){
					$array=$row->toArray();
					unset($array['id']);
					$array['game_id']=$game_id;
					$this->addRound($array);		
					}
			}	
			//en esta estructura de if-else es donde deberíamos cambiar la inicialización a 0
			//de las tablas de editar juego en lugar de copiar de game_id=0 para cada producto,
			//canal, región...
			//actualmente deja las casillas de la plantilla de edit vacías
		}
		function deleteEntries($game_id){
			$this->delete('game_id = '.$game_id);
		}
	}
?>