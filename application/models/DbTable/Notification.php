<?php
	class Model_DbTable_Notification extends Zend_Db_Table{
		protected $_name = 'notifications';		
		
		function addNotification($notification_data){
			if(count($this->fetchAll('team_id = '.$notification_data['team_id'])->toArray())>0)				
				$this->update($notification_data, 'team_id = '.$notification_data['team_id']);			
			else
				$this->insert($notification_data);				
		}
		function getNotifications($game_id){
			return $this->fetchAll('game_id = '.$game_id, 'team_id ASC');
		}
		function existTeam ($team){
			if(count($this->fetchAll('team_id = '.$team)->toArray())>0)
				return true;
			else 
				return false;
		}
		function deleteNotification ($team){			
			$this->delete('team_id='.$team);
		}
		function getTeamNotification ($team){
			if(count($this->fetchAll('team_id = '.$team)->toArray())>0)
				return $this->fetchAll('team_id = '.$team);
			else 
				return false;
		}
	}
?>