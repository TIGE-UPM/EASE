<?php
//OK
	class Model_Simulation_Company extends Model_Simulation_SimulationObject{
		protected $_company_id;
		
		//PR variables
		protected $_region;
		protected $_factory;
		protected $_time_available;
		protected $_time_needed;
		protected $_production_messages;
		protected $_production_units_decided;
		protected $_capacity;
		protected $_units_available;
		
		protected $_qualities;
		protected $_qualities_weight;
		//VERO
		protected $_functionalities;
		protected $_functionality_parameters;
		protected $_functionalities_weight;
		//VERO
		
		//MK variables
		protected $_prices;
		protected $_advertisingsbudget;
		protected $_tradesmktbudget;
		protected $_advertisingspercentage;
		protected $_tradesmktpercentage;
		protected $_advertisingBudgetDistribution;
		protected $_tradesmktBudgetDistribution;
		//JESUS
		protected $_tradesmktregion;
		protected $_advertisingcampaign;
		
		//FI variables
		protected $_finance_amount;
		protected $_finance_term;
		
		//HR variables
		protected $_humanResources_formation;
		protected $_humanResources_cuartil;
		protected $_recruitmentPercentage;
		protected $_productivityPercentage;
		protected $_sal;
		protected $_baseh;
		protected $_excessproduction;
		protected $_atmosphere;
		protected $_deterioration;
		protected $_staff;
		protected $_basef;
		
		//SU variables
		protected $_suppliers_number;
		protected $_suppliers_payterms;
		
		//IN variables
		protected $_initiatives_production;
		protected $_initiatives_marketing;
		protected $_initiatives_humanresources;
		protected $_initiatives_pr_weights;
		protected $_initiatives_hr_weights;
		protected $_initiatives_mk_weights;
		protected $_initiatives_det_weights;
		protected $_initiatives_pr_costs;
		protected $_initiatives_hr_costs;
		protected $_initiatives_mk_costs;
		protected $_initiatives_det_costs;
		
		//MR variables
		protected $_marketresearches_solicited;
		protected $_marketresearches_costs;
		
		//ID variables
		protected $_idi_newproducts_solicited;
		protected $_idi_newproducts_budget_total;
		protected $_idi_newproducts_budget_round;
		
		protected $_stocks;
		protected $_stock_value;
		
		//////////////////////////////////////////////////////////////////////////////////////////////
		
		function __construct($core, $game_id, $company_id, $round_number){
			$this->_core=$core;
			$this->_game_id=$game_id;
			$this->_company_id=$company_id;
			$this->_round_number=$round_number;
			$this->init();
		} 
		
		//inicializar decisiones.
		//Sacamos de DB las decisiones guardadas
		function init(){
			$this->initIdi();
			$this->initProduction();
			$this->initMarketing();
			$this->initSuppliers();
			$this->initFinance();
			$this->initHumanResources();
			$this->initInitiatives();
			$this->initMarketResearches();
		}
		
		//Iniciamos producci�n... solo una factor�a
		function initProduction(){
			$this->_production=new Model_DbTable_Decisions_Production();
			$this->_quality_parameters=new Model_DbTable_Games_Param_Markets_QualityParams();
			$this->_functionality_parameters=new Model_DbTable_Games_Param_Markets_FunctionalityParams();
			$this->_factory=$this->_production->getFactoriesObjects($this->_game_id,$this->_company_id,$this->_round_number);
			$factory_number=1;
			foreach ($this->_factory as $counterfactories){
				$this->_region[$factory_number]=$counterfactories['region_number'];
				$factory_number++;
			}
			//var_dump($this->_region);die();
			//funcionando correctamente. Unidades a producir para cada region-canal de cada producto
			$this->_production_units_decided=$this->_production->getUnitsArray($this->_game_id, 
																			   $this->_company_id, 
																			   $this->_round_number);
			//funcionando correctamente. Atributos de calidad selecionados para cada producto																   																   
			$this->_qualities=$this->_production->getQualitiesArray($this->_game_id, $this->_company_id);
			//Cargamos pesos
			//VERO
			//$this->_qualities_weight=$this->_quality_parameters->getWeightsArray($this->_game_id);
			$this->_functionalities=$this->_production->getFunctionalitiesArray($this->_game_id, $this->_company_id);
			$this->_functionalities_weight=$this->_functionality_parameters->getFunctionalityParamsWeight($this->_game_id);
			$this->_qualities_weight=$this->_quality_parameters->getQualityParamsWeight($this->_game_id);
			//VERO
			$this->_production_messages=array();
		}
		
		//Inicializacion de marketing... falta presupuesto global
		function initMarketing(){
			$this->_marketing=new Model_DbTable_Decisions_Marketing();
			//funcionando correctamente. Precios seleccionados para cada canal-region de cada producto
			$this->_prices=$this->_marketing->getPrices($this->_game_id, $this->_company_id, $this->_round_number);
			//Faltar�a a�adir el presupuesto completo de marketing
			
			//funcionando correctamente. Presupuesto asignado a Publicidad
			$this->_advertisingsbudget=$this->_marketing->getAdvertisingsBudget($this->_game_id, $this->_company_id, $this->_round_number);
			//OK
			$this->_advertisingBudgetDistribution=$this->_marketing->getAdvertisingBudgetDistribution($this->_game_id, $this->_company_id, $this->_round_number);
			//funcionando correctamente. Reparto del presupuesto de publicidad por producto-medio-region
			$this->_advertisingspercentage=$this->_marketing->getAdvertisingsPercentage($this->_game_id, $this->_company_id, $this->_round_number);
			//funcionando correctamente. Presupuesto asignado a Trade MKT
			$this->_tradesmktbudget=$this->_marketing->getTradesMktBudget($this->_game_id, $this->_company_id, $this->_round_number);
			//OK
			$this->_tradesmktBudgetDistribution=$this->_marketing->getTradeMktBudgetDistribution($this->_game_id, $this->_company_id, $this->_round_number);
			//funcionando correctamente. Reparto del presupuesto de trade MKT por producto-canal-region
			$this->_tradesmktpercentage=$this->_marketing->getTradesMktPercentage($this->_game_id, $this->_company_id, $this->_round_number);
			//JESUS
			$this->_tradesmktregion=$this->_marketing->getTradeMktBudgetRegion($this->_game_id, $this->_company_id, $this->_round_number);
			$this->_advertisingcampaign=$this->_marketing->getAdvertisingCampaign($this->_game_id, $this->_company_id, $this->_round_number);

					}

		
		//Iniciamos proveedores... todo OK
		function initSuppliers(){
			$this->_suppliers=new Model_DbTable_Decisions_Suppliers();			
			//funcionando correctamente. Numero de proveedores seleccionado.
			$this->_suppliers_number=$this->_suppliers->getNumber($this->_game_id, $this->_company_id, $this->_round_number);
			//funcionando correctamente. Plazos de pago a proveedores por canal de distribuci�n.
			$this->_suppliers_payterms=$this->_suppliers->getPayterms($this->_game_id, $this->_company_id, $this->_round_number);
		}
		
		//Iniciamos finanazas... falta el inter�s del cr�dito
		function initFinance(){
			$this->_finance=new Model_DbTable_Decisions_Finance();
			$this->_balance_sheet=new Model_DbTable_Outcomes_Bs_BalanceSheet();
			
			$this->_past_assets=$this->_balance_sheet->getCompanyAssets($this->_game_id, ($this->_round_number-1), $this->_company_id);
			$this->_past_creditors=$this->_balance_sheet->getCompanyCreditors($this->_game_id, ($this->_round_number-1), $this->_company_id);
			$this->_past_debtors=$this->_balance_sheet->getCompanyDebtors($this->_game_id, ($this->_round_number-1), $this->_company_id);
			$this->_past_reserves=$this->_balance_sheet->getCompanyReserves($this->_game_id, ($this->_round_number-1), $this->_company_id);
			$this->_past_stock_value=$this->_balance_sheet->getCompanyStockValue($this->_game_id, ($this->_round_number-1), $this->_company_id);
			$this->_past_year_result=$this->_balance_sheet->getCompanyLastResult($this->_game_id, ($this->_round_number-1), $this->_company_id);
			//funcionando correctamente. Dividendos retribuidos
			$this->_finance_payout=$this->_finance->getDividends($this->_game_id, $this->_company_id, $this->_round_number);
			//funcionando correctamente. Cantidad solicitada
			$this->_finance_amount=$this->_finance->getAmount($this->_game_id, $this->_company_id, $this->_round_number);
			//funcionando correctamente. Plazo de amortizaci�n
			$this->_finance_term=$this->_finance->getTerm($this->_game_id, $this->_company_id, $this->_round_number);
			
			//faltar�a el inter�s que se le va a cobrar por la solicitud del cr�dito
		}
		
		//Inicializaci�n de RRHH... todo OK
		function initHumanResources(){
			$this->_humanResources=new Model_DbTable_Decisions_HumanResources();			
			//funcionando correctamente. Politica salarial seleccionada.
			$this->_humanResources_cuartil=$this->_humanResources->getCuartiles($this->_game_id, $this->_company_id, $this->_round_number);
			//funcionando correctamente. Cualificaci�n del staff seleccionada.
			$this->_humanResources_formation=$this->_humanResources->getFormations($this->_game_id, $this->_company_id);
			//var_dump($this->_region[$factory_number]);//die();
			foreach ($this->_factory as $factory){
				$this->_recruitmentPercentage[$factory['factory_number']]=$this->_core->_games->getHrRegionProfileParam($this->_game_id, $this->_region[$factory['factory_number']], 'hiring_probability');
				$this->_productivityPercentage[$factory['factory_number']]=$this->_core->_games->getHrRegionProfileParam($this->_game_id, $this->_region[$factory['factory_number']], 'productivity');
			}
		}
		
		//Inicializacion de iniciativas... todo OK
		function initInitiatives(){
			$this->_initiatives=new Model_DbTable_Decisions_Initiatives();			
			//funcionando correctamente. Decisi�n sobre iniciativas de producci�n
			$this->_initiatives_production=$this->_initiatives->getInitiativesProduction($this->_game_id, $this->_company_id, $this->_round_number);
			//funcionando correctamente. Decisi�n sobre iniciativas de MKT
			$this->_initiatives_marketing=$this->_initiatives->getInitiativesMarketing($this->_game_id, $this->_company_id, $this->_round_number);
			//funcionando correctamente. Decisi�n sobre iniciativas de HHRR
			$this->_initiatives_humanresources=$this->_initiatives->getInitiativesHumanresources($this->_game_id, $this->_company_id, $this->_round_number);
			//funcionando correctamente. Decisi�n sobre iniciativas de deterioro
			$this->_initiatives_deterioration=$this->_initiatives->getInitiativesDeterioration($this->_game_id, $this->_company_id, $this->_round_number);
			//funcionando correctamente. Traemos los pesos (weight) y los costs (cost) de las iniciativas
			$this->_param_initiatives=new Model_DbTable_Games_Param_Markets_Initiatives();
			$this->_initiatives_pr_weights=$this->_param_initiatives->getPrInitiativesWeights($this->_game_id);
			$this->_initiatives_hr_weights=$this->_param_initiatives->getHrInitiativesWeights($this->_game_id);
			$this->_initiatives_mk_weights=$this->_param_initiatives->getMkInitiativesWeights($this->_game_id);
			$this->_initiatives_det_weights=$this->_param_initiatives->getDetInitiativesWeights($this->_game_id);
			$this->_initiatives_pr_costs=$this->_param_initiatives->getPrInitiativesCosts($this->_game_id);
			$this->_initiatives_hr_costs=$this->_param_initiatives->getHrInitiativesCosts($this->_game_id);
			$this->_initiatives_mk_costs=$this->_param_initiatives->getMkInitiativesCosts($this->_game_id);
			$this->_initiatives_det_costs=$this->_param_initiatives->getDetInitiativesCosts($this->_game_id);
		}
		
		//Iniciamos estudios de mercado. Todo OK
		function initMarketResearches(){
			$this->_marketresearches=new Model_DbTable_Decisions_MarketResearches();
			$this->_param_marketresearches=new Model_DbTable_Games_Param_Markets_MarketResearches();
			//funcionando correctamente. Estudios de mercado solicitados y costes
			$this->_marketresearches_solicited=$this->_marketresearches->getMarketResearchesSolicited($this->_game_id, $this->_company_id, $this->_round_number);
			$this->_marketresearches_costs=$this->_param_marketresearches->getMarketResearchesCosts($this->_game_id);
		}
		
		//Iniciamos I+D+i... Falta incluir la parte de costes en modificaci�n de productos existentes.
		function initIdi(){
			$this->_idi=new Model_DbTable_Decisions_Idi();
			//funcionando correctamente. I+D de nuevos productos solicitados
			$this->_idi_newproducts_solicited=$this->_idi->getNewIdiProductsSolicited($this->_game_id, $this->_company_id, $this->_round_number);
			//funcionando correctamente. Presupuesto asignado a proyectos de I+D de nuevos productos
			$this->_idi_newproducts_budget_total=$this->_idi->getTotalNewIdiProductsBudget($this->_game_id, $this->_company_id);
			$this->_idi_newproducts_budget_round=$this->_idi->getRoundNewIdiProductsBudget($this->_game_id, $this->_company_id, $this->_round_number);
			$this->_idi_newproducts_number=$this->_idi->getNewIdiProductsNumber($this->_game_id, $this->_company_id, $this->_round_number);
			//funcionando correctamente. Cambios propuestos en los productos disponibles
			$this->_idi_product_changes=$this->_idi->getIdiChagesInProducts($this->_game_id, $this->_company_id, $this->_round_number);
			//faltan los costes de los cambios propuestos sobre los productos existentes
		}
		//OK
		function getTimeAvailable(){
			if (! isset ($this->_time_available)){
				$this->_time_available=0;
				$constructed=$this->_core->_games->getRoundFactoryCreated($this->_game_id, $this->_company_id);
				//echo("<br/>DUMP CONSTRUCTED: ");
				//var_dump($constructed);
				$newfactory=$this->_factory->toArray();
				//var_dump($newfactory);
				echo("<br/>");
				//foreach ($this->_factory as $factory){
				foreach ($newfactory as $factory){
					//var_dump($factory);
					//echo("<br/>FACTORY NO.:".$factory['factory_number']."<br/>");
					//echo("<br/>CONSTRUCTED FACTORY NO.".$constructed['factory_number_'.$factory['factory_number']]."<br/>");
					//echo("<br/>ROUND NO.".$this->_round_number."<br/>");
					if(($this->_round_number>$constructed['factory_number_'.$factory['factory_number']])||($constructed['factory_number_'.$factory['factory_number']]==1)){
						echo("<br/> EQUIPO ".$this->_company_id);
						echo("<br/> FABRICA ".$factory['factory_number']);
						echo("<br/> CONSTRUCTED ".$constructed['factory_number_'.$factory['factory_number']]);
						echo("<br/> TIME AVAILABLE BEFORE: ".$this->_time_available."<br/>");
						$this->_time_available+=$this->getProductionCapacity($factory['factory_number']);
						echo("<br/> TIME AVAILABLE AFTER: ".$this->_time_available."<br/>");
					}
				}
			}
			return $this->_time_available;
		}
		//Preparada la programaci�n de la capacidad de cada compa��a en funcion del modelo. Funcionando todo correctamente
		function getProductionCapacity($factory_number){
			if (! isset($this->_capacity)){
			$nominal_time=$this->_core->_games->getNominalTime($this->_game_id, $this->_round_number, $this->_company_id);
			$productivity_param=0.01*$this->_productivityPercentage[$factory_number];
			$recruitment_percentage_param=0.01*$this->_recruitmentPercentage[$factory_number];
			$ideal_suppliers_number=$this->_core->_games->getIdealSuppliersNumber($this->_game_id);
			//Obtenemos ambiente de trabajo, deterioro, productividad, cualificacion y porcentaje de reclutamiento
			$atmosphere=$this->getWorkAtmosphere();
			$deterioration=$this->getFactoryDeterioration($factory_number);
			$productivity=($productivity_param * $atmosphere * $deterioration);
			$cualificationlevel=$this->getCualificationLevel();
			$recruitment_percentage=($recruitment_percentage_param * $cualificationlevel);
			echo("<br>Prod. Param.: ".$productivity_param." | Atmosphere = ".$atmosphere." | Deterioration = ".$deterioration."<br>");
			echo("<br>Nominal Time = ".$nominal_time['factory_number_'.$factory_number]." | Recr. Pct. Param = ".$recruitment_percentage_param." | Cualif. Level = ".$cualificationlevel."<br>");
			if ($this->_suppliers_number>=$ideal_suppliers_number){
				$raw_material_percentage=1;
			}
			else{
				$raw_material_percentage = 1 - (0.05 * ($ideal_suppliers_number - $this->_suppliers_number));
				$this->_productionMessages[]='production_rm_insufficient';
			}
			echo("<br>Raw. Mat. Pct = ".$raw_material_percentage."<br>");
			$this->_capacity=($nominal_time['factory_number_'.$factory_number] * $raw_material_percentage * $productivity * $recruitment_percentage);
			}
			if ($recruitment_percentage<1){
				$this->_productionMessages[]='production_staff_insufficient';
			}
			return $this->_capacity;
		}
		
		function getFactoryDeterioration($factory_number){
			$time_needed=$this->getTimeNeeded();
			$nominal_time=$this->_core->_games->getNominalTime($this->_game_id, $this->_round_number, $this->_company_id);
			$productivity_param=0.01*$this->_productivityPercentage[$factory_number];
			$recruitment_percentage_param=0.01*$this->_recruitmentPercentage[$factory_number];
			$time_available_param=$nominal_time['factory_number_'.$factory_number]*$productivity_param*$recruitment_percentage_param;
			$this->_initiativesdet=$this->getInitiativesDeterioration($factory_number);
			$outcomes_round_deterioration=new Model_DbTable_Outcomes_Rd_PrDeterioration();
			
			$ponderation = $this->factoryDeteriorationPonderation();
			$round_previous=$this->_round_number-1;
			if($this->_round_number>1){
				$deterioration_previous_round=$outcomes_round_deterioration->getDeterioration($this->_game_id, $round_previous,$this->_company_id, $factory_number);
			}
			else {
				$deterioration_previous_round=1;
			}
			$this->_deterioration=($deterioration_previous_round - 0.01*(1+min(1,($time_needed/$time_available_param))))* $this->_initiativesdet*$ponderation;
			if($this->_deterioration>1){
				$this->_deterioration=1;
			}
			echo("<br/> EQUIPO ".$this->_company_id." - DETERIORO FABRICA ".$factory_number.": ".$deterioration_previous_round." (ROUND ".$round_previous.")");
			echo("<br/> EQUIPO ".$this->_company_id." - INICIATIVA MEJORA ".$factory_number.": ".$this->_initiativesdet);
			echo("<br/> EQUIPO ".$this->_company_id." - DETERIORO FABRICA ".$factory_number.": ".$this->_deterioration." (ROUND ".$this->_round_number.")");
			return $this->_deterioration;
		}
		
		function getNumberOfFactories(){//$round_number,$factory_number
			$numberOfFactories=($this->_core->_games->getLastFactory($this->_game_id, $this->_company_id))-1;
			return $numberOfFactories;	
		}
		
			//////////////////////////////////////////////////////////////////////////////////////////////
			//           PRODUCCION              //
 		function getProductionMessages(){
			return implode(";", $this->_production_messages);
		}
		//En esta funcion hay que hacer la calidad media de cada producto. Redondeado al entero mas cerca
		function getProductQuality($product_number){
			$quality_counter=1;
			$average=0;
			$quality_table=new Model_DbTable_Decisions_Pr_ProductsQuality();
			//incluye cambios de calidad de los productos realizados en I+D+i
			while(isset($this->_qualities['product_'.$product_number]['quality_param_'.$quality_counter])){
				$quality_actual=$this->_qualities['product_'.$product_number]['quality_param_'.$quality_counter];
				$quality_changes=$this->_idi_product_changes['product_'.$product_number]['quality_param_'.$quality_counter];
				$quality_param_final=$quality_actual+$quality_changes;
				if($quality_param_final<1){
					$quality_param_final=1;
				}
				if($quality_param_final>10){
					$quality_param_final=10;
				}
				$quality_table->setQualityParam($this->_game_id, $this->_company_id, $quality_param_final, $product_number, $quality_counter);
				//VERO
				//$average+=($quality_param_final)*($this->_qualities_weight['quality_param_'.$quality_counter]);
				$average+=($quality_param_final)*($this->_qualities_weight['quality_param_number_'.$quality_counter]['product_number_'.$product_number]);
				//VERO
				$quality_counter++;
			}
			var_dump($average);
			$quality_average=round($average*0.01);
			return $quality_average;
			
		}
		//VERO
		function getProductQualityFunctionality($product_number){
			$quality_counter=1;
			$functionality_counter=1;
			$average_quality=0;
			$functionality_average=0;
			$quality_table=new Model_DbTable_Decisions_Pr_ProductsQuality();
			$functionality_table=new Model_DbTable_Decisions_Pr_ProductsFunctionality();

			while(isset($this->_qualities['product_'.$product_number]['quality_param_'.$quality_counter])){
				$quality_actual=$this->_qualities['product_'.$product_number]['quality_param_'.$quality_counter];
				$quality_changes=$this->_idi_product_changes['product_'.$product_number]['quality_param_'.$quality_counter];
				$quality_param_final=$quality_actual+$quality_changes;
				if($quality_param_final<1){
					$quality_param_final=1;
				}
				if($quality_param_final>10){
					$quality_param_final=10;
				}
				$quality_table->setQualityParam($this->_game_id, $this->_company_id, $quality_param_final, $product_number, $quality_counter);
				//VERO
				//$average+=($quality_param_final)*($this->_qualities_weight['quality_param_'.$quality_counter]);
				$average_quality+=($quality_param_final)*($this->_qualities_weight['quality_param_number_'.$quality_counter]['product_number_'.$product_number]);
				//VERO
				$quality_counter++;
			}

			while(isset($this->_functionalities['product_'.$product_number]['functionality_param_'.$functionality_counter])){
				$functionality_param=$this->_functionalities['product_'.$product_number]['functionality_param_'.$functionality_counter];

				$average_functionality+=($functionality_param)*($this->_functionalities_weight['functionality_param_number_'.$functionality_counter]['product_number_'.$product_number]);
				$functionality_counter++;
			}
			$functionality_average=round($average_functionality*0.1);
			$quality_average=round($average_quality*0.01);
			$total_average=$functionality_average+$quality_average;
			return $total_average;
			
		}


		function getProductFunctionality($product_number){
			$functionality_counter=1;
			$average=0;
			$functionality_table=new Model_DbTable_Decisions_Pr_ProductsFunctionality();
			var_dump("Product Functionality");
			//incluye cambios de calidad de los productos realizados en I+D+i
			while(isset($this->_functionalities['product_'.$product_number]['functionality_param_'.$functionality_counter])){
				var_dump("Functionality: ");
				var_dump($functionality_counter);
				$functionality_param=$this->_functionalities['product_'.$product_number]['functionality_param_'.$functionality_counter];
				var_dump($functionality_param);

				$average+=($functionality_param)*($this->_functionalities_weight['functionality_param_number_'.$functionality_counter]['product_number_'.$product_number]);
				var_dump($average);
				$functionality_counter++;
			}
			$functionality_average=round($average*0.1);
			var_dump($functionality_average);
			return $functionality_average;

			
		}
		//VERO

		function getTimeNeeded(){
			//$games=new Model_DbTable_Games();
			if (! isset($this->_time_needed)){
				$total=0;
				foreach ($this->_core->_products as $product){
					$availability=$this->_core->_games->getProductAvailibility($this->_game_id, $this->_round_number, $this->_company_id, $product->getProductNumber());
					//var_dump($availability);
					if($availability==1){
						$total+=$this->getProductionTime($product->getProductNumber());
						//var_dump($total);
					}	
				}
				$this->_time_needed=$total;
			}
			return $this->_time_needed;
		}
		function getProductionTime($product_number, $channel_number=null, $region_number=null){
			$product_time=$this->_core->_games->getProductionTime($this->_game_id, 
																  $product_number, 
																  ($this->getProductQuality($product_number))-1);  /* ESTO ES CORRECTO */
			/*$product_time=$this->_core->_games->getProductionTime($this->_game_id, 
																  $product_number, 
																  $this->getProductQuality($product_number)); */
			$units=$this->getUnitsDecided($product_number, $channel_number, $region_number);
			//var_dump($product_time);
			//var_dump($units);
			return $product_time*$units;
		}
		function getUnitsProduced($product_number){			
			$percentage=$this->getProductionTime($product_number)/$this->getTimeNeeded();										
			$time=$this->_time_available*$percentage;
			$unit_time=$this->_core->_games->getProductionTime($this->_game_id, 
																  $product_number, 
																  ($this->getProductQuality($product_number))-1); /* ESTO ES LO CORRECTO. PASAMOS -1 POR LOS �NDICES DEL ARRAY DE CORE->GAMES */
			/*$unit_time=$this->_core->_games->getProductionTime($this->_game_id, 
																$product_number, 
																$this->getProductQuality($product_number));*/
			//echo("Unit Time: ".$unit_time."<br>");
			//if (($unit_time)>0) {											  
			print_r('<br>  -  - Producto: '.$product_number.' ==> '.$percentage.' (%) =>'.$time.' (tiempo) '.($time/$unit_time).' (unidades)');													
			return ($time/$unit_time);
			//} else {
			//	return 0;
			//}
		}
		function getUnitsDecided($product_number, $channel_number=null, $region_number=null){
			$created_aux=$this->_core->_games->getRoundFactoryCreated($this->_game_id, $this->_company_id);
			if (isset($channel_number) && isset($region_number)){// si se especifica canal y region
				foreach ($this->_factory as $factory){
					$created=$created_aux['factory_number_'.$factory['factory_number']];
					if($this->_round_number>$created || $created==1){
						$units['product_'.$product_number]['region_'.$region_number]['channel_'.$channel_number]+=$this->_production_units_decided['factory_number_'.$factory['factory_number']]
															   																					 	   ['product_'.$product_number]													   
															 																						   ['region_'.$region_number]
																																					   ['channel_'.$channel_number];
					}
				}	
				return $units ['product_'.$product_number]													   
							  ['region_'.$region_number]
							  ['channel_'.$channel_number];
			}			
			if (isset($region_number)){//sólo región
				foreach ($this->_factory as $factory){
					$created=$created_aux['factory_number_'.$factory['factory_number']];
					if($this->_round_number>$created || $created==1){
						$array['factory_number_'.$factory['factory_number']]=$this->_production_units_decided['factory_number_'.$factory['factory_number']]['product_'.$product_number]['region_'.$region_number];
					}
				}
				$total=0;
				foreach ($this->_factory as $factory){
					$created=$created_aux['factory_number_'.$factory['factory_number']];
					if($this->_round_number>$created || $created==1){					
						foreach ($array['factory_number_'.$factory['factory_number']] as $units){
							$total+=$units;
						}
					}
				}
				return $total;

			}
			if (isset($channel_number)){//sólo canal
				foreach ($this->_factory as $factory){
					$created=$created_aux['factory_number_'.$factory['factory_number']];
					if($this->_round_number>$created || $created==1){
						$array['factory_number_'.$factory['factory_number']]=$this->_production_units_decided['factory_number_'.$factory['factory_number']]['product_'.$product_number];
					}
				}
				$total=0;
				foreach ($this->_factory as $factory){
					$created=$created_aux['factory_number_'.$factory['factory_number']];
					if($this->_round_number>$created || $created==1){
						foreach ($array['factory_number_'.$factory['factory_number']] as $region_array){
							$total+=$region_array['channel_'.$channel_number];
						}
					}
				}
				return $total;
			}
			//si no se especifica nada, devuelve todas las del producto
			foreach ($this->_factory as $factory){
				$created=$created_aux['factory_number_'.$factory['factory_number']];
				if($this->_round_number>$created || $created==1){
					$array['factory_number_'.$factory['factory_number']]=$this->_production_units_decided['factory_number_'.$factory['factory_number']]['product_'.$product_number];
				}
			}
			//echo("<br/> FACTORY: ");
			//var_dump($this->_factory);
			//var_dump($factory_number);
			//echo("<br/> ARRAY: ");
			//var_dump($array);	
			$total=0;
			foreach ($this->_factory as $factory){
				$created=$created_aux['factory_number_'.$factory['factory_number']];
				if($this->_round_number>$created || $created==1){
					foreach ($array['factory_number_'.$factory['factory_number']] as $channel_array){
						//echo("<br/> CHANNEL ARRAY: ");
						//var_dump($channel_array);
						foreach ($channel_array as $region_units){
							//echo("<br/> REGION ARRAY: ");
							//var_dump($region_units);
								$total+=$region_units;
								//echo("<br/> TOTAL: ");
								//var_dump($total);
						}
					}
				}
			}
			return $total;
		}
		function getId(){
			return $this->_company_id;
		}

		function addProductionMessage($message){
			$this->_production_messages[]=$message;
		}
		
		function getUnitsAvailable($product_number, $channel_number, $region_number){
			if (! isset($this->_units_available[$product_number][$channel_number][$region_number])){
				return 0;
			}
			return $this->_units_available[$product_number][$channel_number][$region_number];
		}
		
		function setUnitsAvailable($product_number, $channel_number, $region_number, $units){
			//VERO
			//$stock=new Model_DbTable_Outcomes_St_Units();
			$stock=new Model_DbTable_Decisions_St_StockFinal();
			//VERO
			if($this->_round_number>1){
				$round_previous=($this->_round_number)-1;
				$stock_units=$stock->getStockByMarket($this->_game_id, $round_previous, $this->_company_id, $product_number, $region_number, $channel_number);
			} else $stock_units=0;			
			$this->_units_available[$product_number][$channel_number][$region_number]=($units+$stock_units);
		}
		
		//      MARKETING      //
		
		function getPrice($product_number, $channel_number, $region_number){
			return $this->_prices['product_'.$product_number]
								 ['channel_'.$channel_number]
								 ['region_'.$region_number];
		}
		
		//Cogemos el presupuesto de publicidad
		function getAdvertisingBudget(){
			return $this->_advertisingsbudget;
		}
		//Cogemos el presupuesto de tradeMkt
		function getTradeMktBudget(){
			return $this->_tradesmktbudget;
		}
		function getAdvertisingBudgetDistribution($product_number){
			//var_dump($this->_advertisingBudgetDistribution['product_'.$product_number]);
			return $this->_advertisingBudgetDistribution['product_'.$product_number];
		}
		//Cogemos el porcentaje de publicidad destinado por producto, region y canal
		function getAdvertisingPercentage($product_number, $region_number, $media_number){
			return $this->_advertisingspercentage['product_'.$product_number]['media_'.$media_number]['region_'.$region_number];
		}
		function getTradeMktBudgetDistribution($product_number){
			return $this->_tradesmktBudgetDistribution['product_'.$product_number];
		}
		//Cogemos el porcentaje de tradeMkt destinado por producto, region y canal
		function getTradeMktPercentage($product_number, $channel_number, $trademedia_number){
			//var_dump($this->_tradesmktpercentage['product_'.$product_number]['trademedia_'.$trademedia_number]['channel_'.$channel_number]);
			return $this->_tradesmktpercentage['product_'.$product_number]['trademedia_'.$trademedia_number]['channel_'.$channel_number];
		}
		//JESUS
		//Cogemos el presupuesto de trademarketing destinado a cada regi�n
		function getTradeMktregion($region_number){
			//var_dump($this->_tradesmktregion['region_'.$region_number];);
			return $this->_tradesmktregion['region_'.$region_number];
		}
		//Devuelve la estrategia de MKT de cada empresa
		function getAdvertisingCampaign(){
			return $this->_advertisingcampaign;
		}
	
		function getQuality($product_number){
			//var_dump($this->_qualities['product_'.$product_number]);die();
			return $this->_qualities['product_'.$product_number];
		}
		
		function setSales($product_number, $channel_number, $region_number, $units){
			$this->_sales['product_'.$product_number]
								 ['region_'.$region_number]
								 ['channel_'.$channel_number]=$units;
		}
		function setIncomes($product_number, $channel_number, $region_number, $income){
			$this->_incomes['product_'.$product_number]
								 ['region_'.$region_number]
								 ['channel_'.$channel_number]=$income;
		}
		
		function getIncomes($channel_number){
			if (!isset ($this->_channel_incomes[$channel_number])){
				$this->_channel_incomes[$channel_number]=0;
				foreach ($this->_core->_products as $product){
					foreach ($this->_core->_regions as $region){						
						$this->_channel_incomes[$channel_number]+=$this->_incomes['product_'.$product->getProductNumber()]
																			     ['region_'.$region->getRegionNumber()]
																			   	 ['channel_'.$channel_number];
					}
				}
			}
			return $this->_channel_incomes[$channel_number];
		}
		//REVISAR
		function getProductCost($round_number, $product_number){
			$factory_number=1;
			$cost=$this->_core->_games->getProductionCost($this->_game_id, $round_number, $this->_region[$factory_number], 'unit', $product_number);
			return $cost;
		}
		
		///// RECURSOS HUMANOS
		//Calculamos la atmosfera de trabajo segun el modelo. Funcionando correctamente, cogemos todas las iniciativas de RRHH, sin distincion entre cualificacion y felicidad
		function getWorkAtmosphere(){
			$this->_baseh=$this->getBaseHappiness();
			$this->_sal=$this->getCuartil();			
			echo("<br/> Politica Salarial: ".$this->_sal);
			$this->_initiativesh=$this->getHappinessInitiatives();
			//var_dump($this->_initiativesh);die();
			$this->_excessproduction=$this->getExcessProduction();
			echo("<br/> Exceso de Produccion: ".$this->_excessproduction);
			$this->_atmosphere=($this->_baseh * $this->_sal * $this->_initiativesh * $this->_excessproduction);
			echo("<br/> Clima Laboral: ".$this->_atmosphere);
			return $this->_atmosphere;
		}
		//Calculamos la base de felicidad segun el modelo
		function getBaseHappiness(){
			$basehappiness=0.98 - ($this->_round_number-1)/100;
			return $basehappiness;
		}
		//Cogemos las decisiones de cuartil
		function getCuartil(){
			return $this->_humanResources_cuartil;
		}
		//Calculamos el exceso de produccion segun el modelo
		function getExcessProduction(){
			$time_needed=$this->getTimeNeeded();
			$nominal_time=$this->_core->_games->getNominalTime($this->_game_id, $this->_round_number,  $this->_company_id);
			//var_dump($nominal_time);die();
			$factoryTimeAvailable=0;
			$constructed=$this->_core->_games->getRoundFactoryCreated($this->_game_id, $this->_company_id);
			$newfactory=$this->_factory->toArray();
			//var_dump($newfactory);die();
			foreach ($newfactory as $factory){
				if(($this->_round_number>$constructed['factory_number_'.$factory['factory_number']])||($constructed['factory_number_'.$factory['factory_number']]==1)){
					//var_dump($factory['factory_number']);die();
					//var_dump($nominal_time['factory_number_'.$factory['factory_number']]);die();
					$factoryProductivity=(0.01*$this->_productivityPercentage[$factory['factory_number']]);
					$factoryRecruitment=(0.01*$this->_recruitmentPercentage[$factory['factory_number']]);
					$factoryTimeAvailable+=$nominal_time['factory_number_'.$factory['factory_number']]*$factoryProductivity*$factoryRecruitment;
					echo("<br/> Nominal Time F�brica ".$factory['factory_number'].": ".$nominal_time['factory_number_'.$factory['factory_number']]);
					echo("<br/> Total factory time available adjusted :".$factoryTimeAvailable);
				}
			}
			$deflect=($time_needed-$factoryTimeAvailable)/$factoryTimeAvailable;
			echo("<br/> Desvio : ".$deflect);
			if ($deflect > 0){
				$excess=1-max(0,(($deflect-0.1)*0.6));
				if($excess > 0.05){
					$excessprod=$excess;
				}
				else{
					$excessprod=0.05;
				}
			}
			else{
				$excessprod=1;
			}
			//var_dump($excessprod);die();
			return $excessprod;
		}
		//Calculamos el nivel de cualificacion de la plantilla
		function getCualificationLevel(){
			$this->_basef=$this->getBaseFormation();
			$this->_staff=$this->getFormation();
			echo("<br/> Cualificacion Staff Inicial: ".$this->_staff);
			$this->_initiativesf=$this->getFormationInitiatives();
			$this->_cualification=($this->_basef * $this->_staff * $this->_initiativesf);
			echo("<br/> Nivel de Cualificacion de la plantilla (con Iniciativas): ".$this->_cualification);
			return $this->_cualification;
		}
		//Calculamos la base de la formacion segun el modelo
		function getBaseFormation(){
			$baseformation=0.98 - ($this->_round_number-1)/100;
			return $baseformation;
		}
		//Cogemos las decisiones de formacion
		function getFormation(){
			return $this->_humanResources_formation;
		}
		
		/////////////////////////
		// INICIATIVAS
		
		//Recoge las iniciativas de Producci�n. Funcionando correctamente
		function getInitiativesProduction(){
			$initiative_number=1;
			$value=1;
			while(isset($this->_initiatives_production['initiativeproduction_number_'.$initiative_number])) {
				$aux=$this->_initiatives_production['initiativeproduction_number_'.$initiative_number];
				$weight=$this->_initiatives_pr_weights['initiativeproduction_number_'.$initiative_number];
				$value=$value-($aux*$weight*0.01);
				$initiative_number++;
			}
			return $value;
		}
		
		//Recoge las iniciativas de Producci�n. Funcionando correctamente
		function getInitiativesDistribution(){
			$initiative_number=1;
			$value=1;
			while(isset($this->_initiatives_production['initiativeproduction_number_'.$initiative_number])) {
				$aux=$this->_initiatives_production['initiativeproduction_number_'.$initiative_number];
				$weight=$this->_initiatives_pr_weights['initiativeproduction_number_'.$initiative_number];
				$value=$value-($aux*$weight*0.01);
				$initiative_number++;
			}
			return $value;
		}
		
		//Recoge las iniciativas de Marketing. Funcionando correctamente
		function getInitiativesMarketing(){
			$initiative_number=1;
			$value=1;
			while(isset($this->_initiatives_marketing['initiativemarketing_number_'.$initiative_number])) {
				$aux=$this->_initiatives_marketing['initiativemarketing_number_'.$initiative_number];
				$weight=$this->_initiatives_mk_weights['initiativemarketing_number_'.$initiative_number];
				$value=$value-($aux*$weight*0.01);
				$initiative_number++;
			}
			return $value;
		}
		function getInitiativesDeterioration($factory_number){
				$aux=$this->_initiatives_deterioration['factory_number_'.$factory_number];
				$weight=$this->_initiatives_det_weights['initiativedeterioration_number_1'];
				$value=1+($aux[$initiative_number]*$weight*0.01);
			return $value;
		}
		
		//Recoge las iniciativas de Recursos Humanos. Funcionando correctamente
		function getFormationInitiatives(){
			$initiative_number=1;
			$value=1;
			while(isset($this->_initiatives_humanresources['initiativehumanresources_number_'.$initiative_number])) {
				$aux=$this->_initiatives_humanresources['initiativehumanresources_number_'.$initiative_number];
				$weight=$this->_initiatives_hr_weights['initiativehumanresources_number_'.$initiative_number];
				$value=$value+($aux*$weight*0.01);
				$initiative_number++;
			}
			return $value;
		}
		
		//Recoge las iniciativas de Recursos Humanos. Funcionando correctamente		
		function getHappinessInitiatives(){
			$initiative_number=1;
			$value=1;
			while(isset($this->_initiatives_humanresources['initiativehumanresources_number_'.$initiative_number])) {
				$aux=$this->_initiatives_humanresources['initiativehumanresources_number_'.$initiative_number];
				$weight=$this->_initiatives_hr_weights['initiativehumanresources_number_'.$initiative_number];
				$value=$value+($aux*$weight*0.01);
				$initiative_number++;
			}
			return $value;
		}
			
		// COSTS
			// producción
		function getPrFixedCost(){
			$result = 0;
			//Precios fijos de las f�bricas
			foreach ($this->_factory as $factory) { 
				//$aux_created=$this->_core->_games->getRoundFactoryCreated($this->_game_id, $this->_company_id);
				//$round_created=$aux_created['factory_number_'.$factory['factory_number']];
				$result+=0.2*($this->_core->_games->getProductionCost($this->_game_id, $this->_round_number, $this->_region[$factory['factory_number']], 'fixed')); //20% del precio de la f�brica actualizado
			}
			// Precios fijos derivados de las extensiones
			$extension_cost=$this->getExtensionCost(); 	//devuelve un Array(Array) con los costes de la ampliaci�n para cada f�brica y ronda
			if(is_null($extension_cost)){
				return $result;				//Si no hay nada m�s que hacer porque no hay extensiones, salimos.
			}
			foreach ($extension_cost as $extcost) { //Si hay extensiones, sumamos todos los costes de ampliaci�n para cada f�brica
				$factory_total_extensions_cost+=array_sum($extcost);
				$result+=0.2*$factory_total_extensions_cost;
			}
															//$extension_cost = F�brica1[CosteExtRonda2,CosteExtRonda3,...]],F�brica2[CosteExtRonda2,CosteExtRonda3,...],...
				// if(($this->_round_number==$round_created)){
					// $result+=0.2*($this->_core->_games->getProductionCost($this->_game_id, $this->_round_number, $this->_region[$factory['factory_number']], 'fixed'));
				// }
				// else{
					// if($this->_round_number<$round_created){
						// $result+=0;
					// }
					// else{
						// $result+=0.2*($this->_core->_games->getProductionCost($this->_game_id, $this->_round_number, $this->_region[$factory['factory_number']], 'fixed'));
					// }
				// }
				// for ($index = 1; $index <= $this->_round_number; $index++) {
					// if($this->_round_number==$index){
						// $result+=0.2*($extension_cost['factory_number_'.$factory['factory_number']][$index]);
					// }
					// else {
						// $result+=0.2*($extension_cost['factory_number_'.$factory['factory_number']][$index]);
					// }
				// }
			//}
			return $result;			
		}
		function getPrVarCost($channel_number, $region_number,$product_number){
			//$stock=new Model_DbTable_Outcomes_St_Units();
			$result=0;
			$counter=0;
			foreach ($this->_factory as $factory) {
				$aux_created=$this->_core->_games->getRoundFactoryCreated($this->_game_id, $this->_company_id);
				$round_created=$aux_created['factory_number_'.$factory['factory_number']];				
				if(($this->_round_number>$round_created) || ($round_created==1)){
					$unit_cost_aux+=$this->_core->_games->getProductionCost($this->_game_id, $this->_round_number, $this->_region[$factory['factory_number']], 'unit', $product_number);
				}
				else{
					$unit_cost_aux+=0;
					$counter--;
				}
				$counter++;
			}
			$unit_cost=$unit_cost_aux/$counter;
			echo("<br/> EQUIPO ".$this->_company_id." UNIT COST ".$unit_cost);
			//VERO
			$functionality_decision=new Model_DbTable_Decisions_Pr_ProductsFunctionality();
			$nfuncionalities=$this->_core->_games->getNumberOfFunctionalities($this->_game_id);
			$aditional_unit_cost=0;
			for ($functionality_param_number=1; $functionality_param_number<=$nfuncionalities; $functionality_param_number++ ){
				if($functionality_decision->getFunctionalityByProductAndParamNumber($this->_game_id, $this->_company_id, $product_number, $functionality_param_number)==1){
					$aditional_unit_cost+=$this->_functionality_parameters->getFunctionalityCost($this->_game_id, $functionality_param_number);
				}
			}
			echo("<br/> EQUIPO ".$this->_company_id." ADITIONAL UNIT COST ".$aditional_unit_cost);
			//VERO
			//$units_available=$this->getUnitsAvailable($product_number, $channel_number, $region_number);
			$units=$this->getProductionUnits($product_number, $channel_number, $region_number);
			echo("<br/> EQUIPO ".$this->_company_id." UNITS ".$units);
			$discount=$this->getInitiativesProduction($round_number);
			//echo("En getPrVarCost<br/>");
			//$stock=$this->getStockValue($product_number, $region_number, $channel_number);
			//VERO		
			//$result+=($unit_cost*$units*$discount);//+$stock;
			$result+=(($unit_cost+$aditional_unit_cost)*$units*$discount);
			echo("<br/> EQUIPO ".$this->_company_id." TOTAL VARIABLE COST ".$result);
			//VERO
			return $result;
		}
		function getPrRawMaterialsCost($channel_number, $region_number,$product_number){ //A INCORPORAR EN EASE2 20120329
			$unit_base_cost=$this->_core->_games->getRawMaterialCost($this->_game_id, $this->_round_number, $product_number, 'base');
			$idealsuppliers_number=$this->_core->_games->getIdealSuppliersNumber($this->_game_id);
			$unit_increment=max(0,$this->_core->_games->getRawMaterialCost($this->_game_id, $this->_round_number, $product_number, 'increment_per_supplier'),(($idealsuppliers_number-$this->_suppliers_number)*$this->_core->_games->getRawMaterialCost($this->_game_id, $this->_round_number, $product_number, 'increment_per_supplier')));
			$suppliers_paytime_increment=$this->_core->_games->getSuppliersPaytimeCost($this->_game_id, $this->_suppliers_payterms['channel_'.$channel_number]);
			$unit_cost=$unit_base_cost*(1+($unit_increment*0.01))*(1+($suppliers_paytime_increment*0.01));
			//$units=$this->getUnitsAvailable($product_number, $channel_number, $region_number);//deberia ser $this->getProductionUnits($product_number, $channel_number, $region_number);
			$units=$this->getProductionUnits($product_number, $channel_number, $region_number);
			/* echo("Unidades : ".$units." | Coste unitario: ".$unit_cost." | Incremento por n proveedores : ".$unit_increment." | Incremento por plazo de pago: ".$suppliers_paytime_increment."<br/>"); */
			return $unit_cost*$units;
		}
		function getPrDistribCost($channel_number, $region_number,$product_number){
			foreach ($this->_factory as $factory) {
				$unit_cost=$this->_core->_games->getDistributionCost($this->_game_id, $this->_round_number, $this->_region[$factory['factory_number']], $region_number);
				//$units=$this->getUnitsAvailable($product_number, $channel_number, $region_number);//deberia ser $this->getProductionUnits($product_number, $channel_number, $region_number);
				$units=$this->getProductionUnits($product_number, $channel_number, $region_number);
				$discount=$this->getInitiativesDistribution($round_number);
				$result+=$unit_cost*$units*$discount;
			}
			return $result;
		}

		//VERO
		function getStDistribCost($channelO, $channelD, $regionO, $regionD, $product){
			$st_distribution=new Model_DbTable_Decisions_St_Distribution();

			$unit_cost=intval($st_distribution->getCostStockDistribution($this->_game_id, $this->_round_number, $this->_company_id, $product, $channelO, $regionO, $channelD, $regionD));
			$units=intval($st_distribution->getUnitsDistribution($this->_game_id, $this->_round_number, $this->_company_id, $product, $channelO, $regionO, $channelD, $regionD));
			$discount=$this->getInitiativesDistribution($round_number);
			$result+=$unit_cost*$units*$discount;

			return $result;
		}
		//VERO
			// marketing
		function getMkAdvertCost($media_number, $region_number, $product_number){
			$percentage=$this->_advertisingspercentage['product_'.$product_number]['media_'.$media_number]['region_'.$region_number];
			/*if(isset($this->decision->_advertisingBudgetDistribution)) echo("Budget distribution: ");var_dump($this->_advertisingBudgetDistribution['product_'.$product_number]);echo("<br/>"); }*/
			$percentage_prod=$this->_advertisingBudgetDistribution['product_'.$product_number];
			//echo("Budget distribution: ");var_dump($percentage_prod);echo("<br/>");
			$budget=$this->_advertisingsbudget;
			//$percentage_prod2=floatval($this->_advertisingBudgetDistribution['product_'.$product_number]);
			//echo("Budget distribution: ");var_dump($percentage_prod2);echo("<br/>");
			$percentage_prod=floatval($percentage_prod);
			if ($percentage > 0) {
				return (($percentage*0.01)*$budget*(0.01*$percentage_prod));
			}
			return 0;
		}
		function getMkTradeCost($trademedia_number, $channel_number, $product_number){
			$percentage=$this->_tradesmktpercentage['product_'.$product_number]['trademedia_'.$trademedia_number]['channel_'.$channel_number];
			$percentage_prod=$this->_tradesmktBudgetDistribution['product_'.$product_number];
			$budget=$this->_tradesmktbudget;
			$percentage_prod=floatval($percentage_prod);
			if ($percentage > 0){
				return (($percentage*0.01)*$budget*($percentage_prod*0.01));
			}
			return 0;
		}
		
		function getMkSalesCost($channel_number, $region_number,$product_number){
			$fare=$this->_core->_games->getMkChannelCost($this->_game_id, $this->_round_number, $channel_number, $region_number, 'fare_cost');
			$incomes=$this->_incomes['product_'.$product_number]['region_'.$region_number]['channel_'.$channel_number];
			//print_r('c'.$channel_number.'/r'.$region_number.'/p'.$product_number.'::'.($incomes*$fare/100).'<br>');
			return $incomes*$fare*0.01;
		}
		function getMkFixedCost($channel_number, $region_number){
			$units=0;
			foreach ($this->_core->_products as $product){
				$units+=$this->getUnitsAvailable($product->getProductNumber(), $channel_number, $region_number);
			}
			if ($units>0){
				return ($this->_core->_games->getMkChannelCost($this->_game_id, $this->_round_number, $channel_number, $region_number, 'fixed_cost'));
			}
			return 0;
		}
		
		//iniciativas
		function getInitiativesProductionCost(){
			$initiative_number=1;
			$totalCost=0;
			while(isset($this->_initiatives_production['initiativeproduction_number_'.$initiative_number])) {
				$aux=$this->_initiatives_production['initiativeproduction_number_'.$initiative_number];
				$cost=$this->_initiatives_pr_costs['initiativeproduction_number_'.$initiative_number];
				$cost+=$this->getInitiativesDeteriorationCost();
				$totalCost=$totalCost+($aux*$cost);
				$initiative_number++;
			}
			return $totalCost;
		}
		
		function getInitiativesMarketingCost(){
			$initiative_number=1;
			$totalCost=0;
			while(isset($this->_initiatives_marketing['initiativemarketing_number_'.$initiative_number])) {
				$aux=$this->_initiatives_marketing['initiativemarketing_number_'.$initiative_number];
				$cost=$this->_initiatives_mk_costs['initiativemarketing_number_'.$initiative_number];
				$totalCost=$totalCost+($aux*$cost);
				$initiative_number++;
			}
			return $totalCost;
		}
		
		function getInitiativesHumanResourcesCost(){
			$initiative_number=1;
			$totalCost=0;
			while(isset($this->_initiatives_humanresources['initiativehumanresources_number_'.$initiative_number])) {
				$aux=$this->_initiatives_humanresources['initiativehumanresources_number_'.$initiative_number];
				$cost=$this->_initiatives_hr_costs['initiativehumanresources_number_'.$initiative_number];
				$totalCost=$totalCost+($aux*$cost);
				$initiative_number++;
			}
			return $totalCost;
		}
		function getInitiativesDeteriorationCost(){
			$initiative_number=1;
			$totalCost=0;
			while(isset($this->_initiatives_deterioration['factory_number_'.$initiative_number])) {
				$aux[$initiative_number]=$this->_initiatives_deterioration['factory_number_'.$initiative_number];
				$cost=$this->_initiatives_det_costs['initiativedeterioration_number_'.$initiative_number];
				$totalCost=$totalCost+($aux[$initiative_number]*$cost);
				$initiative_number++;
			}
			return $totalCost;
		}
		
			//Estudios de Mercado
		function getMarketResearchesCosts(){
			$research_number=1;
			$totalCost=0;
			
			$names[0]=array('value'=>1, 'descriptor'=>'channelResearch');
			$names[1]=array('value'=>2, 'descriptor'=>'pricesResearch');
			$names[2]=array('value'=>3, 'descriptor'=>'mktResearch');
			$names[3]=array('value'=>4, 'descriptor'=>'spectedResearch');
			$names[4]=array('value'=>5, 'descriptor'=>'accountResearch');
			
			while(isset($this->_marketresearches_costs['marketResearch_number_'.$research_number])) {				
				$aux=$this->_marketresearches_solicited[$names[$research_number-1]['descriptor']];
				$cost=$this->_marketresearches_costs['marketResearch_number_'.$research_number];
				$totalCost=$totalCost+($aux*$cost);
				$research_number++;
			}
			return $totalCost;
		}
		
		//costes en modificaci�n de productos I+D+i
		function getIdiChangesCosts(){
			$totalCost=0;
			$product_number=1;
			while (isset($this->_idi_product_changes['product_'.$product_number])){
				$product_changes=$this->_idi_product_changes['product_'.$product_number];
				$param=1;
				$average_change=0;
				$changeCost=0;
				while (isset($product_changes['product_quality_'.$param])){
					//VERO
					//$average_change+=($product_changes['product_quality_'.$param])*($this->_qualities_weight['quality_param_'.$param])*0.01;
					$average_change+=($product_changes['product_quality_'.$param])*($this->_qualities_weight['quality_param_number_'.$param]['product_number_'.$product_number])*0.01;
					//VERO
					$param++;
				}
				if ($average_change>0){
					$changeCost=$average_change*0.1*$this->getPrFixedCost()*$this->getIdiParabolicValue($product_number, $average_change);
				} else {
					$changeCost=$average_change*0.1*(-0.8)*$this->getPrFixedCost();
					//ver posibilidad de parametrizar 0.1 (fracci�n de costes fijos de producci�n)
					//0.8 ya que no cuesta lo mismo disminuir la calidad que aumentarla
				}
				$totalCost+=$changeCost;
				$product_number++;
			}
			$product_number=($product_number-1);
			 if($product_number==0){
			 	$product_number=1;
			 }
			return ($totalCost/$product_number);
		}
		
		//factor parab�lico de coste de cambios de I+D+i. aplica en cambios a la alza
		function getIdiParabolicValue($product_number, $idiChange){
			$initQuality=($this->getProductQuality($product_number))-$idiChange;
			$parabolicVaulue=1+(0.05*abs(5-$initQuality));
			//0.05 para penalizar un 20% en los extremos de calidad
			return $parabolicVaulue;
		}
		
		//costes es I+D+i para lanzamiento de nuevos de nuevos proyectos
		function getIdiNewCosts(){
			$product_number=1;
			while (isset($this->_idi_newproducts_solicited['idiproduct_'.$product_number])){
				$proyectSelection=$this->_idi_newproducts_solicited['idiproduct_'.$product_number];
				$proyectBudget=$this->_idi_newproducts_budget_round['idiproduct_'.$product_number];
				$totalCost+=$proyectSelection*$proyectBudget;
				//var_dump($proyectSelection);
				//var_dump($proyectBudget);
				$product_number++;
			}
			return $totalCost;
		}
		
			// recursos humanos
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				
		function getHrHiringCost(){
			$result=0;
			$totalemployees=0;
			foreach ($this->_factory as $factory){
				if ($this->isFactoryNew($this->_game_id, $this->_company_id, $this->_region[$factory['factory_number']])==false){
					$employee_cost=$this->_core->_games->getHrStaffCost($this->_game_id, $this->_round_number, $this->_region[$factory['factory_number']], 'hiring_cost');
//	QUITADOS LOS ISSET PORQUE CON UNA EXTENSI�N EN LA PRIMERA F�BRICA, PON�A ESE N�MERO DE EMPLEADOS EN TODAS (NO VOLV�A A ENTRAR EN EL BUCLE): �POR QU� SE PUSO ESA CONDICI�N? 
//				if (! isset ($staff)){
					// $staff=$this->_core->_games->getOrganizationParam($this->_game_id, 'production_workers');
					// $staff+=$this->_core->_games->getOrganizationParam($this->_game_id, 'packaging_workers');
					// $staff+=$this->_core->_games->getOrganizationParam($this->_game_id, 'quality_workers');
					// $staff+=$this->_core->_games->getOrganizationParam($this->_game_id, 'maintenance_workers');
					// $staff+=$this->_core->_games->getExtensionEmployees($this->_game_id, $this->_round_number, $this->_company_id, $factory['factory_number']);

					//JESUS s�lo se suma el coste de los empleados contratados en esta ronda
						$employees = new Model_DbTable_Decisions_Hr_Employees();

						
							
							$factory_number = $this->_region[$factory['factory_number']];
							

							$employees_dec= $employees->getDecision($this->_game_id, $this->_company_id, $this->_round_number);
							$employees_factory = $employees_dec['factory_number_'.$factory_number];
							if ($employees_factory['category_1']>0){
							$staff+=$employees_factory['category_1'];}
							if ($employees_factory['category_2']>0){
							$staff+=$employees_factory['category_2'];}
							if ($employees_factory['category_3']>0){
							$staff+=$employees_factory['category_3'];}
							if ($employees_factory['category_4']>0){
							$staff+=$employees_factory['category_4'];}
							


							$hired_percentage=0.01*$this->_recruitmentPercentage[$factory['factory_number']];
					
							$staff=round($staff*$hired_percentage);
							
							
						
	//					echo("<br>Staff Hiring: ".$staff."<br/>");
	//				}
				
				//Sumo todos los empleados
					if ($this->_round_number>1){
						for ($round_number = 1; $round_number <= $this->_round_number-1; $round_number++){
								$factory_number = $this->_region[$factory['factory_number']];
							

							$employees_dec= $employees->getDecision($this->_game_id, $this->_company_id, $round_number);
							$employees_factory = $employees_dec['factory_number_'.$factory_number];
							$totalemployees+=$employees_factory['category_1'];
							$totalemployees+=$employees_factory['category_2'];
							$totalemployees+=$employees_factory['category_3'];
							$totalemployees+=$employees_factory['category_4'];
						}
					}
					echo("<br>Staff Contratado: ".$staff."<br/>");
					echo("<br>total employees: ".$totalemployees."<br/>");
					$cost=($staff+(1-$this->getWorkAtmosphere()/100)*$totalemployees)*$employee_cost;

					$result+=$cost;
				}

			}

			return $result;
		}
		function getHrTrainingCost(){
			$result=0;
			foreach ($this->_factory as $factory){
				if ($this->isFactoryNew($this->_game_id, $this->_company_id, $this->_region[$factory['factory_number']])==false){
					$employee_cost=$this->_core->_games->getHrStaffCost($this->_game_id, $this->_round_number, $this->_region[$factory['factory_number']], 'training_cost');
	//	QUITADOS LOS ISSET PORQUE CON UNA EXTENSI�N EN LA PRIMERA F�BRICA, PON�A ESE N�MERO DE EMPLEADOS EN TODAS (NO VOLV�A A ENTRAR EN EL BUCLE): �POR QU� SE PUSO ESA CONDICI�N? 
	//				if (! isset ($staff)){
						// $staff=$this->_core->_games->getOrganizationParam($this->_game_id, 'production_workers');
						// $staff+=$this->_core->_games->getOrganizationParam($this->_game_id, 'packaging_workers');
						// $staff+=$this->_core->_games->getOrganizationParam($this->_game_id, 'quality_workers');
						// $staff+=$this->_core->_games->getOrganizationParam($this->_game_id, 'maintenance_workers');
						// $staff+=$this->_core->_games->getExtensionEmployees($this->_game_id, $this->_round_number, $this->_company_id, $factory['factory_number']);
				
						//JESUS Sumo los empleados contratados cada ronda
						$employees = new Model_DbTable_Decisions_Hr_Employees();
						for ($round_number = 1; $round_number <= $this->_round_number; $round_number++){
							$factory_number = $this->_region[$factory['factory_number']];
							

							$employees_dec= $employees->getDecision($this->_game_id, $this->_company_id, $round_number);
							$employees_factory = $employees_dec['factory_number_'.$factory_number];
							
								//echo ("<br> ******************* STAFF TRAINING RONDA ".$round_number." C1".$employees_factory['category_1']);
							$staff+=$employees_factory['category_1'];
						
							
								//echo ("<br> ******************* STAFF TRAINING RONDA ".$round_number." C2 ".$employees_factory['category_2']);
							$staff+=$employees_factory['category_2'];
						
							
								//echo ("<br> ******************* STAFF TRAINING RONDA ".$round_number." C3 ".$employees_factory['category_3']);
							$staff+=$employees_factory['category_3'];
						
							
								//echo ("<br> ******************* STAFF TRAINING RONDA ".$round_number." C4 ".$employees_factory['category_4']);
							$staff+=$employees_factory['category_4'];
						
								//echo ("<br> ******************* STAFF TRAINING RONDA ".$round_number." IGUAL A ".$staff);
						}


						$hired_percentage=0.01*$this->_recruitmentPercentage[$factory['factory_number']];;
						$staff=round($staff*$hired_percentage);
					echo("<br>Staff Training: ".$staff."<br/>");
	//				}
					$cost=$staff*$employee_cost;
					$result+=$cost;
				}	
			}
			return $result;
		}
		function getHrWagesCost(){
			$result=0;
			$i=1;
			foreach ($this->_factory as $factory){
				if ($this->isFactoryNew($this->_game_id, $this->_company_id, $this->_region[$factory['factory_number']])==false){
				
				$i++;
				$employee_cost=$this->_core->_games->getHrStaffCost($this->_game_id, $this->_round_number, $this->_region[$factory['factory_number']], 'wages_cost');
//	QUITADOS LOS ISSET PORQUE CON UNA EXTENSI�N EN LA PRIMERA F�BRICA, PON�A ESE N�MERO DE EMPLEADOS EN TODAS (NO VOLV�A A ENTRAR EN EL BUCLE): �POR QU� SE PUSO ESA CONDICI�N? 
//				if (! isset ($staff)){
					// $staff=$this->_core->_games->getOrganizationParam($this->_game_id, 'production_workers');
					// $staff+=$this->_core->_games->getOrganizationParam($this->_game_id, 'packaging_workers');
					// $staff+=$this->_core->_games->getOrganizationParam($this->_game_id, 'quality_workers');
					// $staff+=$this->_core->_games->getOrganizationParam($this->_game_id, 'maintenance_workers');
					// $staff+=$this->_core->_games->getExtensionEmployees($this->_game_id, $this->_round_number, $this->_company_id, $factory['factory_number']);
					//echo("<br/>  NUEVA PLANTILLA ".($this->_core->_games->getExtensionEmployees($this->_game_id, $this->_round_number, $this->_company_id, $factory['factory_number'])));
					
					//JESUS Sumo los empleados contratados cada ronda
					$employees = new Model_DbTable_Decisions_Hr_Employees();
					for ($round_number = 1; $round_number <= $this->_round_number; $round_number++){
						$factory_number = $this->_region[$factory['factory_number']];
							

							$employees_dec= $employees->getDecision($this->_game_id, $this->_company_id, $round_number);
							$employees_factory = $employees_dec['factory_number_'.$factory_number];
							
								// echo ("<br> ******************* STAFF TRAINING RONDA ".$round_number." C1".$employees_factory['category_1']);
							$staff+=$employees_factory['category_1'];
						
							
								// echo ("<br> ******************* STAFF TRAINING RONDA ".$round_number." C2 ".$employees_factory['category_2']);
							$staff+=$employees_factory['category_2'];
						
							
								// echo ("<br> ******************* STAFF TRAINING RONDA ".$round_number." C3 ".$employees_factory['category_3']);
							$staff+=$employees_factory['category_3'];
						
							
								// echo ("<br> ******************* STAFF TRAINING RONDA ".$round_number." C4 ".$employees_factory['category_4']);
							$staff+=$employees_factory['category_4'];
						
							// echo ("<br> ******************* STAFF TRAINING RONDA ".$round_number." IGUAL A ".$staff);
						}

					$hired_percentage=0.01*$this->_recruitmentPercentage[$factory['factory_number']];
					$wages=$this->_sal;
					$staff=round($staff*$hired_percentage*$wages);
					echo("<br>Staff Wages: ".$staff."<br/>");
//				}
				$cost=$staff*$employee_cost;
				$result+=$cost;
			}
		}
			return $result;
		}

		function getHrDismissalsCost(){
			$result=0;
			$i=1;
			foreach ($this->_factory as $factory){
				if ($this->isFactoryNew($this->_game_id, $this->_company_id, $this->_region[$factory['factory_number']])==false){
				echo("<br>Pass ".$i."<br/>");
				$i++;
				$employee_cost=$this->_core->_games->getHrStaffCost($this->_game_id, $this->_round_number, $this->_region[$factory['factory_number']], 'dismissals_cost');
//	QUITADOS LOS ISSET PORQUE CON UNA EXTENSI�N EN LA PRIMERA F�BRICA, PON�A ESE N�MERO DE EMPLEADOS EN TODAS (NO VOLV�A A ENTRAR EN EL BUCLE): �POR QU� SE PUSO ESA CONDICI�N? 
//				if (! isset ($staff)){
					// $staff=$this->_core->_games->getOrganizationParam($this->_game_id, 'production_workers');
					// $staff+=$this->_core->_games->getOrganizationParam($this->_game_id, 'packaging_workers');
					// $staff+=$this->_core->_games->getOrganizationParam($this->_game_id, 'quality_workers');
					// $staff+=$this->_core->_games->getOrganizationParam($this->_game_id, 'maintenance_workers');
					// $staff+=$this->_core->_games->getExtensionEmployees($this->_game_id, $this->_round_number, $this->_company_id, $factory['factory_number']);
					//echo("<br/>  NUEVA PLANTILLA ".($this->_core->_games->getExtensionEmployees($this->_game_id, $this->_round_number, $this->_company_id, $factory['factory_number'])));
					
					//JESUS Sumo los empleados contratados cada ronda
					$employees = new Model_DbTable_Decisions_Hr_Employees();
						$factory_number = $this->_region[$factory['factory_number']];
						$employees_dec= $employees->getDecision($this->_game_id, $this->_company_id, $this->_round_number);
						$employees_factory = $employees_dec['factory_number_'.$factory_number];
						
						if ($employees_factory['category_1']<0){
						$staff+=(-$employees_factory['category_1']);}
						if ($employees_factory['category_2']<0){
						$staff+=(-$employees_factory['category_2']);}
						if ($employees_factory['category_3']<0){
						$staff+=(-$employees_factory['category_3']);}
						if ($employees_factory['category_4']<0){
						$staff+=(-$employees_factory['category_4']);}

					

					$hired_percentage=0.01*$this->_recruitmentPercentage[$factory['factory_number']];
					$wages=$this->_sal;
					$staff=round($staff*$hired_percentage*$wages);
					echo("<br>Staff Dismissals: ".$staff."<br/>");
//				}


				$cost=$staff*$employee_cost;
				$result+=$cost;
					echo("<br>********** EN COMPANY, COSTE EMPLEADO DESPEDIDO: ".$employee_cost."<br/>");
					echo("<br>********** EN COMPANY, COSTE TOTAL DESPIDOS : ".$result."<br/>");
			}
		}
			return $result;
		}
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		function getPayTerm($channel_number){
			return $this->_suppliers_payterms['channel_'.$channel_number];
		}
		
		function getIncomeTerm($channel_number){
			if (! isset ($this->_income_term[$channel_number])){
				$this->_income_term[$channel_number]=$this->_core->_games->getMkChannelIncomeTerms($this->_game_id, $channel_number);
			}
			return $this->_income_term[$channel_number];
		}
		
		function getFiDebtCostsSt(){
			$interest_table=new Model_DbTable_Games_Param_Fi_Cashflow();
			$interest_rate=$interest_table->getShortTermDebtRateInGame($this->_game_id);
			$overdraft_table=new Model_DbTable_Games_Evolution_Fi_Overdraft();
			$overdraft=$overdraft_table->getOverdraft($this->_game_id, $this->_company_id, ($this->_round_number-1));
			$overdraft_interest=$overdraft*($interest_rate*0.01);
			return $overdraft_interest; //return 0;
		}
		
		function getFiDebtCostsLt(){
			$decision_amount=new Model_DbTable_Decisions_Fi_Amount();
			$decision_term=new Model_DbTable_Decisions_Fi_Term();
			$decision_interest=new Model_DbTable_Decisions_Fi_Interest();
			for ($round_number=1; $round_number<=$this->_round_number; $round_number++){
				$amount=$decision_amount->getDecision($this->_game_id, $this->_company_id, $round_number);
				$term=$decision_term->getDecision($this->_game_id, $this->_company_id, $round_number);
				$interest_aux=$decision_interest->getDecision($this->_game_id, $this->_company_id, $round_number);
				echo("<br>EXTRACTION: GAMEID=".$this->_game_id."/COMPANYID=".$this->_company_id."/RDNO=".$round_number."<br>");
				$interest=$interest_aux['interest'];
				//VERO
				$term_aux = $this->_round_number-$round_number;
				//if (($this->_round_number-$round_number)<$term['term']){
				if ($term_aux<$term['term']){
					$annual_quota= ($amount['amount']*($interest*0.01))/(1-pow((1+$interest*0.01),(-$term['term'])));
					//$costs=($amount['amount']/$term['term'])*($interest*0.01);
					$principal_quota = 0;
					$paid_quota=0;
					for ($round_number_aux = 0; $round_number_aux<=$term_aux; $round_number_aux++){
						$costs= $interest*0.01*($amount['amount']-$paid_quota);
						$principal_quota= $annual_quota-$costs;
						$paid_quota+=$principal_quota;
					}
				}
				//VERO
				else{
					$costs=0;
				}
				$lt_costs+=$costs;
				echo("<br>AMOUNT: ".$amount['amount'].", TERM: ".$term['term'].", INTEREST: ".$interest_aux['interest'].", LTC: ".$lt_costs."<br>");
			}
			return $lt_costs;
		}
		//VERO
		function getCreditPayment(){
			$decision_amount=new Model_DbTable_Decisions_Fi_Amount();
			$decision_term=new Model_DbTable_Decisions_Fi_Term();
			$decision_interest=new Model_DbTable_Decisions_Fi_Interest();
			for ($round_number=1; $round_number<=$this->_round_number; $round_number++){
				$amount=$decision_amount->getDecision($this->_game_id, $this->_company_id, $round_number);
				$term=$decision_term->getDecision($this->_game_id, $this->_company_id, $round_number);
				$interest_aux=$decision_interest->getDecision($this->_game_id, $this->_company_id, $round_number);
				$interest=$interest_aux['interest'];
				$term_aux = $this->_round_number-$round_number;
				if ($term_aux<$term['term']){
					//$payment=($amount['amount']/$term['term']);
					$annual_quota= ($amount['amount']*($interest*0.01))/(1-pow((1+$interest*0.01),(-$term['term'])));
					$principal_quota = 0;
					$paid_quota=0;
					for ($round_number_aux = 0; $round_number_aux<=$term_aux; $round_number_aux++){
						$costs= $interest*0.01*($amount['amount']-$paid_quota);
						$payment= $annual_quota-$costs;
						$paid_quota+=$payment;
					}
				}
				else{
					$payment=0;
				}
				$cr_payment+=$payment;
			}
			return $cr_payment;
		}
		//Devuelve el valor ganado o perdido en las inversiones (totales) y guarda en la tabla games_evolution_fi_investment los intereses producidos en la ronda actual por cada inversi�n
		function getInvestmentInterest(){
			$decision_investment=new Model_DbTable_Decisions_Fi_Investment();
			$game=new Model_DbTable_Games();
			$n_investment = $game->getNumberOfInvestments($this->_game_id);
			$outcomesPrevious=new Model_DbTable_Outcomes_In_InvestmentUnitary();
			$investment_param=new Model_DbTable_Games_Param_Markets_InvestmentsParams();
			$evolution=new Model_DbTable_Games_Evolution_Fi_Investment();
			$result_positive=0;
			$result_negative=0;
			for($investment_number=1; $investment_number<=$n_investment; $investment_number++){
				$result_final=0;
				$evolutionInterest=0;
				for ($round_number=2; $round_number<=$this->_round_number; $round_number++){
					$investments=$decision_investment->getDecision($this->_game_id, $this->_company_id, $round_number);
					$amount= $investments['investment_number_'.$investment_number]['amount'];
					$term= $investments['investment_number_'.$investment_number]['term'];
					echo("<br>EXTRACTION INVESTMENT: GAMEID=".$this->_game_id."/COMPANYID=".$this->_company_id."/RDNO=".$round_number."/CNT=".$amount."/PLAZO=".$term."<br>");
					$term_aux = $this->_round_number-$round_number;
					if($term_aux < $term ){
						for ($round_number_aux = 0; $round_number_aux<=$term_aux; $round_number_aux++){
							$interest=$evolution->getInvestment($this->_game_id, $round_number, $investment_number);
							if($term == 1 Or ($term_aux==0 And is_null($outcomesPrevious->getInvestment($this->_game_id, $this->_company_id, $round_number, $investment_number))==true)){
								$amountResult =$interest*$amount;
								$result_final+=$amountResult;
							}elseif($round_number_aux==$term_aux){
								$amountResult =$interest*($evolutionInterest+$amount);
								$result_final+=$amountResult;
							}else{
								$evolutionInterest+=$outcomesPrevious->getInvestment($this->_game_id, $this->_company_id, $round_number_aux+$round_number, $investment_number);
							}
						}
					}
				}
				$outcomesPrevious->setInvestment($this->_game_id, $this->_company_id, $this->_round_number, $investment_number, $result_final);
				echo("<br>Interest: ".$interest.", Result: ".$result_final."<br>");
				if($result_final>0 ){
					$result_positive+=$result_final;
				}else{
					$result_negative+=$result_final;
				}
			}

			return array('fi_investment_losses'=>$result_negative, 'fi_investment_earnings'=>$result_positive);
		}

		function getInvestmentBalanceSheet(){
			$decision_investment=new Model_DbTable_Decisions_Fi_Investment();
			$game=new Model_DbTable_Games();
			$n_investment = $game->getNumberOfInvestments($this->_game_id);
			$outcomes=new Model_DbTable_Outcomes_In_InvestmentUnitary();
			$investment_param=new Model_DbTable_Games_Param_Markets_InvestmentsParams();
			$evolution=new Model_DbTable_Games_Evolution_Fi_Investment();
			for($investment_number=1; $investment_number<=$n_investment; $investment_number++){
				$result=0;
				$result_final=0;
				for ($round_number=2; $round_number<=$this->_round_number; $round_number++){
					$investments=$decision_investment->getDecision($this->_game_id, $this->_company_id, $round_number);
					$term_aux = $this->_round_number-$round_number;
					$term= $investments['investment_number_'.$investment_number]['term'];
					$amount= $investments['investment_number_'.$investment_number]['amount'];
					if($term_aux < $term ){
						$result=$outcomes->getInvestment($this->_game_id, $this->_company_id, $round_number, $investment_number);
						if($term == 1){
							$liquid_assets +=$result;
						}elseif($term_aux==0){
							$start_assets +=(-$amount);
							$activeInvestment+=$result+$amount+$result_final;
						}elseif($term_aux==$term-1){
							$result_final=$this->getAllResultsByInvestment($this->_round_number, $round_number, $investment_number);
							$liquid_assets +=($amount+$result_final);
						}else{
							$result_final=$this->getAllResultsByInvestment($this->_round_number, $round_number, $investment_number);
							$activeInvestment+=($result_final+$amount);
						}
					}
				}
			}
			echo("<br>Company: ".$this->_company_id.", +
				: ".$activeInvestment.", Liquid assets: ".$liquid_assets."<br>");
			return array('start_assets'=>$start_assets, 'liquid_assets'=>$liquid_assets, 'investment_assets'=>$activeInvestment);
		}

		function getAllResultsByInvestment($round_number_act, $round_number_decision, $investment_number){
			$outcomes=new Model_DbTable_Outcomes_In_InvestmentUnitary();
			for($round_number=$round_number_decision;$round_number<=$round_number_act; $round_number++){
				$result+=$outcomes->getInvestment($this->_game_id, $this->_company_id, $round_number, $investment_number);
			}
			return $result;
		}

		//VERO
		
		function getTotalAmortization(){
			$amortization=new Model_DbTable_Games_Evolution_Am_Amortization();
			for($round_number=1; $round_number<=$this->_round_number; $round_number++){
				$amountCons=$amortization->getConsAmount($this->_game_id, $this->_company_id, $round_number);
				$termCons=$amortization->getConsTerm($this->_game_id, $this->_company_id, $round_number);
				$totalAmount+=($this->_round_number-$round_number+1)*($amountCons/$termCons);
				$amountExt=$amortization->getExtAmount($this->_game_id, $this->_company_id, $round_number);
				$termExt=$amortization->getExtTerm($this->_game_id, $this->_company_id, $round_number);
				$totalAmount+=($this->_round_number-$round_number+1)*($amountExt/$termExt);
			}
			return $totalAmount;
		}
		
		function getYearAmortization(){
			$amortization=new Model_DbTable_Games_Evolution_Am_Amortization();
			for($round_number=1; $round_number<=$this->_round_number; $round_number++){
				$amountCons=$amortization->getConsAmount($this->_game_id, $this->_company_id, $round_number);
				$termCons=$amortization->getConsTerm($this->_game_id, $this->_company_id, $round_number);
				$totalAmount+=($amountCons/$termCons);
				$amountExt=$amortization->getExtAmount($this->_game_id, $this->_company_id, $round_number);
				$termExt=$amortization->getExtTerm($this->_game_id, $this->_company_id, $round_number);
				$totalAmount+=($amountExt/$termExt);
			}
			return $totalAmount;	
		}
		
		function getStockValue($product_number, $region_number, $channel_number){
			$stocks=new Model_DbTable_Games_Evolution_St_Stocks();
			$stock_value=0;
			for($round_number=1; $round_number<=$this->_round_number; $round_number++){
				$round_stock=$stocks->getStockClasified($this->_game_id, $this->_company_id, $round_number, $product_number, $region_number, $channel_number);
				$cost_stock=$stocks->getStockPrCost($this->_game_id, $this->_company_id, $round_number, $product_number, $region_number, $channel_number);
				$stock_value_aux=($round_stock*$cost_stock);
				$stock_value+=$stock_value_aux;
				//echo("<br/>Round stock = ".$round_stock." y Cost Stock = ".$cost_stock.", con un valor de stock de ".$stock_value."<br/>");
			}
			echo("CHECK POINT 4: stock_value = ".$stock_value."<br/>");
			return $stock_value;
		}
		//En principio los clientes pagarian al contado y por tanto nunca tendrismos deudores.
		function getTradeDebtors($channel_number){
			$incomes=$this->getIncomes($channel_number);
			$income_terms=$this->getIncomeTerm($channel_number);
			$debtors=$incomes*($income_terms/12);
			return $debtors;
		}
		
		function getAcumulatedCosts(){
			$outcomes_data=new Model_DbTable_Outcomes();
			for ($round_number=1; $round_number<=$this->_round_number; $round_number++){
				$total+=$outcomes_data->getYearCosts($this->_game_id, $round_number, $this->_company_id);
			}
			return $total;
		}
		
		function getStartingCash(){	
			$assets=$this->getPastLiquidAssets();
			$past_debtors=$this->getPastDebtors();
			$past_creditors=$this->getPastCreditors();
			$dividends=$this->getPaidDividends();
			$credit_amount=$this->getCreditAmount();
			$investment_amount=$this->getInvestmentBalanceSheet();
			//VERO
			$starting_cash=$assets+$past_debtors-$past_creditors-$dividends+$credit_amount+$investment_amount['start_assets'];
			//VERO
			return $starting_cash;
		}
		
		function getPastLiquidAssets(){
			return $this->_past_assets;	
		}
		
		function getPastDebtors(){
			return $this->_past_debtors;
		}
		
		function getPastCreditors(){
			return $this->_past_creditors;
		}
		
		function getPaidDividends(){
			//$year_result=$this->_core->_games->getYearResult($this->_game_id, $this->_round_number, $this->_company_id);
			//20130501: Los dividendos se refieren al a�o anterior
			if ($this->_round_number>1) {
				$past_year_result=$this->_core->_games->getYearResult($this->_game_id, $this->_round_number-1, $this->_company_id);
				if ($past_year_result>0) {
					if ($this->_finance_payout>0 && $this->_finance_payout<=100){
						$paid_dividends=($this->_finance_payout)*0.01*$past_year_result;
					} else	if ($this->_finance_payout>100){
						$paid_dividends=$past_year_result;
					} else {
						$paid_dividends=0;
					}
				} else {
					$paid_dividends=0;
				}
			} else {
				$paid_dividends=0;	
			}
			return $paid_dividends;
		}
		
		function getDividendsRetributed(){
			$finance_data=new Model_DbTable_Decisions_Finance();
			$total=0;
			for ($round_number=1; $round_number<=$this->_round_number; $round_number++){
				$round_payout=$finance_data->getDividends($this->_game_id, $this->_company_id, $round_number);
				//20130501: Los dividendos se refieren al a�o anterior
				$round_result=$this->_core->_games->getYearResult($this->_game_id, $round_number-1, $this->_company_id);
				$total_aux=($round_payout*$round_result);
				$total+=$total_aux;
			}
			return $total;
		}
		
		function getCreditAmount(){
			return $this->_finance_amount;
		}
		
		function getLiquidAssets(){
			if ($this->_round_number == 1){
				$starting_cash=$this->_core->_games->getFiCashflowParameter($this->_game_id, 'starting_cash');
				//$prev_stock_value=0;
			}
			else {
				$starting_cash=$this->getStartingCash();
				//$prev_stock_value=getPrevStockValue($this->game_id, $this->round_number, $this->company_id)
			}
			$overdraft_table=new Model_DbTable_Games_Evolution_Fi_Overdraft();
			$overdraft_payment=$overdraft_table->getOverdraft($this->_game_id, $this->_company_id, ($this->_round_number-1));
			$credit_payment=$this->getCreditPayment();
			//echo("<br/> ");echo("<br/> ");
			//var_dump($credit_payment);
			//echo("<br/> ");echo("<br/> "); 
			$year_result=$this->getYearResult();
			$year_amortization=$this->getYearAmortization();
			$year_tiedup_investments=$this->getTiedUpInvestment();
			if($this->_round_number==1){
				$year_investments=0;
			}else{
				$year_investments=$this->getInvestmentBalanceSheet();
			}	
					
			$stock_value=0;
			foreach ($this->_core->_products as $product){
				foreach ($this->_core->_regions as $region){
					foreach ($this->_core->_channels as $channel){
						$stock_value+=$this->getStockValue($product->getProductNumber(), $region->getRegionNumber(), $channel->getChannelNumber());
						//echo("En getLiquidAssets. Stock acumulado: ".$stock."<br/>");
					}
				}
			}
			if($this->_round_number>1){	
				$variation_stock_value=$stock_value-$this->getPrevStockValue($this->_game_id, $this->_round_number,$this->_company_id);
			} else {
				$variation_stock_value=$stock_value;
			}
			echo("<br/>CHECK PREV STOCK VALUE = ".$this->getPrevStockValue($this->_game_id, $this->_round_number,$this->_company_id)."<br/>");
			echo("<br/>CHECK VARIATION STOCK VALUE = ".$variation_stock_value."<br/>");
			foreach ($this->_core->_channels as $channel){
				$creditors+=$this->getComercialCreditors($channel->getChannelNumber());
				$debtors+=$this->getTradeDebtors($channel->getChannelNumber());
			}
			$liquid_asset=$starting_cash-$credit_payment-$overdraft_payment+$year_result-$variation_stock_value+$year_amortization-$debtors+$creditors-$year_tiedup_investments+$year_investments['liquid_assets'];//-$stock;
			echo("<br/><br/> ");
			echo("Liquid Asset: ".$liquid_asset);
			echo("<br/><br/>");
			echo("INPUTS: ".$starting_cash." | ".$credit_payment." | ".$overdraft_payment." | ".$year_result." | ".$year_amortization." | ".$debtors." | ".$creditors." | ".$year_tiedup_investments." | ".$stock." | ".$year_investments['liquid_assets']);
			//var_dump($credit_payment);
			echo("<br/><br/> "); 
			if($liquid_asset<0){
				$interest_table=new Model_DbTable_Games_Param_Fi_Cashflow();
				$interest_rate=$interest_table->getShortTermDebtRateInGame($this->_game_id);
				$overdraft=(($liquid_asset)*(-1));
				$this->SetOverdraft($overdraft);
				$liquid_asset=0;
			}
			return $liquid_asset;
		}
		
		
		function getPrevStockValue($game_id, $round_number, $company_id){
			$stock=new Model_DbTable_Outcomes_Bs_BalanceSheet();			
			$prev_stock_value=$stock->getCompanyStockValue($game_id, $round_number-1, $company_id);
			return $prev_stock_value;
		}
		
		function SetOverdraft($overdraft) {
			$overdraft_amount=new Model_DbTable_Games_Evolution_Fi_Overdraft();
			$boolean=$overdraft_amount->getOverdraftRow($this->_game_id, $this->_company_id, $this->_round_number);
			echo("<br/> EQUIPO ".$this->_company_id."<br/> OVERDRAFT CONTROL: ");
			if ($boolean==null){
				echo(" INSERT ");
				$overdraft_amount->insert(array('game_id'=>$this->_game_id, 'company_id'=>$this->_company_id, 'round_number'=>$this->_round_number, 'overdraft'=>$overdraft));
			}
			else {
				echo(" UPDATE ");
				$overdraft_amount->update(array('overdraft'=>$overdraft), 'game_id = '.$this->_game_id.' AND company_id = '.$this->_company_id.' AND round_number = '.$this->_round_number);
			}
		}
		
		function getCapital(){
			$capital=$this->_core->_games->getFiCashflowParameter($this->_game_id, 'starting_cash');
			return $capital;
		}
		
		function getReserves(){
			$reserves_past=$this->_past_reserves;
			$dividends=$this->getPaidDividends();
			$year_result_past=$this->_past_year_result;
			$reserves=$reserves_past+($year_result_past-$dividends);
			return $reserves;
		}
		
		function getYearResult(){
			$result=$this->_core->_games->getYearResult($this->_game_id, $this->_round_number, $this->_company_id);
			return $result;
		}
		
		function getLongTermDebts(){
			$decision_amount=new Model_DbTable_Decisions_Fi_Amount();
			$decision_term=new Model_DbTable_Decisions_Fi_Term();
			$decision_interest=new Model_DbTable_Decisions_Fi_Interest();
			for ($round_number=1; $round_number<=$this->_round_number; $round_number++){
				$amount=$decision_amount->getDecision($this->_game_id, $this->_company_id, $round_number);
				$term=$decision_term->getDecision($this->_game_id, $this->_company_id, $round_number);
				//VERO
				$interest_aux=$decision_interest->getDecision($this->_game_id, $this->_company_id, $round_number);
				$interest=$interest_aux['interest'];
				if (($this->_round_number-$round_number+2)<$term['term']){
					//$debts=($term['term']-($this->_round_number-$round_number+2))*($amount['amount']/$term['term']);
					$annual_quota= ($amount['amount']*($interest*0.01))/(1-pow((1+$interest*0.01),(-$term['term'])));
					$principal_quota = 0;
					$paid_quota=0;
					$term_aux = $this->_round_number-$round_number;
					$debts_lt=$amount['amount'];
					//Ponemos term_aux+1 para tener en cuenta la siguiente ronda (deuda a corto plazo)
					for ($round_number_aux = 0; $round_number_aux<=($term_aux+1); $round_number_aux++){
						$costs= $interest*0.01*($amount['amount']-$paid_quota);
						$principal_quota= $annual_quota-$costs;
						$paid_quota+=$principal_quota;
						$debts_lt-=$principal_quota;
					}

				}		
				//VERO
				else{
					$debts_lt=0;
				}
				$long_term_debts+=$debts_lt;
			}
			return $long_term_debts;
		}
		
		function getShortTermDebts(){
			$decision_amount=new Model_DbTable_Decisions_Fi_Amount();
			$decision_term=new Model_DbTable_Decisions_Fi_Term();
			$decision_interest=new Model_DbTable_Decisions_Fi_Interest();
			$bank_overdraft=new Model_DbTable_Games_Evolution_Fi_Overdraft();
			for ($round_number=1; $round_number<=$this->_round_number; $round_number++){
				$amount=$decision_amount->getDecision($this->_game_id, $this->_company_id, $round_number);
				$term=$decision_term->getDecision($this->_game_id, $this->_company_id, $round_number);
				//VERO
				$interest_aux=$decision_interest->getDecision($this->_game_id, $this->_company_id, $round_number);
				$interest=$interest_aux['interest'];
				if (($this->_round_number-$round_number+1)<$term['term']){
					//$debts=($amount['amount']/$term['term']);
					$annual_quota= ($amount['amount']*($interest*0.01))/(1-pow((1+$interest*0.01),(-$term['term'])));
					$principal_quota = 0;
					$paid_quota=0;
					$term_aux = $this->_round_number-$round_number;
					for ($round_number_aux = 0; $round_number_aux<=($term_aux+1); $round_number_aux++){
						$costs= $interest*0.01*($amount['amount']-$paid_quota);
						$principal_quota= $annual_quota-$costs;
						$paid_quota+=$principal_quota;
						//La deuda a corto plazo es la que se va a pagar en la siguiente ronda
						if ($round_number_aux==($term_aux+1)){
							$debts_st=$principal_quota;
						}
					}
				}
					
				//VERO
				else{
					$debts_st=0;
				}
				$short_term_debts+=$debts_st;
			}
			$overdraft=$bank_overdraft->getOverdraft($this->_game_id, $this->_company_id, $this->_round_number);
			$short_term_debts+=$overdraft;
			return $short_term_debts;
		}
		
		function getComercialCreditors($channel_number){
			$payterm=$this->getPayTerm($channel_number);
			foreach ($this->_core->_regions as $region){
				foreach ($this->_core->_products as $product){
					$su_costs+=$this->getPrRawMaterialsCost($channel_number, $region->getRegionNumber(),$product->getProductNumber());
					
				}
			}
			$creditors=$su_costs*($payterm/12);
			return $creditors;
		}
		
		//Devuelve unidades producidas, para pr_var_cost
		function getProductionUnits($product_number, $channel_number, $region_number){			
			$outcomes=new Model_DbTable_Outcomes_Pr_Units();										
			$result=$outcomes->getOutcomes($this->_game_id, $this->_round_number);
			$units=$result['company_'.$this->_company_id]
						  ['product_'.$product_number]
						  ['region_'.$region_number]
						  ['channel_'.$channel_number];
			return $units;	
		}
		
		//Revisar el c�lculo del inmovilizado, porque ahora mismo calcula el valor de f�brica y ampliaciones en la ronda actual, no el que se pag� por ella.
		//Solucionado al hacer referencia a la nueva funci�n getOriginalExtensionCost, que calcula el coste original (getExtensionCost saca el valor actualizado de la f�brica y ampliaciones para el 20% de costes fijos)
		function getTiedUp(){
			$consCost=$this->getConstructionCost();
			$extCost=$this->getOriginalExtensionCost();
			foreach ($this->_factory as $factory){
				for ($round_number=1; $round_number<=$this->_round_number; $round_number++){
					$extTiedUp+=$extCost['factory_number_'.$factory['factory_number']][$round_number];
					$consTiedUp+=$consCost['factory_number_'.$factory['factory_number']][$round_number];
					echo("<br/>extTiedUp: ".$extTiedUp."<br/>");
					echo("<br/>consTiedUp: ".$consTiedUp."<br/>");
				}
			}
			$tiedUp=$extTiedUp+$consTiedUp;
			return $tiedUp;
		}
		function getTiedUpInvestment(){
			$consCost=$this->getConstructionCost();
			$extCost=$this->getOriginalExtensionCost();
			foreach ($this->_factory as $factory){
				$extTiedUp+=$extCost['factory_number_'.$factory['factory_number']][$this->_round_number];
				$consTiedUp+=$consCost['factory_number_'.$factory['factory_number']][$this->_round_number];
			}
			$tiedUp=$extTiedUp+$consTiedUp;
			return $tiedUp;
		}
		
		//Para valoracion ponderada del stock
		function getProductCostStock($round_number, $product_number){			
			$counter=0;
			foreach ($this->_factory as $factory){
				$aux_created=$this->_core->_games->getRoundFactoryCreated($this->_game_id, $this->_company_id);
				$round_created=$aux_created['factory_number_'.$factory['factory_number']];
				if(($round_number>$round_created) || ($round_created==1)){
					$cost_aux+=$this->_core->_games->getProductionCost($this->_game_id, $round_number, $this->_region[$factory['factory_number']], 'unit', $product_number);
				}
				else{
					$cost_aux+=0;
					$counter--;
				}
				$counter++;
			}
			$cost=($cost_aux/$counter);
			return $cost;		
		}
		
		function getConstructionCost(){
			foreach ($this->_factory as $factory) {
				$aux_created=$this->_core->_games->getRoundFactoryCreated($this->_game_id, $this->_company_id);
				$round_created=$aux_created['factory_number_'.$factory['factory_number']];
				//echo("<br/> CREATED: ".$round_created);
				for ($round = 1; $round <= $this->_round_number; $round++) {
					if(($round==$round_created)){
						$factory_cost=$this->_core->_games->getProductionCost($this->_game_id, $round, $this->_region[$factory['factory_number']], 'fixed');
					}
					else{
						$factory_cost=0;
					}
						$cost['factory_number_'.$factory['factory_number']][$round]=$factory_cost;
				}
			}
			//echo("<br/> CONSTRUCTION COST: ");
			//var_dump($cost);
			return $cost;		
		}
		
		//Devuelve coste original de cada ampliacion
		function getOriginalExtensionCost(){			
			$capacity=new Model_DbTable_Decisions_Pr_Capacity();
			foreach ($this->_factory as $factory){
				$extension_factory=$capacity->getExtensionWasCreated($this->_game_id, $this->_company_id, $factory['factory_number']);				
				//echo("<br/> Extension Factory: ".var_dump($extension_factory));
				$cost['factory_number_'.$factory['factory_number']][1] = 0; //Inciamos a 0 la ronda 1, donde no hay rondas, para que no peten el resto de rutinas
				for ($round = 2; $round <= $this->_round_number; $round++) { //Iniciamos en 2 porque no hay ampliaciones en la ronda 1
					
					//$round_created[$round]=$extension_factory['factory_number_'.$factory['factory_number']]['round_number_created_'.$round];
					//echo("<br/> CREATED: ".$round_created[$round]);
					//echo("<br/> ROUND (VUELTA BUCLE 1): ".$round);
					//for ($round_aux = 1; $round_aux <= $round; $round_aux++) {
						//echo("<br/> ROUND AUX (VUELTA BUCLE 2): ".$round_aux);
						//if($round>=$round_created[$round_aux]){
							//$extension[$round]=$extension_factory['factory_number_'.$factory['factory_number']]['capacity_'.$round_aux];
							//echo("<br/> EXTENSION 1: ");//.$extension[$round]);
							//var_dump($extension[$round]);
						//}
						//else {
						//	$extension[$round_aux]=0;
						//}
						$extension[$round]=$extension_factory['factory_number_'.$factory['factory_number']]['capacity_'.$round];//_aux];
						$cost_aux=$this->_core->_games->getProductionCost($this->_game_id, $round, $this->_region[$factory['factory_number']], 'fixed');
						$machines=$this->_core->_games->getOrganizationParam($this->_game_id, 'machines');
						//echo("<br/> EXTENSION 2: ".$extension[$round]);
						////echo("<br/> ROUND 2 (VUELTA BUCLE 1): ".$round);
						//echo("<br/>");
						$cost['factory_number_'.$factory['factory_number']][$round]=($extension[$round]*($cost_aux/$machines))*0.7;											
					echo("<br/>");
				}
			}
			echo("<br/> EXTENSION ORIGINAL COST: ");
			//var_dump($cost);
			echo("<br/>");			
			return $cost;			
		}
		
		//Devuelve coste de cada ampliacion contemplando las variaciones de precio de la regi�n
		function getExtensionCost(){			
			$capacity=new Model_DbTable_Decisions_Pr_Capacity();
			foreach ($this->_factory as $factory){
				$extension_factory=$capacity->getExtensionWasCreated($this->_game_id, $this->_company_id, $factory['factory_number']);				
				$cost['factory_number_'.$factory['factory_number']][1] = 0; //Inciamos a 0 la ronda 1, donde no hay rondas, para que no peten el resto de rutinas
				for ($round = 2; $round <= $this->_round_number; $round++) { //Iniciamos en 2 porque no hay ampliaciones en la ronda 1
						$extension[$round]=$extension_factory['factory_number_'.$factory['factory_number']]['capacity_'.$round];//_aux];
						$cost_aux=$this->_core->_games->getProductionCost($this->_game_id, $this->_round_number, $this->_region[$factory['factory_number']], 'fixed');
						$machines=$this->_core->_games->getOrganizationParam($this->_game_id, 'machines');
						$cost['factory_number_'.$factory['factory_number']][$round]=($extension[$round]*($cost_aux/$machines))*0.7;					
					echo("<br/>");
				}
			}
			echo("<br/> EXTENSION UPDATED IPC COST: ");
			//var_dump($cost);
			echo("<br/>");			
			return $cost;			
		}
		
		function setInterestTotal(){			
			$term_table=new Model_DbTable_Decisions_Fi_Term();
			$term_aux=$term_table->getDecision($this->_game_id, $this->_company_id, $this->_round_number);
			$term=$term_aux['term'];	
			$interest_adjustment=$this->_core->_games->getInterestAdjustment($this->_game_id, $this->_round_number, $this->_company_id);
			$interest_rate_aux=$this->_core->_games->getInterestRate($this->_game_id);
			$interest_rate=$interest_rate_aux['term_'.$term];
			$interest_total=$interest_rate+$interest_adjustment;
			$int_table=new Model_DbTable_Decisions_Fi_Interest();
			$int_table->setInterestTotal($this->_game_id, $this->_company_id, $this->_round_number, $interest_total);
		}
		
		function getScoreNewProducts(){			
			$budget_table=new Model_DbTable_Decisions_Idi();
			$product_number=1;
			while (isset($this->_idi_newproducts_solicited['idiproduct_'.$product_number])){
				//echo("<br/>NewProducts<br/>");
				//var_dump($this->_idi_newproducts_budget);
				//echo("<br/>NewProducts<br/>");
				$proyectSelection=$this->_idi_newproducts_solicited['idiproduct_'.$product_number];
				$proyect_product_number_aux=$this->_idi_newproducts_number['idiproduct_'.$product_number];
				$proyectBudget_aux=$this->_idi_newproducts_budget_total['idiproduct_'.$product_number];
				$proyectBudget[$proyect_product_number_aux]=$proyectBudget_aux*$proyectSelection;
				$proyect_product_number[$proyect_product_number_aux]=$proyect_product_number_aux*$proyectSelection;
				$ideal[$proyect_product_number_aux]=$this->_core->_games->getIdealInvestment($this->_game_id, $this->_round_number, $proyect_product_number_aux);
				$score[$proyect_product_number_aux]=($proyectBudget[$proyect_product_number_aux]/$ideal[$proyect_product_number_aux]);
				/*echo("<br/> EQUIPO ".$this->_company_id." SELECTION: ");
				var_dump($proyectSelection);
				echo("<br/> EQUIPO ".$this->_company_id." BUDGET: ");
				var_dump($proyectBudget);
				echo("<br/> EQUIPO ".$this->_company_id." PRODUCT NUMBER: ");
				var_dump($proyect_product_number);
				echo("<br/> EQUIPO ".$this->_company_id." IDEAL: ");
				var_dump($ideal);
				echo("<br/> EQUIPO ".$this->_company_id." SCORE: ");
				var_dump($score);*/
				$product_number++;
			}
			
			return $score;
		}
		function processNewReleases($product_number, $success_probability){			
			$threshold=$this->_core->_games->getNewIdiProductsThreshold($this->_game_id, $this->_round_number, $product_number);
			if($success_probability>$threshold){
				$availability=1;
			}
			else {
				$availability=0;
			}
			return $availability;
		}

		function factoryDeteriorationPonderation(){
			$shifts_dec = new Model_DbTable_Decisions_Hr_Shifts();
			$shifts = $shifts_dec->getDecision($this->_game_id, $this->_company_id, $this->_round_number);

			$ponderation = 1;
			if ($shifts==1){
				$ponderation = 1;
			}
			if ($shifts==2){
				$ponderation = 1.05;
			}
			if ($shifts==3){
				$ponderation = 1.2;
			}
			//echo ("<br> LA PONDERACI�N POR EL DETERIORO DE LA F�BRICA ES ". $ponderation."<br>");
			return $ponderation;
		}

		function isFactoryNew($game_id, $company_id, $factory_number){
			$factories = $this->_core->_games->getRoundFactoryCreated($game_id, $company_id);
			$factory_round = $factories['factory_number_'.$factory_number];
			if($factory_round==$this->_round_number && $this->_round_number!= 1){
				return true;
				echo("<br>IS FACTORY NEW true<br/>");
			}
			else {
				echo("<br>IS FACTORY NEW FALSE<br/>");
				return false;
				}


		}

	

	}

?>