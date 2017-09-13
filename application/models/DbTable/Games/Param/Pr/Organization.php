<?php
	class Model_DbTable_Games_Param_Pr_Organization extends Model_DbTable_Games_Param_Base{
		protected $_name = 'games_param_pr_organization';
		
		function updateOrganization($game_id, $data){
			$this->delete('game_id = '.$game_id);
			$organization=$data['prOrganization'];
			$this->add(array('game_id'=>$game_id, 'work_shifts'=>$organization['shifts'], 'work_hours_per_week'=>$organization['working_hours'], 
							 'machines'=>$organization['machines'], 'production_workers'=>$organization['production_workers'], 'packaging_workers'=>$organization['packaging_workers'], 
							 'quality_workers'=>$organization['quality_workers'], 'maintenance_workers'=>$organization['maintenance_workers']));
		}
	}
?>