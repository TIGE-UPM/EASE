<?php
	class Model_DbTable_Games_Evolution_Base extends Zend_Db_Table{

		function add($parameters){		
			$this->insert($parameters);
		}
		function applyTemplate($game_id, $template_id){
			if ($template_id!=0){
				$this->delete('game_id = '.$game_id);
				$result=$this->fetchAll('game_id = '.$template_id);
				foreach ($result as $row){
					$array=$row->toArray();
					unset($array['id']);
					$array['game_id']=$game_id;
					$this->add($array);		
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