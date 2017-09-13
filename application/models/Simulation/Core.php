<?php
/*ConvendrÃ­a optimizar minimizando el nÃºmero de accesos a BD, cacheando en variables inicializadas en los init*/
	class Model_Simulation_Core{
		public $code;
		
		// game variables
		public $_games;		
		public $_game;
		public $_round;
		public $_companies;
		public $_products;
		public $_regions;
		public $_qualities;
		
		// decision containers
		public $_production;
		public $_suppliers;
		public $_humanResources;
		public $_initiatives;
		public $_finance;
		
		//public $_outcomes_registry;
		public $_outcomes_updating;
		public $_outcomes_production_units;
		public $_outcomes_production_capacity_data;
		public $_outcomes;
		public $_outcomes_sales;
		public $_outcomes_stocks_units;
		public $_outcomes_prev_stock_value;
		
		//Save data
		public $_outcomes_round_humanresources;
		public $_outcomes_round_deterioration; 
		public $_outcomes_round_shares;  
		
		//production variables		
		public $_production_available_time;
		public $_product_time_needed;
		public $_productionMessages;
		public $_production_new_products;
		
		//Shares
		public $_share_real;
		public $_share_model;
		
		function simulate($game, $round){
			$this->_game=$game;
			$this->_round=$round;
			//Comprobar existencia de datos completos.
			//inicializar
			$this->initSimulation();
			//producciÃ³n
			//VERO
			$this->setDistributionStock();
			//VERO

			$this->production();
			
			$this->demand();
			$this->production_new_Products();
			//VERO
			$this->investment();
			//VERO
			$this->stock();
			$this->costs();
			$this->balanceSheet();
			$this->saveData();
			$this->_outcomes_registry->register();
			$this->Charts();
			return $this->code;
			
		}
		function initSimulation(){
			$this->_games = new Model_DbTable_Games();

			$this->_trademedias[0]=array('trademedia_number'=>1, 'name'=>'Patrocinio');
			$this->_trademedias[1]=array('trademedia_number'=>2, 'name'=>'PromociÃ³n');
			
			//inicializaciÃ³n objetos de la simulaciÃ³n:
			$this->initProducts();
			$this->initCompanies();
			$this->initRegions();
			$this->initQualities();
			$this->initChannels();
			$this->initMedias();
			$this->initRound();
			
			//inicializaciÃ³n resultados
			$this->initOutcomes();
		}
		function initProducts(){
			$products=$this->_games->getProducts($this->_game['id']);
			foreach ($products as $product){
				$this->_products[]=new Model_Simulation_Product($this, 
																$this->_game['id'], 
																$this->_round['round_number'], 
																$product['product_number']);
			}
			//var_dump($this->_products);die();
		}
		function initCompanies(){
			$companies=$this->_games->getCompaniesInGame($this->_game['id']);
			foreach ($companies as $company){
				$this->_companies[]=new Model_Simulation_Company($this, 
																$this->_game['id'], 
																$company['id'],
																$this->_round['round_number']);
			}
			//var_dump($this->_companies);die();
		}
		function initRegions(){
			$regions=$this->_games->getRegions($this->_game['id']);	
			foreach ($regions as $region){
				$this->_regions[]=new Model_Simulation_Region($this,
															  $this->_game['id'], 
															  $this->_round['round_number'],
															  $region['region_number']);
			}
			//var_dump($this->_regions);die();
		}
		
		function initQualities(){
			$qualities=$this->_games->getQualityParams($this->_game['id']);
			foreach ($qualities as $quality){
				//var_dump($quality);die();
				$this->_qualities[]=new Model_Simulation_Quality($this, 
																$this->_game['id'], 
																$this->_round['round_number'], 
																$quality['quality_param_number'],
																$quality['product_number']);												
			}
			//var_dump($this->_qualities);die();
		}
		function initChannels(){
			$channels=$this->_games->getChannels($this->_game['id']);	
			foreach ($channels as $channel){
				$this->_channels[]=new Model_Simulation_Channel($this->_game['id'], 
															  $this->_round['round_number'],
															  $channel['channel_number']);
			}
		}
		function initMedias(){
			$medias=$this->_games->getMedia($this->_game['id']);	
			foreach ($medias as $media){
				$this->_medias[]=new Model_Simulation_Media($this->_game['id'], 
															  $this->_round['round_number'],
															  $media['media_number']);
			}
		}
		// ojo convendrÃ­a establecer sistema de registro de forma que:
		// si existe registro previo, se borra el registro y, hasta que no estÃ© todo grabado, no se introduce uno nuevo.
		// si no existe registro previo-> por si acaso se borra todo lo que coincida con el juego y la ronda (por si se interrumpiÃ³ una grabaciÃ³n previa
		function initOutcomes(){
			//die($this->_game['id']." - ".$this->_round['round_number']);
			$this->_outcomes_registry=new Model_DbTable_Outcomes();
			$this->_outcomes_registry->initOutcomes($this->_game['id'], $this->_round['round_number']);
			//die("Stop");
			$this->_outcomes_updating=$this->_outcomes_registry->exists();	
			//var_dump($this->_outcomes_registry);die();
			$this->_outcomes_registry->clean();
			//var_dump($this->_outcomes_registry);die();
			// tablas de producciÃ³n
			$this->_outcomes_production_units=new Model_DbTable_Outcomes_Pr_Units();
			$this->_outcomes_production_capacity_data=new Model_DbTable_Outcomes_Pr_CapacityData();
			$this->_outcomes_production_messages=new Model_DbTable_Outcomes_Pr_Messages();
			// tablas de demanda
			$this->_outcomes_sales=new Model_DbTable_Outcomes_De_Sales();
			//tablas de stocks
			$this->_outcomes_stocks_units=new Model_DbTable_Outcomes_St_Units();
			// tablas de costes
			$this->_outcomes_costs=new Model_DbTable_Outcomes_Co_Costs();
			$this->_outcomes_cashflow=new Model_DbTable_Outcomes_Co_Cashflow();
			//tablas de balances
			$this->_outcomes_balance_sheet=new Model_DbTable_Outcomes_Bs_BalanceSheet();
			//amortizaciones
			$this->_amortization_data=new Model_DbTable_Games_Evolution_Am_Amortization();
		}
		function initRound(){
			$round_number=$this->_round['round_number'];
			$next_round=$round_number+1;
			$numberOfRounds=$this->_games->getNumberOfRounds($this->_game['id']);
			if($next_round<=$numberOfRounds){
				$round_actual=$round_number;
				// PARAMETRIZACION
				// Tamano Mercado. OK
				foreach ($this->_products as $product){
					foreach ($this->_regions as $region){						
						$market_sizes_actual=$this->_games->getMarketSize($this->_game['id'], $round_actual, $product->getProductNumber(), $region->getRegionNumber());
						//echo ("MARKET SIZES ACTUAL : ".$market_sizes_actual."<br/>");
						$evolution_market_size_aux=$this->_games->getMarketSizeEvolution($this->_game['id'], $round_actual+1, $product->getProductNumber(), $region->getRegionNumber());
						//$evolution_market_size_aux=$product->getMarketsSizesEvolution($region->getRegionNumber(), $next_round);
						//echo ("EVOLUTION MARKET SIZE AUX :".$evolution_market_size_aux."<br/>");
						$evolution_market_size=1+($evolution_market_size_aux*0.01);
						//echo ("EVOLUTION MARKET SIZE :".$evolution_market_size."<br/>");
						$market_sizes_actual=$market_sizes_actual*$evolution_market_size;
						//echo ("MARKET SIZES ACTUAL : ".$market_sizes_actual."<br/>");
						$product->setMarketSizes($next_round, $region->getRegionNumber(), $market_sizes_actual);		
					}
				}		

				// PRODUCTION
				// MARKETING
				// Ideal_Price and Max_Price. OK
				foreach ($this->_products as $product){
					foreach ($this->_regions as $region){
						foreach ($this->_channels as $channel){
							$ideal_price_actual=$product->getProductIdealPrice($region->getRegionNumber(), $channel->getChannelNumber(),$round_actual);
							$max_price_actual=$product->getProductMaxPrice($region->getRegionNumber(), $channel->getChannelNumber(),$round_actual);
							$evolution_price_aux=$product->getPricesEvolution($channel->getChannelNumber(), $region->getRegionNumber(), $next_round);
							//echo ("EVOLUTION PRICE AUX : ".$evolution_price_aux."<br/>");
							$evolution_price=1+($evolution_price_aux*0.01);
							$ideal_price_actual=$ideal_price_actual*$evolution_price;
							$max_price_actual=$max_price_actual*$evolution_price;
							$product->setProductPrices($next_round, $region->getRegionNumber(), $channel->getChannelNumber(), $ideal_price_actual, $max_price_actual);
						}
					}
				}

				// Ideales de inversion en Publicidad
				foreach ($this->_products as $product){
					foreach ($this->_regions as $region){
						foreach ($this->_medias as $media){
						$advertising_intensity_actual=$this->_games->getMediaWeight($this->_game['id'], $round_actual, $product->getProductNumber(), $media->getMediaNumber(), $region->getRegionNumber());
						$evolution_advertising_intensity_aux=$this->_games->getMediaWeightEvolution($this->_game['id'], $round_actual+1, $product->getProductNumber(), $media->getMediaNumber(), $region->getRegionNumber());
						//$evolution_advertising_intensity_aux=$product->getAdvertisingIntensityEvolution($region->getRegionNumber(), $media->getMediaNumber(), $next_round);
						//echo ("EVOLUTION ADV. INTENS. AUX : ".$evolution_advertising_intensity_aux."<br/>");
						$evolution_advertising_intensity=1+($evolution_advertising_intensity_aux*0.01);
						$advertising_intensity_actual=$advertising_intensity_actual*$evolution_advertising_intensity;
						$product->setAdvertisingIntensity($next_round, $region->getRegionNumber(), $media->getMediaNumber(), $advertising_intensity_actual);	
						}
					}
				}
				// Ideales de inversion en TradeMKT
				foreach ($this->_products as $product){
					foreach ($this->_channels as $channel){
						foreach ($this->_trademedias as $trademedia){
						$tradeMkt_intensity_actual=$this->_games->getTradeMediaWeight($this->_game['id'], $round_actual, $product->getProductNumber(), $trademedia['trademedia_number'], $channel->getChannelNumber());
						//$evolution_tradeMkt_intensity_aux=$product->getTradeMktIntensityEvolution($region->getRegionNumber(), $trademedia['trademedia_number'], $next_round);
						$evolution_tradeMkt_intensity_aux=$this->_games->getTradeMediaWeightEvolution($this->_game['id'], $round_actual+1, $product->getProductNumber(), $trademedia['trademedia_number'], $channel->getChannelNumber()); //¿Channel en lugar de Region?
						//echo ("EVOLUTION TRDMKT. INTENS. AUX : ".$evolution_tradeMkt_intensity_aux."<br/>");
						$evolution_tradeMkt_intensity=1+($evolution_tradeMkt_intensity_aux*0.01);
						$tradeMkt_intensity_actual=$tradeMkt_intensity_actual*$evolution_tradeMkt_intensity;
						$product->setTradeMktIntensity($next_round, $region->getRegionNumber(), $trademedia['trademedia_number'], $tradeMkt_intensity_actual);
						}
					}
				}
				// HUMAN RESOURCES
				// FINANCES
				// INITIATIVES
				// MARKET RESEARCHES
				// I+D+i
			
				// COSTS
				// Costes de Produccion
				foreach ($this->_regions as $region){
					//pr_fixed_cost
					$product_number=0;	
					$production_fixed_cost_actual = $this->_games->getProductionCost($this->_game['id'], $round_actual, $region->getRegionNumber(), 'fixed', $product_number);
					$evolution_fixed_cost_aux = $this->_games->getProductionCostEvolution($this->_game['id'], $next_round, $region->getRegionNumber(), 'fixed', $product_number);
					$evolution_fixed_cost = 1+($evolution_fixed_cost_aux*0.01);
					$production_fixed_cost_actual = $evolution_fixed_cost * $production_fixed_cost_actual;
					$cost_type='fixed';
					$region->setProductionCosts($next_round, $cost_type, $product_number ,$production_fixed_cost_actual);
					foreach ($this->_products as $product){
						//pr_var_cost
						$production_var_cost_actual = $this->_games->getProductionCost($this->_game['id'], $round_actual, $region->getRegionNumber(), 'unit', $product->getProductNumber());
						$evolution_var_cost_aux = $this->_games->getProductionCostEvolution($this->_game['id'], $next_round, $region->getRegionNumber(), 'unit', $product->getProductNumber());
						//echo ("EVOLUTION VAR. COST AUX : ".$evolution_var_cost_aux."<br/>");
						$evolution_var_cost = 1+($evolution_var_cost_aux*0.01);
					 	$production_var_cost_actual = $evolution_var_cost * $production_var_cost_actual;
						$cost_type='unit';
					 	$product->setProductionCosts($next_round, $region->getRegionNumber(), $cost_type, $product->getProductNumber(), $production_var_cost_actual);
					}
				}
				// pr_distrib_cost
				foreach ($this->_regions as $region_source){
					foreach ($this->_regions as $region_destination){
						$distribution_cost_actual = $this->_games->getDistributionCost($this->_game['id'], $round_actual, $region_source->getRegionNumber(), $region_destination->getRegionNumber());
						$evolution_distribution_cost_aux = $this->_games->getDistributionCostEvolution($this->_game['id'], $next_round, $region_source->getRegionNumber(), $region_destination->getRegionNumber());
						//echo ("EVOLUTION DIST. COST AUX : ".$evolution_distribution_cost_aux."<br/>");
						$evolution_distribution_cost = 1+($evolution_distribution_cost_aux*0.01);
					 	$distribution_cost_actual = $evolution_distribution_cost * $distribution_cost_actual;
					 	$product->setDistributionCosts($next_round, $region_source->getRegionNumber(), $region_destination->getRegionNumber(), $distribution_cost_actual);
					}
				}
				// pr_rwa_material_cost
				foreach ($this->_products as $product){
					$rawMaterial_base_cost_actual = $this->_games->getRawMaterialCost($this->_game['id'], $round_actual, $product->getProductNumber(), 'base');
					$rawMaterial_increment_cost_actual = $this->_games->getRawMaterialCost($this->_game['id'], $round_actual, $product->getProductNumber(), 'increment_per_supplier');
					$evolution_rawMaterial_base_aux = $this->_games->getRawMaterialCostEvolution($this->_game['id'], $next_round, $product->getProductNumber(), 'evolution');
					//echo ("EVOLUTION RAWMAT BASE COST AUX : ".$evolution_rawMaterial_base_aux."<br/>");
					$evolution_rawMaterial_base = 1+($evolution_rawMaterial_base_aux*0.01);
					$rawMaterial_base_cost_actual = $evolution_rawMaterial_base * $rawMaterial_base_cost_actual;
					$product->setRawMaterialsCosts($next_round, $rawMaterial_base_cost_actual, $rawMaterial_increment_cost_actual);
				}
				// hr_staff_cost
				foreach ($this->_regions as $region){
					$hr_hiring_cost_actual = $this->_games->getHrStaffCost($this->_game['id'], $round_actual, $region->getRegionNumber(), 'hiring_cost');
					$hr_training_cost_actual = $this->_games->getHrStaffCost($this->_game['id'], $round_actual, $region->getRegionNumber(), 'training_cost');
					$hr_wages_cost_actual = $this->_games->getHrStaffCost($this->_game['id'], $round_actual, $region->getRegionNumber(), 'wages_cost');
					$evolution_hr_hiring_aux = $this->_games->getHrStaffCostEvolution($this->_game['id'], $next_round, $region->getRegionNumber(), 'hiring_cost_evolution');
					$evolution_hr_training_aux = $this->_games->getHrStaffCostEvolution($this->_game['id'], $next_round, $region->getRegionNumber(), 'training_cost_evolution');
					$evolution_hr_wages_aux = $this->_games->getHrStaffCostEvolution($this->_game['id'], $next_round, $region->getRegionNumber(), 'wages_cost_evolution');
					$evolution_hr_hiring = 1+($evolution_hr_hiring_aux*0.01);
					$evolution_hr_training = 1+($evolution_hr_training_aux*0.01);
					$evolution_hr_wages = 1+($evolution_hr_wages_aux*0.01);
					$hr_hiring_cost_actual = $evolution_hr_hiring * $hr_hiring_cost_actual;
					$hr_training_cost_actual = $evolution_hr_training * $hr_training_cost_actual;
					$hr_wages_cost_actual = $evolution_hr_wages * $hr_wages_cost_actual;
					$region->setHumanResourcesCosts($next_round, $hr_hiring_cost_actual, $hr_training_cost_actual, $hr_wages_cost_actual);					
				}
				// channels_cost
				foreach ($this->_regions as $region){
					foreach ($this->_channels as $channel){
						$channel_fixed_cost_actual = $this->_games->getMkChannelCost($this->_game['id'], $round_actual, $channel->getChannelNumber(), $region->getRegionNumber(), 'fixed_cost');
						$channel_fare_cost_actual = $this->_games->getMkChannelCost($this->_game['id'], $round_actual, $channel->getChannelNumber(), $region->getRegionNumber(), 'fare_cost');
						$evolution_channel_fixed_cost_aux = $this->_games->getMkChannelCostEvolution($this->_game['id'], $next_round, $channel->getChannelNumber(), $region->getRegionNumber(), 'evolution');
						//echo ("EVOLUTION CHANNEL FIXED COST AUX : ".$evolution_channel_fixed_cost_aux."<br/>");
						$evolution_channel_fixed_cost = 1+($evolution_channel_fixed_cost_aux*0.01);
					 	$channel_fixed_cost_actual = $evolution_channel_fixed_cost * $channel_fixed_cost_actual;
					 	$region->setChannelsCosts($next_round, $channel->getChannelNumber(), $channel_fixed_cost_actual, $channel_fare_cost_actual);
					}
				}	
			} //if
			//die();
		} //initRound()

		// OK
		//AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA//
		function production(){
			foreach ($this->_companies as $company){				
				$time_available=$company->getTimeAvailable();
				$time_needed=$company->getTimeNeeded();
				//var_dump($time_needed);
				if ($time_needed>$time_available){
					print_r('<br>'.$company->getId().' Capacidad Insuficiente:');
					print_r('<br>   -  Tiempo Necesario: '.$time_needed);
					print_r('<br>   -  Tiempo Disponible: '.$time_available);
					$this->production_notEnoughTime($company, $time_needed);
				}
				else{
					print_r('<br>'.$company->getId().' Capacidad Suficiente');
					print_r('<br>   -  Tiempo Necesario: '.$time_needed);
					print_r('<br>   -  Tiempo Disponible: '.$time_available);
					$this->production_enoughTime($company);
				}
				$this->production_save_messages($company);
				$this->production_save_capacity_data($company);
			}
		}

		//VERO
		//Función que setea en la tabla evolution los cambios en el stock y setea en la tabla distribution_stock los costes de dicha distribución
		function setDistributionStock(){
			$stock_distribution_units=new Model_DbTable_Decisions_St_Distribution();
			$distribution_param=new Model_DbTable_Games_Param_Pr_DistributionCosts();
			$stocks_evolution=new Model_DbTable_Games_Evolution_St_Stocks();
			$stock_final_units=new Model_DbTable_Decisions_St_StockFinal();
			foreach ($this->_companies as $company){
				foreach($this->_products as $product){
					foreach($this->_channels as $channelO){
						foreach($this->_regions as $regionO){
							for($round=2; $round<intval($this->_round['round_number']); $round++){
								$changes=false;
								$stockEvolution=$stocks_evolution->getStockClasified(intval($this->_game['id']), intval($company->getId()), intval($round), intval($product->getProductNumber()), intval($regionO->getRegionNumber()), intval($channelO->getChannelNumber()));
								$stock_units=$stock_final_units->getStockByMarket(intval($this->_game['id']), intval($this->_round['round_number']), intval($company->getId()),  intval($product->getProductNumber()), intval($regionO->getRegionNumber()), intval($channelO->getChannelNumber()));

								if($stockEvolution!='0' && $stockEvolution!=$stock_units){
									$changes=true;
									$round_number = $round;
								}
							}
							foreach($this->_channels as $channelD){
								foreach($this->_regions as $regionD){
									if($regionO<>$regionD){
										$distributionUnitCost=$this->_games->getDistributionCost($this->_game['id'], $this->_round['round_number'], $regionO->getRegionNumber(), $regionD->getRegionNumber());
										$stock_distribution_units->setCostDistribution($this->_game['id'], $this->_round['round_number'], $company->getId(), $product->getProductNumber(), $channelO->getChannelNumber(), $regionO->getRegionNumber(), $channelD->getChannelNumber(), $regionD->getRegionNumber(), $distributionUnitCost);
									}else{
										$stock_distribution_units->setCostDistribution($this->_game['id'], $this->_round['round_number'], $company->getId(), $product->getProductNumber(), $channelO->getChannelNumber(), $regionO->getRegionNumber(), $channelD->getChannelNumber(), $regionD->getRegionNumber(), 0.0);
									}
									if($changes){
										$stock_units=$stock_final_units->getStockByMarket(intval($this->_game['id']), intval($this->_round['round_number']), intval($company->getId()),  intval($product->getProductNumber()), intval($regionD->getRegionNumber()), intval($channelD->getChannelNumber()));
										$stocks_evolution->update(array('units'=>$stock_units), 'game_id = '.$this->_game['id'].
											   						 ' AND company_id = '.$company->getId().
											   						 ' AND round_number = '.$round_number.
																     ' AND product_number = '.$product->getProductNumber().
											   						 ' AND region_number = '.$regionD->getRegionNumber().
										       						 ' AND channel_number = '.$channelD->getChannelNumber());
									}
								}
							}
						}
					}
				}
			}
		}
		//VERO
		function production_save_messages($company){
			if ($this->_outcomes_updating){
				$this->_outcomes_production_messages->update(array('messages'=>$company->getProductionMessages()), 
													'game_id = '.$this->_game['id'].
													' AND company_id = '.$company->getId().
													' AND round_number = '.$this->_round['round_number']);
			}
			else{				
				$this->_outcomes_production_messages->insert(array('game_id'=>$this->_game['id'],
														   'company_id'=>$company->getId(),
														   'round_number'=>$this->_round['round_number'],
														   'messages'=>$company->getProductionMessages()));
			}
		}
		function production_save_units($company_id, $product_number, $channel_number, $region_number, $units){
			if ($this->_outcomes_updating){
				$this->_outcomes_production_units->update(array('units'=>$units), 
													'game_id = '.$this->_game['id'].
													' AND company_id = '.$company_id.
													' AND round_number = '.$this->_round['round_number'].
													' AND product_number = '.$product_number.
													' AND region_number = '.$region_number.
													' AND channel_number = '.$channel_number);
			}
			else{				
				$this->_outcomes_production_units->insert(array('game_id'=>$this->_game['id'],
														   'company_id'=>$company_id,
														   'round_number'=>$this->_round['round_number'],
														   'product_number'=>$product_number,
														   'region_number'=>$region_number,
														   'channel_number'=>$channel_number, 
														   'units'=>$units));
			}
		}
		function production_save_capacity_data($company){
			$needed=$company->getTimeNeeded();
			$capacity=$company->getTimeAvailable();
			//var_dump($this->_outcomes_updating);die();
			if ($this->_outcomes_updating){
				$this->_outcomes_production_capacity_data->update(array('capacity'=>$capacity, 'capacity_needed'=>$needed), 
													'game_id = '.$this->_game['id'].
													' AND round_number = '. $this->_round['round_number'].
													' AND company_id = '.$company->getId());
			}
			else{				
				$this->_outcomes_production_capacity_data->insert(array('game_id'=>$this->_game['id'],
														   'company_id'=>$company->getId(), 
														   'round_number'=>$this->_round['round_number'],
														   'capacity'=>$capacity, 
														   'capacity_needed'=>$needed));
			}
		}

		function production_enoughTime($company){
			foreach ($this->_products as $product){
				$availability=$this->_games->getProductAvailibility($this->_game['id'], $this->_round['round_number'],$company->getId(), $product->getProductNumber());
				if($availability==1){
					foreach ($this->_regions as $region){
						foreach ($this->_channels as $channel){
							$units=$company->getUnitsDecided($product->getProductNumber(), 
																	$channel->getChannelNumber(),
																	$region->getRegionNumber());
							$company->setUnitsAvailable($product->getProductNumber(), 
														 $channel->getChannelNumber(), 
														 $region->getRegionNumber(), $units);
							$this->production_save_units($company->getId(), 
														 $product->getProductNumber(), 
														 $channel->getChannelNumber(), 
														 $region->getRegionNumber(), $units);
						}
					}
				}
			}
		}
		
		function production_notEnoughTime($company, $total_time_needed){			
			foreach ($this->_products as $product){
				$availability=$this->_games->getProductAvailibility($this->_game['id'], $this->_round['round_number'],$company->getId(), $product->getProductNumber());
				if($availability==1){
				$units=$company->getUnitsProduced($product->getProductNumber());		
					// por regiones:
					foreach ($this->_regions as $region){
						$region_units_decided = $company->getUnitsDecided($product->getProductNumber(), 
																	   null, 
																	   $region->getRegionNumber());					
						$total_units_decided = $company->getUnitsDecided($product->getProductNumber());
						if ($total_units_decided==0){
							$region_percentage=0;
						}
						else{
							$region_percentage=$region_units_decided/$total_units_decided;
						}
						$region_units = round ($units*$region_percentage);					
						foreach ($this->_channels as $channel){
							$channel_units_decided = $company->getUnitsDecided($product->getProductNumber(), 
																	        $channel->getChannelNumber(), 
																			$region->getRegionNumber());
							if ($region_units_decided==0 || $channel_units_decided==0){
								$channel_percentage = 0;
							}
							else{
								$channel_percentage = $channel_units_decided/$region_units_decided;
							}
							$channel_units = round ($region_units * $channel_percentage);
							$company->setUnitsAvailable($product->getProductNumber(), 
														 $channel->getChannelNumber(), 
														 $region->getRegionNumber(), $channel_units);
							$this->production_save_units($company->getId(), 
														 $product->getProductNumber(), 
														 $channel->getChannelNumber(), 
														 $region->getRegionNumber(), $channel_units);
						}
					}
				}
			}
		}
		
		// Lanzamiento de nuevos productos
		function production_new_Products(){
			foreach ($this->_companies as $company){
				$cualification=$company->getCualificationLevel();
				$score=$company->getScoreNewProducts();
				foreach ($this->_products as $product){
					$initial_availability=$this->_games->getProductParamAvailability($this->_game['id'], $product->getProductNumber());
					if($initial_availability==1){
						$next_round=$this->_round['round_number']+1;
						$numberOfRounds=$this->_games->getNumberOfRounds($this->_game['id']);
						if($numberOfRounds>=$next_round){
							$this->_production_new_products[$company->getId()][$product->getProductNumber()]['success_probability']=$cualification*$score[$product->getProductNumber()];
							$success_probability=$this->_production_new_products[$company->getId()][$product->getProductNumber()]['success_probability'];
							$availability=$company->processNewReleases($product->getProductNumber(), $success_probability);
							echo("<br/> EQUIPO ".$company->getId()." SUCCESS PROBABILITY: ".$success_probability);
							echo("<br/> EQUIPO ".$company->getId()." AVAILABILITY: ".$availability);
							$this->production_save_success_probability($company->getId(), $this->_round['round_number'], $product->getProductNumber(), $success_probability, $availability);
						}
					}
				}
				//echo("<br/> EQUIPO ".$this->_company_id." SUCCESS PROBABILITY: ".VAR_DUMP($this->_production_new_products[$company->getId()]));
			}
		}
		//Salvamos probabilidad de exito de lanzamiento de nuevos productos
		function production_save_success_probability($company_id, $round_number, $product_number, $success_probability, $availability){
			$new=new Model_DbTable_Games_Evolution_Np_NewProducts();
			$next_round=$round_number+1;
			$boolean=$new->getNewReleasesRow($this->_game['id'], $company_id, $next_round, $product_number);
			if ($boolean==null){
				echo(" INSERT NEW RELEASES ");
				$new->insert(array('game_id'=>$this->_game['id'], 
								   'company_id'=>$company_id,
								   'round_number'=>$next_round,
								   'product_number'=>$product_number,
								   'success_probability'=>$success_probability,
								   'availability'=>$availability));
			}
			else {
				echo(" UPDATE NEW RELEASES");
				$new->update(array('success_probability'=>$success_probability, 'availability'=>$availability), 'game_id = '.$this->_game['id'].
																				   			 				    ' AND company_id = '.$company_id.
																				   			   				    ' AND round_number = '.$next_round.
																							   				    ' AND product_number = '.$product_number);
			}
		}
		
		//Motor de demanda
		function demand(){
			foreach ($this->_regions as $region){
				foreach ($this->_products as $product){
					foreach ($this->_channels as $channel){
						$prices=array();
						$qualities=array();
						$any_availability=0;
						foreach ($this->_companies as $company){
							$availability=$this->_games->getProductAvailibility($this->_game['id'], $this->_round['round_number'],$company->getId(), $product->getProductNumber());
							var_dump("Entro en availabilit==1");
							var_dump($company->getId());
							if($availability==1){
								var_dump("Ya Entro en availabilit==1");
								var_dump($company->getPrice($product->getProductNumber(), $channel->getChannelNumber(), $region->getRegionNumber()));
								var_dump($company->getUnitsAvailable($product->getProductNumber(), $channel->getChannelNumber(), $region->getRegionNumber()));
								if ($company->getPrice($product->getProductNumber(), $channel->getChannelNumber(), $region->getRegionNumber())!=0 &&
									$company->getUnitsAvailable($product->getProductNumber(), $channel->getChannelNumber(), $region->getRegionNumber())!=0){
										$prices[]=$company->getPrice($product->getProductNumber(), $channel->getChannelNumber(), $region->getRegionNumber());
										//VERO
										//$qualities[]=$company->getProductQuality($product->getProductNumber());	
										$qualities[]=$company->getProductQualityFunctionality($product->getProductNumber());	
										//VERO
										$any_availability++;							 								
								}
							}
						}

						if($any_availability!=0){	
							$this->_avg_price=array_sum($prices)/count($prices);
							$this->_max_price=max($prices);
							$this->_avg_quality=array_sum($qualities)/count($qualities);
							$this->_max_quality=max($qualities);
							echo("<br/> EQUIPO ".$company->getId()." QUALITY MAX (when any_availability!=0) ".$this->_max_quality);
							$this->_first_approx_shares[$product->getProductNumber()]
												[$region->getRegionNumber()]
												[$channel->getChannelNumber()]=$this->demand_first_approx_shares($region, $product, $channel);
			
							$this->_second_approx_shares[$product->getProductNumber()]
												 [$region->getRegionNumber()]
												 [$channel->getChannelNumber()]=$this->demand_second_approx_shares($region, $product, $channel);
								
							$this->_sales[$product->getProductNumber()]
										 [$region->getRegionNumber()]
										 [$channel->getChannelNumber()]=$this->demand_sales($region, $product, $channel);
						} else {
								$this->_sales[$product->getProductNumber()]
										 [$region->getRegionNumber()]
										 [$channel->getChannelNumber()]=0;							
						}
					}
				}
			}
			$this->demand_save_sales();
		}
		
		//primera aproximaciÃ³n 
		function demand_first_approx_shares($region, $product, $channel){
			$price_score=array();
			$ideal_price=$product->getProductIdealPrice($region->getRegionNumber(), $channel->getChannelNumber(),$this->_round['round_number']);			
			$h=$ideal_price;			
			//var_dump($h); die();
			$k=10*$h;
			$p=(-$h*$h)/(4*$k);
			$max_price=$product->getProductMaxPrice($region->getRegionNumber(), $channel->getChannelNumber(),$this->_round['round_number']);
			foreach ($this->_companies as $company){
			//precios - valoraciÃ³n parabÃ³lica de precios: (x-h)(x-h)=4p(y-k);
				$total_score[$company->getId()]=0;
				$price=$company->getPrice($product->getProductNumber(), $channel->getChannelNumber(), $region->getRegionNumber());
				//metemos el efecto de las iniciativas de marketing, disminuyen el precio percibido
				$price_perceived[$company->getId()]=$price*($company->getInitiativesMarketing($this->_round['round_number']));
				$units_available=$company->getUnitsAvailable($product->getProductNumber(), $channel->getChannelNumber(), $region->getRegionNumber());
				$price_score[$company->getId()]=0;
				/*echo("<br/> ");
				echo("<br/> EQUIPO ".$company->getId()." PRICE PERCIVED ".$price_perceived);
				echo("<br/> EQUIPO ".$company->getId()." IDEAL PRICE ".$ideal_price);
				echo("<br/> EQUIPO ".$company->getId()." PRICE DIFFERENCE ".($price_perceived-$ideal_price));
				echo("<br/> EQUIPO ".$company->getId()." H ".$h);
				echo("<br/> EQUIPO ".$company->getId()." K=10*H : ".$k);
				echo("<br/> EQUIPO ".$company->getId()." P= -(H^2)/(4*K) : ".$p);
				echo("<br/> EQUIPO ".$company->getId()." (PRICE PRECIVED - IDEAL PRICE)^2 / 4*P : ".(($price_perceived-$ideal_price)*($price_perceived-$ideal_price)/(4*$p)));
				echo("<br/> EQUIPO ".$company->getId()." PRICE VALUE= K + (PRICE PRECIVED - IDEAL PRICE)^2 / 4*P : ".($k+($price_perceived-$ideal_price)*($price_perceived-$ideal_price)/(4*$p)));*/
				if ($price_perceived[$company->getId()]>0){				
					if ($price_perceived[$company->getId()]<$max_price){						
						if ($units_available>0){
							$price_value=$k+($price_perceived[$company->getId()]-$ideal_price)*
											($price_perceived[$company->getId()]-$ideal_price)/(4*$p);
							$price_score[$company->getId()]=$price_value/$price_perceived[$company->getId()];
							//echo("<br/> EQUIPO ".$company->getId()." PRICE SCORE 1 ".$price_score/20);
						}
					}
				}
			}
			
			// Cogemos la mayor puntuacion del precio de entre todos los equipos, y normalizamos respecto a ella.
			$max=0;
			foreach ($this->_companies as $company){	
			 	$max=max($price_score[$company->getId()],$max);				
			}	
			foreach ($this->_companies as $company){
				$price_score[$company->getId()]=$price_score[$company->getId()]/$max;		
				if ($price_perceived[$company->getId()]>$this->_avg_price){
					if ($price_perceived[$company->getId()]>$ideal_price){
						//valor absoluto porque si el termino sale negativo se sumaria el error
						/*echo("<br/> EQUIPO ".$company->getId()." PRICE PERCIVED ".$price_perceived[$company->getId()]);
						echo("<br/> EQUIPO ".$company->getId()." IDEAL PRICE ".$ideal_price);
						echo("<br/> EQUIPO ".$company->getId()." PRICE DIFFERENCE ".($price_perceived[$company->getId()]-$ideal_price));
						echo("<br/> EQUIPO ".$company->getId()." PRICE MAX DIFFERENCE ".($max_price-$ideal_price));
						echo("<br/> EQUIPO ".$company->getId()." PRICE MAX ".$max_price);
						echo("<br/> DATOS: ".(($price_perceived[$company->getId()]-$ideal_price)/($max_price-$ideal_price)));*/
						$price_score[$company->getId()]-=$price_score[$company->getId()]*(($price_perceived[$company->getId()]-$ideal_price)/($max_price-$ideal_price));
					}
				}
				if ($price_score[$company->getId()]<0){
					$price_score[$company->getId()]=0;
				}
				$total_score[$company->getId()]+=$price_score[$company->getId()]*0.01*$product->getPriceWeight();
				$this->_price_score[$company->getId()]=$price_score[$company->getId()];
			
				echo("<br/> ");
				echo("<br/> MARKET: PRODUCT ".$product->getProductNumber()." REGION ".$region->getRegionNumber()." CHANNEL ".$channel->getChannelNumber());
				echo("<br/> EQUIPO ".$company->getId()." PRICE DECIDED ".$price_perceived[$company->getId()]);
				echo("<br/> EQUIPO ".$company->getId()." PRICE SCORE ".$price_score[$company->getId()]);
				
				// Publicidad - comparación con el ideal
				foreach ($this->_medias as $media){
					$advertising_score=1/($this->_games->getNumberOfMedia($this->_game['id']));
					$advertPercentage=$company->getAdvertisingPercentage($product->getProductNumber(), $region->getRegionNumber(), $media->getMediaNumber());
					$advertProductDistribution=$company->getAdvertisingBudgetDistribution($product->getProductNumber());
					$advertBudget=$company->getAdvertisingBudget();
					$advert=$advertPercentage*0.01*$advertBudget*$advertProductDistribution;
					$idealAdvert=$product->getProductIdealAdvertising($region->getRegionNumber(), $media->getMediaNumber());
					if ($advert<$idealAdvert){
						$advertising_score=($advert/$idealAdvert)/($this->_games->getNumberOfMedia($this->_game['id']));
					}
					echo("<br/> EQUIPO ".$company->getId()." MEDIA NUMBER ".$media->getMediaNumber()." ADVERTISING PERCENTAGE ".$advertPercentage);
					echo("<br/> EQUIPO ".$company->getId()." MEDIA NUMBER ".$media->getMediaNumber()." ADVERTISING DISTRIBUTION ".$advertProductDistribution);
					echo("<br/> EQUIPO ".$company->getId()." MEDIA NUMBER ".$media->getMediaNumber()." ADVERTISING BUDGET ".$advertBudget);
					echo("<br/> EQUIPO ".$company->getId()." MEDIA NUMBER ".$media->getMediaNumber()." ADVERTISING DECIDED ".$advert);
					echo("<br/> EQUIPO ".$company->getId()." MEDIA NUMBER ".$media->getMediaNumber()." ADVERTISING IDEAL ".$idealAdvert);
					echo("<br/> EQUIPO ".$company->getId()." MEDIA NUMBER ".$media->getMediaNumber()." ADVERTISING SCORE ".$advertising_score);	
					$total_score[$company->getId()]+=$advertising_score*0.01*$product->getMediaWeight();
				}
			// Trade Mkt - comparación con el ideal
				foreach ($this->_trademedias as $trademedia){
					$trademkt_score=0.5;
					$tradePercentage=$company->getTradeMktPercentage($product->getProductNumber(), $channel->getChannelNumber(), $trademedia['trademedia_number']);
					$tradeProductDistribution=$company->getTradeMktBudgetDistribution($product->getProductNumber());
					$tradeBudget=$company->getTradeMktBudget();
					$trade=$tradePercentage*$tradeBudget*$tradeProductDistribution*0.01;
					$idealTrade=$product->getProductIdealTradeMkt($channel->getChannelNumber(), $trademedia['trademedia_number']);
					if ($trade<$idealTrade){
						$trademkt_score=0.5*($trade/$idealTrade);
					}
					echo("<br/> EQUIPO ".$company->getId()." TRADE NUMBER ".$trademedia['trademedia_number']." TRADE MKT PERCENTAGE ".$tradePercentage);
					echo("<br/> EQUIPO ".$company->getId()." TRADE NUMBER ".$trademedia['trademedia_number']." TRADE MKT BUDGET ".$tradeBudget);
					echo("<br/> EQUIPO ".$company->getId()." TRADE NUMBER ".$trademedia['trademedia_number']." TRADE MKT DISTRIBUTION ".$tradeProductDistribution);
					echo("<br/> EQUIPO ".$company->getId()." TRADE NUMBER ".$trademedia['trademedia_number']." TRADE MKT DECIDED ".$trade);
					echo("<br/> EQUIPO ".$company->getId()." TRADE NUMBER ".$trademedia['trademedia_number']." TRADE MKT IDEAL ".$idealTrade);
					echo("<br/> EQUIPO ".$company->getId()." TRADE NUMBER ".$trademedia['trademedia_number']." TRADE MKT SCORE ".$trademkt_score);
					
					$total_score[$company->getId()]+=$trademkt_score*0.01*$product->getTradeMediaWeight();
				}			
			// Calidad: recta calidad y=mx+n calidades superiores a la media.
			// Calidad: parábola y=a(x^2) calidades inferiores a la media.
				$m=0;
				$n=0;
				//VERO
				//$quality=$company->getProductQuality($product->getProductNumber());
				$quality=$company->getProductQualityFunctionality($product->getProductNumber());
				//VERO
				var_dump("Maxima y media");
				var_dump($this->_max_quality);
				var_dump($this->_avg_quality);
				if ($quality == $this->_max_quality){
					$n=1;
					$qualityScore=$m*($quality+$functionalities)+$n;
				}
				else{
					if ($quality >= $this->_avg_quality) {
						$m=(0.5/(($this->_max_quality)-($this->_avg_quality)));
						$n=0.5*(1-($this->_avg_quality/($this->_max_quality-$this->_avg_quality)));
						$qualityScore=$m*$quality+$n;
					}
					else{
						if ($quality<$this->_avg_quality){
							$a=0.5/(($this->_avg_quality)*($this->_avg_quality));
							$qualityScore=$quality*$quality*$a;
						}
					}
				}
				
				$total_score[$company->getId()]+=$qualityScore*0.01*$product->getQualityWeight();
				echo("<br/> EQUIPO ".$company->getId()." QUALITY DECIDED ".$quality);
				echo("<br/> EQUIPO ".$company->getId()." QUALITY SCORE ".$qualityScore);

				if (($company->getUnitsAvailable($product->getProductNumber(), $channel->getChannelNumber(), $region->getRegionNumber()==0))){
					$total_score[$company->getId()]=0;
				}
				echo("<br/> EQUIPO ".$company->getId()." TOTAL SCORE ".$total_score[$company->getId()]);
			}
			
			echo("<br/> ");
			echo("<br/> MARKET: PRODUCT ".$product->getProductNumber()." REGION ".$region->getRegionNumber()." CHANNEL ".$channel->getChannelNumber());
			//echo ("<br/> ARRAY ".($total_score[$company->getId()]/array_sum($total_score)));
			//$prod_availability=$this->_games->getProductsAvailibilityBySomeone($this->_game['id'], $this->_round['round_number']);
			//$prevrnd_prod_availability=$this->_games->getProductsAvailibilityBySomeone($this->_game['id'], $this->_round['round_number']-1);
			//echo("¿Lo tiene alguien?: ");var_dump($prod_availability);echo("<br>");
			//echo("¿Lo tenía alguien?: ");var_dump($prevrnd_prod_availability);echo("<br>");

			foreach($this->_companies as $company){
				if (array_sum($total_score)>0){
					$share[$company->getId()]=$total_score[$company->getId()]/array_sum($total_score);
				}
				else{
					$share[$company->getId()]=0;
				}
				$coef_fidelity=0.01*$this->_games->getFidelity($this->_game['id'], $product->getProductNumber(),
														  	   $channel->getChannelNumber(), $region->getRegionNumber());
				if ($this->_round['round_number'] ==1){
					$share_total[$company->getId()]=$share[$company->getId()];
				} else {
					//OJO: LA SIGUIENTE LÍNEA ES VÁLIDA SÓLO CON MÁS DE 4 PRODUCTOS
					
					//
					// ¡HAY QUE SACAR BIEN LA CONDICIÓN!
					//
					
					//if (($this->_round['round_number']==4)&&($product->getProductNumber()>=4)) { //((($this->_games->getProductsAvailibilityBySomeone($this->_game['id'],
					if (($this->_round['round_number']==4)&&($product->getProductNumber()==3)) {
						//$this->_round['round_number']))==1)&&(($this->_games->getProductsAvailibilityBySomeone($this->_game['id'], $this->_round['round_number']-1))==0)) {
						$share_total[$company->getId()]=$share[$company->getId()];
					} elseif (($this->_round['round_number']==5)&&($product->getProductNumber()==4)) {
						//$this->_round['round_number']))==1)&&(($this->_games->getProductsAvailibilityBySomeone($this->_game['id'], $this->_round['round_number']-1))==0)) {
						$share_total[$company->getId()]=$share[$company->getId()];		
					} else {
						$ms=new Model_DbTable_Outcomes_Rd_MarketShares();
						$past_share=$ms->getPastShare($this->_game['id'], $company->getId(), $this->_round['round_number'],
									$product->getProductNumber(), $region->getRegionNumber(), $channel->getChannelNumber());
						$share_total[$company->getId()]=((1-$coef_fidelity)*$share[$company->getId()])+($coef_fidelity*$past_share);
					}
				}
				$shares_sum+=$share_total[$company->getId()];
				echo("<br/> EQUIPO ".$company->getId()." CUOTA ".$share_total[$company->getId()]);
				$this->_share_model[$company->getId()][$product->getProductNumber()] 
													  [$region->getRegionNumber()]
													  [$channel->getChannelNumber()]=$share_total[$company->getId()];
			}
			echo("<br/> COMPROBACION SUMA CUOTAS ".$shares_sum);
			echo("<br/> ");
			return $share_total;
		}
		
		
		function demand_second_approx_shares($region, $product, $channel){	
			$ideal_price=$product->getProductIdealPrice($region->getRegionNumber(), $channel->getChannelNumber(), $this->_round['round_number']);
			$channel_weight=$this->_games->getChannelWeight($this->_game['id'],
														$this->_round['round_number'], 
														$product->getProductNumber(), 
														$channel->getChannelNumber(), 
														$region->getRegionNumber())*0.01;
			$market_size=$this->_games->getMarketSize($this->_game['id'],
														$this->_round['round_number'], 
														$product->getProductNumber(), 
														$region->getRegionNumber());
			$channel_size=$market_size*$channel_weight;			
			foreach ($this->_companies as $company){
				$sales=$this->_first_approx_shares[$product->getProductNumber()]
												[$region->getRegionNumber()]
												[$channel->getChannelNumber()]
												[$company->getId()]*$channel_size;						
				$incomes[$company->getId()]=$sales*$company->getPrice($product->getProductNumber(), 
																	  $channel->getChannelNumber(), 
																	  $region->getRegionNumber());
			}
			if (array_sum($incomes)>0){//Si al menos uno vende
			$corrections_sum=0;
			$positive_corrections_sum=0;
			$positive_corrections_scores_sum=0;
			$negative_corrections_sum=0;
			$negative_corrections_scores_sum=0;			
			foreach ($this->_companies as $company){					
				$company_percentage[$company->getId()]=0;
				$price=$company->getPrice($product->getProductNumber(), $channel->getChannelNumber(), $region->getRegionNumber());
				$units_available=$company->getUnitsAvailable($product->getProductNumber(), $channel->getChannelNumber(), $region->getRegionNumber());
				if ($price>0 && $units_available>0){					
					$company_percentage[$company->getId()]=$incomes[$company->getId()]/array_sum($incomes);

					$price_deviation=($price-$ideal_price)/$ideal_price;

					$correction_coef=0.5-($product->getPriceWeight())/100;
					$initial_correction[$company->getId()]=$correction_coef*$price_deviation;												
					if ($initial_correction[$company->getId()]>0){
						$positive_corrections_sum+=abs($initial_correction[$company->getId()]);
						$positive_corrections_scores_sum+=$this->_price_score[$company->getId()];
						
					}
					else{							
						$negative_corrections_sum+=abs($initial_correction[$company->getId()]);
						$negative_corrections_scores_sum+=$this->_price_score[$company->getId()];
					}
				}
				else{
					$initial_correction[$company->getId()]=0;					
				}
			}
			$corrections_sum=$positive_corrections_sum-$negative_corrections_sum;
			}			
			foreach ($this->_companies as $company){				
				if (array_sum($incomes)>0){//Si al menos uno vende
					$new_sales[$company->getId()]=0;
					$price=$company->getPrice($product->getProductNumber(), $channel->getChannelNumber(), $region->getRegionNumber());
					if ($price>0){				
						$zero_sum_correction=$initial_correction[$company->getId()];						
						
						if ($corrections_sum<0){					
							if ($positive_corrections_scores_sum>0){	
								if ($zero_sum_correction>0){
									if ($positive_corrections_scores_sum==0){
										$positive_corrections_scores_sum=1;
									}
									$zero_sum_correction=$zero_sum_correction-
														$corrections_sum*
														$this->_price_score[$company->getId()]/
														$positive_corrections_scores_sum;
								}
							}
						}
						else {
							if ($negative_corrections_scores_sum>0){
								if ($zero_sum_correction<0){
									$zero_sum_correction=$zero_sum_correction+
														$corrections_sum*
														$this->_price_score[$company->getId()]/$negative_corrections_scores_sum;
								}
							}
						}
						// correcciÃ³n de cuotas de ingresos
						$company_percentage[$company->getId()]+=$zero_sum_correction;
						$new_incomes=array_sum($incomes)*$company_percentage[$company->getId()];
					
						$new_sales[$company->getId()]=$new_incomes / $price;
						
						if ($new_sales[$company->getId()]<0){ 
							$new_sales[$company->getId()]=0;
						}						
					}
					
				}
				else{
					$new_sales[$company->getId()]=0;
				}
			}
			$total_sales=array_sum($new_sales);
			foreach ($this->_companies as $company){
				if ($total_sales>0){
					$share[$company->getId()]=$new_sales[$company->getId()]/$total_sales;
				}
				else{
					$share[$company->getId()]=0;
				}				
			}
			
			return $share;											
		}
		function demand_sales($region, $product, $channel){			
			$channel_weight=$this->_games->getChannelWeight($this->_game['id'],
														$this->_round['round_number'], 
														$product->getProductNumber(), 
														$channel->getChannelNumber(), 
														$region->getRegionNumber())*0.01;
			$market_size=$this->_games->getMarketSize($this->_game['id'],
														$this->_round['round_number'], 
														$product->getProductNumber(), 
														$region->getRegionNumber());			
			//var_dump($channel_weight);die();
			$channel_size=$market_size*$channel_weight;
			//echo("Channel ".$channel['channel_number']." producto ".$product->getProductNumber()." region ".$region->getRegionNumber()." = ".$channel_size."<br/>");			
			foreach ($this->_companies as $company){				
				$units_available=$company->getUnitsAvailable($product->getProductNumber(), 
															$channel->getChannelNumber(), 
															$region->getRegionNumber());
				//var_dump($product->getPriceWeight());
				if (($product->getPriceWeight())*0.01>0.5){
					$share=$this->_second_approx_shares[$product->getProductNumber()]
										[$region->getRegionNumber()]
										[$channel->getChannelNumber()]
										[$company->getId()];
				}
				else{
					$share=$this->_first_approx_shares[$product->getProductNumber()]
										[$region->getRegionNumber()]
										[$channel->getChannelNumber()]
										[$company->getId()];
				}
				$demanded_units=floor($channel_size*$share);
				/*echo("<br/> ");
				echo("<br/> tamano ");
				var_dump($channel_size);
				echo("<br/> first aprox");
				var_dump($this->_first_approx_shares[$product->getProductNumber()][$region->getRegionNumber()][$channel->getChannelNumber()][$company->getId()]);
				echo("<br/> share");
				var_dump($share);
				echo("<br/> ");*/
				echo("<br/> ");
				echo("<br/> EQUIPO ".$company->getId()." VENTAS INICIALES (CUOTA*MARKET_SIZE) ".$demanded_units);
				$total_demanded_units+=$demanded_units;
				//echo("Demanded units company ".$company->getId()." canal ".$channel['channel_number']." producto ".$product->getProductNumber()." region ".$region->getRegionNumber()." = ".$demanded_units."<br/>");			

				if ($demanded_units<$units_available){
					$sales[$company->getId()]=$demanded_units;
					$units_available-=$sales[$company->getId()];
					$sobrante[$company->getId()]=0;
				}
				else{
					$sales[$company->getId()]=$units_available;
					//$sobrante[$company->getId()]=$demanded_units-$units_available;//TamaÃ±o Pseudomercado
					$sobrante[$company->getId()]=$demanded_units-$units_available;//min(($demanded_units-$units_available),floor($demanded_units * (1-$coef_fidelidad)));
					echo("<br/> EQUIPO ".$company->getId()." EXCEDENTE INICIAL ".$sobrante[$company->getId()]);
					$units_available=0;
					}	
					$units_disponibles[$company->getId()]=$units_available;
				}
				echo("<br/> ");
				echo("<br/>  VENTAS INICIALES (TOTALES) ".$total_demanded_units);
				echo("<br/> TAMAÃ±O MERCADO DEL CANAL ".$channel_size);
				$sobranteTotal=array_sum($sobrante);
				echo("<br/> ");
				echo("<br/> EXCEDENTE TOTAL ".$sobranteTotal);
				//Booleano para salir del bucle en caso de no haber asignacion de unidades, cuando redondeamos con el 'floor' podemos entrar en un bucle infinito si $sobrante_total fuese 1.
				$boolean=1;
				//VAR_DUMP($units_disponibles);
			while($sobranteTotal>0 && $units_disponibles>0 && $boolean>0){
				//DIE();
				$boolean=0;
				//Normalizamos las cuotas entre los equipos que intervengan en el re-reparto
				$normalized_share=0;
				foreach($this->_companies as $company){
					if($units_disponibles[$company->getId()]>0){
						if (($product->getPriceWeight())*0.01>0.5){
							$second_share[$company->getId()]=$this->_second_approx_shares[$product->getProductNumber()]
											[$region->getRegionNumber()]
											[$channel->getChannelNumber()]
											[$company->getId()];
						}
						else{
							$second_share[$company->getId()]=$this->_first_approx_shares[$product->getProductNumber()]
											[$region->getRegionNumber()]
											[$channel->getChannelNumber()]
											[$company->getId()];
						}
						//normalizacion cuotas
						$normalized_share+=$second_share[$company->getId()];
					}
				}
				echo("<br/> SUMA CUOTAS NORMALIZADA ".$normalized_share);
				//La cuota normalizada debe aplicarse sobre el total a repartir
				$aux_sobrante=$sobranteTotal;
			foreach($this->_companies as $company){
				$units_available=$units_disponibles[$company->getId()];
				echo("<br/> ");
				echo("<br/> EQUIPO ".$company->getId()." UNIDADES DISPONIBLES ".$units_available);
					if ($units_available>0){
					//Cuota normalizada
					echo("<br/> EQUIPO ".$company->getId()." CUOTA ORIGINAL ".$second_share[$company->getId()]);
					$aux_share=$second_share[$company->getId()]/$normalized_share;
					echo("<br/> EQUIPO ".$company->getId()." CUOTA NORMALIZADA ".$aux_share);
					$extra=floor($aux_share*$aux_sobrante);
					//Si hay algun $extra mayor que cero, $boolean tambien lo sera
					$boolean=$boolean+$extra;
					echo("<br/> EQUIPO ".$company->getId()." VENTAS RE-REPARTIDAS PARA UN EQUIPO ".$extra);
					if($extra>0){
					/*echo("<br/> Equipo ".$company->getId());
					echo("<br/> producto ".$product->getProductNumber()." cuota ".$share);
					echo("<br/> producto ".$product->getProductNumber()." Disponibles ".$units_available);
					echo("<br/> producto ".$product->getProductNumber()." sobrante ".$sobranteTotal);
					echo("<br/> producto ".$product->getProductNumber()." extra ".$extra);
						*/if ($extra<$units_available){
							$sales[$company->getId()]+=$extra;
							$units_disponibles[$company->getId()]=$units_available-$extra;
							$sobranteTotal=$sobranteTotal-$extra;
							echo("<br/> EQUIPO ".$company->getId()." VENTAS RE-REPARTO PASO 1: ".$sales[$company->getId()]);
							echo("<br/> MERCADO SOBRANTE RE-REPARTO PASO 2: ".$sobranteTotal);
							/*echo("<br/> Equipo ".$company->getId());
							echo("<br/> producto ".$product->getProductNumber()." cuota2 ".$share);
							echo("<br/> producto ".$product->getProductNumber()." Disponibles2 ".$units_available);
							echo("<br/> producto ".$product->getProductNumber()." sobrante2 ".$sobranteTotal);
							echo("<br/> producto ".$product->getProductNumber()." extra2 ".$extra);
							*/}
							else{
							$sales[$company->getId()]+=$units_available;
							$sobranteTotal=$sobranteTotal-$units_available;
							echo("<br/> EQUIPO ".$company->getId()." VENTAS RE-REPARTO PASO 2: ".$sales[$company->getId()]);
							echo("<br/> MERCADO SOBRANTE RE-REPARTO PASO 2: ".$sobranteTotal);
							/*echo("<br/> Equipo ".$company->getId());
							echo("<br/> producto ".$product->getProductNumber()." cuota3 ".$share);
							echo("<br/> producto ".$product->getProductNumber()." Disponibles3 ".$units_available);
							echo("<br/> producto ".$product->getProductNumber()." sobrante3 ".$sobranteTotal);
							echo("<br/> producto ".$product->getProductNumber()." extra3 ".$extra);
							*/
							$units_disponibles[$company->getId()]=0;
							}						
						}
						else{
							break;
						}
					}
					echo("<br/> ");	
					echo("<br/> EQUIPO ".$company->getId()." VENTAS FINALES (TENIENDO EN CUENTA UNIDADES DISPONIBLES) ".$sales[$company->getId()]);
							
				}
					
					$units_disponibles[$company->getId()]=$units_disponibles[$company->getId()];
					echo("<br/> ");
			}
			$ventas_acumuladas=0;
			foreach($this->_companies as $company){
				$ventas_acumuladas+=$sales[$company->getId()];
			}
			foreach($this->_companies as $company){
				$share_final[$company->getId()]=($sales[$company->getId()]/$ventas_acumuladas);
				echo("<br/> EQUIPO ".$company->getId()." CUOTA FINAL ".$share_final[$company->getId()]);
				$acumulated_share+=$share_final[$company->getId()];
				$this->_share_real[$company->getId()][$product->getProductNumber()]
											 		 [$region->getRegionNumber()]
											 		 [$channel->getChannelNumber()]=$share_final[$company->getId()];
			}
			echo("<br/> CUOTA ACUMULADA ".$acumulated_share);
			echo("<br/> VENTAS ACUMULADAS ".$ventas_acumuladas);
			echo("<br/> TAMA&Ntilde;O MERCADO DEL CANAL ".$channel_size);

			return $sales;
		}

		function demand_save_sales(){
			if ($this->_outcomes_updating){
				foreach ($this->_regions as $region){
					foreach ($this->_products as $product){
						foreach ($this->_channels as $channel){
							foreach ($this->_companies as $company){
								$availability=$this->_games->getProductAvailibility($this->_game['id'], $this->_round['round_number'],$company->getId(), $product->getProductNumber());
								if($availability==1){
									$units=$this->_sales[$product->getProductNumber()]
												 		[$region->getRegionNumber()]
												 		[$channel->getChannelNumber()]
														[$company->getId()];
									$price=$company->getPrice($product->getProductNumber(), $channel->getChannelNumber(), $region->getRegionNumber());
									$company->setIncomes($product->getProductNumber(),
														$channel->getChannelNumber(),
												 		$region->getRegionNumber(),											 		
														$units*$price);
									$company->setSales($product->getProductNumber(),
												 		$channel->getChannelNumber(),
												 		$region->getRegionNumber(),
														$units);
									$this->_outcomes_sales->update(array('units'=>$units, 'incomes'=>$units*$price), 
																		'game_id = '.$this->_game['id'].
																		' AND company_id = '.$company->getId().
																		' AND round_number = '.$this->_round['round_number'].
																		' AND product_number = '.$product->getProductNumber().
																		' AND region_number = '.$region->getRegionNumber().
																		' AND channel_number = '.$channel->getChannelNumber());
								}
							}
						}
					}
				}				
			}
			else{
				foreach ($this->_regions as $region){
					foreach ($this->_products as $product){
						foreach ($this->_channels as $channel){
							foreach ($this->_companies as $company){
								$availability=$this->_games->getProductAvailibility($this->_game['id'], $this->_round['round_number'],$company->getId(), $product->getProductNumber());
								if($availability==1){
									$units=$this->_sales[$product->getProductNumber()]
												 		[$region->getRegionNumber()]
												 		[$channel->getChannelNumber()]
														[$company->getId()];
									if(is_null($units)){	// ComprobaciÃ³n introducida porque $this->_sales es NULL cuando no hay nadie que produzca un producto.
															// TODO: Probablemente, haya que modificar no aquÃ­ sino en la funciÃ³n que crea $this->_sales[]
										$units=0;
									}
									$price=$company->getPrice($product->getProductNumber(), $channel->getChannelNumber(), $region->getRegionNumber());
									
									$company->setIncomes($product->getProductNumber(),
												 		$channel->getChannelNumber(),
												 		$region->getRegionNumber(),
														$units*$price);
									$company->setSales($product->getProductNumber(),
												 		$channel->getChannelNumber(),
												 		$region->getRegionNumber(),
														$units);
									$this->_outcomes_sales->insert(array('game_id'=>$this->_game['id'],
																		 'company_id'=>$company->getId(),	  
																		 'round_number'=>$this->_round['round_number'],																
																		 'product_number'=>$product->getProductNumber(),
																		 'region_number'=>$region->getRegionNumber(),
																		 'channel_number'=>$channel->getChannelNumber(), 
																		 'units'=>$units, 'incomes'=>$units*$price));
								}
							}
						}
					}
				}
			}
		}	
		
		//calcular stock
		function stock(){		
			$produced=$this->_outcomes_production_units->getOutcomes($this->_game['id'], $this->_round['round_number']); 														//Producido en esta ronda
			$sold=$this->_outcomes_sales->getSales($this->_game['id'], $this->_round['round_number']);																			//Vendido en esta ronda
			foreach ($this->_companies as $company){																															//Para cada equipo
				$stock_value=0;
				foreach ($this->_products as $product){																															// Para cada producto
					$availability=$this->_games->getProductAvailibility($this->_game['id'], $this->_round['round_number'],$company->getId(), $product->getProductNumber());
					if($availability==1){
						foreach ($this->_regions as $region){																													//  Para cada región
							foreach ($this->_channels as $channel){																												//   Para cada canal
								if ($this->_round['round_number']==1){																												//Si ronda 1
									$stock=($produced['company_'.$company->getId()]['product_'.$product->getProductNumber()]														//Stock = Producido - Vendido
													 ['region_'.$region->getRegionNumber()]['channel_'.$channel->getChannelNumber()])-
									   	   ($sold['company_'.$company->getId()]['product_'.$product->getProductNumber()]
												 ['region_'.$region->getRegionNumber()]['channel_'.$channel->getChannelNumber()]);
									$pr_cost=$company->getProductCost($this->_round['round_number'], $product->getProductNumber());													//Saca coste de stock para ese producto en ronda 1
									$this->stock_save_units($company->getId(), 																										//Guarda unidades stock
														$this->_round['round_number'],
														$product->getProductNumber(), 
														$region->getRegionNumber(),
													    $channel->getChannelNumber(),
													    $stock, $pr_cost);
									$this->stock_save_outcomes_units($company->getId(), 
														$product->getProductNumber(), 
														$region->getRegionNumber(),
														$channel->getChannelNumber(),
														$new, $pr_cost);	
									$stock_value+=$company->getStockValue($product->getProductNumber(), $region->getRegionNumber(), $channel->getChannelNumber());
								} else {																																			//Si rondas sucesivas
									$stocks=new Model_DbTable_Games_Evolution_St_Stocks();
									$sales=$sold['company_'.$company->getId()]['product_'.$product->getProductNumber()]																//Número de ventas de producto (por canal por región)
												['region_'.$region->getRegionNumber()]['channel_'.$channel->getChannelNumber()];													// (este turno)
									$new=$produced['company_'.$company->getId()]['product_'.$product->getProductNumber()]															//Número de producidas este turno
												  ['region_'.$region->getRegionNumber()]['channel_'.$channel->getChannelNumber()];													
									for($round_number=1; $round_number<$this->_round['round_number']; $round_number++){																//Para cada ronda
										$pr_cost=$company->getProductCostStock($round_number, $product->getProductNumber());														//Saca coste de stock en ronda
										//var_dump($pr_cost);
										//echo("<br/> COUNTER: ".$round_number);
										$units_stocked=$stocks->getStockClasified($this->_game['id'], $company->getId(), $round_number, $product->getProductNumber(), 				//¿Cuánto se stockó esa ronda?
																					$region->getRegionNumber(), $channel->getChannelNumber());
										echo("Unidades vendidas = ".$sales." y unidades en stock ronda ".$round_number." = ".$units_stocked."<br/>");
										if ($sales > $units_stocked){																												//Si las 
											$sales=$sales-$units_stocked;
											$units_stocked=0;
											$this->stock_save_units($company->getId(),
																	$round_number, 
																	$product->getProductNumber(), 
																	$region->getRegionNumber(),
													    			$channel->getChannelNumber(),
													    			$units_stocked, $pr_cost);
										}
										else {
											$units_stocked=$units_stocked-$sales;
											$sales=0;
											$this->stock_save_units($company->getId(), 
																	$round_number,
																	$product->getProductNumber(), 
																	$region->getRegionNumber(),
																    $channel->getChannelNumber(),
																    $units_stocked, $pr_cost);
										}										
									}
									//var_dump($pr_cost);
									//echo("<br/> COUNTER: ".$round_number+1);
									$pr_cost=$company->getProductCostStock($this->_round['round_number'], $product->getProductNumber());
									echo("Unidades producidas último turno = ".$new." y unidades vendidas tras actualizar stock = ".$sales."<br/>");
									$new=$new-$sales;
									/*if($new<0){
										$new=0;
									}*/
									$this->stock_save_units($company->getId(), 
															$this->_round['round_number'],
															$product->getProductNumber(), 
															$region->getRegionNumber(),
															$channel->getChannelNumber(),
															$new, $pr_cost);
									$this->stock_save_outcomes_units($company->getId(), 
															$product->getProductNumber(), 
															$region->getRegionNumber(),
															$channel->getChannelNumber(),
															$new, $pr_cost);
									$stock_value+=$company->getStockValue($product->getProductNumber(), $region->getRegionNumber(), $channel->getChannelNumber());
								}
							}
						}
					}
				}
			$this->_balance[$company->getId()]['stock']=$stock_value;
			$this->save_stock_total($company->getId(), $stock_value);
			}
		}
		
		//guardar stock
		function save_stock_total($company_id, $stock_value){
			if ($this->_outcomes_updating){
				$this->_outcomes_balance_sheet->update(array('value'=>$stock_value), 
															'game_id = '.$this->_game['id'].
															' AND company_id = '.$company_id.
															' AND round_number = '.$this->_round['round_number'].
															' AND type = "stock"');
			}
			else {
				$this->_outcomes_balance_sheet->insert(array('game_id'=>$this->_game['id'],
															 'company_id'=>$company_id,	  
															 'round_number'=>$this->_round['round_number'],																
															 'type'=>"stock",
															 'value'=>$stock_value));
			}
		}
		
		function stock_save_units($company_id, $round_number, $product_number, $region_number, $channel_number, $units, $pr_cost){
			$stocks=new Model_DbTable_Games_Evolution_St_Stocks();
			$boolean=$stocks->getStockRow($this->_game['id'], $company_id, $round_number, $product_number, $region_number, $channel_number);
			//echo("<br/> EQUIPO ".$company_id."<br/> STOCK EVOLUTION: ");
			if ($boolean==null){
				//echo(" INSERT ");
				$stocks->insert(array('game_id'=>$this->_game['id'], 'company_id'=>$company_id,
									  'round_number'=>$round_number,
									  'product_number'=>$product_number,
									  'region_number'=>$region_number,
									  'channel_number'=>$channel_number, 
									  'units'=>$units, 'pr_cost'=>$pr_cost));
			}
			else {
				//echo(" UPDATE ");
				$stocks->update(array('units'=>$units, 'pr_cost'=>$pr_cost), 'game_id = '.$this->_game['id'].
													   						 ' AND company_id = '.$company_id.
													   						 ' AND round_number = '.$round_number.
																		     ' AND product_number = '.$product_number.
													   						 ' AND region_number = '.$region_number.
												       						 ' AND channel_number = '.$channel_number);
			}
		}
		
		function stock_save_outcomes_units($company_id, $product_number, $region_number, $channel_number, $units, $pr_cost) {
			$stocks=new Model_DbTable_Games_Evolution_St_Stocks();
			for ($round_aux=1; $round_aux <= $this->_round['round_number']; $round_aux++){
				//$total_stock+=$stocks->getStockClasified($this->_game['id'], $company_id, $round_aux, $product_number, $region_number, $channel_number);
				$total_stock+=$stocks->getStockClasified($this->_game['id'], $company_id, $round_aux, $product_number, $region_number, $channel_number);
				//echo("<br/> EQUIPO ".$company_id." TOTAL STOCK: ".$total_stock);
				//echo("<br/> ROUND COUNTER: ".$round_aux);
			}
			$outcomes_stocks=new Model_DbTable_Outcomes_St_Units();
			$boolean_total=$outcomes_stocks->getStockTotalRow($this->_game['id'], $this->_round['round_number'], $company_id, $product_number, $region_number, $channel_number);
			//echo("<br/> EQUIPO ".$company_id."<br/> STOCK TOTAL: ");
			//var_dump($boolean_total);
			if ($boolean_total==null){
				//echo(" INSERT ");
				$outcomes_stocks->insert(array('game_id'=>$this->_game['id'], 'company_id'=>$company_id,
														  'round_number'=>$this->_round['round_number'],
														  'product_number'=>$product_number,
														  'region_number'=>$region_number,
														  'channel_number'=>$channel_number, 
														  'units'=>$total_stock));
			}else {
				//echo(" UPDATE ");	
				$outcomes_stocks->update(array('units'=>$total_stock), 'game_id = '.$this->_game['id'].
													   				   ' AND company_id = '.$company_id.
													   			       ' AND round_number = '.$this->_round['round_number'].
																	   ' AND product_number = '.$product_number.
													   				   ' AND region_number = '.$region_number.
												       				   ' AND channel_number = '.$channel_number);										
				}
		}
		
		//Funcionando correctamente. Clasifica costes de acuerdo con el modelo
		//Falta por incluir costes financieros (revisando getFiDebtCost())
		function costs(){
			foreach ($this->_companies as $company){
				$company->setInterestTotal();				
				$this->_costs[$company->getId()]['pr_fixed_cost']=$company->getPrFixedCost();
				$this->_costs[$company->getId()]['hr_hiring_costs']=$company->getHrHiringCost();
				$this->_costs[$company->getId()]['hr_training_costs']=$company->getHrTrainingCost();
				$this->_costs[$company->getId()]['hr_wages_costs']=$company->getHrWagesCost();	
				//todo lo incluido en este foreach funciona debidamente
				foreach ($this->_channels as $channel){
					foreach ($this->_regions as $region){
						foreach ($this->_products as $product){ 
							$availability=$this->_games->getProductAvailibility($this->_game['id'], $this->_round['round_number'],$company->getId(), $product->getProductNumber());
							if($availability==1){
								if (! isset($this->_costs[$company->getId()]['pr_var_costs'])){
									$this->_costs[$company->getId()]['pr_var_costs']=0;
								}
								$this->_costs[$company->getId()]['pr_var_costs']+=$company->getPrVarCost($channel->getChannelNumber(), 
																										 $region->getRegionNumber(),
																										 $product->getProductNumber());
		
								if (! isset($this->_costs[$company->getId()]['pr_rawMaterials_costs'][$channel->getChannelNumber()])){
									$this->_costs[$company->getId()]['pr_rawMaterials_costs'][$channel->getChannelNumber()]=0;
								}
								$this->_costs[$company->getId()]['pr_rawMaterials_costs'][$channel->getChannelNumber()]+=$company->getPrRawMaterialsCost($channel->getChannelNumber(),
																														$region->getRegionNumber(),
																														$product->getProductNumber());
								if (! isset($this->_costs[$company->getId()]['pr_distrib_costs'])){
									$this->_costs[$company->getId()]['pr_distrib_costs']=0;
								}
								
								$this->_costs[$company->getId()]['pr_distrib_costs']+=(($company->getPrDistribCost($channel->getChannelNumber(),
																												$region->getRegionNumber(),
																												$product->getProductNumber())));
								//VERO
								foreach($this->_channels as $channelD){
									foreach ($this->_regions as $regionD){
										$this->_costs[$company->getId()]['pr_distrib_costs']+=$company->getStDistribCost($channel->getChannelNumber(), $channelD->getChannelNumber(), $region->getRegionNumber(), $regionD->getRegionNumber(), $product->getProductNumber());
									}
								}
								//VERO
								if (! isset($this->_costs[$company->getId()]['mk_sales_costs'][$channel->getChannelNumber()])){
									$this->_costs[$company->getId()]['mk_sales_costs'][$channel->getChannelNumber()]=0;
								}							
								$this->_costs[$company->getId()]['mk_sales_costs'][$channel->getChannelNumber()]+=$company->getMkSalesCost($channel->getChannelNumber(),
																														$region->getRegionNumber(),
																														$product->getProductNumber());
								if (! isset($this->_costs[$company->getId()]['mk_fixed_costs'][$channel->getChannelNumber()])){
									$this->_costs[$company->getId()]['mk_fixed_costs'][$channel->getChannelNumber()]=0;
								}							
								$this->_costs[$company->getId()]['mk_fixed_costs'][$channel->getChannelNumber()]+=$company->getMkFixedCost($channel->getChannelNumber(), $region->getRegionNumber(), $product->getProductNumber());
							}																																		
						}
					}					
				}
				//Costes funcionan por canal, para todos los productos y todas las regiones
				
				foreach ($this->_medias as $media){
					foreach ($this->_regions as $region){
						foreach ($this->_products as $product){
							$availability=$this->_games->getProductAvailibility($this->_game['id'], $this->_round['round_number'],$company->getId(), $product->getProductNumber());
							if($availability==1){
								if (! isset($this->_costs[$company->getId()]['mk_advert_costs'][$media->getMediaNumber()])){
									$this->_costs[$company->getId()]['mk_advert_costs'][$media->getMediaNumber()]=0;
								}
								$this->_costs[$company->getId()]['mk_advert_costs'][$media->getMediaNumber()]+=$company->getMkAdvertCost($media->getMediaNumber(),
																												$region->getRegionNumber(),
																												$product->getProductNumber());
							}
						}
					}
				}
				foreach ($this->_trademedias as $trademedia){
					foreach ($this->_channels as $channel){
						foreach ($this->_products as $product){
							$availability=$this->_games->getProductAvailibility($this->_game['id'], $this->_round['round_number'],$company->getId(), $product->getProductNumber());
							if($availability==1){
								if (! isset($this->_costs[$company->getId()]['mk_trade_costs'][$trademedia['trademedia_number']])){
									$this->_costs[$company->getId()]['mk_trade_costs'][$trademedia['trademedia_number']]=0;
								}
								$this->_costs[$company->getId()]['mk_trade_costs'][$trademedia['trademedia_number']]+=$company->getMkTradeCost($trademedia['trademedia_number'],
																												$channel->getChannelNumber(),
																												$product->getProductNumber());
							}
						}
					}
				}
				//Medias y TradeMedias funcionan para todos los productos y todas las regiones
				
				//costes de las iniciativas selecionadas funcionando OK.
				$this->_costs[$company->getId()]['initiatives_pr_costs']=$company->getInitiativesProductionCost();
				$this->_costs[$company->getId()]['initiatives_mk_costs']=$company->getInitiativesMarketingCost();
				$this->_costs[$company->getId()]['initiatives_hr_costs']=$company->getInitiativesHumanResourcesCost();
				
				//costes de los estudios de mercado solicitados funcionando OK
				$this->_costs[$company->getId()]['market_researches_costs']=$company->getMarketResearchesCosts();
				//costes de cambios de I+D+i funcionando OK
				$this->_costs[$company->getId()]['idi_changes_costs']=$company->getIdiChangesCosts();
				//costes de I+D+i en lanzamiento de nuevos productos funcionando OK
				$this->_costs[$company->getId()]['idi_new_costs']=$company->getIdiNewCosts();
				
				$this->_costs[$company->getId()]['fi_debt_costs_st']=$company->getFiDebtCostsSt();
				$this->_costs[$company->getId()]['fi_debt_costs_lt']=$company->getFiDebtCostsLt();
				echo("<br>CORE (1) FIDEBTCOSTS: ".$company->getFiDebtCostsLt()."<br>");
				
				//Para que no haya ningun coste a NULL
				foreach ($this->_costs[$company->getId()] as $name=>$cost){
					if(!isset($cost)){
						$this->_costs[$company->getId()][$name]=0;
					}
				}																				
			}
			$this->save_costs();
		}
		
		function save_costs(){
			if ($this->_outcomes_updating){
				foreach ($this->_companies as $company){
					$numberOfFactories=$company->getNumberOfFactories();
					$consAmount=$company->getConstructionCost();
					$extAmount=$company->getOriginalExtensionCost();
					for ($factory_number=1; $factory_number<=$numberOfFactories; $factory_number++){
						$amortizationConsAmount+=$consAmount['factory_number_'.$factory_number][$this->_round['round_number']];
						$amortizationExtAmount+=$extAmount['factory_number_'.$factory_number][$this->_round['round_number']];
					}
					$amortizationConsTerm=20;
					$amortizationExtTerm=6;							
					$this->_amortization_data->update(array('amount'=>$amortizationConsAmount, 'term'=>$amortizationConsTerm), 
															'game_id = '.$this->_game['id'].
															' AND company_id = '.$company->getId().
															' AND round_number = '.$this->_round['round_number'].
															' AND type = "cons"');
					$this->_amortization_data->update(array('amount'=>$amortizationExtAmount, 'term'=>$amortizationExtTerm), 
															'game_id = '.$this->_game['id'].
															' AND company_id = '.$company->getId().
															' AND round_number = '.$this->_round['round_number'].
															' AND type = "ext"');
					$amortizationConsAmount=0;
					$amortizationExtAmount=0;
					foreach ($this->_costs[$company->getId()] as $name=>$cost){
						if (! is_array($cost)){//para costes agregados							
							$this->_outcomes_costs->update(array('cost'=>$cost), 
																	'game_id = '.$this->_game['id'].
																	' AND company_id = '.$company->getId().
																	' AND round_number = '.$this->_round['round_number'].
																	' AND type = "'.$name.'"');
						}
						else{//para aquellos que se desglosan por canal
							foreach ($cost as $channel=>$single_cost){
								$this->_outcomes_costs->update(array('cost'=>$single_cost), 
																		'game_id = '.$this->_game['id'].
																		' AND company_id = '.$company->getId().
																		' AND round_number = '.$this->_round['round_number'].
																		' AND channel_number = '.$channel.
																		' AND type = "'.$name.'"');
							}
						}
					}
				}				
			}
			else{ 
				foreach ($this->_companies as $company){
					$numberOfFactories=$company->getNumberOfFactories();
					$consAmount=$company->getConstructionCost();
					$extAmount=$company->getOriginalExtensionCost();
					for ($factory_number=1; $factory_number<=$numberOfFactories; $factory_number++){
						$amortizationConsAmount+=$consAmount['factory_number_'.$factory_number][$this->_round['round_number']];
						$amortizationExtAmount+=$extAmount['factory_number_'.$factory_number][$this->_round['round_number']];
					}
					$amortizationConsTerm=20;
					$amortizationExtTerm=6;
					$this->_amortization_data->insert(array('game_id'=>$this->_game['id'], 'company_id'=>$company->getId(),	  
															'round_number'=>$this->_round['round_number'], 'type'=>"cons",
															'amount'=>$amortizationConsAmount, 'term'=>$amortizationConsTerm));
					$this->_amortization_data->insert(array('game_id'=>$this->_game['id'], 'company_id'=>$company->getId(),	  
															'round_number'=>$this->_round['round_number'], 'type'=>"ext",
															'amount'=>$amortizationExtAmount, 'term'=>$amortizationExtTerm));
					$amortizationConsAmount=0;
					$amortizationExtAmount=0;
					foreach ($this->_costs[$company->getId()] as $name=>$cost){
						if (! is_array($cost)){//para costes agregados
							//var_dump($this->_outcomes_costs);die();
							$this->_outcomes_costs->insert(array('game_id'=>$this->_game['id'],
																	 'company_id'=>$company->getId(),	  
																	 'round_number'=>$this->_round['round_number'],																
																	 'type'=>$name,
																	 'cost'=>$cost));
						}
						else{//para aquellos que se desglosan por canal
							foreach ($cost as $channel=>$single_cost){
								$this->_outcomes_costs->insert(array('game_id'=>$this->_game['id'],
																		 'company_id'=>$company->getId(),	  
																		 'round_number'=>$this->_round['round_number'],																
																		 'type'=>$name,
																		 'channel_number'=>$channel,												 
																		 'cost'=>$single_cost));
							}
						}
					}
				}				
			}
		}
		//VERO
		function investment(){
			$investment= new Model_DbTable_Outcomes_In_Investment();
			foreach ($this->_companies as $company){
				$result=$company->getInvestmentInterest();
				var_dump("Core - función investment");
				var_dump($result);
				if($this->_round_number==1){
					$investment->setInvestment($this->_game['id'], $this->_round['round_number'], $company->getId(), 'fi_investment_losses', 0);
				$investment->setInvestment($this->_game['id'], $this->_round['round_number'], $company->getId(), 'fi_investment_earnings', 0);
				}else{
					$investment->setInvestment($this->_game['id'], $this->_round['round_number'], $company->getId(), 'fi_investment_losses', $result['fi_investment_losses']);
					$investment->setInvestment($this->_game['id'], $this->_round['round_number'], $company->getId(), 'fi_investment_earnings', $result['fi_investment_earnings']);
				}
				
			}
		}
		//VERO
		
		//Generamos balance clasificado por conceptos
		function balanceSheet(){
			foreach ($this->_companies as $company){
				$stock_value=0;
				$this->_balance[$company->getId()]['tied_up']=$company->getTiedUp();
				$this->_balance[$company->getId()]['amortization']=$company->getTotalAmortization();
				$investmentBalance=$company->getInvestmentBalanceSheet();
				if($this->_round_number==1){
					$this->_balance[$company->getId()]['investment_assets']=0;
				}else{
					$this->_balance[$company->getId()]['investment_assets']=$investmentBalance['investment_assets'];
				}
				
				/*foreach ($this->_products as $product){
					$availability=$this->_games->getProductAvailibility($this->_game['id'], $this->_round['round_number'],$company->getId(), $product->getProductNumber());
					if($availability==1){
						foreach ($this->_regions as $region){
							foreach ($this->_channels as $channel){
								//echo("En balanceSheet<br/>");
								$this->_balance[$company->getId()]['stock']+=$company->getStockValue($product->getProductNumber(), $region->getRegionNumber(), $channel->getChannelNumber());
							}																										
						}
					}
				}*/
				$stock_value=$this->_balance[$company->getId()]['stock'];
				echo("CHECK POINT 3: stock_value = ".$stock_value."<br/>");
				if($this->_round['round_number']>1){	
					$this->_outcomes_prev_stock_value=$stock_value-$company->getPrevStockValue($this->_game['id'], $this->_round['round_number'],$company->getId());
				} else {
					$this->_outcomes_prev_stock_value=$stock_value;
				}	
				foreach ($this->_channels as $channel){
					$this->_balance[$company->getId()]['trade_debtors']+=$company->getTradeDebtors($channel->getChannelNumber());
				}
				$this->_balance[$company->getId()]['liquid_assets']=$company->getLiquidAssets();
				$this->_balance[$company->getId()]['capital']=$company->getCapital();
				$this->_balance[$company->getId()]['reserves']=$company->getReserves();
				$this->_balance[$company->getId()]['year_result']=$company->getYearResult();
				$this->_balance[$company->getId()]['long_term_debts']=$company->getLongTermDebts();
				$this->_balance[$company->getId()]['short_term_debts']=$company->getShortTermDebts();
				foreach ($this->_channels as $channel){
					$this->_balance[$company->getId()]['creditors']+=$company->getComercialCreditors($channel->getChannelNumber());
				}
				/*$active_validation=($this->_balance[$company->getId()]['tied_up']-$this->_balance[$company->getId()]['amortization']
					   +$this->_balance[$company->getId()]['stock']+$this->_balance[$company->getId()]['trade_debtors']
					   +$this->_balance[$company->getId()]['liquid_assets']);
				$passive_validation=($this->_balance[$company->getId()]['capital']+$this->_balance[$company->getId()]['reserves']
						+$this->_balance[$company->getId()]['year_result']+$this->_balance[$company->getId()]['long_term_debts']
						+$this->_balance[$company->getId()]['short_term_debts']+$this->_balance[$company->getId()]['creditors']);
				echo("VALIDACIÓN:<br/>&nbsp;ACTIVO = ".$active_validation." Y PASIVO = ".$passive_validation."<br/>");		
				if ($active_validation!=$passive_validation){
					$dif=$passive_validation-$active_validation;
					if($dif>0){
						$this->_balance[$company->getId()]['stock']+=$dif;
					}
					else{
						$this->_balance[$company->getId()]['reserves']-=$dif;
					}
				}*/
			}
			$this->save_balanceSheet();
		}
		
		// Guardamos balance
		function save_balanceSheet(){
			//echo("¡Guardando!");
			if ($this->_outcomes_updating){
				foreach ($this->_companies as $company){
					foreach ($this->_balance[$company->getId()] as $name=>$value){
						if($name!="stock"){	//Ya lo hemos actualizado en la sección de stocks
							$this->_outcomes_balance_sheet->update(array('value'=>$value), 
																	'game_id = '.$this->_game['id'].
																	' AND company_id = '.$company->getId().
																	' AND round_number = '.$this->_round['round_number'].
																	' AND type = "'.$name.'"');
						}
					}
				}
			}
			else {
				foreach ($this->_companies as $company){
					foreach ($this->_balance[$company->getId()] as $name=>$value){
						if($name!="stock"){	//Ya lo hemos actualizado en la sección de stocks
							$this->_outcomes_balance_sheet->insert(array('game_id'=>$this->_game['id'],
																	 'company_id'=>$company->getId(),	  
																	 'round_number'=>$this->_round['round_number'],																
																	 'type'=>$name,
																	 'value'=>$value));
						}
					}
				}
			}
		}
		
		// Guardamos variables utilles
		function saveData(){
			$this->_outcomes_round_humanresources=new Model_DbTable_Outcomes_Rd_HrData();
			$this->_outcomes_round_deterioration=new Model_DbTable_Outcomes_Rd_PrDeterioration();
			$this->_outcomes_round_shares=new Model_DbTable_Outcomes_Rd_MarketShares();
			$this->_outcomes_performance=new Model_DbTable_Outcomes_Bs_Performance();
			$outcomes=new Model_DbTable_Outcomes();
			foreach ($this->_companies as $company){
				$atmorphere=$company->getWorkAtmosphere();
				$this->_outcomes_round_humanresources->delete('game_id = '.$this->_game['id'].' AND company_id = '.$company->getId().' AND round_number ='.$this->_round['round_number']);
				$this->_outcomes_round_humanresources->insert(array('game_id'=>$this->_game['id'], 'company_id'=>$company->getId(), 'round_number'=>$this->_round['round_number'], 'type'=>'hr_atmosphere', 'value'=>$atmorphere));
				$cualification=$company->getCualificationLevel();
				$this->_outcomes_round_humanresources->insert(array('game_id'=>$this->_game['id'], 'company_id'=>$company->getId(), 'round_number'=>$this->_round['round_number'], 'type'=>'hr_cualification', 'value'=>$cualification));
				$numberOfFactories=$company->getNumberOfFactories();
				$this->_outcomes_performance->delete('game_id = '.$this->_game['id'].' AND company_id = '.$company->getId().' AND round_number ='.$this->_round['round_number']);
				$profit=$company->getYearResult();
				if ($this->_round['round_number']==1) {
					$past_year_profit=0;
				} else {
					$past_year_profit=$this->_games->getYearResult($this->_game['id'], $this->_round['round_number']-1, $company->getId());
				}				
				$interests_short=$company->getFiDebtCostsSt();
				$interests_long=$company->getFiDebtCostsLt();
				echo("<br>CORE (2) FIDEBTCOSTS: ".$interests_long."<br>");
				$interests=$interests_long+$interests_short;
				$active=($this->_balance[$company->getId()]['tied_up']-$this->_balance[$company->getId()]['amortization']
					   +$this->_balance[$company->getId()]['stock']+$this->_balance[$company->getId()]['trade_debtors']
					   +$this->_balance[$company->getId()]['activeInvestment']
					   +$this->_balance[$company->getId()]['liquid_assets']);
				$reserves=$this->_balance[$company->getId()]['reserves'];
				foreach ($this->_channels as $channel){
					$incomes+=$company->getIncomes($channel->getChannelNumber());
				}
				// Ratios anuales
				$margin=($profit/$incomes)*100;
				$liabilities=($this->_balance[$company->getId()]['long_term_debts']+$this->_balance[$company->getId()]['short_term_debts']+$this->_balance[$company->getId()]['creditors']);
				$patrimony=($this->_balance[$company->getId()]['capital']+$this->_balance[$company->getId()]['reserves']+$this->_balance[$company->getId()]['year_result']);
				$indebtedness=($liabilities/$patrimony)*100;
				$curret_assets=($this->_balance[$company->getId()]['stock']+$this->_balance[$company->getId()]['trade_debtors']+$this->_balance[$company->getId()]['liquid_assets']);
				$curret_liabilities=($this->_balance[$company->getId()]['short_term_debts']+$this->_balance[$company->getId()]['creditors']);
				$treasury=$this->_balance[$company->getId()]['liquid_assets'];
				$solvency=($curret_assets/$curret_liabilities)*100;
				$liquidity=($treasury/$curret_liabilities)*100;
				if($curret_liabilities==0){
					$solvency=-1;
					$liquidity=-1;
				}
				$dividends=$company->getPaidDividends();
				//$payout_ratio=($dividends/$profit)*100;
				$payout_ratio=($dividends/$past_year_profit)*100;
				$roa=(($profit+$interests)/$active)*100;
				//$roa=($profit/$active)*100;
				//Ratios Acumulados
				$ac_profit=$profit+$reserves;
				$ac_roa=($ac_profit/$active)*100;
				$capital=$this->_balance[$company->getId()]['capital'];
				$roe=($ac_profit/$capital)*100;				
				//Guardamos Ratios Anuales
				$this->_outcomes_performance->insert(array('game_id'=>$this->_game['id'], 'company_id'=>$company->getId(), 'round_number'=>$this->_round['round_number'], 'type'=>'margin', 'value'=>$margin));
				$this->_outcomes_performance->insert(array('game_id'=>$this->_game['id'], 'company_id'=>$company->getId(), 'round_number'=>$this->_round['round_number'], 'type'=>'indebtedness', 'value'=>$indebtedness));
				$this->_outcomes_performance->insert(array('game_id'=>$this->_game['id'], 'company_id'=>$company->getId(), 'round_number'=>$this->_round['round_number'], 'type'=>'liquidity', 'value'=>$liquidity));
				$this->_outcomes_performance->insert(array('game_id'=>$this->_game['id'], 'company_id'=>$company->getId(), 'round_number'=>$this->_round['round_number'], 'type'=>'solvency', 'value'=>$solvency));
				$this->_outcomes_performance->insert(array('game_id'=>$this->_game['id'], 'company_id'=>$company->getId(), 'round_number'=>$this->_round['round_number'], 'type'=>'roa', 'value'=>$roa));
				$this->_outcomes_performance->insert(array('game_id'=>$this->_game['id'], 'company_id'=>$company->getId(), 'round_number'=>$this->_round['round_number'], 'type'=>'payout_ratio', 'value'=>$payout_ratio));
				//Guardamos Ratios Acumulados
				$this->_outcomes_performance->insert(array('game_id'=>$this->_game['id'], 'company_id'=>$company->getId(), 'round_number'=>$this->_round['round_number'], 'type'=>'ac_roa', 'value'=>$ac_roa));
				$this->_outcomes_performance->insert(array('game_id'=>$this->_game['id'], 'company_id'=>$company->getId(), 'round_number'=>$this->_round['round_number'], 'type'=>'roe', 'value'=>$roe));
				
				//Indicadores de actividad
				$performance_index=($ac_roa*0.85)+($roe*0.15);
				$this->_outcomes_performance->insert(array('game_id'=>$this->_game['id'], 'company_id'=>$company->getId(), 'round_number'=>$this->_round['round_number'], 'type'=>'performance_index', 'value'=>$performance_index));
				$share_sum=0;
				$share_counter=1;
				foreach ($this->_products as $product){
					foreach ($this->_regions as $region){
						foreach ($this->_channels as $channel){
							$share_sum+=$this->_share_model[$company->getId()]
														   [$product->getProductNumber()]
											 		   	   [$region->getRegionNumber()]
											 		   	   [$channel->getChannelNumber()];
							$share_counter++;							
						}
					}
				}
				
				$share=($share_sum/$share_counter);
				$chance=rand(0,600)*0.1;
				$exchange_index=($performance_index*0.6)+($roa*0.15)+($share*0.15)+($chance*0.1);
				$this->_outcomes_performance->insert(array('game_id'=>$this->_game['id'], 'company_id'=>$company->getId(), 'round_number'=>$this->_round['round_number'], 'type'=>'exchange_index', 'value'=>$exchange_index));
				$this->_outcomes_round_deterioration->delete('game_id = '.$this->_game['id'].' AND company_id = '.$company->getId().' AND round_number ='.$this->_round['round_number']);
				$factory_number=1;
				for ($i = 0; $i < $numberOfFactories; $i++) {
					$constructed=$this->_games->getRoundFactoryCreated($this->_game['id'], $company->getId());
					if(($this->_round['round_number']>$constructed['factory_number_'.$factory_number])||($constructed['factory_number_'.$factory_number]==1)){
						$deterioration=$company->getFactoryDeterioration($factory_number);
						$this->_outcomes_round_deterioration->insert(array('game_id'=>$this->_game['id'], 'company_id'=>$company->getId(), 'round_number'=>$this->_round['round_number'], 'factory_number'=>$factory_number, 'value'=>$deterioration));
					}
					$factory_number++;
				}
				foreach ($this->_products as $product){
					$availability=$this->_games->getProductAvailibility($this->_game['id'], $this->_round['round_number'],$company->getId(), $product->getProductNumber());
					if($availability==1){
						foreach ($this->_regions as $region){
							foreach ($this->_channels as $channel){
								$share_model=$this->_share_model[$company->getId()]
																[$product->getProductNumber()]
												 		   		[$region->getRegionNumber()]
												 		   		[$channel->getChannelNumber()];
								if(is_null($share_model)){       	// ComprobaciÃ³n introducida porque $this->_sales es NULL cuando no hay nadie que produzca un producto.
																	// TODO: Probablemente, haya que modificar no aquÃ­ sino en la funciÃ³n que crea $this->_sales[]
									$share_model=0;
								}
								$share_real=$this->_share_real[$company->getId()]
											    			  [$product->getProductNumber()]
												 		   	  [$region->getRegionNumber()]
												 		   	  [$channel->getChannelNumber()];
								if(is_null($share_real)){			// ComprobaciÃ³n introducida porque $this->_sales es NULL cuando no hay nadie que produzca un producto.
																	// TODO: Probablemente, haya que modificar no aquÃ­ sino en la funciÃ³n que crea $this->_sales[]
									$share_real=0;
								}
								$this->_outcomes_round_shares->delete('game_id = '.$this->_game['id'].' AND company_id = '.$company->getId().' AND round_number ='.$this->_round['round_number'].
																	   ' AND product_number = '.$product->getProductNumber().' AND region_number = '.$region->getRegionNumber().' AND channel_number = '.$channel->getChannelNumber());				 		   	  
								$this->_outcomes_round_shares->insert(array('game_id'=>$this->_game['id'], 'company_id'=>$company->getId(), 'round_number'=>$this->_round['round_number'],
																		   'product_number'=>$product->getProductNumber(), 'region_number'=>$region->getRegionNumber(), 'channel_number'=>$channel->getChannelNumber(),
																		   'share_model'=>$share_model, 'share_real'=>$share_real));
							}
						}
					}
				}
			}
		}
		function Charts(){
			//$Draw_counter=0;
			$channels_names_aux=$this->_games->getChannels($this->_game['id']);
			$companies_names_aux=$this->_games->getCompaniesInGame($this->_game['id']);
			foreach ($this->_products as $product){
				foreach ($this->_regions as $region){
					$regions_number_chart=$region->getRegionNumber();
					//echo("<br/> region->getRegionNumber(): ");
					//var_dump($region->getRegionNumber());
					foreach ($this->_channels as $channel){
						//var_dump($channel);
						foreach ($channels_names_aux as $channels_names_all) {
							if($channels_names_all['channel_number']==$channel->getChannelNumber()){
								$channels_names['channel_number_'.$channel->getChannelNumber()]=$channels_names_all['name'];
							}
						}
						foreach ($companies_names_aux as $company){
							//Probar si hay un error de simulacion aleatorio o si com availability (quitando letras) desaparece el error, borrar tablas
							$game_products_availability_chart=$this->_games->getProductsAvailibilityBySomeone($this->_game['id'], $this->_round['round_number']);
							//echo("<br/> products_availability_chart: ");
							//var_dump($game_products_availability_chart);
							$products_availability_chart=$game_products_availability_chart['product_number_'.$product->getProductNumber()];
							//echo("<br/> products_availability_chart: ");
							//var_dump($products_availability_chart);
							if($products_availability_chart==1){
								$products_number_chart=$product->getProductNumber(); 
								$chart['product_number_'.$product->getProductNumber()]['region_number_'.$region->getRegionNumber()]['channel_number_'.$channel->getChannelNumber()][$company['id']]=(intval(10000*($this->_games->getRealShare($this->_game['id'], $company['id'], $this->_round['round_number'], $product->getProductNumber(), $region->getRegionNumber(), $channel->getChannelNumber()))))/10000;
								$names[$company['id']]=$company['name'];
							}
						}							
					}
					if($products_availability_chart==1){
						$markets_names['channels']=$channels_names;
						//drawChart($chart, $names, $markets_names, $this->_game['id'], $this->_round['round_number'], $products_number_chart, $regions_number_chart);
						//$Draw_counter++;
						//echo("<br/> ");
						//echo("<br/> Pintando...");
						//var_dump($Draw_counter);
					}
				}
			}
		}
	}
	
	
	function drawChart($chartArray, $namesArray, $marketsArray, $game_id_chart, $round_number_chart, $product_number_chart, $region_number_chart) {
		include_once ('Zend/jpgraph/jpgraph.php'); 
		include_once ('Zend/jpgraph/jpgraph_pie.php');
		include_once ('Zend/jpgraph/jpgraph_pie3d.php');				
			
		/*echo("<br/> ");
		echo("<br/> ");
		echo("<br/> chartArray: ");
		var_dump($chartArray);
		echo("<br/> ");*/
		$graph  = new PieGraph (1000,1000);
		$theme_class= new VividTheme;
		$graph->SetTheme($theme_class);
		$graph->SetShadow();
		$graph->legend->Pos( 0.3,0.05,"left","top"); 
		//$graph->legend->SetFont(FF_VERDANA,FS_BOLD,18);  
		$graph->title-> Set("Cuotas de mercado"); 
		$graph->title->SetFont(FF_FONT1,FS_BOLD,24);
		//$graph->xaxis->SetLabelAngle(45);
		//Vemos el numero de mercados existentes
		$aux_array=$chartArray['product_number_'.$product_number_chart];
		$array_aux=$aux_array['region_number_'.$region_number_chart];
		$channel_counter=1;
		$n_markets=0;
		while(isset ($array_aux['channel_number_'.$channel_counter])){
			$n_markets++;
			$data=$array_aux['channel_number_'.$channel_counter];
			$j=0;
			for ($i = 1; $i <= max(array_keys($data))+1; $i++) {
				//if(($data[$i])!=0){
					$string[$j]="Equipo ".$i. " ";
					//$string[$j]="Equipo: ".$namesArray[$i];
					$j++;
				//}
			}
			$channel_counter++;
		}
		/*
		echo("<br/> product_number_chart: ");
		var_dump($product_number_chart);
		echo("<br/> region_number_chart: ");
		var_dump($region_number_chart);
		echo("<br/> channel_counter: ");
		var_dump($channel_counter);*/
		$size=1/(2.6*$n_markets);
		$x_center=0.5;
		$y_center=0.17;
		$array=$chartArray['product_number_'.$product_number_chart];
		$channel_counter=1;
		$array_aux=$array['region_number_'.$region_number_chart];
		$market_counter=0;
		while(isset ($array_aux['channel_number_'.$channel_counter])){
			$market_counter++;
			$data=array_values($array_aux['channel_number_'.$channel_counter]);
			//echo("<br/> ");
			//echo("<br/> DATOS: ");
			//var_dump($data);
			//echo("<br/> ");
			//echo("<br/> COUNTER");
			//var_dump($market_counter);
			//echo("<br/> ");
			$p[$market_counter]  = new PiePlot3D($data);
			$graph->Add($p[$market_counter]); 
			$p[1]->SetLegends($string);
			//$p[$market_counter]->SetLabels($string, 'right');
			$p[$market_counter]->title->Set("Canal: ".$marketsArray['channels']['channel_number_'.$channel_counter]."                                                                      "); 		
			//$p[$market_counter]->subtitle->Set("  ");
			$p[$market_counter]->ShowBorder();
			$p[$market_counter]->SetSize($size);
			if($n_markets==1){
				$p[$market_counter]->SetCenter(($center*0.5),(0.5*$center));
			}
			else {
				$x_position=$x_center;
				$y_position=$y_center*$channel_counter;
				$p[$market_counter]->SetCenter($x_position,$y_position);
			}
			//$p1->title->SetLabels(" Canal: ".$marketsArray['channels']['channel_number_'.$channel_counter]);
			//echo("<br/> ");
			//$p1->SetLabelFormat("%1.2f");
			//$p1->SetLegends($string);
			$p[$market_counter]->value->Show();
			$p[$market_counter]->value->SetMargin(0);
			$channel_counter++;
		}
		//$graph->Stroke();
		try {
			$gdImgHandler = $graph->Stroke(_IMG_HANDLER);
			$fileName = "/var/www/simu2/public/tmp/" . md5("img_".$game_id_chart."_".$round_number_chart."_".$product_number_chart."_".$region_number_chart) .".png";
			$graph->img->Stream($fileName);
		} catch (Exception $e) {
			echo 'Excepción capturada: ',  $e->getMessage(), "\n";
		}
	}
	
?>