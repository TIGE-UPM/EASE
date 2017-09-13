<?php
/*Convendría optimizar minimizando el número de accesos a BD, cacheando en variables inicializadas en los init*/
	class Model_Simulation_Product extends Model_Simulation_SimulationObject{
		
		protected $_price_weight;
		protected $_media_weight;
		protected $_trademedia_weight;
		protected $_quality_weight;
		
		protected $_product_number;
		protected $_prices;
		protected $_media_sensibilities;
		protected $_trademedia_sensibilities;
		function __construct($core, $game_id, $round_number, $product_number){
			$this->_core=$core;
			$this->_game_id=$game_id;
			$this->_round_number=$round_number;
			$this->_product_number=$product_number;

		}
		//OK
		function getProductNumber(){
			return $this->_product_number;
		}
		
		function getProductIdealPrice($region_number, $channel_number, $round_number){
			if (! isset($this->_prices[$round_number][$region_number][$channel_number])){
				$this->_prices[$round_number][$region_number][$channel_number]=$this->_core->_games->getMarketPrices($this->_game_id, $round_number, $this->_product_number, $channel_number, $region_number);
			}
			return $this->_prices[$round_number][$region_number][$channel_number]['ideal_price'];
		}
		
		function getProductMaxPrice($region_number, $channel_number, $round_number){
			if (! isset($this->_prices[$round_number][$region_number][$channel_number])){
				$this->_prices[$round_number][$region_number][$channel_number]=$this->_core->_games->getMarketPrices($this->_game_id, $round_number, $this->_product_number, $channel_number, $region_number);
			}
			return $this->_prices[$round_number][$region_number][$channel_number]['max_price'];
		}
		
		function getProductIdealAdvertising($region_number, $media_number){
			if (! isset($this->_media_sensibilities[$region_number][$media_number])){
				$this->_media_sensibilities[$region_number][$media_number]=$this->_core->_games->getMediaWeight($this->_game_id, $this->_round_number, $this->_product_number, $media_number, $region_number);
			}
			return $this->_media_sensibilities[$region_number][$media_number];
		}
		function getProductIdealTradeMkt($region_number, $trademedia_number){
			if (! isset($this->_trademedia_sensibilities[$region_number][$trademedia_number])){
				$this->_trademedia_sensibilities[$region_number][$trademedia_number]=$this->_core->_games->getTradeMediaWeight($this->_game_id, $this->_round_number, $this->_product_number, $trademedia_number, $region_number);
			}
			return $this->_trademedia_sensibilities[$region_number][$trademedia_number];
		}
		
		function getPriceWeight(){
			if (! isset($this->_price_weight)){			
				$products_params=new Model_DbTable_Games_Param_Markets_Products();
				$this->_price_weight=$products_params->getPriceWeight($this->_game_id, $this->_product_number);
			}
			return $this->_price_weight;
		}
		function getQualityWeight(){
			if (! isset($this->_quality_weight)){			
				$products_params=new Model_DbTable_Games_Param_Markets_Products();
				$this->_quality_weight=$products_params->getQualityWeight($this->_game_id, $this->_product_number);
			}
			return $this->_quality_weight;
		}
		function getMediaWeight(){
			if (! isset($this->_media_weight)){			
				$products_params=new Model_DbTable_Games_Param_Markets_Products();
				$this->_media_weight=$products_params->getMediaWeight($this->_game_id, $this->_product_number);
			}
			return $this->_media_weight;
		}
		function getTradeMediaWeight(){
			if (! isset($this->_trademedia_weight)){			
				$products_params=new Model_DbTable_Games_Param_Markets_Products();
				$this->_trademedia_weight=$products_params->getTradeMediaWeight($this->_game_id, $this->_product_number);
			}
			return $this->_trademedia_weight;
		}
		function getPricesEvolution($channel_number, $region_number, $round_number){
			$result=$this->_core->_games->getMarketPricesEvolution($this->_game_id,$round_number, $this->_product_number, $channel_number, $region_number);
			return $result['evolution'];
		}
		
		//NUEVO
		function setProductPrices($round_number, $region_number, $channel_number, $ideal_price, $max_price){
			$prices=new Model_DbTable_Games_Param_Markets_Prices();
			$prices->setRoundMarketPrices($this->_game_id, $round_number, $this->_product_number, $region_number, $channel_number, $ideal_price, $max_price);
		}
		
		function getMarketsSizesEvolution($region_number, $round_number){
			$result=$this->_core->_games->getMarketSizeEvolution($this->_game_id, $round_number, $this->_product_number, $region_number);
			var_dump($result);
			//return $result['evolution'];
			return $result;
		}
		
		//NUEVO
		function setMarketSizes($round_number, $region_number, $market_size){
			$sizes=new Model_DbTable_Games_Param_Markets_Sizes();
			$sizes->setRoundMarketSizes($this->_game_id, $round_number, $this->_product_number, $region_number, $market_size);
		}
		function getAdvertisingIntensityEvolution($region_number, $media_number, $round_number){
			$result=$this->_core->_games->getMediaWeightEvolution($this->_game_id, $round_number, $this->_product_number, $media_number, $region_number);
			return $result['evolution'];
		}
		
		//NUEVO
		function setAdvertisingIntensity($round_number, $region_number, $media_number, $advertising_intensity){
			$advertising=new Model_DbTable_Games_Param_Markets_MediaProfile();
			$advertising->setRoundMarketAdvertisingIntensity($this->_game_id, $round_number, $this->_product_number, $region_number, $media_number, $advertising_intensity);
		}
		function getTradeMktIntensityEvolution($channel_number, $trademedia_number, $round_number){
			$result=$this->_core->_games->getTradeMediaWeightEvolution($this->_game_id, $round_number, $this->_product_number, $trademedia_number, $channel_number);
			return $result['evolution'];
		}
		
		//NUEVO
		function setTradeMktIntensity($round_number, $channel_number, $trademedia_number, $tradeMKT_intensity){
			$advertising=new Model_DbTable_Games_Param_Markets_TradeMediaProfile();
			$advertising->setRoundMarketTradeMktIntensity($this->_game_id, $round_number, $this->_product_number, $channel_number, $trademedia_number, $tradeMKT_intensity);
		}
		function setProductionCosts($round_number, $region_number, $cost_type, $product_number, $cost){
			$prod_costs=new Model_DbTable_Games_Param_Pr_ProductionCosts();
			$prod_costs->setRoundProductionCosts($this->_game_id, $round_number, $cost_type, $region_number, $product_number, $cost);
		}
		function setDistributionCosts($round_number, $source_region_number, $destination_region_number, $cost){
			$distrib_costs=new Model_DbTable_Games_Param_Pr_DistributionCosts();
			$distrib_costs->setRoundDistributionCosts($this->_game_id, $round_number, $source_region_number, $destination_region_number, $cost);
		}
		function setRawMaterialsCosts($round_number, $base, $increment){
			$raw_costs=new Model_DbTable_Games_Param_Pr_RawMaterialsCosts();
			$raw_costs->setRoundRawMaterialsCosts($this->_game_id, $round_number, $this->_product_number, $base, $increment);
		}
	}
?>