<?php
	class Model_DbTable_Games extends Zend_Db_Table{
		protected $_name = 'games';
		
		
		/* games table operations*/
		
		function addGame($data){
			return $this->insert($data);
		}
		
		function getAllGames(){
			$results=$this->fetchAll();
			return $results;
		}
		
		function getAllNonTemplateGames(){
			$results=$this->fetchAll('is_template=0');
			return $results;
		}
		
		function getTemplates(){
			$results=$this->fetchAll('is_template = 1 AND id > 0');
			return $results;
		}
		
		function existsByName($name){
			return (! null==$this->fetchRow('name = "'.$name.'"'));
		}
		
		function getGame($game_id){
			return $this->fetchRow('id = '.$game_id)->toArray();
		}
		
		function getGameByName($game_name){
			$game=$this->fetchRow('name = "'.$game_name.'"')->toArray();
			return $game['name'];
		}
		
		function editGame($id, $data, $formulario){
			switch($formulario){ 

				case 1:
					self::update(array('description' => $data['description']), 'id =' . $id );

					//rounds
					$gameRounds=new Model_DbTable_Games_Config_GameRounds();
					$gameRounds->updateRounds($id, $data);
					break;
				case 2:	
					//products
					$gameProducts=new Model_DbTable_Games_Param_Markets_Products();
					$gameProducts->updateProducts($id, $data);
					//products I+D+i budget
					$gameProductsBudget=new Model_DbTable_Games_Param_Idi_Budget();
					$gameProductsBudget->updateBudgets($id, $data);
					//regions
					$gameRegions=new Model_DbTable_Games_Param_Markets_Regions();
					$gameRegions->updateRegions($id, $data);
					//channels
					$gameChannels=new Model_DbTable_Games_Param_Markets_Channels();
					$gameChannels->updateChannels($id, $data);
					//media
					$gameMedia=new Model_DbTable_Games_Param_Markets_Media();
					$gameMedia->updateMedia($id, $data);
					//quality_params
					$gameQualityParams=new Model_DbTable_Games_Param_Markets_QualityParams();
					$gameQualityParams->updateQualityParams($id, $data);
					//VERO
					/**$gameThreshold=new Model_DbTable_Games_Param_Idi_Threshold();
					$gameThreshold->updateThreshold($id, $data);**/

					$gameFunctionalityParams=new Model_DbTable_Games_Param_Markets_FunctionalityParams();
					$gameFunctionalityParams->updateFunctionalityParams($id, $data);
					//VERO
					//initiatives
					$gameInitiatives=new Model_DbTable_Games_Param_Markets_Initiatives();
					$gameInitiatives->updateInitiatives($id, $data);
					//marketSizes
					$gameMarketSizes=new Model_DbTable_Games_Param_Markets_Sizes();
					$gameMarketSizes->updateMarketSizes($id, $data);
					//marketSizesEvolution
					$gameMarketSizesEvolution=new Model_DbTable_Games_Evolution_Markets_Sizes();
					$gameMarketSizesEvolution->updateMarketSizesEvolution($id, $data);
					//channelWeights
					$channelWeights=new Model_DbTable_Games_Param_Markets_ChannelsWeights();
					$channelWeights->updateChannelWeights($id, $data);
					//fidelity
					$fidelity=new Model_DbTable_Games_Param_Markets_Fidelity();
					$fidelity->updateFidelity($id, $data);
					//marketPrices
					$marketPrices=new Model_DbTable_Games_Param_Markets_Prices();
					$marketPrices->updateMarketPrices($id, $data);
					//marketPricesEvolution
					$marketPricesEvolution=new Model_DbTable_Games_Evolution_Markets_Prices();
					$marketPricesEvolution->updateMarketPricesEvolution($id, $data);
					//marketMediaWeights
					$marketMediaProfile=new Model_DbTable_Games_Param_Markets_MediaProfile();
					$marketMediaProfile->updateMediaProfile($id, $data);
					//marketMediaWeightsEvolution
					$marketMediaProfileEvolution=new Model_DbTable_Games_Evolution_Markets_MediaProfile();
					$marketMediaProfileEvolution->updateMediaProfileEvolution($id, $data);
					//marketTradeMediaWeights
					$marketTradeMediaProfile=new Model_DbTable_Games_Param_Markets_TradeMediaProfile();
					$marketTradeMediaProfile->updateTradeMediaProfile($id, $data);
					//marketTradeMediaWeightsEvolution
					$marketTradeMediaProfileEvolution=new Model_DbTable_Games_Evolution_Markets_TradeMediaProfile();
					$marketTradeMediaProfileEvolution->updateTradeMediaProfileEvolution($id, $data);
					//fidelity
					$marketFidelity=new Model_DbTable_Games_Param_Markets_Fidelity();
					$marketFidelity->updateFidelity($id, $data);
					break;

					
				case 3:
					//prProductionTimes
					$prProductionTimes=new Model_DbTable_Games_Param_Pr_ProductionTimes();
					$prProductionTimes->updateProductionTimes($id, $data);
					//prProductionCosts
					$prProductionCosts=new Model_DbTable_Games_Param_Pr_ProductionCosts();
					$prProductionCosts->updateProductionCosts($id, $data);
					//prProductionCostsEvolution
					$prProductionCostsEvolution=new Model_DbTable_Games_Evolution_Pr_ProductionCosts();
					$prProductionCostsEvolution->updateProductionCostsEvolution($id, $data);
					//prDistributionCosts
					$prDistributionCosts=new Model_DbTable_Games_Param_Pr_DistributionCosts();
					$prDistributionCosts->updateDistributionCosts($id, $data);
					//prDistributionCostsEvolution
					$prDistributionCostsEvolution=new Model_DbTable_Games_Evolution_Pr_DistributionCosts();
					$prDistributionCostsEvolution->updateDistributionCostsEvolution($id, $data);
					//prRawMaterialsCosts
					$prRawMaterialsCosts=new Model_DbTable_Games_Param_Pr_RawMaterialsCosts();
					$prRawMaterialsCosts->updateRawMaterialsCosts($id, $data);
					//prRawMaterialsCostsEvolution
					$prRawMaterialsCostsEvolution=new Model_DbTable_Games_Evolution_Pr_RawMaterialsCosts();
					$prRawMaterialsCostsEvolution->updateRawMaterialsCostsEvolution($id, $data);
					//prSuppliersPayterms
					$prSuppliers=new Model_DbTable_Games_Param_Pr_Suppliers();
					$prSuppliers->updateSuppliers($id, $data);
					//prOrganization
					$prOrganization=new Model_DbTable_Games_Param_Pr_Organization();
					$prOrganization->updateOrganization($id, $data);
					//hrStaffCosts
					$hrCosts=new Model_DbTable_Games_Param_Hr_Costs();
					$hrCosts->updateHrCosts($id, $data);
					//hrStaffCostsEvolution
					$hrCostsEvolution=new Model_DbTable_Games_Evolution_Hr_Costs();
					$hrCostsEvolution->updateHrCostsEvolution($id, $data);
					//hrRegionProfile
					$hrRegionProfile=new Model_DbTable_Games_Param_Hr_RegionProfile();
					$hrRegionProfile->updateHrProfile($id, $data);
					//mkChannelsCosts
					$mkChannelsCosts=new Model_DbTable_Games_Param_Mk_ChannelsCosts();
					$mkChannelsCosts->updateChannelsCosts($id, $data);
					//mkChannelsCostsEvolution
					$mkChannelsCostsEvolution=new Model_DbTable_Games_Evolution_Mk_ChannelsCosts();
					$mkChannelsCostsEvolution->updateChannelsCostsEvolution($id, $data);
					//mkChannelsIncomeTerms
					$mkChannelsIncomeTerms=new Model_DbTable_Games_Param_Mk_ChannelsIncomeTerms();
					$mkChannelsIncomeTerms->updateChannelsIncomeTerms($id, $data);
					//mkMediaCosts
					$mkMediaCosts=new Model_DbTable_Games_Param_Mk_MediaCosts();
					$mkMediaCosts->updateMediaCosts($id, $data);
					//mkTradeMediaCosts
					$mkTradeMediaCosts=new Model_DbTable_Games_Param_Mk_TradeMediaCosts();
					$mkTradeMediaCosts->updateTradeMediaCosts($id, $data);
					//marketResearchesCosts
					$marketResearchesCosts=new Model_DbTable_Games_Param_Markets_MarketResearches();
					$marketResearchesCosts->updateMarketResearches($id, $data);
					//VERO
					$investmentParam=new Model_DbTable_Games_Param_Markets_InvestmentsParams();
					$investmentParam->updateinvestmentParams($id, $data);
		
					$gameInvestmentEvolution=new Model_DbTable_Games_Evolution_Fi_Investment();
					$gameInvestmentEvolution->updateInvestmentEvolution($id, $data);

					//VERO
					//break;//fiCashFlow
					$fiCashflowParams=new Model_DbTable_Games_Param_Fi_Cashflow();
					$fiCashflowParams->updateCashflowParameters($id, $data);
					//fiInterestRate
					$fiInterestRateParams=new Model_DbTable_Games_Param_Fi_InterestRate();
					$fiInterestRateParams->updateInterestRateParameters($id, $data);
					
					break;
			}
		}
		
		function applyGameTemplate($id, $template_id){
			$parametersTables = array(new Model_DbTable_Games_Config_GameRounds(),
							new Model_DbTable_Games_Param_Markets_Products(),
							new Model_DbTable_Games_Param_Idi_Budget(), 
							new Model_DbTable_Games_Param_Markets_Regions(),
							new Model_DbTable_Games_Param_Markets_Channels(),
							new Model_DbTable_Games_Param_Markets_Media(),
							new Model_DbTable_Games_Param_Markets_QualityParams(),
							//VERO
							new Model_DbTable_Games_Param_Markets_FunctionalityParams(),
							new Model_DbTable_Games_Param_Markets_InvestmentsParams(),
							//VERO
							new Model_DbTable_Games_Param_Markets_Initiatives(),
							new Model_DbTable_Games_Param_Markets_Sizes(),
							new Model_DbTable_Games_Evolution_Markets_Sizes(),
							new Model_DbTable_Games_Param_Markets_ChannelsWeights(),
							new Model_DbTable_Games_Param_Markets_Fidelity(),
							new Model_DbTable_Games_Param_Markets_Prices(),
							new Model_DbTable_Games_Evolution_Markets_Prices(),
							new Model_DbTable_Games_Param_Markets_MediaProfile(),
							new Model_DbTable_Games_Evolution_Markets_MediaProfile(),
							new Model_DbTable_Games_Param_Markets_TradeMediaProfile(),
							new Model_DbTable_Games_Evolution_Markets_TradeMediaProfile(),
							new Model_DbTable_Games_Param_Markets_MarketResearches(),
							new Model_DbTable_Games_Param_Pr_ProductionTimes(),
							new Model_DbTable_Games_Param_Pr_ProductionCosts(),
							new Model_DbTable_Games_Evolution_Pr_ProductionCosts(),
							new Model_DbTable_Games_Param_Pr_DistributionCosts(),
							new Model_DbTable_Games_Evolution_Pr_DistributionCosts(),
							new Model_DbTable_Games_Param_Pr_RawMaterialsCosts(),
							new Model_DbTable_Games_Evolution_Pr_RawMaterialsCosts(),
							new Model_DbTable_Games_Param_Pr_Suppliers(),
							new Model_DbTable_Games_Param_Pr_Organization(),
							new Model_DbTable_Games_Param_Hr_RegionProfile(),
							new Model_DbTable_Games_Param_Hr_Costs(),
							new Model_DbTable_Games_Evolution_Hr_Costs(),
							new Model_DbTable_Games_Param_Mk_ChannelsCosts(),
							new Model_DbTable_Games_Evolution_Mk_ChannelsCosts(),
							new Model_DbTable_Games_Param_Mk_ChannelsIncomeTerms(),
							new Model_DbTable_Games_Param_Mk_MediaCosts(),
							new Model_DbTable_Games_Param_Mk_TradeMediaCosts(),
							new Model_DbTable_Games_Param_Fi_Cashflow(),
							new Model_DbTable_Games_Param_Fi_InterestRate());	
			foreach ($parametersTables as $parameter){
				$parameter->applyTemplate($id, $template_id);
			}
		}
		
		function deleteGame($game_id){
			$parametersTables = array(new Model_DbTable_Games_Config_GameRounds(),
							new Model_DbTable_Games_Param_Markets_Products(),
							new Model_DbTable_Games_Param_Idi_Budget(),  
							new Model_DbTable_Games_Param_Markets_Regions(),
							new Model_DbTable_Games_Param_Markets_Channels(),
							new Model_DbTable_Games_Param_Markets_Media(),
							new Model_DbTable_Games_Param_Markets_QualityParams(),
							//VERO
							new Model_DbTable_Games_Param_Markets_FunctionalityParams(),
							new Model_DbTable_Games_Param_Markets_InvestmentsParams(),
							//VERO
							new Model_DbTable_Games_Param_Markets_Initiatives(),
							new Model_DbTable_Games_Param_Markets_Sizes(),
							new Model_DbTable_Games_Evolution_Markets_Sizes(),
							new Model_DbTable_Games_Param_Markets_ChannelsWeights(),
							new Model_DbTable_Games_Param_Markets_Fidelity(),
							new Model_DbTable_Games_Param_Markets_Prices(),
							new Model_DbTable_Games_Evolution_Markets_Prices(),
							new Model_DbTable_Games_Param_Markets_MediaProfile(),
							new Model_DbTable_Games_Evolution_Markets_MediaProfile(),
							new Model_DbTable_Games_Param_Markets_TradeMediaProfile(),
							new Model_DbTable_Games_Evolution_Markets_TradeMediaProfile(),
							new Model_DbTable_Games_Param_Markets_MarketResearches(),
							new Model_DbTable_Games_Param_Pr_ProductionTimes(),
							new Model_DbTable_Games_Param_Pr_ProductionCosts(),
							new Model_DbTable_Games_Evolution_Pr_ProductionCosts(),
							new Model_DbTable_Games_Param_Pr_DistributionCosts(),
							new Model_DbTable_Games_Evolution_Pr_DistributionCosts(),
							new Model_DbTable_Games_Param_Pr_RawMaterialsCosts(),
							new Model_DbTable_Games_Evolution_Pr_RawMaterialsCosts(),
							new Model_DbTable_Games_Param_Pr_Suppliers(),
							new Model_DbTable_Games_Param_Pr_Organization(),
							new Model_DbTable_Games_Param_Hr_RegionProfile(),
							new Model_DbTable_Games_Param_Hr_Costs(),
							new Model_DbTable_Games_Evolution_Hr_Costs(),
							new Model_DbTable_Games_Param_Mk_ChannelsCosts(),
							new Model_DbTable_Games_Evolution_Mk_ChannelsCosts(),
							new Model_DbTable_Games_Param_Mk_ChannelsIncomeTerms(),
							new Model_DbTable_Games_Param_Mk_MediaCosts(),
							new Model_DbTable_Games_Param_Mk_TradeMediaCosts(),
							new Model_DbTable_Games_Param_Fi_Cashflow(),
							new Model_DbTable_Games_Param_Fi_InterestRate());
			foreach ($parametersTables as $parameter){
				$parameter->deleteEntries($game_id);
			}
			$this->delete('id = '.$game_id);
		}
		
		/* game parameters accessors */
		
		function getGameNameById($game_id){
			$game=$this->fetchRow('id = '.$game_id)->toArray();
			return $game['name'];
		}
		
		function getCompaniesInGame($game_id){
			$companies = new Model_DbTable_Companies();
			$results=$companies->fetchAll('game_id = '.$game_id, 'name ASC');
			return $results;			
		}
		function getNumberOfCompanies($game_id){
			$companies = new Model_DbTable_Companies();
			$results=$companies->fetchAll('game_id = '.$game_id, 'name ASC');
			$companyCounter=0;
			foreach ($results as $result){
				$companyCounter++;
			}
			return $companyCounter;			
		}
		
		function getDescriptionById($id){
			$game=$this->fetchRow('id = '.$id)->toArray();
			return $game['description'];
		}
		
		function getShortDescriptionById($id){
			if (strlen($shortDescription)>20){
			  $shortDescription=substr($this->getDescription($id), 0, 20)."[...]";
			}
			return $this->getDescription();
		}
		
		function getRounds($game_id){
			$gameRounds=new Model_DbTable_Games_Config_GameRounds();
			return $gameRounds->fetchAll('game_id = '.$game_id, 'round_number ASC');
		}
		
		function getRound($game_id, $round_number){
			$gameRounds=new Model_DbTable_Games_Config_GameRounds();
			return $gameRounds->fetchRow('game_id = '.$game_id.' AND round_number = '.$round_number);
		}
		function getProducts($game_id){
			$results=new Model_DbTable_Games_Param_Markets_Products();
			return $results->fetchAll('game_id = '.$game_id, 'product_number ASC');
		}
		function getProductsBudgets($game_id){
			$results=new Model_DbTable_Games_Param_Idi_Budget();
			return $results->fetchAll('game_id = '.$game_id, 'product_number ASC');
		}
		function getChannels($game_id){
			$results=new Model_DbTable_Games_Param_Markets_Channels();
			return $results->fetchAll('game_id = '.$game_id, 'channel_number ASC');
		}
		function getMedia($game_id){
			$results=new Model_DbTable_Games_Param_Markets_Media();
			return $results->fetchAll('game_id = '.$game_id, 'media_number ASC');
		}
		function getQualityParams($game_id){
			$results=new Model_DbTable_Games_Param_Markets_QualityParams();
			//var_dump($results->fetchAll('game_id = '.$game_id, 'quality_param_number ASC'));die();
			return $results->fetchAll('game_id = '.$game_id, 'quality_param_number ASC');
		}
		function getNumberOfRounds($game_id){
			$gameRounds=new Model_DbTable_Games_Config_GameRounds();
			$rounds=$gameRounds->fetchAll('game_id = '.$game_id, 'round_number ASC');
			return count($rounds);
		}
		function getNumberOfProducts($game_id){
			$game=$this->fetchRow('id = '.$game_id)->toArray();
			return $game['n_products'];
		}
		function getNumberOfProductsAvailable($game_id, $round_number, $company_id){
			$game=$this->getProductsAvailibility($game_id, $round_number, $company_id);
			$n_products=$this->getNumberOfProducts($game_id);
			$products_available=0;
			for ($ProductCounter = 1; $ProductCounter < ($n_products+1); $ProductCounter++) {
				$availability=$game['product_number_'.$ProductCounter];
				if($availability==1){
					$products_available++;
				}
			}
			return $products_available;
		}
		function getNumberOfQualities($game_id){
			$game=$this->fetchRow('id = '.$game_id)->toArray();
			return $game['n_qualities'];
		}
		//VERO
		function getNumberOfFunctionalities($game_id){
			$game=$this->fetchRow('id = '.$game_id)->toArray();
			return $game['n_functionalities'];
		}

		function getNumberOfInvestments($game_id){
			$game=$this->fetchRow('id = '.$game_id)->toArray();
			return $game['n_investment'];
		}
		//VERO
		function getNumberOfRegions($game_id){
			$game=$this->fetchRow('id = '.$game_id)->toArray();
			return $game['n_regions'];
		}
		function getNumberOfChannels($game_id){
			$game=$this->fetchRow('id = '.$game_id)->toArray();
			return $game['n_channels'];
		}
		function getNumberOfMedia($game_id){
			$game=$this->fetchRow('id = '.$game_id)->toArray();
			return $game['n_media'];
		}
		function getNumberOfInitiatives($game_id){
			$game=$this->fetchRow('id = '.$game_id)->toArray();
			return $game['n_initiatives'];
		}
		function getProductParamAvailability($game_id, $product_number){
			$products=new Model_DbTable_Games_Param_Markets_Products();
			$result=$products->getProductAvailability($game_id, $product_number);
			return $result;
		}
		function getRegions($game_id){
			$results=new Model_DbTable_Games_Param_Markets_Regions();
			return $results->fetchAll('game_id = '.$game_id, 'region_number ASC');
		}
		function getMarketSize($game_id, $round_number, $product_number, $region_number){
			$marketSizes=new Model_DbTable_Games_Param_Markets_Sizes();
			$result=$marketSizes->fetchRow('game_id = '.$game_id.' AND round_number = '.$round_number. ' AND product_number = '.$product_number. ' AND region_number = '.$region_number);
			//var_dump($result);die();
			return $result['size'];
		}
		function getMarketsSizes($game_id){
			$marketSizes=new Model_DbTable_Games_Param_Markets_Sizes();
			$results=$marketSizes->fetchAll('game_id = '.$game_id, array('product_number ASC' , 'region_number ASC'));
			foreach ($results as $result){
				$sizes ['product_'.$result['product_number']]['region_'.$result['region_number']]=$result['size'];
				}
			return $sizes;
		}
		function getMarketSizeEvolution($game_id, $round_number, $product_number, $region_number){
			$marketSizesEvolution=new Model_DbTable_Games_Evolution_Markets_Sizes();
			$result=$marketSizesEvolution->fetchRow('game_id = '.$game_id.' AND round_number = '.$round_number. ' AND product_number = '.$product_number. ' AND region_number = '.$region_number);
			//$fl_result=(float)$result['evolution'];
			//var_dump($fl_result);
			return $result['evolution'];
			//return $fl_result;
		}
		function getChannelWeight($game_id, $round_number, $product_number, $channel_number, $region_number){
			$marketSizes=new Model_DbTable_Games_Param_Markets_ChannelsWeights();
			$result=$marketSizes->fetchRow('game_id = '.$game_id.' AND round_number = '.$round_number.' AND product_number = '.$product_number. ' AND channel_number = '.$channel_number.' AND region_number = '.$region_number);
			return $result['weight'];
		}
		/*function getMarketPrices($game_id, $product_number, $channel_number, $region_number){
			$marketSizes=new Model_DbTable_Games_Param_Markets_Prices();
			$result=$marketSizes->fetchRow('game_id = '.$game_id.' AND product_number = '.$product_number. ' AND channel_number = '.$channel_number.' AND region_number = '.$region_number);
			return $result;
		}*/
		function getMarketPrices($game_id, $round_number, $product_number, $channel_number, $region_number){
			$marketPrices=new Model_DbTable_Games_Param_Markets_Prices();
			$result=$marketPrices->fetchRow('game_id = '.$game_id.' AND round_number = '.$round_number.' AND product_number = '.$product_number. ' AND channel_number = '.$channel_number.' AND region_number = '.$region_number);
			return $result;
		}
		function getMarketPricesEvolution($game_id, $round_number, $product_number, $channel_number, $region_number){
			$marketPricesEvolution=new Model_DbTable_Games_Evolution_Markets_Prices();
			$result=$marketPricesEvolution->fetchRow('game_id = '.$game_id.' AND round_number = '.$round_number.' AND product_number = '.$product_number. ' AND channel_number = '.$channel_number.' AND region_number = '.$region_number);
			return $result;
		}
		function getMediaWeight($game_id, $round_number, $product_number, $media_number, $region_number){
			$marketMediaProfile=new Model_DbTable_Games_Param_Markets_MediaProfile();
			$result=$marketMediaProfile->fetchRow('game_id = '.$game_id.' AND round_number = '.$round_number.' AND product_number = '.$product_number.' AND media_number = '.$media_number.' AND region_number = '.$region_number);
			return $result['ideal_intensity'];
		}
		function getMediaWeightEvolution($game_id, $round_number, $product_number, $media_number, $region_number){
			$marketMediaProfileEvolution=new Model_DbTable_Games_Evolution_Markets_MediaProfile();
			$result=$marketMediaProfileEvolution->fetchRow('game_id = '.$game_id.' AND round_number = '.$round_number.' AND product_number = '.$product_number.' AND media_number = '.$media_number.' AND region_number = '.$region_number);
			return $result['evolution'];
		}
		function getTradeMediaWeight($game_id, $round_number, $product_number, $tradeMedia_number, $channel_number){
			$marketTradeMediaProfile=new Model_DbTable_Games_Param_Markets_TradeMediaProfile();
			$result=$marketTradeMediaProfile->fetchRow('game_id = '.$game_id.' AND round_number = '.$round_number.' AND product_number = '.$product_number. ' AND tradeMedia_number = '.$tradeMedia_number.' AND channel_number = '.$channel_number);
			return $result['ideal_intensity'];
		}
		function getTradeMediaWeightEvolution($game_id, $round_number, $product_number, $tradeMedia_number, $channel_number){
			$marketTradeMediaProfileEvolution=new Model_DbTable_Games_Evolution_Markets_TradeMediaProfile();
			$result=$marketTradeMediaProfileEvolution->fetchRow('game_id = '.$game_id.' AND round_number = '.$round_number.' AND product_number = '.$product_number. ' AND tradeMedia_number = '.$tradeMedia_number.' AND channel_number = '.$channel_number);
			return $result['evolution'];
		}
		//OK
		function getProductionTime($game_id, $product_number, $quality){
			$prProductionTimes=new Model_DbTable_Games_Param_Pr_ProductionTimes();
			$result=$prProductionTimes->fetchRow('game_id = '.$game_id.' AND product_number = '.$product_number);
			$productTimes=explode(";", $result['times']);
			//var_dump($productTimes);
			return $productTimes[$quality];
		}
		function getProductionTimes($game_id){
			$prProductionTimes=new Model_DbTable_Games_Param_Pr_ProductionTimes();
			$results=$prProductionTimes->fetchAll('game_id = '.$game_id);
			foreach ($results as $result){
				$times['product_'.$result['product_number']]=explode(';',$result['times']);
			}
			return $times;
		}
		function getInitiatives($game_id){
			$results=new Model_DbTable_Games_Param_Markets_Initiatives();
			$result=$results->fetchAll('game_id = '.$game_id, 'initiative_number ASC');
			return $result;
		}
		function getInitiativesProd($game_id){
			$results=new Model_DbTable_Games_Param_Markets_Initiatives();
			$p=0;
			$result=$results->fetchAll('game_id = '.$game_id. ' AND area = '.$p, 'initiative_number ASC');
			return $result;
		}
		function getInitiativesHR($game_id){
			$results=new Model_DbTable_Games_Param_Markets_Initiatives();
			$h=1;
			$result=$results->fetchAll('game_id = '.$game_id. ' AND area = '.$h, 'initiative_number ASC');
			return $result;
		}
		function getInitiativesMKT($game_id){
			$results=new Model_DbTable_Games_Param_Markets_Initiatives();
			$m=2;
			$result=$results->fetchAll('game_id = '.$game_id. ' AND area = '.$m, 'initiative_number ASC');
			return $result;
		}
		function getInitiativesDET($game_id){
			$results=new Model_DbTable_Games_Param_Markets_Initiatives();
			$m=3;
			$result=$results->fetchAll('game_id = '.$game_id. ' AND area = '.$m, 'initiative_number ASC');
			return $result;
		}
		function getFidelity($game_id, $product_number, $channel_number, $region_number){
			$results=new Model_DbTable_Games_Param_Markets_Fidelity();
			$result=$results->fetchRow('game_id = '.$game_id.' AND product_number = '.$product_number.' AND region_number = '.$region_number.' AND channel_number = '.$channel_number);
			return $result['fidelity'];
		}
		function getInitiativeChosen($game_id, $round_number, $company_id){
			$results=new Model_DbTable_Decisions_Initiatives();
			$result=$results->getInitiativesChosen($game_id, $company_id, $round_number);
			return $result;
		}
		function getProductionCost($game_id, $round_number, $region_number, $cost_type, $product_number=0){
			$prProductionCosts=new Model_DbTable_Games_Param_Pr_ProductionCosts();
			if ($cost_type=='fixed'){
				$result=$prProductionCosts->fetchRow('game_id = '.$game_id.' AND round_number = '.$round_number.' AND region_number = '.$region_number.' AND cost_type = "fixed"');
			}
			else{
				$result=$prProductionCosts->fetchRow('game_id = '.$game_id.' AND round_number = '.$round_number.' AND region_number = '.$region_number.' AND cost_type = "unit"'.
											' AND product_number = '.$product_number);
			}
			return $result['cost'];
		}
		function getProductionCostEvolution($game_id, $round_number, $region_number, $cost_type, $product_number=0){
			$prProductionCostsEvolution=new Model_DbTable_Games_Evolution_Pr_ProductionCosts();
			if ($cost_type=='fixed'){
				$result=$prProductionCostsEvolution->fetchRow('game_id = '.$game_id.' AND round_number = '.$round_number.' AND region_number = '.$region_number.' AND cost_type = "fixed"');
			}
			else{
				$result=$prProductionCostsEvolution->fetchRow('game_id = '.$game_id.' AND round_number = '.$round_number.' AND region_number = '.$region_number.' AND cost_type = "unit"'.
											' AND product_number = '.$product_number);
			}
			return $result['evolution'];
		}
		function getDistributionCost($game_id, $round_number, $source_region_number, $destination_region_number){
			$prProductionCosts=new Model_DbTable_Games_Param_Pr_DistributionCosts();
			$result=$prProductionCosts->fetchRow('game_id = '.$game_id.' AND round_number = '.$round_number.' AND source_region_number = '.$source_region_number.' AND destination_region_number = '.$destination_region_number);
			return $result['cost'];
		}
		function getDistributionCostEvolution($game_id, $round_number, $source_region_number, $destination_region_number){
			$prProductionCostsEvolution=new Model_DbTable_Games_Evolution_Pr_DistributionCosts();
			$result=$prProductionCostsEvolution->fetchRow('game_id = '.$game_id.' AND round_number = '.$round_number.' AND source_region_number = '.$source_region_number.' AND destination_region_number = '.$destination_region_number);
			return $result['evolution'];
		}
		function getRawMaterialCost($game_id, $round_number, $product_number, $type){
			$prRawMaterialsCosts=new Model_DbTable_Games_Param_Pr_RawMaterialsCosts();
			$result=$prRawMaterialsCosts->fetchRow('game_id = '.$game_id.' AND round_number = '.$round_number.' AND product_number = '.$product_number);
			return $result[$type];
		}
		function getRawMaterialCostEvolution($game_id, $round_number, $product_number, $type){
			$prRawMaterialsCostsEvolution=new Model_DbTable_Games_Evolution_Pr_RawMaterialsCosts();
			$result=$prRawMaterialsCostsEvolution->fetchRow('game_id = '.$game_id.' AND round_number = '.$round_number.' AND product_number = '.$product_number);
			return $result[$type];
		}
		function getSuppliersPaytimeCost($game_id, $months){
			$prSuppliers=new Model_DbTable_Games_Param_Pr_Suppliers();
			$result=$prSuppliers->fetchRow('game_id = '.$game_id);
			$payterms=explode(";",$result['payTerms']);
			return $payterms[$months];
		}
		function getIdealSuppliersNumber($game_id){
			$prSuppliers=new Model_DbTable_Games_Param_Pr_Suppliers();
			$result=$prSuppliers->fetchRow('game_id = '.$game_id);
			return $result['ideal_number'];
		}
	 	function getInitiativesNumber($game_id){
			$inNumber=new Model_DbTable_Games_Param_In_Initiatives();
			$result=$inNumber->fetchRow('game_id = '.$game_id);
			return $result['game_initiatives'];
		}
		function getOrganizationParam($game_id, $param){
			$prOrganization=new Model_DbTable_Games_Param_Pr_Organization();
			$result=$prOrganization->fetchRow('game_id = '.$game_id);
			return $result[$param];
		}
		//Hay que pasar por parametro el numero de fabrica y asi cogemos el numero de maquinas de cada una
		function getNominalTime($game_id, $round_number, $company_id){
			$nominal_time=array();
			$prOrganization=new Model_DbTable_Games_Param_Pr_Organization();
			$result=$prOrganization->fetchRow('game_id = '.$game_id);
			$work_shifts=$result['work_shifts'];
			//$work_shifts_extension=ceil(($work_shifts/$machines)*$machines_extension);
			$work_shifts_extension=0; //porque lo anterior no tiene sentido
			$work_hours_per_week=$result['work_hours_per_week'];
			$constructed=$this->getRoundFactoryCreated($game_id, $company_id);
			$prod=New Model_DbTable_Decisions_Production();
			$factories=$prod->getFactoriesObjects($game_id, $company_id, $round_number)->toArray();
			//$factories=$this->_core->_companies->_factory->toArray();
			foreach ($factories as $factory){
				if(($round_number>$constructed['factory_number_'.$factory['factory_number']])||($constructed['factory_number_'.$factory['factory_number']]==1)){
					$machines=$result['machines'];
					$machines_extension=$this->getExtensionFactory($game_id, $round_number, $company_id, $factory['factory_number'], 0); //El último parámetro es 0 para que considere los sueldos de las ampliaciones de todas las rondas.
					echo("<br>Factory: ".$factory['factory_number']."<br>");
					echo("<br>Machines extension".$machines_extension."<br>");
					$nominal_time['factory_number_'.$factory['factory_number']]=$work_shifts*($machines+$machines_extension)*$work_hours_per_week*52*60;
					echo("<br>Nominal time added: ".$nominal_time."<br>");
				}
			}
			return $nominal_time;
			//return ($work_shifts+$work_shifts_extension)*($machines+$machines_extension)*$work_hours_per_week*52*60;
		}
		function getHrStaffCost($game_id, $round_number, $region_number, $type){
			$hrCosts=new Model_DbTable_Games_Param_Hr_Costs();
			$result=$hrCosts->fetchRow('game_id = '.$game_id.' AND round_number = '.$round_number.' AND region_number = '.$region_number);
			return $result[$type];
		}
		function getHrStaffCostEvolution($game_id, $round_number, $region_number, $type){
			$hrCostsEvolution=new Model_DbTable_Games_Evolution_Hr_Costs();
			$result=$hrCostsEvolution->fetchRow('game_id = '.$game_id.' AND round_number = '.$round_number.' AND region_number = '.$region_number);
			return $result[$type];
		}
		function getHrRegionProfileParam($game_id, $region_number, $type){
			$hrRegionProfile=new Model_DbTable_Games_Param_Hr_RegionProfile();
			$result=$hrRegionProfile->fetchRow('game_id = '.$game_id.' AND region_number = '.$region_number);
			return $result[$type];
		}
		function getMkChannelCost($game_id, $round_number, $channel_number, $region_number, $type){
			$mkChannelCosts=new Model_DbTable_Games_Param_Mk_ChannelsCosts();
			$result=$mkChannelCosts->fetchRow('game_id = '.$game_id.' AND round_number = '.$round_number.' AND channel_number = '.$channel_number. ' AND region_number = '.$region_number);
			return $result[$type];
		}
		function getMkChannelCostEvolution($game_id, $round_number, $channel_number, $region_number, $type){
			$mkChannelCostsEvolution=new Model_DbTable_Games_Evolution_Mk_ChannelsCosts();
			$result=$mkChannelCostsEvolution->fetchRow('game_id = '.$game_id.' AND round_number = '.$round_number.' AND channel_number = '.$channel_number. ' AND region_number = '.$region_number);
			return $result[$type];
		}
		function getMkChannelIncomeTerms($game_id, $channel_number){
			$mkChannelTerms=new Model_DbTable_Games_Param_Mk_ChannelsIncomeTerms();
			$result=$mkChannelTerms->fetchRow('game_id = '.$game_id.' AND channel_number = '.$channel_number);
			return $result['time'];
		}
		//No se usa ni el metodo ni la tabla
		function getMkMediaCost($game_id, $media_number, $region_number, $intensity){
			$mkMediaCosts=new Model_DbTable_Games_Param_Mk_MediaCosts();
			$result=$mkMediaCosts->fetchRow('game_id = '.$game_id.' AND media_number = '.$media_number.' AND region_number = '.$region_number);
			$costs=explode(";",$result['costs']);
			return $costs[$intensity-1];
		}
		//No se usa ni el metodo ni la tabla
		function getMkTradeMediaCost($game_id, $trademedia_number, $region_number, $intensity){
			$mkTradeMediaCosts=new Model_DbTable_Games_Param_Mk_TradeMediaCosts();
			$result=$mkTradeMediaCosts->fetchRow('game_id = '.$game_id.' AND trademedia_number = '.$trademedia_number.' AND region_number = '.$region_number);
			$costs=explode(";",$result['costs']);
			return $costs[$intensity-1];
		}
		
		function getMarketResearchesCosts($game_id){
			$results=new Model_DbTable_Games_Param_Markets_MarketResearches();
			$result=$results->fetchAll('game_id = '.$game_id, 'research_number ASC');
			return $result;
		}
		
		function getFiCashflowParameter($game_id, $type){
			$fiCashflowParameters=new Model_DbTable_Games_Param_Fi_Cashflow();
			$result=$fiCashflowParameters->fetchRow('game_id = '.$game_id);
			return $result[$type];
		}
		
		function getInterestRateParameter($game_id, $type, $term){
			$interestRateParameters=new Model_DbTable_Games_Param_Fi_InterestRate();
			$result=$interestRateParameters->fetchRow('game_id = '.$game_id.' AND term = '.$term);
			return $result[$type];
		}
		
		function getIdiProducts($game_id){
			$results=new Model_DbTable_Games_Param_Markets_Products();
			$availability=1;
			$result=$results->fetchAll('game_id = '.$game_id. ' AND availability = '.$availability, 'product_number ASC');
			return $result;
		}
		//Devuelve el umbral de P(acierto). Esta metido directamente en la base de datos, hay que modificarla si queremos cambiar el umbral
		function getNewIdiProductsThreshold($game_id, $round_number, $product_number){
			$umbral_db=new Model_DbTable_Games_Param_Idi_Threshold();
			$threshold=$umbral_db->getProductThreshold($game_id, $product_number);
			$availability_all=$this->getProductsAvailibilityBySomeone($game_id, $round_number);
			$availability=$availability_all['product_number_'.$product_number];
			if($availability==1){
				$threshold=0.96*(1-(3*($round_number/100)));
			}
			return $threshold;
		}
		//Funciona
		function getIdealInvestment($game_id, $round_number, $product_number){
			$budget=new Model_DbTable_Games_Param_Idi_Budget();

			$result=$budget->getProductBudget($game_id, $product_number);
			return $result;
		}
		// Devuelve la disponibilidad de los productos de una compa–ia
		function getProductsAvailibility($game_id, $round_number, $company_id){
			$ProductsNumber=$this->getNumberOfProducts($game_id);
			for ($product_number = 1; $product_number <= $ProductsNumber; $product_number++) {
					$availabilty['product_number_'.$product_number]=$this->getProductAvailibility($game_id, $round_number, $company_id, $product_number);
			}
			return $availabilty;
		}
		//Devuelve $availability=1 de cada producto si al menos un equipo lo tiene disponible, si no esta disponible para ningun equipo $availability=0
		function getProductsAvailibilityBySomeone($game_id, $round_number){
			$ProductsNumber=$this->getNumberOfProducts($game_id);
			$products=$this->getProducts($game_id);
			$companies=$this->getCompaniesInGame($game_id);
			foreach ($companies as $company) {
				foreach ($products as $product) {
					$availabilty_aux=$this->getProductAvailibility($game_id, $round_number, $company['id'], $product['product_number']);
					//var_dump($availabilty_aux);
					if($availabilty_aux==1){
						$availabilty['product_number_'.$product['product_number']]=$availabilty_aux;
						//break 1;
					}
				}
			}
			//var_dump($availabilty);
			return $availabilty;
		}
		// Devuelve la disponibilidad de un producto para una compa–ia
		function getProductAvailibility($game_id, $round_number, $company_id, $product_number){
			$new=new Model_DbTable_Games_Evolution_Np_NewProducts();
			$game_availability=$this->getProductParamAvailability($game_id, $product_number);
			for ($round_counter = 1; $round_counter <= $round_number; $round_counter++) {
				$actual_availability_aux=$new->getActualAvailability($game_id, $company_id, $round_counter, $product_number);
				if($actual_availability_aux==null){
					$actual_availability=0;
				}
				if($actual_availability_aux==1){
					$actual_availability=1;
				}
			}
			//var_dump($actual_availability);
			if($game_availability!=0) {
				//var_dump($game_availability);
				if($actual_availability!=1){
					$availability=0;
					//var_dump($availability);
				}
				else {
					$availability=1;
				}
			}
			else {
				$availability=1;
			}
			//echo("<br/> EQUIPO ".$company_id." - PRODUCTO ".$product_number." - AVAILABILITY: ".$availability);
			return $availability;
		}
		
		function getFactories($game_id, $company_id){
			$results=new Model_DbTable_Decisions_Pr_Region();
			$result=$results->fetchAll('game_id = '.$game_id.' AND company_id = '.$company_id, 'factory_number ASC');
			return $result;
		}
		
		function getExtensions($game_id, $company_id){
			$results=new Model_DbTable_Decisions_Pr_Capacity();
			$result=$results->getExtensionTotalArray($game_id, $company_id);
			//$result=$results->fetchAll('game_id = '.$game_id.' AND company_id = '.$company_id, 'factory_number ASC');
			return $result;
		}
		
		//Devuelve deterioro para outcomes
		function getFactoryDeterioration($game_id, $round_number, $company_id){
			$deterioration=new Model_DbTable_Outcomes_Rd_PrDeterioration();
			$results=$deterioration->getDeteriorationByCompany($game_id, $round_number, $company_id, $factory_number);
			foreach ($results as $result){
				if($result['value']<0.7){
					$array['factory_number_'.$result['factory_number']]=0;
				}
				else {
					$array['factory_number_'.$result['factory_number']]=(6*exp(1)-(6*exp($result['value'])))*100;
				}
			}
			return $array;
		}
		//Devuelve WorkAtmosphere para outcomes AHG 20171106: ¿Por qué la condición de 0.87?
		function getWorkAtmosphere($game_id, $round_number, $company_id){
			$atmosphere=new Model_DbTable_Outcomes_Rd_HrData();
			$results=$atmosphere->getHrDataCompanyAtmosphere($game_id, $round_number, $company_id);
			if($results['value']<0.87){
				return 0;
			}
			else{
				return (1+(3*exp($results['value'])-3*exp(1)))*100;
			}
		}
		//Devuelve QualificationLevel para outcomes
		function getQualificationLevel($game_id, $round_number, $company_id){
			$qualification=new Model_DbTable_Outcomes_Rd_HrData();
			$results=$qualification->getHrDataCompanyCualification($game_id, $round_number, $company_id);
			if($results['value']<0.87){
				return 0;
			}
			else{
				return (1+(3*exp($results['value'])-3*exp(1)))*100;
			}
		}
		//Devuelve SuccessProbability para outcomes
		function getSuccessProbabilityOutcomes($game_id, $round_number, $company_id){
			$success=new Model_DbTable_Games_Evolution_Np_NewProducts();
			$round_next=$round_number+1;
			$products=$this->getProducts($game_id);
			foreach ($products as $product) {
				$probability=($success->getSuccessProbability($game_id, $company_id, $round_next, $product['product_number']))*100;
				if ($probability==null){
					$probability=0;
				}
				$results['product_number_'.$product['product_number']]=$probability;
			}
			return $results;
		}
		//Funcion que nos da los ingresos ideales de la totalidad del mercado, dividido por el numero de equipos.
		function getFinanceAmount($game_id, $round_number, $company_id){
			$ProductsNumber=$this->getNumberOfProducts($game_id);
			$RegionsNumber=$this->getNumberOfRegions($game_id);
			$ChannelsNumber=$this->getNumberOfChannels($game_id);
			$total_market_size=0;
			for ($ProductCounter = 1; $ProductCounter < ($ProductsNumber+1); $ProductCounter++) {
				$availability=$this->getProductAvailibility($game_id, $round_number, $company_id, $ProductCounter);
				//var_dump($availability);
				if($availability==1){
					for ($RegionCounter = 1; $RegionCounter < ($RegionsNumber+1); $RegionCounter++) {
						$market_size=$this->getMarketSize($game_id, $round_number, $ProductCounter, $RegionCounter);
						for ($ChannelCounter = 1; $ChannelCounter < ($ChannelsNumber+1); $ChannelCounter++) {
							$ideal_price=$this->getMarketPrices($game_id, $round_number, $ProductCounter, $ChannelCounter, $RegionCounter);
							$average_price=$ideal_price['ideal_price'];
							$total_market_size=$total_market_size+($market_size*$average_price);
							//var_dump($market_size);die();
						}
					}
				}
				else {
					$total_market_size+=0;
				}
			}
			$companies_counter=$this->getNumberOfCompanies($game_id);
			$amount=($total_market_size/$ChannelsNumber)/$companies_counter;
			
			$option[0]=array('value'=>ceil(0.0*$amount), 'descriptor'=>'Ninguno');
			$option[1]=array('value'=>ceil(0.6*$amount), 'descriptor'=>'OpciÃ³n 1: '.number_format(ceil(0.6*$amount), 2, '.', ',').' â‚¬');
			$option[2]=array('value'=>ceil(0.9*$amount), 'descriptor'=>'OpciÃ³n 2: '.number_format(ceil(0.9*$amount), 2, '.', ',').' â‚¬');
			$option[3]=array('value'=>ceil(1.2*$amount), 'descriptor'=>'OpciÃ³n 3: '.number_format(ceil(1.2*$amount), 2, '.', ',').' â‚¬');
			
			return $option;
		}

		function getLastFactory($game_id, $company_id){
			$results=new Model_DbTable_Decisions_Pr_Region();
			$factory_number=1;
			while(($results->fetchRow('game_id = '.$game_id.' AND company_id = '.$company_id.' AND factory_number = '.$factory_number))!=null){
				$factory_number++;
			}
			return $factory_number;	
		}
		function getRoundFactoryCreated($game_id, $company_id){
			$aux=new Model_DbTable_Decisions_Pr_Region();
			$results=$aux->fetchAll('game_id = '.$game_id.' AND company_id = '.$company_id, 'factory_number ASC');
			foreach ($results as $result){
				$array['factory_number_'.$result['factory_number']]=$result['round_number_created'];
			}
			return $array;	
		}
		function getInterestAdjustment($game_id, $round_number, $company_id){
			if ($round_number>1){
				$outcomes=new Model_DbTable_Outcomes();
				$round_previous=$round_number-1;
				$asset_current=$outcomes->getAssetCurrent($game_id, $round_previous, $company_id);
				$liabilities_total=$outcomes->getLiabilitiesTotal($game_id, $round_previous, $company_id);
				$finance_rating=($asset_current/$liabilities_total);
				$ideal_rating=2;
				$adjustment=($ideal_rating-$finance_rating);
				return $adjustment;
			}
			return 0;
		}
		function getInterestRate($game_id){
			$interestRate=new Model_DbTable_Games_Param_Fi_InterestRate();
			$results=$interestRate->fetchAll('game_id = '.$game_id, 'term ASC');
			foreach ($results as $result){
				$array['term_'.$result['term']]=$result['interest_rate'];
			}
			return $array;
		}
		function getYearAmortization($game_id, $round_number, $company_id){
			$amortization=new Model_DbTable_Games_Evolution_Am_Amortization();
			for($r_number=1; $r_number<=$round_number; $r_number++){
				$amountCons=$amortization->getConsAmount($game_id, $company_id, $r_number);
				$termCons=$amortization->getConsTerm($game_id, $company_id, $r_number);
				$totalAmount+=($amountCons/$termCons);
				$amountExt=$amortization->getExtAmount($game_id, $company_id, $r_number);
				$termExt=$amortization->getExtTerm($game_id, $company_id, $r_number);
				$totalAmount+=($amountExt/$termExt);
			}
			return $totalAmount;	
		}
		
		// Creada para que muestre bien la salida a los jugadores (es independiente de CompanyID, que es la culpable de que no saliera bien)
		function getYearAmortizationArray($game_id, $round_number){
			$amortization=new Model_DbTable_Games_Evolution_Am_Amortization();
			$amortizationArray=array();
			$companies=$this->getCompaniesInGame($game_id);
			foreach ($companies as $company) {
				$totalAmount=0;
				//var_dump($company['id']);die();
				for($r_number=1; $r_number<=$round_number; $r_number++){
					$amountCons=$amortization->getConsAmount($game_id, $company['id'], $r_number);
					$termCons=$amortization->getConsTerm($game_id, $company['id'], $r_number);
					$totalAmount+=($amountCons/$termCons);
					$amountExt=$amortization->getExtAmount($game_id, $company['id'], $r_number);
					$termExt=$amortization->getExtTerm($game_id, $company['id'], $r_number);
					$totalAmount+=($amountExt/$termExt);
				}
				$amortizationArray[$company['id']]=$totalAmount;
			}
			return $amortizationArray;	
		}

		
		function getYearResult($game_id, $round_number, $company_id){
			$outcomes=new Model_DbTable_Outcomes();
			$incomes=$outcomes->getYearIncomes($game_id, $round_number, $company_id);
			$costs=$outcomes->getYearCosts($game_id, $round_number, $company_id);
			$amortization=$this->getYearAmortization($game_id, $round_number, $company_id);
			$stock_var=new Model_DbTable_Outcomes_Bs_BalanceSheet();
			$investmentResult=$outcomes->getInvestmentByCompany($game_id, $round_number, $company_id);
			$stock_var_value=0;
			if($round_number==1){
				$stock_var_value=$stock_var->getCompanyStockValue($game_id, $round_number, $company_id);
			} else {
				$stock_var_value=($stock_var->getCompanyStockValue($game_id, $round_number, $company_id))-($stock_var->getCompanyStockValue($game_id, ($round_number-1), $company_id));
			}
			//echo("CHECK POINT 2: stock_var->getCompanyStockValue($game_id, $round_number, $company_id)) = ".$stock_var->getCompanyStockValue($game_id, $round_number, $company_id)."<br>CHECK POINT 2: stock_var->getCompanyStockValue($game_id, ($round_number-1), $company_id) = ".$stock_var->getCompanyStockValue($game_id, ($round_number-1), $company_id)."<br>");			
			//echo("CHECK POINT 2: stock_var_value = ".$stock_var_value."<br>");
			/*$stocks=new Model_DbTable_Games_Evolution_St_Stocks();
			$product_number=$this->getNumberOfProducts($game_id);
			$region_number=$this->getNumberOfRegions($game_id);
			$channel_number=$this->getNumberOfChannels($game_id);
			for($round_n=1; $round_n<=$round_number; $round_n++){
				for ($pn = 1; $pn <= $product_number; $pn++) {
					for ($rg = 1; $rg <= $region_number; $rg++) {
						for ($ch = 1; $ch <= $channel_number; $ch++) {
							$round_stock=$stocks->getStockClasified($game_id, $company_id, $round_n, $pn, $rg, $ch);
							$cost_stock=$stocks->getStockPrCost($game_id, $company_id, $round_n, $pn, $rg, $ch);
							$stock_value=($round_stock*$cost_stock);
						}
					}
				}
			}*/
			//$stock_value=0;
			echo("<br/>STOCK VARIANCE VALUE = ".$stock_var_value."<br/>");
			echo("COSTS = ".$costs."<br/>");
			$ebt=$incomes-($costs-$stock_var_value+$investmentResult['fi_investment_losses'])+$investmentResult['fi_investment_earnings'];
			echo("EBT = ".$ebt."<br/>");
			if(($ebt<=0)||($ebt-$amortization)<0){
				$taxes=0;
			}
			else {
				$taxes=($ebt-$amortization)*0.25;
			}
			$result=$ebt-$amortization-$taxes;
			echo("<br/> EQUIPO ".$company_id."<br/> INGRESOS ANUALES: ".$incomes);
			echo("<br/> EQUIPO ".$company_id."<br/> COSTES ANUALES: ".$costs);
			echo("<br/> EQUIPO ".$company_id."<br/> INTERESES GANADOS EN INVERSIONES: ".$investmentResult['fi_investment_earnings']);
			echo("<br/> EQUIPO ".$company_id."<br/> INTERESES PERDIDOS EN INVERSIONES: ".$investmentResult['fi_investment_losses']);
			echo("<br/> EQUIPO ".$company_id."<br/> EBT: ".$ebt);
			echo("<br/> EQUIPO ".$company_id."<br/> AMORTIZACION: ".$amortization);
			echo("<br/> EQUIPO ".$company_id."<br/> IMPUESTOS: ".$taxes);
			echo("<br/> EQUIPO ".$company_id."<br/> RESULTADO: ".$result);
			return $result;	
		}
		function getStockValue($game_id, $round_number, $company_id, $product_number, $region_number, $channel_number){
			$stocks=new Model_DbTable_Games_Evolution_St_Stocks();
			$stock_value=0;
			for($round_numb=1; $round_num<=$round_number; $round_num++){
				$round_stock=$stocks->getStockClasified($game_id, $company_id, $round_numb, $product_number, $region_number, $channel_number);
				$cost_stock=$stocks->getStockPrCost($game_id, $company_id, $round_numb, $product_number, $region_number, $channel_number);
				$stock_value_aux=($round_stock*$cost_stock);
				$stock_value+=$stock_value_aux;
			}
			return $stock_value;
		}
		
		//Devuelve la extension de la fabrica efectiva para el numero de ronda en el que estemos
		// (si hay ampliaciones creadas en esta ronda no estaran disponibles hasta la siguiente)
		function getExtensionFactory($game_id, $round_number, $company_id, $factory_number, $single_round){
			$result = 0;
			$capacity=new Model_DbTable_Decisions_Pr_Capacity();
			$extension_factory=$capacity->getExtensionWasCreated($game_id, $company_id, $factory_number);
			if ($single_round==0) {		//Ampliaciones de todas las rondas
				for ($round = 2; $round < $round_number; $round++) {
					$result+=$extension_factory['factory_number_'.$factory_number]['capacity_'.$round];
					// $round_created=$extension_factory['factory_number_'.$factory_number]['round_number_created_'.$round];
					// //var_dump($round_created);
					// if($round_number>$round_created){
						// $extension[$round]=$extension_factory['factory_number_'.$factory_number]['capacity_'.$round];
						// //var_dump($extension);
					// }
					// else {
						// $extension[$round]=0;
					// }
					// $result+=$extension[$round];
					// if($result==null){
						// $result=0;
					// }
				}
			} elseif ($single_round==1) {
				$result+=$extension_factory['factory_number_'.$factory_number]['capacity_'.($round_number-1)];
			}
			return $result;		
		}
		//Devuelve el numero de empleados necesarios para la ampliacion de fabrica existente
		function getExtensionEmployees($game_id, $round_number, $company_id, $factory_number, $single_round){
			$machines=$this->getOrganizationParam($game_id, 'machines');
			$production_workers=$this->getOrganizationParam($game_id, 'production_workers');
			$packaging_workers=$this->getOrganizationParam($game_id, 'packaging_workers');
			$quality_workers=$this->getOrganizationParam($game_id, 'quality_workers');
			$maintenance_workers=$this->getOrganizationParam($game_id, 'maintenance_workers');
			
			$extension_factory=$this->getExtensionFactory($game_id, $round_number, $company_id, $factory_number, $single_round);
			$production_workers_extension=ceil(($production_workers/$machines)*$extension_factory);
			$packaging_workers_extension=ceil(($packaging_workers/$machines)*$extension_factory);
			$quality_workers_extension=ceil(($quality_workers/$machines)*$extension_factory);
			$maintenance_workers_extension=ceil(($maintenance_workers/$machines)*$extension_factory);
			
			$new_staff=$production_workers_extension+$packaging_workers_extension+$quality_workers_extension+$maintenance_workers_extension;
			
			return $new_staff;		
		}
		//Devuelve la cuota real de mercado
		function getRealShare($game_id, $company_id, $round_number, $product_number, $region_number, $channel_number){
			$share=new Model_DbTable_Outcomes_Rd_MarketShares();
			$result=$share->getRealShare($game_id, $company_id, $round_number, $product_number, $region_number, $channel_number);
			//var_dump($result);
			return $result;
		}
		//Devuelve la situacion del presupuesto de publicidad respecto de los competidores
		function getMktSituation($game_id, $round_number, $company_id){
			$mkt=new Model_DbTable_Decisions_Marketing();
			$result=$mkt->getAdvertisingsBudget($game_id, $company_id, $round_number);
			$company_advertising=$result;
			$max=0;
			$min=0;
			$min_counter=0;
			$companies=$this->getCompaniesInGame($game_id);
			foreach ($companies as $company) {
				$result=$mkt->getAdvertisingsBudget($game_id, $company['id'], $round_number);
				$advertising=$result;
				if($min_counter==0){
					$min=$advertising;
					$min_counter++;
				}
				if ($advertising>$max){
					$max=$advertising;
				}
				if($advertising<$min){
					$min=$advertising;
				}
			}
			$situation=($company_advertising/$max)*100;
			$array['max']=$max;
			$array['min']=$min;
			$array['situation']=$situation;
			return $array;
		}
		//Devuelve la situacion del presupuesto de trademkt respecto de los competidores
		function getTradeSituation($game_id, $round_number, $company_id){
			$mkt=new Model_DbTable_Decisions_Marketing();
			$result=$mkt->getTradesMktBudget($game_id, $company_id, $round_number);
			$company_trade=$result;
			$max=0;
			$min=0;
			$min_counter=0;
			$companies=$this->getCompaniesInGame($game_id);
			foreach ($companies as $company) {
				$result=$mkt->getTradesMktBudget($game_id, $company['id'], $round_number);
				$trade=$result;
				if($min_counter==0){
					$min=$trade;
					$min_counter++;
				}
				if ($trade>$max){
					$max=$trade;
				}
				if($trade<$min){
					$min=$trade;
				}
			}
			$situation=($company_trade/$max)*100;
			$array['max']=$max;
			$array['min']=$min;
			$array['situation']=$situation;
			return $array;
		}
		//Devuelve la situacion del precio respecto de los competidores
		function getPriceSituation($game_id, $round_number, $company_id, $product_number, $region_number, $channel_number){
			$prices=new Model_DbTable_Decisions_Mk_Prices();
			$result=$prices->getAllDecisions($game_id, $round_number);
			$result_company=$result['company_id_'.$company_id];
			$result_product=$result_company['product_'.$product_number];
			$result_channel=$result_product['channel_'.$channel_number];
			$result_region=$result_channel['region_'.$region_number];
			$company_price=$result_region;
			$max=0;
			$min=0;
			$min_counter=0;
			$companies=$this->getCompaniesInGame($game_id);
			foreach ($companies as $company) {
				if($company['id']!=$company_id){
					$result_company=$result['company_id_'.$company['id']];
					$result_product=$result_company['product_'.$product_number];
					$result_channel=$result_product['channel_'.$channel_number];
					$result_region=$result_channel['region_'.$region_number];
					$price=$result_region;
				}
				else $price=$company_price;
				//var_dump($price);
				if($min_counter==0){
					$min=$price;
					$min_counter++;
				}
				if ($price>$max){
					$max=$price;
				}
				if (($price<$min)&&($price!=0)){
					$min=$price;
				}
			}
			if($min==$max){
				return 100;
			}
			//$scale=$max-$min;
			//$situation=(($company_price*100)/$scale)-$scale;
			$situation=(($company_price-$min)/($max-$min))*100;
			$array['max']=$max;
			$array['min']=$min;
			$array['situation']=$situation;
			return $array;
		}
		//Devuelve el precio maximo para los estudios de mercado
		function getPriceMax($game_id, $round_number, $product_number, $region_number, $channel_number){
			$prices=new Model_DbTable_Decisions_Mk_Prices();
			$max=0;
			$companies=$this->getCompaniesInGame($game_id);
			foreach ($companies as $company) {
				$result=$prices->getDecision($game_id, $company['id'], $round_number);
				$result_product=$result['product_'.$product_number];
				$result_channel=$result_product['channel_'.$channel_number];
				$result_region=$result_channel['region_'.$region_number];
				$price=$result_region;
				if ($price>$max){
					$max=$price;
				}
			}
			return $max;
		}
		//Devuelve todos los precios para los estudios de mercado
		function getPricesAll($game_id, $round_number,$company_id){
			$prices=new Model_DbTable_Decisions_Mk_Prices();
			$result=$prices->getDecision($game_id, $company_id, $round_number);
			return $result;
		}
		//devuelve el precio minimo para los estudios de mercado
		function getPriceMin($game_id, $round_number, $product_number, $region_number, $channel_number){
			$prices=new Model_DbTable_Decisions_Mk_Prices();
			$min=0;
			$min_counter=0;
			$companies=$this->getCompaniesInGame($game_id);
			foreach ($companies as $company) {
				$result=$prices->getDecision($game_id, $company['id'], $round_number);
				$result_product=$result['product_'.$product_number];
				$result_channel=$result_product['channel_'.$channel_number];
				$result_region=$result_channel['region_'.$region_number];
				$price=$result_region;
				if($min_counter==0){
					$min=$price;
					$min_counter++;
				}
				if($price<$min){
					$min=$price;
				}
			}
			return $min;
		}
		//Devuelve la ronda espera del lanzamiento de un nuevo product. Para los estudios de mercado
		function getRoundLaunches($game_id, $company_id, $round_number, $product_number){
			$n_rounds=$this->getNumberOfRounds($game_id);
			$launches=new Model_DbTable_Games_Evolution_Np_NewProducts();
			$new=new Model_DbTable_Decisions_Idi_New();
			$solicited=$new->getProductSolicited($game_id, $company_id, $round_number, $product_number);
			//for ($round = $round_number; $round <= $n_rounds; $round++) {
				$release[$round_number]=$launches->getActualAvailability($game_id, $company_id, $round_number+1, $product_number); //$round, $product_number);
								//var_dump($release[$round_number]);								
				if($release[$round_number]==1){
					$result['product_'.$product_number]['Equipo_'.$company_id]=1;//"Lanzamiento Exitoso";//$round;
				}
				else{
					if ($solicited==1) {
						//Comentadas estas líneas porque si no, se entiende que siempre se lanza el producto.
						//if($round_number+1>$n_rounds){							
							$result['product_'.$product_number]['Equipo_'.$company_id]=0;//"No ha conseguido lanzar el producto";
						//}
						//else {
							//$result['product_'.$product_number]['Equipo_'.$company_id]="Lanzamiento exitoso";//($round+1);
						//}
					}
					else {
						$result['product_'.$product_number]['Equipo_'.$company_id]=-1;//"No ha solicitado lanzar el producto";
					}
				}
			//}
			//var_dump($result);
			return $result;
		}
		//Devuleve un 1 si el equipo tiene dinero para llevar a cabo las decisiones tomadas
		function getDecisionValidated($game_id,$round_number,$company_id){
			//creamos las tablas
			$outcomes=new Model_DbTable_Outcomes();
			$advertising=new Model_DbTable_Decisions_Mk_AdvertisingBudget();
			$trade= new Model_DbTable_Decisions_Mk_TradeMktBudget();
			$new=new Model_DbTable_Decisions_Idi_New();
			$finance=new Model_DbTable_Decisions_Fi_Amount();
			
			//cogemos el dinero disponible tesoreria + creditos
			$round_previous=$round_number-1;
			if ($round_number==1){
				$liquid_assets=$this->getFiCashflowParameter($game_id, 'starting_cash');
			}
			else {
				$liquid_assets_aux=$outcomes->getBalanceSheet($game_id, $round_previous);
				$liquid_assets=$liquid_assets_aux[$company_id]['liquid_assets'];
			}
			$credit=$finance->getDecision($game_id, $company_id, $round_number);
			$money_actual+=$liquid_assets;
			$money_actual+=$credit['amount'];
			//calculamos el dinero necesario que incluye
			// Produccion: Construccion y ampliacin fabricas
			// Marketing: Presupuesto Publicidad y Trade Mkt
			// Iniciativas contratadas
			// Estudios de Mercado contratados
			// I+D: Presupuesto de lanzamiento de nuevos productos
			$factory=$this->getConstructionCost($game_id, $company_id, $round_number);
			$extension=$this->getExtensionCost($game_id, $company_id, $round_number);
			$initiatives_prod=$this->getInitiativesProductionCost($game_id, $company_id, $round_number);
			$initiatives_mkt=$this->getInitiativesMarketingCost($game_id, $company_id, $round_number);
			$initiatives_hr=$this->getInitiativesHumanResourcesCost($game_id, $company_id, $round_number);
			$initiatives_det=$this->getInitiativesDeteriorationCost($game_id, $company_id, $round_number);
			$market_researches=$this->getMarketResearchesDirectPayment($game_id,$round_number,$company_id);		
			
			$money_needed+=$factory;
			$money_needed+=$extension;
			$money_needed+=$advertising->getDecision($game_id, $company_id, $round_number);
			$money_needed+=$trade->getDecision($game_id, $company_id, $round_number);
			$money_needed+=$initiatives_prod;
			$money_needed+=$initiatives_mkt;
			$money_needed+=$initiatives_hr;
			$money_needed+=$initiatives_det;
			$money_needed+=$market_researches;
			$money_needed+=$new->getDecisionBudget($game_id, $company_id, $round_number);
			/*echo ("<br/>");
			var_dump($credit);echo ("<br/>");
			var_dump($liquid_assets);echo ("<br/>");
			var_dump($money_actual);echo ("<br/>");
			var_dump($factory);echo ("<br/>");
			var_dump($extension);echo ("<br/>");
			var_dump($advertising->getDecision($game_id, $company_id, $round_number));echo ("<br/>");
			var_dump($trade->getDecision($game_id, $company_id, $round_number));echo ("<br/>");
			var_dump($initiatives_prod);echo ("<br/>");
			var_dump($initiatives_mkt);echo ("<br/>");
			var_dump($initiatives_hr);echo ("<br/>");
			var_dump($initiatives_det);echo ("<br/>");
			var_dump($market_researches);echo ("<br/>");
			var_dump($new->getDecisionBudget($game_id, $company_id, $round_number));echo ("<br/>");
			var_dump($money_actual);echo ("<br/>");
			var_dump($money_needed);echo ("<br/>");*/
			
			if($money_needed>$money_actual){
				$validate=0;
			}
			else {
				$validate=1;
			}
			return $validate;
		}
		
		// Cantidad de dinero necesaria para los Estudios de Mercado solicitados
		function getMarketResearchesDirectPayment($game_id,$round_number,$company_id){
			$market=new Model_DbTable_Decisions_MarketResearches();
			$param_marketresearches=new Model_DbTable_Games_Param_Markets_MarketResearches();
			//funcionando correctamente. Estudios de mercado solicitados y costes
			$marketresearches_solicited=$market->getMarketResearchesSolicited($game_id, $company_id, $round_number);
			$marketresearches_costs=$param_marketresearches->getMarketResearchesCosts($game_id);
			
			$research_number=1;
			$totalCost=0;
			
			$names[0]=array('value'=>1, 'descriptor'=>'channelResearch');
			$names[1]=array('value'=>2, 'descriptor'=>'pricesResearch');
			$names[2]=array('value'=>3, 'descriptor'=>'mktResearch');
			$names[3]=array('value'=>4, 'descriptor'=>'spectedResearch');
			$names[4]=array('value'=>5, 'descriptor'=>'accountResearch');
			
			while(isset($marketresearches_costs['marketResearch_number_'.$research_number])) {				
				$aux=$marketresearches_solicited[$names[$research_number-1]['descriptor']];
				$cost=$marketresearches_costs['marketResearch_number_'.$research_number];
				$totalCost=$totalCost+($aux*$cost);
				$research_number++;
			}
			return $totalCost;
		}
		//Devuelve el dinero necesario para llevar a cabo las iniciativas solicitadas
		function getInitiativesProductionCost($game_id, $company_id, $round_number){
			$initiatives=new Model_DbTable_Decisions_Initiatives();
			$initiatives_production=$initiatives->getInitiativesProduction($game_id, $company_id, $round_number);
			$param_initiatives=new Model_DbTable_Games_Param_Markets_Initiatives();
			$initiatives_pr_costs=$param_initiatives->getPrInitiativesCosts($game_id);
			$initiative_number=1;
			$totalCost=0;
			while(isset($initiatives_production['initiativeproduction_number_'.$initiative_number])) {
				$aux=$initiatives_production['initiativeproduction_number_'.$initiative_number];
				$cost=$initiatives_pr_costs['initiativeproduction_number_'.$initiative_number];
				$totalCost=$totalCost+($aux*$cost);
				$initiative_number++;
			}
			return $totalCost;
		}
		function getInitiativesMarketingCost($game_id, $company_id, $round_number){
			$initiatives=new Model_DbTable_Decisions_Initiatives();
			$initiatives_marketing=$initiatives->getInitiativesMarketing($game_id, $company_id, $round_number);
			$param_initiatives=new Model_DbTable_Games_Param_Markets_Initiatives();
			$initiatives_mk_costs=$param_initiatives->getMkInitiativesCosts($game_id);
			$initiative_number=1;
			$totalCost=0;
			while(isset($initiatives_marketing['initiativemarketing_number_'.$initiative_number])) {
				$aux=$initiatives_marketing['initiativemarketing_number_'.$initiative_number];
				$cost=$initiatives_mk_costs['initiativemarketing_number_'.$initiative_number];
				$totalCost=$totalCost+($aux*$cost);
				$initiative_number++;
			}
			return $totalCost;
		}
		function getInitiativesHumanResourcesCost($game_id, $company_id, $round_number){
			$initiatives=new Model_DbTable_Decisions_Initiatives();
			$initiatives_humanresources=$initiatives->getInitiativesHumanresources($game_id, $company_id, $round_number);
			$param_initiatives=new Model_DbTable_Games_Param_Markets_Initiatives();
			$initiatives_hr_costs=$param_initiatives->getHrInitiativesCosts($game_id);
			$initiative_number=1;
			$totalCost=0;
			while(isset($initiatives_humanresources['initiativehumanresources_number_'.$initiative_number])) {
				$aux=$initiatives_humanresources['initiativehumanresources_number_'.$initiative_number];
				$cost=$initiatives_hr_costs['initiativehumanresources_number_'.$initiative_number];
				$totalCost=$totalCost+($aux*$cost);
				$initiative_number++;
			}
			return $totalCost;
		}
		function getInitiativesDeteriorationCost($game_id, $company_id, $round_number){
			$initiatives=new Model_DbTable_Decisions_Initiatives();
			$initiatives_deterioration=$initiatives->getInitiativesDeterioration($game_id, $company_id, $round_number);
			$param_initiatives=new Model_DbTable_Games_Param_Markets_Initiatives();
			$initiatives_det_costs=$param_initiatives->getDetInitiativesCosts($game_id);
			$initiative_number=1;
			$totalCost=0;
			while(isset($initiatives_deterioration['factory_number_'.$initiative_number])) {
				$aux[$initiative_number]=$initiatives_deterioration['factory_number_'.$initiative_number];
				$cost=$initiatives_det_costs['initiativedeterioration_number_'.$initiative_number];
				$totalCost=$totalCost+($aux[$initiative_number]*$cost);
				$initiative_number++;
			}
			return $totalCost;
		}
		//Devuelve dinero necesario para realizar cada construcci—n
		function getConstructionCost($game_id, $company_id, $round_number){
			$production=new Model_DbTable_Decisions_Production();
			$factories=$production->getFactories($game_id, $company_id, $round_number);
			$factory_number=1;
			if($factories!=null){
				foreach ($factories as $counterfactories){
					$region[$factory_number]=$factories['factory_number_'.$factory_number];
					$factory_number++;
				}
				//var_dump($factories); die();
				foreach ($factories as $factory) {
					$aux_created=$this->getRoundFactoryCreated($game_id, $company_id);
					$round_created=$aux_created['factory_number_'.$factory];//['factory_number']];
					for ($round = 1; $round <= $round_number; $round++) {
						if(($round==$round_created)){
							//$factory_cost=$this->getProductionCost($game_id, $round, $region[$factory['factory_number']], 'fixed');
							$factory_cost=$this->getProductionCost($game_id, $round, $region[$factory], 'fixed');
						}
						else{
							$factory_cost=0;
						}
							//$cost['factory_number_'.$factory['factory_number']][$round]=$factory_cost;
							$cost['factory_number_'.$factory][$round]=$factory_cost;
					}
				}
				foreach ($factories as $factory) {
					//$result+=$cost['factory_number_'.$factory['factory_number']][$round_number];
					$result+=$cost['factory_number_'.$factory][$round_number];
				}
			}
			else {
				$result=0;
			}
			return $result;		
		}
		//Devuelve dinero necesario para realizar cada ampliacion
		function getExtensionCost($game_id, $company_id, $round_number){			
			$capacity=new Model_DbTable_Decisions_Pr_Capacity();
			$production=new Model_DbTable_Decisions_Production();
			$factories=$production->getFactories($game_id, $company_id, $round_number);
			$factory_number=1;
			if($factories!=null){
				foreach ($factories as $counterfactories){
					$region[$factory_number]=$factories['factory_number_'.$factory_number];
					$factory_number++;
				}
				$factory_number_aux=1;
				if($factory_number_aux<=$factory_number)
				foreach ($factories as $factory){
					$extension_factory=$capacity->getExtensionWasCreated($game_id, $company_id, $factory_number_aux);
					for ($round = 1; $round <= $round_number; $round++) {
						$round_created[$round]=$extension_factory['factory_number_'.$factory_number_aux]['round_number_created_'.$round];
						for ($round_aux = 1; $round_aux <= $round; $round_aux++) {
							if($round>=$round_created[$round_aux]){
								$extension[$round]=$extension_factory['factory_number_'.$factory_number_aux]['capacity_'.$round_aux];
							}
							else {
								$extension[$round_aux]=0;
							}
							$cost_aux=$this->getProductionCost($game_id, $round_number, $region[$factory_number_aux], 'fixed');
							$machines=$this->getOrganizationParam($game_id, 'machines');
							$cost['factory_number_'.$factory_number_aux][$round]=($extension[$round]*($cost_aux/$machines))*0.7;
						}
					}
					$factory_number_aux++;
				}
				foreach ($factories as $factory) {
					//$result+=$cost['factory_number_'.$factory['factory_number']][$round_number];
					$result+=$cost['factory_number_'.$factory][$round_number];
				}
			}
			else {
				$result=0;
			}
			return $result;			
		}
		
	}
?>