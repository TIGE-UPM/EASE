<?php
	class Model_DbTable_Games_Evolution_Markets_MediaProfile extends Model_DbTable_Games_Evolution_Base{
		protected $_name = 'games_evolution_markets_media_profile';

		function getIdealIntensityEvolutionByMediaAndProductAndRound($media_id, $product_id, $round_number){
			$results=$this->fetchRow('media_number = '.$media_id.' AND product_number ='.$product_id.' AND round_number ='.$round_number);
			return $results['evolution'];			
		}

		function add($parameters){		
			$this->insert($parameters);
		}
		function updateMediaProfileEvolution($game_id, $data){
			$this->delete('game_id = '.$game_id);
			$roundCounter=2;
			$mediaWeightsEvolution=$data['marketMediaWeightEvolution'];
			while (isset($mediaWeightsEvolution['round_'.$roundCounter])){
				$productCounter=1;
				while (isset($mediaWeightsEvolution['round_'.$roundCounter]['product_'.$productCounter])){
					$mediaCounter=1;
					while (isset($mediaWeightsEvolution['round_'.$roundCounter]['product_'.$productCounter]['media_'.$mediaCounter])){
						$regionCounter=1;
						while (isset($mediaWeightsEvolution['round_'.$roundCounter]['product_'.$productCounter]['media_'.$mediaCounter]['region_'.$regionCounter])){
							$evolution=$mediaWeightsEvolution['round_'.$roundCounter]['product_'.$productCounter]['media_'.$mediaCounter]['region_'.$regionCounter];
							$this->add(array('game_id'=>$game_id, 'round_number'=>$roundCounter, 'product_number'=>$productCounter, 'media_number'=>$mediaCounter, 'region_number'=>$regionCounter, 'evolution'=>$evolution));
							$regionCounter++;
						}
						$mediaCounter++;
					}
					$productCounter++;
				}
				$roundCounter++;
			}
		}
	}
?>