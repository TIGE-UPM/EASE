<?php
	class Model_DbTable_Games_Param_Markets_MediaProfile extends Model_DbTable_Games_Param_Base{
		protected $_name = 'games_param_markets_media_profile';

		function getIdealIntensityByMediaAndProduct($media_id, $product_id, $round_number){
			$results=$this->fetchRow('media_number = '.$media_id.' AND product_number ='.$product_id.' AND round_number ='.$round_number);
			return $results['ideal_intensity'];			
		}

		function add($parameters){		
			$this->insert($parameters);
		}
		function updateMediaProfile($game_id, $data){
			$this->delete('game_id = '.$game_id);
			$productCounter=1;
			$mediaWeights=$data['marketMediaWeight']['round_1'];
			while (isset($mediaWeights['product_'.$productCounter])){
				$mediaCounter=1;
				while (isset($mediaWeights['product_'.$productCounter]['media_'.$mediaCounter])){
					$regionCounter=1;
					while (isset($mediaWeights['product_'.$productCounter]['media_'.$mediaCounter]['region_'.$regionCounter])){
						$ideal_intensity=$mediaWeights['product_'.$productCounter]['media_'.$mediaCounter]['region_'.$regionCounter];
						$this->add(array('game_id'=>$game_id, 'round_number'=>1, 'product_number'=>$productCounter, 'media_number'=>$mediaCounter, 'region_number'=>$regionCounter, 'ideal_intensity'=>$ideal_intensity));
						$regionCounter++;
					}
					$mediaCounter++;
				}				
				$productCounter++;
			}
		}
	
		function setRoundMarketAdvertisingIntensity($game_id, $round_number, $product_number, $region_number, $media_number, $advertising_intensity){
			$this->delete(array('game_id = '.$game_id, 'round_number = '.$round_number, 'product_number = '.$product_number, 'media_number = '.$media_number, 'region_number = '.$region_number));
			$this->add(array('game_id'=>$game_id, 'round_number'=>$round_number, 'product_number'=>$product_number, 'media_number'=>$media_number, 'region_number'=>$region_number, 'ideal_intensity'=>$advertising_intensity));
		}
	}
?>