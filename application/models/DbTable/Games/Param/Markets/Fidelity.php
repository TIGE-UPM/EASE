<?php
	class Model_DbTable_Games_Param_Markets_Fidelity extends Model_DbTable_Games_Param_Base{
		protected $_name = 'games_param_markets_fidelity';			
		
		function add($parameters){					
			$this->insert($parameters);
		}
		function updateFidelity($game_id, $data){
			$this->delete('game_id = '.$game_id);
			$markets=$data['marketFidelity'];
			$productCounter=1;
			while (isset($markets['product_'.$productCounter])){
				$pSelected=$markets['product_'.$productCounter];
				$channelCounter=1;
				while (isset($pSelected['channel_'.$channelCounter])){
					$cSelected=$pSelected['channel_'.$channelCounter];
					$regionCounter=1;
					while (isset($cSelected['region_'.$regionCounter])){
						$fidelity=$cSelected['region_'.$regionCounter];
						$this->add(array('game_id'=>$game_id, 'product_number'=>$productCounter, 'region_number'=>$regionCounter,
										 'channel_number'=>$channelCounter, 'fidelity'=>$fidelity));
						$regionCounter++;
					}
					$channelCounter++;
				}	
				$productCounter++;
			}
		}
	}
?>