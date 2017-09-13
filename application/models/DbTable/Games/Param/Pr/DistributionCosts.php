<?php
	class Model_DbTable_Games_Param_Pr_DistributionCosts extends Model_DbTable_Games_Param_Base{
		protected $_name = 'games_param_pr_distribution_costs';
		
		function add($parameters){		
			$this->insert($parameters);
		}
		function updateDistributionCosts($game_id, $data){
			$this->delete('game_id = '.$game_id);
			$sourceRegionCounter=1;
			$distributionCosts=$data['prDistributionCosts']['round_1'];
			while (isset($distributionCosts['region_'.$sourceRegionCounter])){
				$regionCosts=$distributionCosts['region_'.$sourceRegionCounter];
				$destinationRegionCounter=1;
				while (isset($regionCosts['region_'.$destinationRegionCounter])){
					$cost=$regionCosts['region_'.$destinationRegionCounter];
					$this->add(array('game_id'=>$game_id, 'round_number'=>1, 'source_region_number'=>$sourceRegionCounter, 'destination_region_number'=>$destinationRegionCounter, 'cost'=>$cost));
					$destinationRegionCounter++;
				}
				
				$sourceRegionCounter++;
			}
		}
		function setRoundDistributionCosts($game_id, $round_number, $source_region_number, $destination_region_number, $cost){
			$this->delete(array('game_id = '.$game_id, 'round_number = '.$round_number, 'source_region_number = '.$source_region_number, 'destination_region_number = '.$destination_region_number));
			$this->add(array('game_id'=>$game_id, 'round_number'=>$round_number, 'source_region_number'=>$source_region_number, 'destination_region_number'=>$destination_region_number, 'cost'=>$cost));
		}
	}
?>