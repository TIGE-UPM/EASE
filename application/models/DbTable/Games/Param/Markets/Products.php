<?php
	class Model_DbTable_Games_Param_Markets_Products extends Model_DbTable_Games_Param_Base{
		protected $_name = 'games_param_markets_products';

		function getProductNumberById($id){
			$results=$this->fetchRow('id = '.$id);
			return $results['product_number'];			
		}
		
		function getNameById($id){
			$results=$this->fetchRow('id = '.$id);
			return $results['name'];			
		}
		
		function getTypeById($id){
			$results=$this->fetchRow('id = '.$id);
			return $results['type'];			
		}
		
		function getPriceWeightById($id){
			$results=$this->fetchRow('id = '.$id);
			return $results['price_weight'];			
		}
		
		function getPriceWeight($game_id, $product_number){
			$results=$this->fetchRow('game_id = '.$game_id.' AND product_number = '.$product_number);
			return $results['price_weight'];			
		}
		
		function getQualityWeightById($id){
			$results=$this->fetchRow('id = '.$id);
			return $results['quality_weight'];			
		}
		
		function getQualityWeight($game_id, $product_number){
			$results=$this->fetchRow('game_id = '.$game_id.' AND product_number = '.$product_number);
			return $results['quality_weight'];			
		}
		
		function getMediaWeightById($id){
			$results=$this->fetchRow('id = '.$id);
			return $results['media_weight'];			
		}
				
		function getMediaWeight($game_id, $product_number){
			$results=$this->fetchRow('game_id = '.$game_id.' AND product_number = '.$product_number);
			return $results['media_weight'];			
		}
		function getTradeWeightById($id){
			$results=$this->fetchRow('id = '.$id);
			return $results['trade_weight'];			
		}
				
		function getTradeMediaWeight($game_id, $product_number){
			$results=$this->fetchRow('game_id = '.$game_id.' AND product_number = '.$product_number);
			return $results['trade_weight'];			
		}
		function getProductAvailability($game_id, $product_number){
			$results=$this->fetchRow('game_id = '.$game_id.' AND product_number = '.$product_number);
			return $results['availability'];			
		}
		function add($parameters){					
			$this->insert($parameters);
		}
		function updateProducts($game_id, $data){
			$this->delete('game_id = '.$game_id);
			$productCounter=1;
			while (isset($data['product_'.$productCounter])){
				$product=$data['product_'.$productCounter];
				$name=$product['name'];
				$priceWeight=$product['price_weight'];
				$qualityWeight=$product['quality_weight'];
				$product_number=$productCounter;
				$mediaWeight=$product['media_weight'];
				$tradeWeight=$product['trade_weight'];
				$availability=$product['availability'];
				$this->add(array('game_id'=>$game_id, 'name'=>$name, 'product_number'=>$product_number, 'price_weight'=>$priceWeight, 
								 'quality_weight'=> $qualityWeight, 'media_weight'=> $mediaWeight, 'trade_weight'=> $tradeWeight, 'availability'=>$availability));
				$productCounter++;
			}
		}
	}
?>