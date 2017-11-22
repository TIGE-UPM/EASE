<?php
	class DecisionController extends Zend_Controller_Action {
		public $_controllerTitle= "Decisiones";

		public function preDispatch(){//Se ejecuta antes que ninguna acción
			$this->view->title = $this->_controllerTitle;
			$this->_helper->authHelper();
			
			$front = Zend_Controller_Front::getInstance();
			$this->game=$front->getParam('activeGame');//carga el juego actual antes de procesar ninguna acción
			$this->company=$front->getParam('activeCompany');
			$this->round=$front->getParam('activeRound');
	    }
	
		function indexAction(){
			$this->view->headTitle($this->view->title, 'PREPEND');
			$this->view->controllerName='decision';
			$this->view->actionName="index";
			$users=new Model_DbTable_Users();
			$front = Zend_Controller_Front::getInstance();
			$this->view->user=$front->getParam('loggedUserData');
			
			$role[0]=array('role'=>0, 'name'=>'Director General');
			$role[1]=array('role'=>1, 'name'=>'Director Financiero');
			$role[2]=array('role'=>2, 'name'=>'Director de Producción');
			$role[3]=array('role'=>3, 'name'=>'Director de Recursos Humanos');
			$role[4]=array('role'=>4, 'name'=>'Director de Marketing');
			$this->view->role_profile=$role;
			
			$rounds=new Model_DbTable_Games_Config_GameRounds();
			$active_round=$rounds->getActiveRound($this->game['id']);
			$this->view->active_round=$active_round;
			$this->view->next_rounds=$rounds->getNextRounds($this->game['id']);
			
			$decision=new Model_DbTable_Decisions_Va_Validated();
			if($this->view->active_round['round_number']!=null){
				$result=$decision->getDecision($this->game['id'], $this->company['id'], $this->view->active_round['round_number']);
				$this->view->validate_aux=$result['validated'];
			}
			
			if ($active_round!=null){
				$production=new Model_DbTable_Decisions_Production();
				$marketing=new Model_DbTable_Decisions_Marketing();
				$suppliers=new Model_DbTable_Decisions_Suppliers();
				$humanResources=new Model_DbTable_Decisions_HumanResources();
				$finance=new Model_DbTable_Decisions_Finance();
				$initiatives=new Model_DbTable_Decisions_Initiatives();
				$marketResearches=new Model_DbTable_Decisions_MarketResearches();
				$idi=new Model_DbTable_Decisions_Idi();
				$validate=new Model_DbTable_Decisions_Validate();
				if ($production->existsPrevious()){
					$previous=$production->getActiveRoundDecisionRegistry();
					$this->view->production_decision_date=$previous['date'];
				}
				if ($marketing->existsPrevious()){
					$previous=$marketing->getActiveRoundDecisionRegistry();
					$this->view->marketing_decision_date=$previous['date'];
				}
				if ($suppliers->existsPrevious()){
					$previous=$suppliers->getActiveRoundDecisionRegistry();
					$this->view->suppliers_decision_date=$previous['date'];
				}
				if ($humanResources->existsPrevious()){
					$previous=$humanResources->getActiveRoundDecisionRegistry();
					$this->view->humanResources_decision_date=$previous['date'];
				}
				if ($finance->existsPrevious()){
					$previous=$finance->getActiveRoundDecisionRegistry();
					$this->view->finance_decision_date=$previous['date'];
				}
				if ($initiatives->existsPrevious()){
					$previous=$initiatives->getActiveRoundDecisionRegistry();
					$this->view->initiatives_decision_date=$previous['date'];
				}
				if ($marketResearches->existsPrevious()){
					$previous=$marketResearches->getActiveRoundDecisionRegistry();
					//var_dump($previous['date']); die();
					$this->view->marketResearches_decision_date=$previous['date'];
				}
				if ($idi->existsPrevious()){
					$previous=$idi->getActiveRoundDecisionRegistry();
					//var_dump($previous['date']); die();
					$this->view->idi_decision_date=$previous['date'];
				}
				if ($validate->existsPrevious()){
					$previous=$validate->getActiveRoundDecisionRegistry();
					$this->view->validate_decision_date=$previous['date'];
				}
			}
		}
	
		function historyAction(){
			$this->view->headTitle($this->view->title, 'PREPEND');
			$this->view->controllerName='decision';
			$this->view->actionName="history";
			$rounds=new Model_DbTable_Games_Config_GameRounds();
			$past_rounds=$rounds->getPastRounds($this->game['id']);
			$this->view->rounds=$past_rounds;			
		}
		
		function productionAction(){
			$this->view->title .= " / Dirección de Operaciones.";
			$this->view->headTitle($this->view->title, 'PREPEND');
			$this->view->controllerName='decision';
			$this->view->actionName="production";
			$this->view->round_number = $this->round['round_number'];

			
			$games=new Model_DbTable_Games();
			$region_count= new Model_DbTable_Decisions_Pr_Region();
			
			$regions=$games->getRegions($this->game['id']);
			$products=$games->getProducts($this->game['id']);
			$qualityParams=$games->getQualityParams($this->game['id']);	
			$game_channels=$games->getChannels($this->game['id']);
			$game_media=$games->getMedia($this->game['id']);

			//VERO
			$this->view->channelsO=$game_channels;
			$this->view->channelsD=$game_channels;
			$this->view->regionsO=$regions;
			$this->view->regionsD=$regions; 
			$outcomes=new Model_DbTable_Outcomes();
			$this->view->stocks_units=$outcomes->getStock($this->game['id'],$this->round['round_number']-1);
			//VERO

			
			$this->view->lastFactory=$games->getLastFactory($this->game['id'], $this->company['id']);
			$this->view->roundFactory=$games->getRoundFactoryCreated($this->game['id'], $this->company['id']);
			$this->view->product_availability=$games->getProductsAvailibility($this->game['id'],$this->round['round_number'],$this->company['id']);
			//var_dump($this->view->product_availability);die();
			
			$factories=$games->getFactories($this->game['id'],$this->company['id']);
			$aux=$region_count->countFactories($this->game['id'],$this->company['id']);
			$this->view->numberOfFactories=$aux;
			$this->view->game_factories=$factories;
			$this->view->booleanCreate=0;
				
			$this->view->regions=$regions;
			$this->view->products=$products;
			$this->view->qualityParams=$qualityParams;
			$this->view->channels=$game_channels;
			$this->view->company_id=$this->game['id'];
			$this->view->game_id=$this->company['id'];
			$newproducts=new Model_DbTable_Games_Evolution_Np_NewProducts();
			$this->view->new_product=array();
			$this->view->prev_rnd_new_product=array();
			foreach($products as $product){
				$this->view->new_product[$product['product_number']]=$newproducts->getActualAvailability($this->game['id'], $this->company['id'], $this->round['round_number'], $product['product_number']);
				$this->view->prev_rnd_new_product[$product['product_number']]=$newproducts->getActualAvailability($this->game['id'], $this->company['id'], $this->round['round_number']-1, $product['product_number']);
			}


			//VERO
			$n_products=$games->getNumberOfProductsAvailable($this->game['id'], $this->round['round_number'], $this->company['id']);
			$n_channels=$games->getNumberOfChannels($this->game['id']);
			$n_regions=$games->getNumberOfRegions($this->game['id']);
			
			$this->view->n_channels=$n_channels;
			$this->view->n_regions=$n_regions;
			
			//VERO
			
			$channel_payterms[0]=array('value'=>0, 'descriptor'=>'Inmediato');
			$channel_payterms[1]=array('value'=>1, 'descriptor'=>'Aplazado 1 mes');
			$channel_payterms[2]=array('value'=>2, 'descriptor'=>'Aplazado 2 meses');
			$channel_payterms[3]=array('value'=>3, 'descriptor'=>'Aplazado 3 meses');
			$channel_payterms[4]=array('value'=>4, 'descriptor'=>'Aplazado 4 meses');

			$this->view->channel_payterms=$channel_payterms;

			//VERO
			$n_product=$games->getNumberOfProductsAvailable($this->game['id'], $this->round['round_number'], $this->company['id']);			

			$gameFunctionalityParams = new Model_DbTable_Games_Param_Markets_FunctionalityParams();
			$game_functionality_params_weight=$gameFunctionalityParams->getFunctionalityParamsWeight($this->game['id']);
			$game_functionality_params_name=$gameFunctionalityParams->getFunctionalityParamsName($this->game['id']);
			$n_functionalities=$games->getNumberOfFunctionalities($this->game['id']);
			for($i=1; $i<=$n_functionalities;$i++){
				$a=$i+1;
				$functionalities[$a]= array('value'=>$i, 'descriptor'=>$game_functionality_params_name['functionality_param_number_'.$i]);
			}
			$this->view->functionalities=$functionalities;

			$this->view->game_functionality_params_weight=$game_functionality_params_weight;
			//VERO
			

			$decisions=new Model_DbTable_Decisions_Production();
			$decisions_sup=new Model_DbTable_Decisions_Suppliers();
			//VERO
			$st_decisions=new Model_DbTable_Decisions_Stock();
			$st_outcomes=new Model_DbTable_Outcomes_St_Units();
			//VERO


			$lastDecision=$decisions->getActiveRoundLastDecisionSaved();
			$this->view->regionDecision=$lastDecision['factories'];
			$this->view->qualitiesDecision=$lastDecision['qualities'];
			$this->view->addCapacityDecision=$lastDecision['capacity'];
			
			$this->view->lastFactory=$games->getLastFactory($this->game['id'], $this->company['id']);
			$this->view->roundFactory=$games->getRoundFactoryCreated($this->game['id'], $this->company['id']);
			
			$round_actual=$this->round['round_number'];
			if($round_actual>1){
				$round_previous=$round_actual-1;
				//VERO
				$st_outcomes_prev=$st_outcomes->getStockByCompany($this->game['id'], $round_previous, $this->company['id']);
				$this->view->unitsStockCacheDecision=$st_outcomes_prev;
				//VERO
				$pr_lastDecision_prev=$decisions->getDecisionArray($this->game['id'], $this->company['id'], $round_previous);
				$sup_lastDecision_prev=$decisions_sup->getDecisionArray($this->game['id'], $this->company['id'], $round_previous);
					
				$this->view->unitsDecision=$pr_lastDecision_prev['units'];
				$this->view->addCapacityDecision=0;//$pr_lastDecision_prev['capacity'];
				//var_dump($this->view->addCapacityDecision);die();
				$this->view->numberDecision=$sup_lastDecision_prev['number'];
				$this->view->paytermsDecision=$sup_lastDecision_prev['payterms'];


			}		
		
			
			//si hay decisión sobre este turno guardada se imprime		
			if ($this->getRequest()->isPost()){
				//VERO
				$postData=$this->getRequest()->getPost();
				$st_decisionLeft=$postData['stock']['unitsStockLeft'];
				$st_decisionCache=$postData['unitsStockCache'];
				$st_decisionResult=$postData['stock']['unitsStockRight'];
				$contadorResult=0;
				$contadorCache=0;
				$error= false;
				
				for ($product=1; $product<=$n_products; $product++){
					for ($channel=1; $channel<=$n_channels; $channel++){
						for ($region=1; $region<=$n_regions; $region++){
							/**if($st_decisionLeft['product_'.$product]['channel_'.$channel] ['region_'.$region]  <> "0" AND !is_null($st_decisionLeft['product_'.$product]['channel_'.$channel] ['region_'.$region])){
								$error=true;
							}**/
							$contadorResult+= intval($st_decisionResult['product_'.$product]['channel_'.$channel] ['region_'.$region]);
							$contadorCache+= intval($st_decisionCache['product_'.$product]['channel_'.$channel] ['region_'.$region]);

						}
					}
				}
				if($contadorResult!=$contadorCache){
						$error=true;
				}
				if($error){
					print '<script language="JavaScript">'; 
					print 'alert("La distribución del stock realizada es incorrecta");'; 
					print '</script>'; 
				}else{
					if (isset ($postData['stock'])){
						$st_decisionDistributionData=$postData['stock']['distributionInformation'];
						$distribution= explode (";" ,  $st_decisionDistributionData);
						$distributionInformation=array();
						for ($product=1; $product<=$n_products; $product++){
							for ($channelO=1; $channelO<=$n_channels; $channelO++){
								for ($regionO=1; $regionO<=$n_regions; $regionO++){
									for ($channelD=1; $channelD<=$n_channels; $channelD++){
										for ($regionD=1; $regionD<=$n_regions; $regionD++){
											if((count($distribution)-1)==0){
												$distributionInformation['product_'.$product]
													['channelO_'.$channelO]
													['regionO_'.$regionO]
													['channelD_'.$channelD]
													['regionD_'.$regionD] = "0";
											}
											for ($i=0; $i<count($distribution)-1; $i++ ){
												if($product==substr($distribution[$i], 0, 1) && $channelO ==substr($distribution[$i], 2, 1)&&$regionO ==substr($distribution[$i], 1, 1)&&$channelD ==substr($distribution[$i], 4, 1)&&$regionD ==substr($distribution[$i], 3, 1)){
													$distributionInformation['product_'.$product]
													['channelO_'.$channelO]
													['regionO_'.$regionO]
													['channelD_'.$channelD]
													['regionD_'.$regionD] = substr($distribution[$i], 5);
												}
												elseif($distributionInformation['product_'.$product]['channelO_'.$channelO] ['regionO_'.$regionO] ['channelD_'.$channelD]
													['regionD_'.$regionD] == null or
													$distributionInformation['product_'.$product]['channelO_'.$channelO]['regionO_'.$regionO]['channelD_'.$channelD]
													['regionD_'.$regionD] == ""){
													$distributionInformation['product_'.$product]
													['channelO_'.$channelO]
													['regionO_'.$regionO]
													['channelD_'.$channelD]
													['regionD_'.$regionD] = "0";
												}
												
											}
										}
									}
								}

							}

						}
						$st_decisionData['stock']['unitsStockResult']=$postData['stock']['unitsStockRight'];
						$st_decisionData['stock']['distributionInformation']=$distributionInformation;


						$st_decisions->processDecision($st_decisionData, $this->game['id'], $this->company['id'], $this->round['round_number']);

					}
					//VERO
					if(!$error){				
						$postData=$this->getRequest()->getPost();
						$decisionData=$postData['production_decision'];
						//VERO						
						$productionFunctionalities=$decisionData['functionality_params'];
						// var_dump($decisionData);
						// echo("<br/>");
						// var_dump($productionFunctionalities);
						// die();
						$functionalityInformation=array();



						$auxLoop = New Model_DbTable_Games();
						$nFunct = $auxLoop->getNumberOfFunctionalities($this->game['id']);
						for ($product=1; $product<=$n_products; $product++){
							$productionFSet=$productionFunctionalities['product_number_'.$product];
							for ($functionality_param_number=1; $functionality_param_number<=$nFunct; $functionality_param_number++){
								if(count($productionFSet)==0){$functionalityInformation['product_number_'.$product]['functionality_param_number_'.$functionality_param_number] = "0";
								} else {
									$product_numberLoop=1;
									//var_dump($productionFunctionalities);
									foreach ($productionFunctionalities as $productionFunctionality){									
										foreach($productionFunctionality as $j ){
											if (($product==$product_numberLoop) && ($j==$functionality_param_number)){
												$functionalityInformation['product_number_'.$product]['functionality_param_number_'.$functionality_param_number] = "1";
											}
											elseif (($functionalityInformation['product_number_'.$product]['functionality_param_number_'.$functionality_param_number] == null) or ($functionalityInformation['product_number_'.$product]['functionality_param_number_'.$functionality_param_number] == "")){
												$functionalityInformation['product_number_'.$product]['functionality_param_number_'.$functionality_param_number]= "0";
											}
										}
										$product_numberLoop++;							
									}
								}
							}
						}
						$decisionData['functionality_params']=$functionalityInformation;	
						//var_dump($functionalityInformation);die();						
						//VERO
						$decisionData_sup=$postData['suppliers'];
						$decisions->processDecision($decisionData, $this->game['id'], $this->company['id'], $this->round['round_number']);
						$decisions_sup->processDecision($decisionData_sup, $this->game['id'], $this->company['id'], $this->round['round_number']);

						$validateData=0;
						$validate=new Model_DbTable_Decisions_Validate();
						$validate->processDecision($validateData, $this->game['id'], $this->company['id'], $this->round['round_number']);
					}
				}
			}
			if ($decisions->existsPrevious()){
				$lastDecision=$decisions->getActiveRoundLastDecisionSaved();
				$this->view->unitsDecision=$lastDecision['units'];
				$aux=$region_count->countFactories($this->game['id'],$this->company['id']);
				$this->view->numberOfFactories=$aux;
				$this->view->lastFactory=$games->getLastFactory($this->game['id'], $this->company['id']);
				$this->view->roundFactory=$games->getRoundFactoryCreated($this->game['id'], $this->company['id']);
				$factories=$games->getFactories($this->game['id'],$this->company['id']);
				$this->view->game_factories=$factories;
				$this->view->regionDecision=$lastDecision['factories'];
				$this->view->qualitiesDecision=$lastDecision['qualities'];
				//VERO
				$this->view->FunctionalitiesDecision=$lastDecision['functionalities'];
				//VERO
				$this->view->addCapacityDecision=$lastDecision['capacity'];
				$lastDecision_sup=$decisions_sup->getActiveRoundLastDecisionSaved();
				$this->view->numberDecision=$lastDecision_sup['number'];
				$this->view->paytermsDecision=$lastDecision_sup['payterms'];

			}
			//VERO
			if ($st_decisions->existsPrevious($this->game['id'], $this->company['id'], $this->round['round_number'])){

				$st_lastDecision=$st_decisions->getDecisionArray($this->game['id'], $this->company['id'], $this->round['round_number']);
				$this->view->unitsStockRightDecision=$st_lastDecision['unitsStock'];
				$distributionData=$st_lastDecision['distributionStock'];

				$product_number=1;
				$distributionData['product_'.$product_number];
				while (isset($distributionData['product_'.$product_number])){
					$units_product=$distributionData['product_'.$product_number];
					$channelO_number=1;
					while (isset($units_product['channelO_number_'.$channelO_number])){
						$units_channelO=$units_product['channelO_number_'.$channelO_number];
						$regionO_number=1;
						while (isset($units_channelO['regionO_number_'.$regionO_number])){
							$units_regionO=$units_channelO['regionO_number_'.$regionO_number];
							$channelD_number=1;
							while (isset($units_regionO['channelD_number_'.$channelD_number])){
								$units_channelD=$units_regionO['channelD_number_'.$channelD_number];
								$regionD_number=1;
								while (isset($units_channelD['regionD_number_'.$regionD_number])){
									$units=$units_channelD['regionD_number_'.$regionD_number];
									if ($units<> "0"){
										$st_distribuion_lastDecision=($st_distribuion_lastDecision.strval($product_number).strval($regionO_number).strval($channelO_number).strval($regionD_number).strval($channelD_number).strval($units).";");
									}
									$regionD_number++;
								}
								$channelD_number++;
							}
							$regionO_number++;
						}
						$channelO_number++;
					}
					$product_number++;
				}

				$this->view->unitsStockDistributiontDecision=$st_distribuion_lastDecision;
			}else{
				$this->view->unitsStockRightDecision=$st_outcomes_prev;
			}
			//VERO	
		}
	
		function marketingAction(){
			$this->view->title .= " / Marketing.";
			$this->view->headTitle($this->view->title, 'PREPEND');
			$this->view->controllerName='decision';
			$this->view->actionName="marketing";
			
			$games=new Model_DbTable_Games();
			$regions=$games->getRegions($this->game['id']);
			$products=$games->getProducts($this->game['id']);			
			$game_channels=$games->getChannels($this->game['id']);
			$game_media=$games->getMedia($this->game['id']);
			
			$trademedia[0]=array('trademedia_number'=>1, 'name'=>'Patrocinio');
			$trademedia[1]=array('trademedia_number'=>2, 'name'=>'Promoción');
			
			$this->view->product_availability=$games->getProductsAvailibility($this->game['id'],$this->round['round_number'],$this->company['id']);
			
			$this->view->regions=$regions;
			$this->view->products=$products;
			$this->view->channels=$game_channels;
			$this->view->media=$game_media;
			$this->view->trademedia=$trademedia;
			$this->view->n_products=$games->getNumberOfProductsAvailable($this->game['id'],$this->round['round_number'],$this->company['id']);
			
			$decisions=new Model_DbTable_Decisions_Marketing();
			//si hay decisión sobre este turno guardada se imprime	
				$round_actual=$this->round['round_number'];
				if($round_actual>1){
					$round_previous=$round_actual-1;
					$mk_lastDecision_prev=$decisions->getDecisionArray($this->game['id'], $this->company['id'], $round_previous);
					
					$this->view->pricesDecision=$mk_lastDecision_prev['prices'];
					$this->view->advertisingbudgetDecision=$mk_lastDecision_prev['advertising_budget'];
					$this->view->advertisingbudgetProductsDecision=$mk_lastDecision_prev['advertising_budget_distribution'];
					$this->view->trademktbudgetProductsDecision=$mk_lastDecision_prev['trademkt_budget_distribution'];
					$this->view->advertisingpercentageDecision=$mk_lastDecision_prev['advertising_percentage'];
					$this->view->trademktbudgetDecision=$mk_lastDecision_prev['trademkt_budget'];
					$this->view->trademktpercentageDecision=$mk_lastDecision_prev['trademkt_percentage'];
				}
			
			if ($this->getRequest()->isPost()){				
				$postData=$this->getRequest()->getPost();
				$decisionData=$postData['marketing'];
				$decisions->processDecision($decisionData, $this->game['id'], $this->company['id'], $this->round['round_number']);
				$validateData=0;
				$validate=new Model_DbTable_Decisions_Validate();
				$validate->processDecision($validateData, $this->game['id'], $this->company['id'], $this->round['round_number']);
			}
			if ($decisions->existsPrevious()){
				$lastDecision=$decisions->getActiveRoundLastDecisionSaved();
				//var_dump($lastDecision); die();
				$this->view->pricesDecision=$lastDecision['prices'];
				$this->view->advertisingbudgetDecision=$lastDecision['advertising_budget'];
				$this->view->advertisingbudgetProductsDecision=$lastDecision['advertising_budget_distribution'];
				$this->view->trademktbudgetProductsDecision=$lastDecision['trademkt_budget_distribution'];
				$this->view->advertisingpercentageDecision=$lastDecision['advertising_percentage'];
				$this->view->trademktbudgetDecision=$lastDecision['trademkt_budget'];
				$this->view->trademktpercentageDecision=$lastDecision['trademkt_percentage'];
			}		
		}


		
		function suppliersAction(){
			$this->view->title .= " / Proveedores.";
			$this->view->headTitle($this->view->title, 'PREPEND');
			$this->view->controllerName='decision';
			$this->view->actionName="suppliers";

			$games=new Model_DbTable_Games();
			$game_channels=$games->getChannels($this->game['id']);			
			
			$channel_payterms[0]=array('value'=>0, 'descriptor'=>'Inmediato');
			$channel_payterms[1]=array('value'=>1, 'descriptor'=>'Aplazado 1 mes');
			$channel_payterms[2]=array('value'=>2, 'descriptor'=>'Aplazado 2 meses');
			$channel_payterms[3]=array('value'=>3, 'descriptor'=>'Aplazado 3 meses');
			$channel_payterms[4]=array('value'=>4, 'descriptor'=>'Aplazado 4 meses');

			
			$this->view->channels=$game_channels;
			$this->view->channel_payterms=$channel_payterms;
			
			$decisions=new Model_DbTable_Decisions_Suppliers();
			//si hay decisión sobre este turno guardada se imprime		
			if ($this->getRequest()->isPost()){				
				$postData=$this->getRequest()->getPost();
				$decisionData=$postData['suppliers'];
				$decisions->processDecision($decisionData);
				$validateData=0;
				$validate=new Model_DbTable_Decisions_Validate();
				$validate->processDecision($validateData, $this->game['id'], $this->company['id'], $this->round['round_number']);
			}
			if ($decisions->existsPrevious()){
				$lastDecision=$decisions->getActiveRoundLastDecisionSaved();
				$this->view->numberDecision=$lastDecision['number'];
				$this->view->paytermsDecision=$lastDecision['payterms'];
			}
		}
		
		function humanresourcesAction(){
			$this->view->title .= " / Recursos Humanos.";
			$this->view->headTitle($this->view->title, 'PREPEND');
			$this->view->controllerName='decision';
			$this->view->actionName="humanresources";
			$this->view->round_number = $this->round['round_number'];
			
			$wages[0]=array('value'=>1.015, 'descriptor'=>'Primer Intervalo');
			$wages[1]=array('value'=>1.000, 'descriptor'=>'Segundo Intervalo');
			$wages[2]=array('value'=>0.985, 'descriptor'=>'Tercer Intervalo');
			
			$level[0]=array('value'=>1.02, 'descriptor'=>'Experto');
			$level[1]=array('value'=>1.00, 'descriptor'=>'Avanzado');
			$level[2]=array('value'=>0.98, 'descriptor'=>'Basico');
			
			$this->view->wages=$wages;
			$this->view->level=$level;
			
			$decisions=new Model_DbTable_Decisions_HumanResources();
			//si hay decisión sobre este turno guardada se imprime		
			if ($this->getRequest()->isPost()){				
				$postData=$this->getRequest()->getPost();
				$decisionData=$postData['humanResources'];
				$decisions->processDecision($decisionData, $this->game['id'], $this->company['id'], $this->round['round_number']);
				$validateData=0;
				$validate=new Model_DbTable_Decisions_Validate();
				$validate->processDecision($validateData, $this->game['id'], $this->company['id'], $this->round['round_number']);
			}
			$lastDecision=$decisions->getActiveRoundLastDecisionSaved();
			$this->view->formationDecision=$lastDecision['formation']['formation'];
			if ($decisions->existsPrevious()){
				//$lastDecision=$decisions->getActiveRoundLastDecisionSaved();
				$this->view->cuartilDecision=$lastDecision['cuartil']['cuartil'];
			}
		}
		
		function financeAction(){
			$this->view->title .= " / Finanzas.";
			$this->view->headTitle($this->view->title, 'PREPEND');
			$this->view->controllerName='decision';
			$this->view->actionName="finance";
			$this->view->round_number = $this->round['round_number'];
			$round_number = $this->round['round_number'];
			
			$games=new Model_DbTable_Games();
			$amount=$games->getFinanceAmount($this->game['id'],$this->round['round_number'],$this->company['id']);
			//var_dump($amount);die();
			$interest_adjustment=$games->getInterestAdjustment($this->game['id'],$this->round['round_number'],$this->company['id']);
			
			if ($round_number>1 && $round_number<4){
				$term[0]=array('value'=>2, 'descriptor'=>'Dos a&ntilde;os');
				$term[1]=array('value'=>3, 'descriptor'=>'Tres a&ntilde;os');
				$term[2]=array('value'=>4, 'descriptor'=>'Cuatro a&ntilde;os');
			}
			else {
				if($round_number==4){
					$term[0]=array('value'=>2, 'descriptor'=>'Dos a&ntilde;os');
					$term[1]=array('value'=>3, 'descriptor'=>'Tres a&ntilde;os');
				}
				else {
					if($round_number==5){
					$term[0]=array('value'=>2, 'descriptor'=>'Dos a&ntilde;os');
					}
				}
			}
			//VERO
			$gameInvestmentParams = new Model_DbTable_Games_Param_Markets_InvestmentsParams();
			$investmentsType=$gameInvestmentParams->getinvestmentParamsName($this->game['id']);
			$this->view->investmentsType=$investmentsType;
			//VERO

			//Para calcular el interes de la opcion seleccionada
			$this->view->interest_rate=$games->getInterestRate($this->game['id']);
			$interest[0]=array('value'=>$this->view->interest_rate['term_2']);
			$interest[1]=array('value'=>$this->view->interest_rate['term_3']);
			$interest[2]=array('value'=>$this->view->interest_rate['term_4']);
			
			$this->view->interest_adjustment=$interest_adjustment;
			$this->view->interest_aux=$interest;
			$this->view->term=$term;
			$this->view->amount=$amount;
			
			$decisions=new Model_DbTable_Decisions_Finance();
			//si hay decisión sobre este turno guardada se imprime		
			if ($this->getRequest()->isPost()){				
				$postData=$this->getRequest()->getPost();
				$decisionData=$postData['finance'];
				$decisions->processDecision($decisionData, $this->game['id'], $this->company['id'], $this->round['round_number']);
				$validateData=0;
				$validate=new Model_DbTable_Decisions_Validate();
				$validate->processDecision($validateData, $this->game['id'], $this->company['id'], $this->round['round_number']);
			}
			if ($decisions->existsPrevious()){
				$lastDecision=$decisions->getActiveRoundLastDecisionSaved();
				$this->view->termDecision=$lastDecision['term'];
				$this->view->amountDecision=$lastDecision['amount'];
				$this->view->patrimonyDecision=$lastDecision['patrimony'];
				//VERO
				$this->view->investmentDecision=$lastDecision['investment'];
				//VERO
			}
		}
		
		function initiativesAction(){
			$this->view->title .= " / Iniciativas.";
			$this->view->headTitle($this->view->title, 'PREPEND');
			$this->view->controllerName='decision';
			$this->view->actionName="initiatives";
			$this->view->round_number = $this->round['round_number'];
			
			$games=new Model_DbTable_Games();
			$region_count= new Model_DbTable_Decisions_Pr_Region();
			
			$game_initiatives_prod=$games->getInitiativesProd($this->game['id']);
			$game_initiatives_hr=$games->getInitiativesHR($this->game['id']);
			$game_initiatives_mkt=$games->getInitiativesMKT($this->game['id']);
			$game_initiatives_det=$games->getInitiativesDET($this->game['id']);
			$game_initiatives_chosen=$games->getInitiativeChosen($this->game['id'],$this->round['round_number'],$this->company['id']);

			$factories=$games->getFactories($this->game['id'],$this->company['id']);
			$aux=$region_count->countFactories($this->game['id'],$this->company['id']);
			$this->view->numberOfFactories=$aux;
			$this->view->game_factories=$factories;
			$this->view->booleanCreate=0;
			
			$initiativevalue[0]=array('value'=>0, 'descriptor'=>'No');
			$initiativevalue[1]=array('value'=>1, 'descriptor'=>'Si');
			
			$this->view->initiativevalue=$initiativevalue;
			$this->view->initiativesProd=$game_initiatives_prod;
			$this->view->initiativesHR=$game_initiatives_hr;
			$this->view->initiativesMKT=$game_initiatives_mkt;
			$this->view->initiativesDET=$game_initiatives_det;
			$this->view->initiative_chosen=$game_initiatives_chosen;
			

			
			$decisions=new Model_DbTable_Decisions_Initiatives();
			//si hay decisión sobre este turno guardada se imprime		
			if ($this->getRequest()->isPost()){				
				$postData=$this->getRequest()->getPost();
				$decisionData=$postData['initiatives'];
				//var_dump($decisionData); die();
				$decisions->processDecision($decisionData, $this->game['id'], $this->company['id'], $this->round['round_number']);
				$validateData=0;
				$validate=new Model_DbTable_Decisions_Validate();
				$validate->processDecision($validateData, $this->game['id'], $this->company['id'], $this->round['round_number']);
			}
			if ($decisions->existsPrevious()){
				$lastDecision=$decisions->getActiveRoundLastDecisionSaved();
				//var_dump($lastDecision); die();
				$this->view->initiativeproductionDecision=$lastDecision['initiativeproduction'];
				$this->view->initiativemarketingDecision=$lastDecision['initiativemarketing'];
				$this->view->initiativehumanresourcesDecision=$lastDecision['initiativehumanresources'];
				$this->view->initiativedeteriorationDecision=$lastDecision['initiativedeterioration'];
			}
		}

		function marketresearchesAction(){
			$this->view->title .= " / Estudios de mercado.";
			$this->view->headTitle($this->view->title, 'PREPEND');
			$this->view->controllerName='decision';
			$this->view->actionName="marketresearches";
			$this->view->round_number = $this->round['round_number'];
			
			$games=new Model_DbTable_Games();
			$researchvalue[0]=array('value'=>0, 'descriptor'=>'No');
			$researchvalue[1]=array('value'=>1, 'descriptor'=>'Si');
			$game_marketResearches_costs=$games->getMarketResearchesCosts($this->game['id']);
			
			$this->view->researchvalue=$researchvalue;
			$this->view->researchcosts=$game_marketResearches_costs;
			
			$decisions=new Model_DbTable_Decisions_MarketResearches();
			//si hay decisión sobre este turno guardada se imprime		
			if ($this->getRequest()->isPost()){				
				$postData=$this->getRequest()->getPost();
				$decisionData=$postData['marketresearch'];
				$decisions->processDecision($decisionData, $this->game['id'], $this->company['id'], $this->round['round_number']);
				$validateData=0;
				$validate=new Model_DbTable_Decisions_Validate();
				$validate->processDecision($validateData, $this->game['id'], $this->company['id'], $this->round['round_number']);
			}
			if ($decisions->existsPrevious()){
				$lastDecision=$decisions->getActiveRoundLastDecisionSaved();
				$this->view->marketResearchDecision=$lastDecision;
			}
		}
		
		function idiAction(){
			$this->view->title .= " / I+D+i.";
			$this->view->headTitle($this->view->title, 'PREPEND');
			$this->view->controllerName='decision';
			$this->view->actionName="idi";
			$this->view->round_number = $this->round['round_number'];
			
			$games=new Model_DbTable_Games();
			$idiProducts=$games->getIdiProducts($this->game['id']);
			$products=$games->getProducts($this->game['id']);
			$qualityParams=$games->getQualityParams($this->game['id']);
			$newidivalue[0]=array('value'=>0, 'descriptor'=>'No');
			$newidivalue[1]=array('value'=>1, 'descriptor'=>'Si');
			$changeidi[0]=array('value'=>2, 'descriptor'=>'(+2)');
			$changeidi[1]=array('value'=>1, 'descriptor'=>'(+1)');
			$changeidi[2]=array('value'=>0, 'descriptor'=>'0');
			$changeidi[3]=array('value'=>-1, 'descriptor'=>'(-1)');
			$changeidi[4]=array('value'=>-2, 'descriptor'=>'(-2)');
			$changeidi[5]=array('value'=>-3, 'descriptor'=>'(-3)');
			
			$this->view->newidivalue=$newidivalue;
			$this->view->idiProducts=$idiProducts;
			$this->view->changeidi=$changeidi;
			$this->view->products=$products;
			$this->view->qualityParams=$qualityParams;
			
			$this->view->product_availability=$games->getProductsAvailibility($this->game['id'],$this->round['round_number'],$this->company['id']);
			
			$decisions=new Model_DbTable_Decisions_Idi();
			//si hay decisión sobre este turno guardada se imprime		
			if ($this->getRequest()->isPost()){				
				$postData=$this->getRequest()->getPost();
				$decisionData=$postData['idi'];
				$decisions->processDecision($decisionData, $this->game['id'], $this->company['id'], $this->round['round_number']);
				$validateData=0;
				$validate=new Model_DbTable_Decisions_Validate();
				$validate->processDecision($validateData, $this->game['id'], $this->company['id'], $this->round['round_number']);
			}
			if ($decisions->existsPrevious()){
				$lastDecision=$decisions->getActiveRoundLastDecisionSaved();
				//var_dump($lastDecision); die();
				$this->view->changeIdiDecision=$lastDecision['changeIdi'];
				$this->view->newIdiDecision=$lastDecision['newIdi'];
			}
			
		}
		
		function validateAction(){
			$this->view->title .= " / Validar Decisiones.";
			$this->view->headTitle($this->view->title, 'PREPEND');
			$this->view->controllerName='decision';
			$this->view->actionName="validate";
			$this->view->round_number = $this->round['round_number'];
			$front = Zend_Controller_Front::getInstance();
			$this->view->user=$front->getParam('loggedUserData');
			$rounds=new Model_DbTable_Games_Config_GameRounds();
			$active_round=$rounds->getActiveRound($this->game['id']);
			$this->view->active_round=$active_round;
			
			$role[0]=array('role'=>0, 'name'=>'Director General');
			$role[1]=array('role'=>1, 'name'=>'Director Financiero');
			$role[2]=array('role'=>2, 'name'=>'Director de Producción');
			$role[3]=array('role'=>3, 'name'=>'Director de Recursos Humanos');
			$role[4]=array('role'=>4, 'name'=>'Director de Marketing');
			$this->view->role_profile=$role;
			
			$games=new Model_DbTable_Games();
			$this->view->decision_validate=$games->getDecisionValidated($this->game['id'],$this->view->round_number,$this->company['id']);
			
			$decisions=new Model_DbTable_Decisions_Validate();
			//si hay decisión sobre este turno guardada se imprime		
			if ($this->getRequest()->isPost()){				
				$postData=$this->getRequest()->getPost();
				$decisionData=$postData['validated'];
				$decisions->processDecision($decisionData, $this->game['id'], $this->company['id'], $this->round['round_number']);
			}
			if ($decisions->existsPrevious()){
				$lastDecision=$decisions->getActiveRoundLastDecisionSaved();
				$this->view->decisionValidated=$lastDecision['validated'];
			}
			$decision=new Model_DbTable_Decisions_Va_Validated();
			$result=$decision->getDecision($this->game['id'], $this->company['id'], $this->view->round_number);
			$this->view->validate_aux=$result['validated'];

		}
		
		function viewAction(){
			$this->view->controllerName='decision';
			$this->view->actionName="history";
			
			$companies = new Model_DbTable_Companies();
			$games=new Model_DbTable_Games();
			
			$this->view->game=$games->getGame($this->game['id']);
			$this->view->round_number=$_GET['round_number'];
			$this->view->company = $companies->getCompany($this->company['id']);
			
			$round_number=$_GET['round_number'];
			
			
			$regions=$games->getRegions($this->game['id']);
			$qualityParams=$games->getQualityParams($this->game['id']);
			$products=$games->getProducts($this->game['id']);
			$game_initiatives=$games->getInitiatives($this->game['id']);	
			$game_channels=$games->getChannels($this->game['id']);
			$game_media=$games->getMedia($this->game['id']);
			$game_initiatives_prod=$games->getInitiativesProd($this->game['id']);
			$game_initiatives_mkt=$games->getInitiativesMKT($this->game['id']);
			$game_initiatives_hr=$games->getInitiativesHR($this->game['id']);
			$game_initiatives_det=$games->getInitiativesDET($this->game['id']);			
			$game_marketResearches_costs=$games->getMarketResearchesCosts($this->game['id']);
			$idiProducts=$games->getIdiProducts($this->game['id']);
			$factories=$games->getFactories($this->game['id'],$_GET['round_number'],$this->company['id']);
			$amount=$games->getFinanceAmount($this->game['id'], $_GET['round_number'], $this->company['id']);
			$interest_adjustment=($games->getInterestAdjustment($this->game['id'],$_GET['round_number'],$this->company['id']));
			$game_initiatives_chosen=$games->getInitiativeChosen($this->game['id'],$_GET['round_number'],$this->company['id']);

			
			$trademedia[0]=array('trademedia_number'=>1, 'name'=>'Patrocinio');
			$trademedia[1]=array('trademedia_number'=>2, 'name'=>'Promoción');
			
			$channel_payterms[0]=array('value'=>0, 'descriptor'=>'Inmediato');
			$channel_payterms[1]=array('value'=>1, 'descriptor'=>'Aplazado 1 mes');
			$channel_payterms[2]=array('value'=>2, 'descriptor'=>'Aplazado 2 meses');
			$channel_payterms[3]=array('value'=>3, 'descriptor'=>'Aplazado 3 meses');
			$channel_payterms[4]=array('value'=>4, 'descriptor'=>'Aplazado 4 meses');
			//VERO
			$n_product=$games->getNumberOfProductsAvailable($this->game['id'], $_GET['round_number'], $this->company['id']);			

			$gameFunctionalityParams = new Model_DbTable_Games_Param_Markets_FunctionalityParams();
			$game_functionality_params_weight=$gameFunctionalityParams->getFunctionalityParamsWeight($this->game['id']);
			$game_functionality_params_name=$gameFunctionalityParams->getFunctionalityParamsName($this->game['id']);
			$n_functionalities=$games->getNumberOfFunctionalities($this->game['id']);
			for($i=1; $i<=$n_functionalities;$i++){
				$a=$i+1;
				$functionalities[$a]= array('value'=>$i, 'descriptor'=>$game_functionality_params_name['functionality_param_number_'.$i]);
			}
			$this->view->functionalities=$functionalities;

			$this->view->game_functionality_params_weight=$game_functionality_params_weight;


			$gameInvestmentParams = new Model_DbTable_Games_Param_Markets_InvestmentsParams();
			$investmentsType=$gameInvestmentParams->getinvestmentParamsName($this->game['id']);
			$this->view->investmentsType=$investmentsType;
			//VERO
			
			$wages[0]=array('value'=>1.015, 'descriptor'=>'Primer Cuartil');
			$wages[1]=array('value'=>1.000, 'descriptor'=>'Segundo Cuartil');
			$wages[2]=array('value'=>0.985, 'descriptor'=>'Tercer Cuartil');

			$level[0]=array('value'=>1.02, 'descriptor'=>'Experto');
			$level[1]=array('value'=>1.00, 'descriptor'=>'Avanzado');
			$level[2]=array('value'=>0.98, 'descriptor'=>'Basico');
			
			if ($round_number>1 && $round_number<4){
				$term[0]=array('value'=>2, 'descriptor'=>'Dos a&ntilde;os');
				$term[1]=array('value'=>3, 'descriptor'=>'Tres a&ntilde;os');
				$term[2]=array('value'=>4, 'descriptor'=>'Cuatro a&ntilde;os');
			}
			else {
				if($round_number==4){
					$term[0]=array('value'=>2, 'descriptor'=>'Dos a&ntilde;os');
					$term[1]=array('value'=>3, 'descriptor'=>'Tres a&ntilde;os');
				}
				else {
					if($round_number==5){
					$term[0]=array('value'=>2, 'descriptor'=>'Dos a&ntilde;os');
					}
				}
			}
			
			//Para calcular el interes de la opcion seleccionada
			$this->view->interest_rate=$games->getInterestRate($this->game['id']);
			$interest[0]=array('value'=>$this->view->interest_rate['term_2']);
			$interest[1]=array('value'=>$this->view->interest_rate['term_3']);
			$interest[2]=array('value'=>$this->view->interest_rate['term_4']);	
			
			$initiativevalue[0]=array('value'=>0, 'descriptor'=>'No');
			$initiativevalue[1]=array('value'=>1, 'descriptor'=>'Si');
			
			$researchvalue[0]=array('value'=>0, 'descriptor'=>'No');
			$researchvalue[1]=array('value'=>1, 'descriptor'=>'Si');
	
			$newidivalue[0]=array('value'=>0, 'descriptor'=>'No');
			$newidivalue[1]=array('value'=>1, 'descriptor'=>'Si');
			
			$changeidi[0]=array('value'=>2, 'descriptor'=>'(+2)');
			$changeidi[1]=array('value'=>1, 'descriptor'=>'(+1)');
			$changeidi[2]=array('value'=>0, 'descriptor'=>'0');
			$changeidi[3]=array('value'=>-1, 'descriptor'=>'(-1)');
			$changeidi[4]=array('value'=>-2, 'descriptor'=>'(-2)');
			$changeidi[5]=array('value'=>-3, 'descriptor'=>'(-3)');
						
			$this->view->regions=$regions;
			
			$this->view->qualityParams=$qualityParams;
			$this->view->products=$products;
			
			$outcomes=new Model_DbTable_Outcomes();
			$this->view->stocks_units=$outcomes->getStock($this->game['id'],$_GET['round_number']);
			
			$this->view->channels=$game_channels;
			$this->view->media=$game_media;
			$this->view->trademedia=$trademedia;
			$this->view->channel_payterms=$channel_payterms;
			$this->view->wages=$wages;
			$this->view->level=$level;
			$this->view->term=$term;
			$this->view->amount=$amount;
			$this->view->interest_adjustment=$interest_adjustment;
			$this->view->interest_aux=$interest;
			$this->view->initiativevalue=$initiativevalue;
			$this->view->initiativesProd=$game_initiatives_prod;
			$this->view->initiativesHR=$game_initiatives_hr;
			$this->view->initiativesMKT=$game_initiatives_mkt;
			$this->view->initiativesDET=$game_initiatives_det;
			$this->view->initiative_chosen=$game_initiatives_chosen;
			$this->view->researchvalue=$researchvalue;
			$this->view->researchcosts=$game_marketResearches_costs;
			$this->view->idiProducts=$idiProducts;
			$this->view->newidivalue=$newidivalue;
			$this->view->changeidi=$changeidi;
			$this->view->lastFactory=$games->getLastFactory($this->game['id'], $this->company['id']);
			$this->view->roundFactory=$games->getRoundFactoryCreated($this->game['id'], $this->company['id']);
			$this->view->product_availability=$games->getProductsAvailibility($this->game['id'],$_GET['round_number'],$this->company['id']);
			//VERO
			$this->view->channelsO=$game_channels;
			$this->view->channelsD=$game_channels;
			$this->view->regionsO=$regions;
			$this->view->regionsD=$regions;
			//VERO
			
			$region_count= new Model_DbTable_Decisions_Pr_Region();
			$factories=$games->getFactories($this->game['id'],$this->company['id']);
			$aux=$region_count->countFactories($this->game['id'],$this->company['id']);
			$this->view->numberOfFactories=$aux;
			$this->view->game_factories=$factories;
			$this->view->booleanCreate=0;	
		
								
			$pr_decisions=new Model_DbTable_Decisions_Production();
			$mk_decisions=new Model_DbTable_Decisions_Marketing();
			$su_decisions=new Model_DbTable_Decisions_Suppliers();

			$hr_decisions=new Model_DbTable_Decisions_HumanResources();
			$fi_decisions=new Model_DbTable_Decisions_Finance();
			$in_decisions=new Model_DbTable_Decisions_Initiatives();
			$idi_decisions=new Model_DbTable_Decisions_Idi();
			$validate_decisions=new Model_DbTable_Decisions_Validate();
			//VERO
			$st_decisions=new Model_DbTable_Decisions_Stock();
			//VERO
		
			if ($pr_decisions->existsPrevious($this->game['id'], $this->company['id'], $_GET['round_number'])){
				$pr_lastDecision=$pr_decisions->getDecisionArray($this->game['id'], $this->company['id'], $_GET['round_number']);
				//$this->view->regionDecision=$pr_lastDecision['region'];
				$this->view->regionDecision=$pr_lastDecision['factories'];
				$this->view->qualitiesDecision=$pr_lastDecision['qualities'];
				//VERO
				$this->view->FunctionalitiesDecision=$pr_lastDecision['functionalities'];
				//VERO
				$this->view->unitsDecision=$pr_lastDecision['units'];
				//$this->view->addCapacityDecision=$pr_lastDecision['capacity'];
			}
			//VERO
			if ($st_decisions->existsPrevious($this->game['id'], $this->company['id'], $_GET['round_number'])){
				$st_lastDecision=$st_decisions->getDecisionArray($this->game['id'], $this->company['id'], $_GET['round_number']);
				$this->view->unitsStockRightDecision=$st_lastDecision['unitsStock'];
				$distributionData=$st_lastDecision['distributionStock'];

				$product_number=1;
				$distributionData['product_'.$product_number];
				while (isset($distributionData['product_'.$product_number])){
					$units_product=$distributionData['product_'.$product_number];
					$channelO_number=1;
					while (isset($units_product['channelO_number_'.$channelO_number])){
						$units_channelO=$units_product['channelO_number_'.$channelO_number];
						$regionO_number=1;
						while (isset($units_channelO['regionO_number_'.$regionO_number])){
							$units_regionO=$units_channelO['regionO_number_'.$regionO_number];
							$channelD_number=1;
							while (isset($units_regionO['channelD_number_'.$channelD_number])){
								$units_channelD=$units_regionO['channelD_number_'.$channelD_number];
								$regionD_number=1;
								while (isset($units_channelD['regionD_number_'.$regionD_number])){
									$units=$units_channelD['regionD_number_'.$regionD_number];
									if ($units<> "0"){
										$st_distribuion_lastDecision=($st_distribuion_lastDecision.strval($product_number).strval($regionO_number).strval($channelO_number).strval($regionD_number).strval($channelD_number).strval($units).";");
									}
									$regionD_number++;
								}
								$channelD_number++;
							}
							$regionO_number++;
						}
						$channelO_number++;
					}
					$product_number++;
				}

				$this->view->unitsStockDistributiontDecision=$st_distribuion_lastDecision;
			}else{
				$this->view->unitsStockLeftDecision=$st_outcomes_prev;
			}
			//VERO
			if ($mk_decisions->existsPrevious($this->game['id'], $this->company['id'], $_GET['round_number'])){
				$mk_lastDecision=$mk_decisions->getDecisionArray($this->game['id'], $this->company['id'], $_GET['round_number']);
				$this->view->pricesDecision=$mk_lastDecision['prices'];
				$this->view->advertisingbudgetDecision=$mk_lastDecision['advertising_budget'];
				$this->view->advertisingbudgetProductsDecision=$mk_lastDecision['advertising_budget_distribution'];
				$this->view->trademktbudgetProductsDecision=$mk_lastDecision['trademkt_budget_distribution'];
				$this->view->advertisingpercentageDecision=$mk_lastDecision['advertising_percentage'];
				$this->view->trademktbudgetDecision=$mk_lastDecision['trademkt_budget'];
				$this->view->trademktpercentageDecision=$mk_lastDecision['trademkt_percentage'];
			}

			if ($su_decisions->existsPrevious($this->game['id'], $this->company['id'], $_GET['round_number'])){
				$su_lastDecision=$su_decisions->getDecisionArray($this->game['id'], $this->company['id'], $_GET['round_number']);
				$this->view->numberDecision=$su_lastDecision['number'];
				$this->view->paytermsDecision=$su_lastDecision['payterms'];
			}
			if ($hr_decisions->existsPrevious($this->game['id'], $this->company['id'], $_GET['round_number'])){
				$hr_lastDecision=$hr_decisions->getDecisionArray($this->game['id'], $this->company['id'], $_GET['round_number']);
				$this->view->cuartilDecision=$hr_lastDecision['cuartil'];
				$this->view->formationDecision=$hr_lastDecision['formation'];
			}
			if ($fi_decisions->existsPrevious($this->game['id'], $this->company['id'], $_GET['round_number'])){
				$fi_lastDecision=$fi_decisions->getDecisionArray($this->game['id'], $this->company['id'], $_GET['round_number']);
				$this->view->termDecision=$fi_lastDecision['term'];
				$this->view->dividends=$fi_lastDecision['patrimony']['dividends'];
				$this->view->amountDecision=$fi_lastDecision['amount']['amount'];
				//VERO
				$this->view->investmentDecision=$fi_lastDecision['investment'];
				//VERO
			}
			if ($in_decisions->existsPrevious($this->game['id'], $this->company['id'], $_GET['round_number'])){
				$in_lastDecision=$in_decisions->getDecisionArray($this->game['id'], $this->company['id'], $_GET['round_number']);
					for ($i = 1; $i < 3; $i++) {
						$auxp[$i-1] = $in_lastDecision['initiativeproduction']['initiativeproduction'.$i];
					}
					$this->view->initiativeproductionDecision = $auxp;
						//echo("<br/> Production ".$i.": ");
						//var_dump($this->view->initiativeproductionDecision);
					for ($i = 1; $i < 2; $i++) {
						$auxm[$i-1] = $in_lastDecision['initiativemarketing']['initiativemarketing'.$i];
						
					}
					$this->view->initiativemarketingDecision=$auxm;
						//echo("<br/> MKT ".$i.": ");
						//var_dump($this->view->initiativemarketingDecision);
					for ($i = 1; $i < 4; $i++) {
						$auxr[$i-1] = $in_lastDecision['initiativehumanresources']['initiativehumanresources'.$i];
					}   
					$this->view->initiativehumanresourcesDecision=$auxr;
						//echo("<br/> RRHH ".$i.": ");
						//var_dump($this->view->initiativehumanresourcesDecision);
			}
			
			if ($idi_decisions->existsPrevious($this->game['id'], $this->company['id'], $_GET['round_number'])){
				$idi_lastDecision=$idi_decisions->getDecisionArray($this->game['id'], $this->company['id'], $_GET['round_number']);
				//var_dump($lastDecision); die();
				$this->view->changeIdiDecision=$idi_lastDecision['changeIdi'];
				$this->view->newIdiDecision=$idi_lastDecision['newIdi'];
			}
			if ($validate_decisions->existsPrevious($this->game['id'], $this->company['id'], $_GET['round_number'])){
				$validate_lastDecision=$validate_decisions->getDecisionArray($this->game['id'], $this->company['id'], $_GET['round_number']);
				$this->view->decisionValidated=$validate_lastDecision['validated'];
			}
		}
	}
	
?>