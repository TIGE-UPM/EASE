<?php
	class Model_DbTable_Games_Evolution_Pr_DistributionCosts extends Model_DbTable_Games_Evolution_Base{
		protected $_name = 'games_evolution_pr_distribution_costs';
		
		function add($parameters){		
			$this->insert($parameters);
		}
		function updateDistributionCostsEvolution($game_id, $data){
			$this->delete('game_id = '.$game_id);
			$distributionCosts=$data['prDistributionCostsEvolution'];
			$roundCounter=2;
			while (isset($distributionCosts['round_'.$roundCounter])){
				$distributionCostsRound=$distributionCosts['round_'.$roundCounter];
				$sourceRegionCounter=1;
				while (isset($distributionCostsRound['region_'.$sourceRegionCounter])){
					$regionCosts=$distributionCostsRound['region_'.$sourceRegionCounter];
					$destinationRegionCounter=1;
					while (isset($regionCosts['region_'.$destinationRegionCounter])){
						$evolution=$regionCosts['region_'.$destinationRegionCounter];
						$this->add(array('game_id'=>$game_id, 'round_number'=>$roundCounter, 'source_region_number'=>$sourceRegionCounter, 'destination_region_number'=>$destinationRegionCounter, 'evolution'=>$evolution));
						$destinationRegionCounter++;
					}
					$sourceRegionCounter++;
				}
				$roundCounter++;
			}
		}
	}
?>