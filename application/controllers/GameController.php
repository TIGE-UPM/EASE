<?php
	class GameController extends Zend_Controller_Action {
		public $_controllerTitle= "Juegos - ";
		public function preDispatch(){
			$this->view->title = $this->_controllerTitle;
			$this->_helper->authHelper();
			$this->_helper->adminControl();			
	    }
		function processAction(){		
			if (isset ($_GET['game_id']) && isset($_GET['round_number'])){
				$this->view->headTitle($this->view->title, 'PREPEND');	
				$game_id=$_GET['game_id'];
				$round_number=$_GET['round_number']; 	 			
				$games = new Model_DbTable_Games();
				$game = $games->getGame($game_id);
				$round = $games->getRound($game_id, $round_number); 
				$core=new Model_Simulation_Core();
				$core->simulate($game, $round);
				
				//$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
				//$redirector->gotoUrl($url."game/outcomes?game_id=".$game['id']."&round_number=".$round['round_number']);
			}
		}


		function editdecisionAction(){

			$this->view->controllerName='game'; 
			$this->view->actionName="view";

			$round_number=$_GET['round'];
			$games=new Model_DbTable_Games();
			$regions=$games->getRegions($_GET['game_id']);
			$qualityParams=$games->getQualityParams($_GET['game_id']);
			$products=$games->getProducts($_GET['game_id']);
			$game_initiatives=$games->getInitiatives($_GET['game_id']);	
			$game_channels=$games->getChannels($_GET['game_id']);
			$game_media=$games->getMedia($_GET['game_id']);
			$game_initiatives_prod=$games->getInitiativesProd($_GET['game_id']);
			$game_initiatives_mkt=$games->getInitiativesMKT($_GET['game_id']);
			$game_initiatives_hr=$games->getInitiativesHR($_GET['game_id']);
			$game_initiatives_det=$games->getInitiativesDET($_GET['game_id']);			
			$game_marketResearches_costs=$games->getMarketResearchesCosts($_GET['game_id']);
			$idiProducts=$games->getIdiProducts($_GET['game_id']);
			$factories=$games->getFactories($_GET['game_id'],$_GET['round'],$_GET['company_id']);
			$amount=$games->getFinanceAmount($_GET['game_id'], $_GET['round'], $_GET['company_id']);
			$interest_adjustment=($games->getInterestAdjustment($_GET['game_id'], $_GET['round'],$_GET['company_id']));
			$game_initiatives_chosen=$games->getInitiativeChosen($_GET['game_id'],$_GET['round'],$_GET['company_id']);

			
			$trademedia[0]=array('trademedia_number'=>1, 'name'=>'Patrocinio');
			$trademedia[1]=array('trademedia_number'=>2, 'name'=>'Promoción');
			
			$channel_payterms[0]=array('value'=>0, 'descriptor'=>'Inmediato');
			$channel_payterms[1]=array('value'=>1, 'descriptor'=>'Aplazado 1 mes');
			$channel_payterms[2]=array('value'=>2, 'descriptor'=>'Aplazado 2 meses');
			$channel_payterms[3]=array('value'=>3, 'descriptor'=>'Aplazado 3 meses');
			$channel_payterms[4]=array('value'=>4, 'descriptor'=>'Aplazado 4 meses');

			//VERO
			$n_product=$games->getNumberOfProductsAvailable($_GET['game_id'],$_GET['round'],$_GET['company_id']);			

			$gameFunctionalityParams = new Model_DbTable_Games_Param_Markets_FunctionalityParams();
			$game_functionality_params_weight=$gameFunctionalityParams->getFunctionalityParamsWeight($_GET['game_id']);
			$game_functionality_params_name=$gameFunctionalityParams->getFunctionalityParamsName($_GET['game_id']);
			$n_functionalities=$games->getNumberOfFunctionalities($_GET['game_id']);
			for($i=1; $i<=$n_functionalities;$i++){
				$a=$i+1;
				$functionalities[$a]= array('value'=>$i, 'descriptor'=>$game_functionality_params_name['functionality_param_number_'.$i]);
			}
			$this->view->functionalities=$functionalities;

			$this->view->game_functionality_params_weight=$game_functionality_params_weight;

			$this->view->n_regions=$games->getNumberOfRegions($_GET['game_id']);
			$this->view->n_channels=$games->getNumberOfChannels($_GET['game_id']);
			//VERO
			
			
			$wages[0]=array('value'=>1.015, 'descriptor'=>'Primer Intervalo');
			$wages[1]=array('value'=>1.000, 'descriptor'=>'Segundo Intervalo');
			$wages[2]=array('value'=>0.985, 'descriptor'=>'Tercer Intervalo');

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
			$this->view->interest_rate=$games->getInterestRate($_GET['game_id']);
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
						
			//$this->view->factories=$factories;
			//VERO
			$gameInvestmentParams = new Model_DbTable_Games_Param_Markets_InvestmentsParams();
			$investmentsType=$gameInvestmentParams->getinvestmentParamsName($_GET['game_id']);
			$this->view->investmentsType=$investmentsType;
			//VERO

			$this->view->regions=$regions;
			
			$this->view->qualityParams=$qualityParams;
			//var_dump($qualityParams);
			$this->view->products=$products;
			
			$outcomes=new Model_DbTable_Outcomes();
			$this->view->stocks_units=$outcomes->getStock($_GET['game_id'],$_GET['round']);

			
			$this->view->channels=$game_channels;
			$this->view->media=$game_media;
			//$this->view->media_intensities=$media_intensities;
			$this->view->trademedia=$trademedia;
			//$this->view->trade_intensities=$trade_intensities;
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
			$this->view->lastFactory=$games->getLastFactory($_GET['game_id'], $_GET['company_id']);
			$this->view->roundFactory=$games->getRoundFactoryCreated($_GET['game_id'], $_GET['company_id']);
			$this->view->product_availability=$games->getProductsAvailibility($_GET['game_id'],$_GET['round'],$_GET['company_id']);
			//VERO
			$this->view->channelsO=$game_channels;
			$this->view->channelsD=$game_channels;
			$this->view->regionsO=$regions;
			$this->view->regionsD=$regions;
			//VERO


			// $game_id=205;
			// $round=3;
			// //var_dump($round);
			// $company_id=113;
			$decision_investment=new Model_DbTable_Decisions_Fi_Investment();
			$game=new Model_DbTable_Games();
			$n_investment = $game->getNumberOfInvestments($_GET['game_id']);
			$outcomes=new Model_DbTable_Outcomes_In_InvestmentUnitary();
			$investment_param=new Model_DbTable_Games_Param_Markets_InvestmentsParams();
			$evolution=new Model_DbTable_Games_Evolution_Fi_Investment();
			for($investment_number=1; $investment_number<=$n_investment; $investment_number++){
				//var_dump("Inversión".$investment_number);
				$result=0;
				$result_final=0;
				for ($round_number=1; $round_number<=$_GET['round']; $round_number++){

					$investments=$decision_investment->getDecision($_GET['game_id'], $_GET['company_id'], $_GET['round']);
					$term_aux = $round-$round_number;
					$term= $investments['investment_number_'.$investment_number]['term'];
					$amount= $investments['investment_number_'.$investment_number]['amount'];

					/**$amount= $investments['investment_number_'.$investment_number]['amount'];
					$term= $investments['investment_number_'.$investment_number]['term'];*/
					
					if($term_aux < $term ){
						$result=$outcomes->getInvestment($_GET['game_id'], $_GET['company_id'], $_GET['round'], $investment_number);
						if($term == 1){
							//var_dump("Entro en 1");
							$liquid_assets +=$result;
						}elseif($term_aux==0){
							//var_dump("Entro en 2");
							$liquid_assets +=(-$amount);
							$activeInvestment+=$result+$amount+$result_final;
						}elseif($term_aux==$term-1){
							$outcomes=new Model_DbTable_Outcomes_In_InvestmentUnitary();
							//var_dump("Entro en 3");
							for($round_number_aux=$round_number;$round_number_aux<=$round;$round_number_aux++){
								//var_dump($round_number_aux);
								//var_dump($investment_number);
								
								$result_final+=$outcomes->getInvestment($game_id, $company_id, $round_number_aux, $investment_number);
								//var_dump($result_final);
							}
							//$result=$this->getAllResultsByInvestment($round, $round_number, $investment_number);
							$liquid_assets +=($amount+$result_final);
						}else{
							$outcomes=new Model_DbTable_Outcomes_In_InvestmentUnitary();
							//var_dump("Entro en 4");
							for($round_number_aux=$round_number;$round_number_aux<=$round;$round_number_aux++){
								
								$result_final+=$outcomes->getInvestment($game_id, $company_id, $round_number_aux, $investment_number);
								//var_dump($result_final);
							}
							//$result=$this->getAllResultsByInvestment($round, $round_number, $investment_number);
							$activeInvestment+=($result_final+$amount);
						}
					}
				}
			}
			//var_dump("Tesorería");
			//var_dump($liquid_assets);
			//var_dump("Activo");
			//var_dump($activeInvestment);
			
			array('liquid_assets'=>$liquid_assets, 'activeInvestment'=>$activeInvestment);
			
			
			$region_count= new Model_DbTable_Decisions_Pr_Region();
			$factories=$games->getFactories($_GET['game_id'],$_GET['company_id']);
			$aux=$region_count->countFactories($_GET['game_id'],$_GET['company_id']);
			$this->view->numberOfFactories=$aux;
			$this->view->game_factories=$factories;
			$this->view->booleanCreate=0;

			$companies = new Model_DbTable_Companies();
			
			$this->view->game=$games->getGame($_GET['game_id']);
			$this->view->round_number = $_GET['round'];

			$this->view->company = $companies->getCompany($_GET['company_id']);
			
			$this->view->n_products=$games->getNumberOfProductsAvailable($_GET['game_id'], $_GET['round'], $_GET['company_id']);
			//VERO
			$n_products=$games->getNumberOfProductsAvailable($_GET['game_id'], $_GET['round'], $_GET['company_id']);
			$n_regions=$games->getNumberOfRegions($_GET['game_id']);
			$n_channels=$games->getNumberOfChannels($_GET['game_id']);
			//VERO
			
			$pr_decisions=new Model_DbTable_Decisions_Production();
			$mk_decisions=new Model_DbTable_Decisions_Marketing();
			$su_decisions=new Model_DbTable_Decisions_Suppliers();
			$hr_decisions=new Model_DbTable_Decisions_HumanResources();
			$fi_decisions=new Model_DbTable_Decisions_Finance();
			$in_decisions=new Model_DbTable_Decisions_Initiatives();
			$mr_decisions=new Model_DbTable_Decisions_MarketResearches();
			$idi_decisions=new Model_DbTable_Decisions_Idi();
			//AHG 20191118
			$this->view->idiChangesUpToThisRound=$idi_decisions->getIdiChangesInProductsUpToThisRound($_GET['game_id'], $_GET['company_id'], $_GET['round']);
			//AHG 20191118

			//VERO
			$st_decisions=new Model_DbTable_Decisions_Stock();
			$st_outcomes=new Model_DbTable_Outcomes_St_Units();
			//VERO



			if ($this->getRequest()->isPost()){	
				$postData=$this->getRequest()->getPost();		
				
				//VERO
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
				if($error == true){
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

						$st_decisions->processDecision($st_decisionData, $_GET['game_id'], $_GET['company_id'], $_GET['round']);

					}
					if(!$error){
						//VERO
						if (isset ($postData['production_decision'])){
							$pr_decisionData=$postData['production_decision'];
							//VERO
							$productionFunctionalities=$pr_decisionData['functionality_params'];
							$functionalityInformation=array();



							$auxLoop = New Model_DbTable_Games();
							$nFunct = $auxLoop->getNumberOfFunctionalities($_GET['game_id']);
							for ($product=1; $product<=$n_products; $product++){
								$productionFSet=$productionFunctionalities['product_number_'.$product];
								for ($functionality_param_number=1; $functionality_param_number<=$nFunct; $functionality_param_number++){
									if(count($productionFSet)==0){
										$functionalityInformation['product_number_'.$product]
														['functionality_param_number_'.$functionality_param_number] = "0";
									}else{
										$product_numberLoop=1;
										foreach ($productionFunctionalities as $productionFunctionality){
										foreach($productionFunctionality as $j ){
											if($product==$product_numberLoop && $j==$functionality_param_number){
												$functionalityInformation['product_number_'.$product]
												['functionality_param_number_'.$functionality_param_number] = "1";
											}
											elseif($functionalityInformation['product_number_'.$product]
												['functionality_param_number_'.$functionality_param_number] == null or
												$functionalityInformation['product_number_'.$product]
												['functionality_param_number_'.$functionality_param_number] == ""){
												$functionalityInformation['product_number_'.$product]
												['functionality_param_number_'.$functionality_param_number]= "0";
											}
										}
										$product_numberLoop++;							
									}

									}
									
								}
							}
							$pr_decisionData['functionality_params']=$functionalityInformation;		
							//VERO
							$pr_decisions->processDecision($pr_decisionData, $_GET['game_id'], $_GET['company_id'], $_GET['round']);
						}
						if (isset ($postData['marketing'])){
							$mk_decisionData=$postData['marketing'];
							$mk_decisions->processDecision($mk_decisionData, $_GET['game_id'], $_GET['company_id'], $_GET['round']);
						}
						if (isset ($postData['suppliers'])){
							$su_decisionData=$postData['suppliers'];
							$su_decisions->processDecision($su_decisionData, $_GET['game_id'], $_GET['company_id'], $_GET['round']);
						}
						if (isset ($postData['humanResources'])){
							$hr_decisionData=$postData['humanResources'];
							$hr_decisions->processDecision($hr_decisionData, $_GET['game_id'], $_GET['company_id'], $_GET['round']);
						}
						if (isset ($postData['finance'])){
							$fi_decisionData=$postData['finance'];
							//var_dump($fi_decisionData); die();
							$fi_decisions->processDecision($fi_decisionData, $_GET['game_id'], $_GET['company_id'], $_GET['round']);
						}
						if (isset ($postData['initiatives'])){
							$in_decisionData=$postData['initiatives'];
							//var_dump($in_decisionData); die();
							$in_decisions->processDecision($in_decisionData, $_GET['game_id'], $_GET['company_id'], $_GET['round']);
						}
						if (isset ($postData['marketresearch'])){
							$mr_decisionData=$postData['marketresearch'];
							//var_dump($mr_decisionData); die();
							$mr_decisions->processDecision($mr_decisionData, $_GET['game_id'], $_GET['company_id'], $_GET['round']);
						}
						if (isset ($postData['idi'])){
							$idi_decisionData=$postData['idi'];
							//var_dump($idi_decisionData); die();
							$idi_decisions->processDecision($idi_decisionData, $_GET['game_id'], $_GET['company_id'], $_GET['round']);
						}
					}
				}
			}
			//No dependen de la ronda
			//production
				$region_count= new Model_DbTable_Decisions_Pr_Region();
				$pr_lastDecision=$pr_decisions->getDecisionArray($_GET['game_id'], $_GET['company_id'], $_GET['round']);
				$factories=$games->getFactories($_GET['game_id'],$_GET['company_id']);
				$this->view->regionDecision=$pr_lastDecision['factories'];
				$this->view->qualitiesDecision=$pr_lastDecision['qualities'];
				//VERO
				$this->view->FunctionalitiesDecision=$pr_lastDecision['functionalities'];
				//VERO
				$aux=$region_count->countFactories($_GET['game_id'],$_GET['company_id']);
				$this->view->numberOfFactories=$aux;
				$this->view->game_factories=$factories;
				$this->view->booleanCreate=0;
				
			//human resources
				$hr_lastDecision=$hr_decisions->getDecisionArray($_GET['game_id'], $_GET['company_id'], $_GET['round']);
				$this->view->formationDecision=$hr_lastDecision['formation'];
				$this->view->addCapacityDecision=$pr_lastDecision['capacity'];

				
				$round_actual=$_GET['round'];
				if($round_actual>1){
					$round_previous=$round_actual-1;
					//VERO
					$st_outcomes_prev=$st_outcomes->getStockByCompany($_GET['game_id'], $round_previous, $_GET['company_id']);
					$this->view->unitsStockCacheDecision=$st_outcomes_prev;
					//VERO
					$mk_lastDecision_prev=$mk_decisions->getDecisionArray($_GET['game_id'], $_GET['company_id'], $round_previous);
					$pr_lastDecision_prev=$pr_decisions->getDecisionArray($_GET['game_id'], $_GET['company_id'], $round_previous);

					
					$sup_lastDecision_prev=$su_decisions->getDecisionArray($_GET['game_id'], $_GET['company_id'], $round_previous);
					
					$this->view->unitsDecision=$pr_lastDecision_prev['units'];
					$this->view->numberDecision=$su_lastDecision['number'];
					$this->view->paytermsDecision=$su_lastDecision['payterms'];
					
					$this->view->pricesDecision=$mk_lastDecision_prev['prices'];
					$this->view->advertisingbudgetDecision=$mk_lastDecision_prev['advertising_budget'];
					$this->view->advertisingbudgetProductsDecision=$mk_lastDecision_prev['advertising_budget_distribution'];
					$this->view->trademktbudgetProductsDecision=$mk_lastDecision_prev['trademkt_budget_distribution'];
					$this->view->advertisingpercentageDecision=$mk_lastDecision_prev['advertising_percentage'];
					$this->view->trademktbudgetDecision=$mk_lastDecision_prev['trademkt_budget'];
					$this->view->trademktpercentageDecision=$mk_lastDecision_prev['trademkt_percentage'];
				}
				
			if ($pr_decisions->existsPrevious($_GET['game_id'], $_GET['company_id'], $_GET['round'])){
				$aux=$region_count->countFactories($_GET['game_id'],$_GET['company_id']);
				$this->view->numberOfFactories=$aux;
				
				$this->view->unitsDecision=$pr_lastDecision['units'];
				$this->view->lastFactory=$games->getLastFactory($_GET['game_id'], $_GET['company_id']);
				$this->view->roundFactory=$games->getRoundFactoryCreated($_GET['game_id'], $_GET['company_id']);
			}
			if ($mk_decisions->existsPrevious($_GET['game_id'], $_GET['company_id'], $_GET['round'])){
				$mk_lastDecision=$mk_decisions->getDecisionArray($_GET['game_id'], $_GET['company_id'], $_GET['round']);
				//var_dump($mk_lastDecision); die();
				$this->view->pricesDecision=$mk_lastDecision['prices'];
				$this->view->advertisingbudgetDecision=$mk_lastDecision['advertising_budget'];
				$this->view->advertisingbudgetProductsDecision=$mk_lastDecision['advertising_budget_distribution'];
				$this->view->trademktbudgetProductsDecision=$mk_lastDecision['trademkt_budget_distribution'];
				//var_dump($this->view->trademktbudgetProductsDecision);die();
				$this->view->advertisingpercentageDecision=$mk_lastDecision['advertising_percentage'];
				$this->view->trademktbudgetDecision=$mk_lastDecision['trademkt_budget'];
				$this->view->trademktpercentageDecision=$mk_lastDecision['trademkt_percentage'];
			}
			if ($su_decisions->existsPrevious($_GET['game_id'], $_GET['company_id'], $_GET['round'])){
				$su_lastDecision=$su_decisions->getDecisionArray($_GET['game_id'], $_GET['company_id'], $_GET['round']);
				$this->view->numberDecision=$su_lastDecision['number'];
				$this->view->paytermsDecision=$su_lastDecision['payterms'];
			}
			//VERO
			if ($st_decisions->existsPrevious($_GET['game_id'], $_GET['company_id'], $_GET['round'])){
				$st_lastDecision=$st_decisions->getDecisionArray($_GET['game_id'], $_GET['company_id'], $_GET['round']);
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
				$this->view->unitsStockRightDecision=$st_outcomes_prev;
			}
			//VERO
			if ($hr_decisions->existsPrevious($_GET['game_id'], $_GET['company_id'], $_GET['round'])){
				$this->view->cuartilDecision=$hr_lastDecision['cuartil'];
			}
			if ($fi_decisions->existsPrevious($_GET['game_id'], $_GET['company_id'], $_GET['round'])){
				$fi_lastDecision=$fi_decisions->getDecisionArray($_GET['game_id'], $_GET['company_id'], $_GET['round']);
				$this->view->termDecision=$fi_lastDecision['term'];
				$this->view->amountDecision=$fi_lastDecision['amount'];
				$this->view->patrimonyDecision=$fi_lastDecision['patrimony'];
				//VERO
				$this->view->investmentDecision=$fi_lastDecision['investment'];
				//VERO
			}
			if ($in_decisions->existsPrevious($_GET['game_id'], $_GET['company_id'], $_GET['round'])){
				$in_lastDecision=$in_decisions->getDecisionArray($_GET['game_id'], $_GET['company_id'], $_GET['round']);
				//var_dump($in_lastDecision['initiativeproduction']); die();
				$this->view->initiativeproductionDecision=$in_lastDecision['initiativeproduction'];
				$this->view->initiativemarketingDecision=$in_lastDecision['initiativemarketing'];
				$this->view->initiativehumanresourcesDecision=$in_lastDecision['initiativehumanresources'];
				$this->view->initiativedeteriorationDecision=$in_lastDecision['initiativedeterioration'];
			}
			if ($mr_decisions->existsPrevious($_GET['game_id'], $_GET['company_id'], $_GET['round'])){
				$mr_lastDecision=$mr_decisions->getDecisionArray($_GET['game_id'], $_GET['company_id'], $_GET['round']);
				//var_dump($mr_lastDecision); die();
				$this->view->marketResearchDecision=$mr_lastDecision;
			}
			if ($idi_decisions->existsPrevious($_GET['game_id'], $_GET['company_id'], $_GET['round'])){
				$idi_lastDecision=$idi_decisions->getDecisionArray($_GET['game_id'], $_GET['company_id'], $_GET['round']);
				//var_dump($idi_lastDecision); die();
				$this->view->changeIdiDecision=$idi_lastDecision['changeIdi'];
				$this->view->newIdiDecision=$idi_lastDecision['newIdi'];
			}
/* PRUEBA DE FINANCIACIÓN
			$round_actual=$_GET['round'];
			$game_id= $_GET['game_id'];
			$company_id = $_GET['company_id'];


			$decision_amount=new Model_DbTable_Decisions_Fi_Amount();
			$decision_term=new Model_DbTable_Decisions_Fi_Term();
			$term_aux = $round_actual-$round_number;
			for ($round_number=1; $round_number<=$round_actual; $round_number++){
				$amount=$decision_amount->getDecision($game_id, $company_id, $round_number);
				$term=$decision_term->getDecision($game_id, $company_id, $round_number);
				$interest=5.66;
				if (($round_actual-$round_number)<$term['term']){
					$payment=($amount['amount']/$term['term']);
					var_dump($payment);
					$annual_quota= ($amount['amount']*($interest*0.01))/(1-pow((1+$interest*0.01),(-$term['term'])));
					$principal_quota = 0;
					$paid_quota=0;
					for ($round_number_aux = 0; $round_number_aux<=$term_aux; $round_number_aux++){
						$costs= $interest*0.01*($amount['amount']-$paid_quota);
						$principal_quota= $annual_quota-$costs;
						$paid_quota+=$principal_quota;
						$payment=$principal_quota;
						var_dump($payment);
					}
				}
				else{
					$payment=0;
				}
				$cr_payment+=$payment;
			}*/

		}
		
		function outcomesAction(){							
			$this->view->controllerName='game';
			$this->view->actionName="view";	
			$this->view->title .= " Ver - Resultados.";			
			if (isset ($_GET['game_id']) && isset($_GET['round_number'])){				
				
				$game_id=$_GET['game_id'];
				$round_number=$_GET['round_number'];
				
				if (isset ($_POST['publish'])){
					$outcomes=new Model_DbTable_Outcomes();
					$outcomes->switchPublication($game_id, $round_number);
				}
				
				$this->view->round_number=$round_number;
				$this->view->headTitle($this->view->title, 'PREPEND');
				
				$games = new Model_DbTable_Games();
				$game = $games->getGame($game_id);	
				$game_channels=$games->getChannels($_GET['game_id']);
				
				// Fabricas y Creditos
				//$factories=$games->getFactories($_GET['game_id'],$_GET['round'],$_GET['company_id']);
				//$amount=$games->getFinanceAmount($_GET['game_id']);

				$outcomes=new Model_DbTable_Outcomes();
				$outcomes->init($game_id,$round_number);
				$this->view->outcomes=$outcomes;
				$this->view->outcomes_production_units=$outcomes->getProductionUnits($_GET['game_id'], $_GET['round_number']);
				$this->view->outcomes_sales_units=$outcomes->getSalesUnits($_GET['game_id'], $_GET['round_number']);
				$this->view->outcomes_prices=$outcomes->getPrices($_GET['game_id'], $_GET['round_number']);
				$this->view->outcomes_sales_incomes=$outcomes->getIncomes($_GET['game_id'], $_GET['round_number']);
				$this->view->outcomes_stocks_units=$outcomes->getStocksUnits($_GET['game_id'], $_GET['round_number']);
				$this->view->outcomes_costs=$outcomes->getCosts($_GET['game_id'], $_GET['round_number']);
				$this->view->outcomes_investments=$outcomes->getInterestInvestment($_GET['game_id'], $_GET['round_number']);
				$this->view->outcomes_balance_sheet=$outcomes->getBalanceSheet($_GET['game_id'], $_GET['round_number']);
				$this->view->prev_outcomes_balance_sheet=0;
				if(($_GET['round_number'])>1){
					$this->view->prev_outcomes_balance_sheet=$outcomes->getBalanceSheet($_GET['game_id'], ($_GET['round_number']-1));
					//echo("CHECK POINT 1: this->view->prev_outcomes_balance_sheet = ".$this->view->prev_outcomes_balance_sheet."<br>");
				}
				//var_dump($this->view->prev_outcomes_balance_sheet);die();
				$this->view->outcomes_performance=$outcomes->getPerformance($_GET['game_id'], $_GET['round_number']);
				//var_dump($this->view->outcomes_sales_incomes);
				//var_dump($this->view->outcomes_costs);

				$this->view->outcomes_production_messages=$outcomes->getProductionMessages($_GET['game_id'], $_GET['round_number']);
				
				$this->view->game = $game;
				$this->view->games = $games;
				$this->view->companies = $games->getCompaniesInGame($_GET['game_id']);
				
				$this->view->channels=$game_channels;
				//productos
				$products=$games->getProducts($game_id);
				$this->view->products=$products;
				$this->view->game_product_availability=$games->getProductsAvailibilityBySomeone($_GET['game_id'], $_GET['round_number']);
				//var_dump($this->view->game_product_availability);die();
				//regiones
				$regions=$games->getRegions($game_id);
				$this->view->regions=$regions;
				//Medias
				$game_media=$games->getMedia($game_id);
				$this->view->media=$game_media;
				//Trademedia
				$trademedia[0]=array('trademedia_number'=>1, 'name'=>'Patrocinio');
				$trademedia[1]=array('trademedia_number'=>2, 'name'=>'Promoción');
				$this->view->trademedia=$trademedia;
				//Iniciativas. De momento no se usa, para el futuro se podría desglosar el coste por cada iniciativa de cada area
				$game_initiatives=$games->getInitiatives($_GET['game_id']);	
				$game_initiatives_prod=$games->getInitiativesProd($_GET['game_id']);
				$game_initiatives_hr=$games->getInitiativesHR($_GET['game_id']);
				$game_initiatives_mkt=$games->getInitiativesMKT($_GET['game_id']);
				$game_initiatives_det=$games->getInitiativesDET($_GET['game_id']);
				$this->view->initiativesProd=$game_initiatives_prod;
				$this->view->initiativesHR=$game_initiatives_hr;
				$this->view->initiativesMKT=$game_initiatives_mkt;
				$this->view->initiativesDET=$game_initiatives_det;
				
				foreach ($this->view->companies as $company) {
					$factories[$company['id']]=$games->getFactories($_GET['game_id'],$company['id']);
					$companies_factories[$company['id']]=$factories[$company['id']];
					$companies_deterioration[$company['id']]=$games->getFactoryDeterioration($_GET['game_id'],$_GET['round_number'],$company['id']);
					$companies_atmosphere[$company['id']]=$games->getWorkAtmosphere($_GET['game_id'],$_GET['round_number'],$company['id']);
					$companies_qualification[$company['id']]=$games->getQualificationLevel($_GET['game_id'],$_GET['round_number'],$company['id']);
					$companies_success[$company['id']]=$games->getSuccessProbabilityOutcomes($_GET['game_id'],$_GET['round_number'],$company['id']);
				}
				$this->view->game_factories=$companies_factories;
				$this->view->deterioration=$companies_deterioration;
				$this->view->atmosphere=$companies_atmosphere;
				$this->view->qualification=$companies_qualification;
				$this->view->success=$companies_success;
				//var_dump($this->view->success);die();
				//var_dump($this->view->atmosphere);die();
				//Estudios de Mercado
				$game_marketResearches_costs=$games->getMarketResearchesCosts($_GET['game_id']);
				$this->view->researchcosts=$game_marketResearches_costs;
				//I+D+i
				$idiProducts=$games->getIdiProducts($_GET['game_id']);
				$this->view->idiProducts=$idiProducts;
				
				$this->view->outcomes_market_sizes=$games->getMarketsSizes($_GET['game_id']);
				$this->view->last_round_market_sizes=$games->getRoundMarketsSizes($_GET['game_id'], $_GET['round_number']);
				
				//var_dump($this->view->outcomes_market_sizes);die();
				foreach ($this->view->companies as $company) {
					$array[$company['id']]=$games->getYearAmortization($_GET['game_id'], $_GET['round_number'], $company['id']);
				}
				$this->view->amortization_view=$array;
				
				foreach ($this->view->companies as $company) {
					$factories=$games->getFactories($_GET['game_id'],$_GET['round_number'],$_GET['id']);
				}
				
				//generación del array de cuotas de mercado
				$this->view->array_cuotas_mercado = prepare_array_cuotas_mercado($this->view);

				
			
			/*	foreach ($this->view->products as $product) {					
					foreach ($this->view->regions as $region) {
						$regions_number=$region['region_number'];
						foreach ($this->view->channels as $channel) {
							$channels_names['channel_number_'.$channel['channel_number']]=$channel['name'];
							foreach ($this->view->companies as $company) {
								$product_availability=$this->view->game_product_availability['product_number_'.$product['product_number']];
								if($product_availability==1){
									$products_number=$product['product_number'];
									$chart['product_number_'.$product['product_number']]['region_number_'.$region['region_number']]['channel_number_'.$channel['channel_number']][$company['id']]=(intval(10000*($games->getRealShare($_GET['game_id'], $company['id'], $_GET['round_number'], $product['product_number'], $region['region_number'], $channel['channel_number']))))/10000;
									$names[$company['id']]=$company['name'];
								}							
							}
						}
						$markets_names['channels']=$channels_names;
						//drawChart($chart, $names, $markets_names, $game_id, $round_number, $regions_number, $products_number);
					}
				}*/
				//$markets_names['products']=$products_names;
/*				$this->view->pie_chart=prepareArrayChart($chart);
				$this->view->pie_names=prepareArrayChart($names);
				$this->view->pie_markets=prepareArrayChart($markets_names);
*/
				
				//$data = array(7, 10, 9, 11, 8);
				

				
				/* Para el futuro, para sacar evoluciones temporales de ingresos y gastos
				$round=$_GET['round_number'];
				for ($index = 1; $index <= $round; $index++) {
					$outcomes_incomes=$outcomes->getIncomes($_GET['game_id'], $index);
					$outcomes_costs=$outcomes->getCosts($_GET['game_id'], $index);
					foreach ($this->view->companies as $company) {
						foreach ($this->view->products as $product) {
							foreach ($this->view->regions as $region) {
								foreach ($this->view->channels as $channel) {
									$incomes[$index][$company['id']]+=$outcomes_incomes['company_'.$company['id']]
																			['product_'.$product['product_number']]
																			['region_'.$region['region_number']]
																			['channel_'.$channel['channel_number']];
								}
							}
						}
					}
					foreach ($this->view->companies as $company) {
						foreach ($this->view->channels as $channel) {
							$costs['round_'.$index][$company['id']]+=$outcomes_costs[$company['id']]['mk_sales_costs'][$channel['channel_number']];
							$costs['round_'.$index][$company['id']]+=$outcomes_costs[$company['id']]['mk_fixed_costs'][$channel['channel_number']];
							$costs['round_'.$index][$company['id']]+=$outcomes_costs[$company['id']]['pr_rawMaterials_costs'][$channel['channel_number']];
						}
						foreach ($this->view->media as $media) {
							$costs['round_'.$index][$company['id']]+=$outcomes_costs[$company['id']]['mk_advert_costs'][$media['media_number']];	
						}
						foreach ($this->view->trademedia as $trademedia) {
							$costs['round_'.$index][$company['id']]+=$outcomes_costs[$company['id']]['mk_trade_costs'][$trademedia['trademedia_number']];	
						}
						$costs['round_'.$index][$company['id']]+=$outcomes_costs[$company['id']]['pr_fixed_cost'];
						$costs['round_'.$index][$company['id']]+=$outcomes_costs[$company['id']]['pr_var_costs'];
						$costs['round_'.$index][$company['id']]+=$outcomes_costs[$company['id']]['pr_distrib_costs'];
						$costs['round_'.$index][$company['id']]+=$outcomes_costs[$company['id']]['hr_hiring_costs'];
						$costs['round_'.$index][$company['id']]+=$outcomes_costs[$company['id']]['hr_training_costs'];
						$costs['round_'.$index][$company['id']]+=$outcomes_costs[$company['id']]['hr_wages_costs'];
						$costs['round_'.$index][$company['id']]+=$outcomes_costs[$company['id']]['fi_debt_costs_st'];
						$costs['round_'.$index][$company['id']]+=$outcomes_costs[$company['id']]['fi_debt_costs_lt'];
						$costs['round_'.$index][$company['id']]+=$outcomes_costs[$company['id']]['initiatives_pr_costs'];
						$costs['round_'.$index][$company['id']]+=$outcomes_costs[$company['id']]['initiatives_mk_costs'];
						$costs['round_'.$index][$company['id']]+=$outcomes_costs[$company['id']]['initiatives_hr_costs'];
						$costs['round_'.$index][$company['id']]+=$outcomes_costs[$company['id']]['market_researches_costs'];
						$costs['round_'.$index][$company['id']]+=$outcomes_costs[$company['id']]['idi_changes_costs'];
						$costs['round_'.$index][$company['id']]+=$outcomes_costs[$company['id']]['idi_new_costs'];
					}
				}
				$this->view->incomes=prepareArrayChart($incomes);
				$this->view->costs=prepareArrayChart($costs);*/	
			}
		}
		function viewAction($params=null){
			$this->view->title .= " Ver.";
			$games = new Model_DbTable_Games();
			if (isset ($_GET['game_id'])){
				$game_id=$_GET['game_id'];
				$game = $games->getGame($game_id);
				$this->view->headTitle($this->view->title, 'PREPEND');
				$this->view->game_id=$game_id;	
				
				$this->view->game = $game;
				$this->view->games = $games;
				$this->view->companies = $games->getCompaniesInGame($game_id);
				
				//decisiones
				$this->view->production=new Model_DbTable_Decisions_Production();
				$this->view->marketing=new Model_DbTable_Decisions_Marketing();
				$this->view->suppliers=new Model_DbTable_Decisions_Suppliers();
				$this->view->humanResources=new Model_DbTable_Decisions_HumanResources();
				$this->view->finance=new Model_DbTable_Decisions_Finance();
				$this->view->initiatives=new Model_DbTable_Decisions_Initiatives();
				//Hay que crear sus tablas
				$this->view->marketresearch=new Model_DbTable_Decisions_MarketResearches();
				$this->view->IDi=new Model_DbTable_Decisions_Idi();
				$this->view->validate_user=new Model_DbTable_Decisions_Va_Validated();
				$this->view->validate_game=$games;
								
				
				$this->view->rounds=$games->getRounds($game_id);

			}
			else{
				$this->view->games = $games->getAllNonTemplateGames();				
			}
			$this->view->controllerName='game';
			$this->view->actionName="view";
			
		}
		
		function editAction($params=null){			
			$games = new Model_DbTable_Games();
			if ($this->getRequest()->isPost()){

				$game_id=$_GET['game_id'];
				
				if (isset ($game_id)){
					$formData = $this->getRequest()->getPost();
					
					if ($formData['formularioGral'] != null){
						$games->editGame($game_id, $formData, 1);	
					}
					if ($formData['formularioParam'] != null){
						$games->editGame($game_id, $formData, 2);	
					}
					if ($formData['formularioModul'] != null){
						$games->editGame($game_id, $formData, 3);	
					}
				
				}				
			}
			if (isset ($_GET['game_id'])){
				$game_id=$_GET['game_id'];
				$game = $games->getGame($game_id);
				$this->view->headTitle($this->view->title, 'PREPEND');				
				$this->view->game_id=$game_id;
				if ($game['is_template']==0){	
					$this->view->title .= " Editar Juego. ";
				}
				else{
					$this->view->title .= " Editar Plantilla. ";
				}
				$this->view->game = $game;
				$this->view->games = $games;
				$this->view->companies = $games->getCompaniesInGame($game_id);
				//rondas
				$game_rounds=$games->getRounds($game_id);
				$this->view->numberOfRounds=count($game_rounds);
				$this->view->game_rounds=$game_rounds;
				//productos
				$game_products=$games->getProducts($game_id);
				$this->view->game_products=$game_products;
				//Budget I+D+i
				$game_products_budget=$games->getProductsBudgets($game_id);
				$this->view->game_products_budget=$game_products_budget;
				//atributos calidad
				//VERO
				$gameQualityParams = new Model_DbTable_Games_Param_Markets_QualityParams();
				$game_quality_params_weight=$gameQualityParams->getQualityParamsWeight($game_id);
				$game_quality_params_name=$gameQualityParams->getQualityParamsName($game_id);
				//$game_quality_params=$games->getQualityParams($game_id);
				//$this->view->game_quality_params=$game_quality_params;
				$this->view->game_quality_params_weight=$game_quality_params_weight;
				$this->view->game_quality_params_name=$game_quality_params_name;


				$gameFunctionalityParams = new Model_DbTable_Games_Param_Markets_FunctionalityParams();
				$game_functionality_params_weight=$gameFunctionalityParams->getFunctionalityParamsWeight($game_id);
				$game_functionality_params_name=$gameFunctionalityParams->getFunctionalityParamsName($game_id);
				$game_functionality_params_cost=$gameFunctionalityParams->getFunctionalityParamsCost($game_id);
				$this->view->game_functionality_params_weight=$game_functionality_params_weight;
				$this->view->game_functionality_params_name=$game_functionality_params_name;
				$this->view->game_functionality_params_cost=$game_functionality_params_cost;


				$gameInvestmentsParams = new Model_DbTable_Games_Param_Markets_InvestmentsParams();
				$game_investment_params_average_performance=$gameInvestmentsParams->getinvestmentParamsAveragePerformace($game_id);
				$game_investment_params_name=$gameInvestmentsParams->getinvestmentParamsName($game_id);
				$game_investment_params_limit=$gameInvestmentsParams->getinvestmentParamsLimit($game_id);
				$this->view->game_investment_params_average_performance=$game_investment_params_average_performance;
				$this->view->game_investment_params_name=$game_investment_params_name;
				$this->view->game_investment_params_limit=$game_investment_params_limit;
				//VERO

				
				//regiones
				$game_regions=$games->getRegions($game_id);
				$this->view->game_regions=$game_regions;
				//canales
				$game_channels=$games->getChannels($game_id);
				$this->view->game_channels=$game_channels;
				//medios
				$game_media=$games->getMedia($game_id);
				$this->view->game_media=$game_media;
				//iniciativas
				$game_initiatives=$games->getInitiatives($game_id);
				$this->view->game_initiatives=$game_initiatives;
				
				//Variables para los estudios de mercado
				//canales
				$channels=$games->getChannels($_GET['game_id']);
				$this->view->channels=$channels;
				//productos
				$products=$games->getProducts($_GET['game_id']);
				$this->view->products=$products;
				//regiones
				$regions=$games->getRegions($_GET['game_id']);
				$this->view->regions=$regions;
				$this->view->round_number=$_GET['round_number'];
				$this->view->game_product_availability=$games->getProductsAvailibilityBySomeone($_GET['game_id'], $_GET['round_number']);

			}
			else{
				$this->view->games = $games->getAllNonTemplateGames();
				$this->view->templates = $games->getTemplates();
				$this->view->title .= " Editar. ";
			}
			
			$this->view->controllerName='game';
			$this->view->actionName="edit";
		}

		function newAction(){
			$this->view->title .= " Crear.";
			$this->view->headTitle($this->view->title, 'PREPEND');
			$this->view->controllerName='game';
			$this->view->actionName="new";
			$games = new Model_DbTable_Games();

			$templates=$games->getTemplates();
			$this->view->templates=$templates;
			$errors=array();
			$form = new Form_NewGame();
			if ($this->getRequest()->isPost()){	
				$formData = $this->getRequest()->getPost();
				$gameFormData=$formData['newGame'];		
					
				print_r($gameFormData);			
				if (! $games->existsByName($gameFormData['name'])){
					//VERO
					$game_data=array('name'=>$gameFormData['name'], 'description'=>$gameFormData['description'], 'is_new'=>1, 
									 'based_on'=>$gameFormData['template'], 'is_template'=>$gameFormData['is_template'],
									 'n_regions'=>$gameFormData['n_regions'], 'n_channels'=>$gameFormData['n_channels'],
									 'n_products'=>$gameFormData['n_products'], 'n_media'=>$gameFormData['n_media'],
									 'n_qualities'=>$gameFormData['n_qualities'], 'n_initiatives'=>$gameFormData['n_initiatives'],
									 'n_functionalities'=>$gameFormData['n_functionalities'], 'n_investment'=>$gameFormData['n_investment']);
					//VERO
					$game_id=$games->addGame($game_data);
					$template_id=$gameFormData['template'];
					$games->applyGameTemplate($game_id, $template_id);
					$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
					$redirector->gotoUrl($url."game/edit?game_id=".$game_id);
				}
				else{
					$errors[]="Ya existe un juego con ese nombre";
				}
				
			}
			$this->view->errors=$errors;
		}
		
		function deleteAction(){			
			if (isset ($_GET['game_id'])){
				$this->view->headTitle($this->view->title, 'PREPEND');						
				$games = new Model_DbTable_Games();
				$this->view->games=$games->deleteGame($_GET['game_id']);
				$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
				$redirector->gotoUrl($url."game/index");
			}
		}
		
		function indexAction(){		
			$this->view->headTitle($this->view->title, 'PREPEND');
			$this->view->controllerName='game';
			$this->view->actionName="index";
			
			
			$games = new Model_DbTable_Games();
			$this->view->games=$games->getAllNonTemplateGames();
			$this->view->templates=$games->getTemplates();
		}
		
		function addcompanyAction(){
			$this->view->title .= " Editar - Añadir Empresa ";
			$this->view->headTitle($this->view->title, 'PREPEND');
			$this->view->controllerName='game';
			$this->view->actionName="addcompany";

			$form = new Form_Company();
			$this->view->form=$form;
			$game_id=$_GET['game_id'];
			if ($this->getRequest()->isPost()){				
				$formData = $this->getRequest()->getPost();
				if (!$form->isValid($formData)){
					$form -> populate($formData);
					$this->view->form=$form;
					$this->view->error = "Algo anda mal.";
				}
				else {					
					$companies = new Model_DbTable_Companies();					
					if (! $companies->exists(array('game_id'=>$game_id, 'name'=>$formData['name']))){	
					//Ojo: daba un error si el nombre del equipo era un número (p.ej. "1"), dado que el fetchRow de exists en Companies.php [DbTable_Companies] encontraba coincidencias aunque no existiera el equipo (!!!)
					//TODO: Ahora no parece detectar nombres duplicados de equipos en un juego, por lo que no entra nunca en el "else" posterior.
						$companyData=array('name'=>$formData['name'], 'game_id'=>$game_id, 'registration_password'=>$formData['registration_password']);
						$companies -> addCompany($companyData);
						$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
						$redirector->gotoUrl($url."game/edit?game_id=".$game_id);
					}
					else {
						$this->view->error = "Ese equipo ya existe.";
					}
				}
			}
		}

		function simulateAction(){	
			$core=new Model_Simulation_Core();
		}
		function downloadAction() {
			require_once 'PHPExcel.php';
			$this->view->controllerName='outcomes';
			$this->view->actionName="download";
			
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(true); //No se renderiza la vista, por defecto			
						
			$games = new Model_DbTable_Games();
			$outcomes= new Model_DbTable_Outcomes();
			$game_id=$_GET['game_id'];
			$round_number=$_GET['round_number'];
			$companies=$games->getCompaniesInGame($game_id);
								
			//PRODUCCIÓN
			/*Se crea el objeto de tipo PHPExcel para componer la hoja excel a través de las consultas a MySQL*/
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->setActiveSheetIndex(0);
			$worksheet = $objPHPExcel->getActiveSheet();
			$worksheet->setTitle(utf8_encode('Producción')); 
			$worksheet->getCell('A1')->setValue(utf8_encode('PRODUCCIÓN'));
			$worksheet->mergeCells('A1:B1'); //Combinamos las celdas
			
			//Contamos el número de regiones en juego			
			$regions=$games->getRegions($game_id);
			$numRegions=count($regions);
			
			//Estilo negrita
			$bold = array ('font' => array('bold' => true));

			//Escribimos el encabezado y combinamos celdas en función del número de regiones				
			$offset=0;
			foreach ($companies as $company){
				$worksheet->setCellValueByColumnAndRow(2+$offset, 1, $company['name']);
				$worksheet->getStyleByColumnAndRow(2+$offset, 1)->applyFromArray($bold);
				//$worksheet->mergeCellsByColumnAndRow(2+$offset, 1, 2+$offset+$numRegions, 1); //Combinamos las celdas
				$offset=$offset+$numRegions;
			}									
						
			//Bucle para la escritura de regiones			
			$region = array();
			foreach ($companies as $company){
				foreach ($regions as $aux){
					$region[]=$aux['name'];
				}
			}
			$regCompanies=count($region);//Producto numero de regiones por número de compañias			
			$worksheet->fromArray($region, null, 'C2');
			//Aplica negrita a las regiones	
			$starting_pos=ord('C');
			$final_pos=chr($starting_pos+$regCompanies-1);		
			$worksheet->getStyleByColumnAndRow('C2:' .$final_pos .'2')->applyFromArray($bold);			
			
			//Obtención de los canales y productos y contamos el número de ellos presentes en el juego
			$channels=$games->getChannels($game_id);
			$numChannels=count($channels);			
			$products=$games->getProducts($game_id);
			$numProducts=count($products);
						
			//Escritura de los productos en la hoja		
			$offset=3;
			foreach ($products as $product){
				$worksheet->setCellValueByColumnAndRow(0, $offset, 'Producto: '.$product['name']);
				$offset=$offset + $numChannels;
			}			
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);

			//Escritura de los canales en la hoja
			$offset=3;
			for ($j=1; $j<=$numProducts; $j++){				
				foreach ($channels as $channel){
					$worksheet->setCellValueByColumnAndRow(1, $offset, 'Canal: '.$channel['name']);
					$offset++;
				}
			}
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			
			//Obtenemos las unidades producidas			
			$outcomes_production_units=$outcomes->getProductionUnits($game_id, $round_number);			

			$offset=3;			
			for ($pr=1; $pr<=$numProducts; $pr++){
				for ($ch=1; $ch<=$numChannels; $ch++){
					foreach ($companies as $company){
						$company_outcomes=$outcomes_production_units['company_'.$company['id']];						 
						for ($reg=1; $reg<=$numRegions; $reg++){
							if (is_null($company_outcomes['product_'.$pr]['region_'.$reg]['channel_'.$ch]))
								$output[]='0';
							else							
								$output[]=$company_outcomes['product_'.$pr]['region_'.$reg]['channel_'.$ch];													
						}						
					}
					$worksheet->fromArray($output, null, 'C'.$offset);
					unset($output); //Elimina los valores del array
					$offset++;
				}					
			}
			//Fin escritura pestaña producción
		
			
			//VENTAS
			$worksheet = new PHPExcel_Worksheet($objPHPExcel, utf8_encode('Ventas'));
			$objPHPExcel->addSheet($worksheet);
			$objPHPExcel->setActiveSheetIndex(1);
			$worksheet->getCell('A1')->setValue(utf8_encode('VENTAS'));
			$worksheet->mergeCells('A1:B1'); //Combinamos las celdas
				
			//Escribimos el encabezado y combinamos celdas en función del número de regiones
			
			$offset=0;
			foreach ($companies as $company){
				$worksheet->setCellValueByColumnAndRow(2+$offset, 1, $company['name']);
				$worksheet->getStyleByColumnAndRow(2+$offset, 1)->applyFromArray($bold);
				//$worksheet->mergeCellsByColumnAndRow(2+$offset, 1, 2+$offset+$numRegions, 1); //Combinamos las celdas
				$offset=$offset+$numRegions*2;
			}					
				
			//En los resultados de ventas se muestran las unidades vendidas y su precio, lo que obliga a combinar las regiones
			$offset=2;
			for ($i=0; $i<$regCompanies; $i++){
				$worksheet->setCellValueByColumnAndRow($offset, 2, $region[$i]);
				$worksheet->getStyleByColumnAndRow($offset, 2)->applyFromArray($bold);//Aplica negrita
				$worksheet->mergeCellsByColumnAndRow($offset, 2, $offset+1, 2);
				$offset=$offset+2;
			}			
				
			//Escritura del par ventas-precio
			$temp=array('Ventas', 'Precio');
			$offset=2;
			foreach ($companies as $company){
				foreach ($regions as $r){
					$worksheet->setCellValueByColumnAndRow($offset, 3, $temp[0]);
					$worksheet->setCellValueByColumnAndRow($offset+1, 3, $temp[1]);
					$offset=$offset+2;
				}
			}			
			
			//Escritura de los productos en la hoja
			$offset=4;
			foreach ($products as $product){
				$worksheet->setCellValueByColumnAndRow(0, $offset, 'Producto: '.$product['name']);
				$offset=$offset + $numChannels;
			}
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
				
			//Escritura de los canales en la hoja
			$offset=4;
			for ($j=1; $j<=$numProducts; $j++){
				foreach ($channels as $channel){
					$worksheet->setCellValueByColumnAndRow(1, $offset, 'Canal: '.$channel['name']);
					$offset++;
				}
			}
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
				
			//Obtenemos las unidades vendidas y su precio			
			$outcomes_sales=$outcomes->getSalesUnits($game_id, $round_number);
			$outcomes_prizes=$outcomes->getExportPrices($game_id, $round_number);			
				
			$col=2;
			$row=4;
			for ($pr=1; $pr<=$numProducts; $pr++){
				for ($ch=1; $ch<=$numChannels; $ch++){
					foreach ($companies as $company){
						$company_sales=$outcomes_sales['company_'.$company['id']];
						$company_prizes=$outcomes_prizes['company_'.$company['id']];
						for ($reg=1; $reg<=$numRegions; $reg++){
							if (is_null($company_sales['product_'.$pr]['region_'.$reg]['channel_'.$ch]))
								$output[]='0';
							else							
								$output[]=$company_sales['product_'.$pr]['region_'.$reg]['channel_'.$ch];
							if (is_null($company_prizes['product_'.$pr]['region_'.$reg]['channel_'.$ch]))
								$output1[]='0';
							else
								$output1[]=$company_prizes['product_'.$pr]['region_'.$reg]['channel_'.$ch];							
						}
					}
					for ($i=0; $i<$regCompanies; $i++){
						$worksheet->setCellValueByColumnAndRow($col,$row, $output[$i]);//Ventas
						$worksheet->setCellValueByColumnAndRow($col+1,$row, $output1[$i]);//Precio
						$col=$col+2;
					}
					unset($output); //Elimina los valores del array
					unset($output1);
					$row++;
					$col=2;
				}
			}//Fin escritura ventas
			
			//Escritura de los sumatorios
			$old_row=$row;
			foreach ($companies as $company){
				$company_sales=$outcomes_sales['company_'.$company['id']];
				$company_prizes=$outcomes_prizes['company_'.$company['id']];
				foreach ($regions as $reg){
					foreach ($products as $product){
						foreach($channels as $channel){							
							$sales+=$company_sales['product_'.$product['product_number']]['region_'.$reg['region_number']]['channel_'.$channel['channel_number']];							
							$prices+=$company_prizes['product_'.$product['product_number']]['region_'.$reg['region_number']]['channel_'.$channel['channel_number']]*$company_sales['product_'.$product['product_number']]['region_'.$reg['region_number']]['channel_'.$channel['channel_number']];							
						}
						$worksheet->setCellValueByColumnAndRow($col,$row, $sales);//Ventas
						$worksheet->setCellValueByColumnAndRow($col+1,$row, $prices);//Precio
						$row++;
						unset($sales);
						unset($prices);						
					}
					$row=$old_row;
					$col=$col+2;									
				}
			}
			
			//STOCKS
				
			$worksheet = new PHPExcel_Worksheet($objPHPExcel, utf8_encode('Stocks'));
			$objPHPExcel->addSheet($worksheet);
			$objPHPExcel->setActiveSheetIndex(2);
			$worksheet->getCell('A1')->setValue(utf8_encode('STOCKS'));
			$worksheet->mergeCells('A1:B1'); //Combinamos las celdas
			
			//Escribimos el encabezado y combinamos celdas en función del número de regiones
			$offset=0;
			foreach ($companies as $company){
				$worksheet->setCellValueByColumnAndRow(2+$offset, 1, $company['name']);
				$worksheet->getStyleByColumnAndRow(2+$offset, 1)->applyFromArray($bold);
				//$worksheet->mergeCellsByColumnAndRow(2+$offset, 1, 2+$offset+$numRegions, 1); //Combinamos las celdas
				$offset=$offset+$numRegions;
			}
			
			$worksheet->fromArray($region, null, 'C2');
			//Aplica negrita a las regiones
			$starting_pos=ord('C');
			$final_pos=chr($starting_pos+$regCompanies-1);
			$worksheet->getStyleByColumnAndRow('C2:' .$final_pos .'2')->applyFromArray($bold);
			
			//Escritura de los productos en la hoja
			$offset=3;
			foreach ($products as $product){
				$worksheet->setCellValueByColumnAndRow(0, $offset, 'Producto: '.$product['name']);
				$offset=$offset + $numChannels;
			}
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			
			//Escritura de los canales en la hoja
			$offset=3;
			for ($j=1; $j<=$numProducts; $j++){
				foreach ($channels as $channel){
					$worksheet->setCellValueByColumnAndRow(1, $offset, 'Canal: '.$channel['name']);
					$offset++;
				}
			}
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			$outcomes_stocks_units=$outcomes->getStocksUnits($game_id, $round_number);
			
			$offset=3;
			for ($pr=1; $pr<=$numProducts; $pr++){
				for ($ch=1; $ch<=$numChannels; $ch++){
					foreach ($companies as $company){
						$company_stocks=$outcomes_stocks_units['company_'.$company['id']];
						for ($reg=1; $reg<=$numRegions; $reg++){
							if (is_null($company_stocks['product_'.$pr]['region_'.$reg]['channel_'.$ch]))
								$output[]='0';
							else
								$output[]=$company_stocks['product_'.$pr]['region_'.$reg]['channel_'.$ch];
						}
					}
					$worksheet->fromArray($output, null, 'C'.$offset);
					unset($output); //Elimina los valores del array
					$offset++;
				}
			}
			//Fin escritura pestaña stocks
			
			//CTAS.RESULTADOS --> INGRESOS
			$worksheet = new PHPExcel_Worksheet($objPHPExcel, utf8_encode('Cuentas Resultados'));
			$objPHPExcel->addSheet($worksheet);
			$objPHPExcel->setActiveSheetIndex(3);
			$worksheet->getCell('A1')->setValue(utf8_encode('CTAS.RESULTADOS'));			
			$worksheet->getCell('A2')->setValue(utf8_encode('INGRESOS'));
				
			$worksheet->getStyle('A1')->applyFromArray($bold);
			$worksheet->getStyle('B1')->applyFromArray($bold);
			
			//Escribimos el encabezado y combinamos celdas en función del número de regiones
			$offset=0;
			foreach ($companies as $company){
				$worksheet->setCellValueByColumnAndRow(1+$offset, 1, $company['name']);
				$worksheet->getStyleByColumnAndRow(1+$offset, 1)->applyFromArray($bold);				
				$offset++;
			}
			
			//Escritura de los canales en la hoja
			$offset=3;
			foreach ($channels as $channel){
				$worksheet->setCellValueByColumnAndRow(0, $offset, $channel['name']);
				$offset++;
			}
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);				
			
			$outcomes_sales_incomes=$outcomes->getIncomes($game_id, $round_number);			
			$offset=3;
			$total_company_incomes = array();			
			for ($ch=1; $ch<=$numChannels; $ch++){
				$col=1;
				foreach ($companies as $company){					
					$company_incomes=$outcomes_sales_incomes['company_'.$company['id']];
					for ($pr=1; $pr<=$numProducts; $pr++){						
						for ($reg=1; $reg<=$numRegions; $reg++){
							if (is_null($company_incomes['product_'.$pr]['region_'.$reg]['channel_'.$ch]))
								$output[]='0';
							else
								$output[]=$company_incomes['product_'.$pr]['region_'.$reg]['channel_'.$ch];							
						}					
					}
					$total_company_incomes[$col]+=array_sum($output);
					$worksheet->setCellValueByColumnAndRow($col, $offset, array_sum($output));
					$col++;
					unset($output); //Elimina los valores del array
				}				
				$offset++;				
			}//Fin escritura ingresos por canal
			
			$worksheet->setCellValueByColumnAndRow(0, $offset, 'Total Ingresos');
			$worksheet->getStyleByColumnAndRow(0, $offset)->applyFromArray($bold);
			$col=1;
			foreach ($companies as $company){
				$worksheet->setCellValueByColumnAndRow($col, $offset, $total_company_incomes[$col]);
				$worksheet->getStyleByColumnAndRow($col, $offset)->applyFromArray($bold);
				$col++;
			}
			$offset++;
			
			//CTAS.RESULTADOS --> GASTOS
			$outcomes_costs=$outcomes->getCosts($game_id, $round_number);
			$worksheet->setCellValueByColumnAndRow(0, $offset, 'GASTOS');
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Producción & Logística'));
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Mantenimiento de Fábricas y Maquinaria'));
			$col=1;
			$total = array();
			foreach ($companies as $company){
				$worksheet->setCellValueByColumnAndRow($col, $offset, $outcomes_costs[$company['id']]['pr_fixed_cost']);
				$total[$col]=$outcomes_costs[$company['id']]['pr_fixed_cost'];//Gastos fijos
				$col++;
			}			
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Fabricación de productos'));
			$col=1;
			foreach ($companies as $company){
				$worksheet->setCellValueByColumnAndRow($col, $offset, $outcomes_costs[$company['id']]['pr_var_costs']);
				$total[$col]+=$outcomes_costs[$company['id']]['pr_var_costs'];//Gastos variables
				$col++;
			}			
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Abastecimiento de materias primas'));
			$offset++;				
			foreach ($channels as $channel){
				$col=1;
				$worksheet->setCellValueByColumnAndRow(0, $offset, $channel['name']);
				foreach ($companies as $company){					
					$worksheet->setCellValueByColumnAndRow($col, $offset, $outcomes_costs[$company['id']]['pr_rawMaterials_costs'][$channel['channel_number']]);					
					$total[$col]+=$outcomes_costs[$company['id']]['pr_rawMaterials_costs'][$channel['channel_number']];
					$col++;
				}
				$offset++;
			}			
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Distribución'));
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Gastos de Distribución'));
			$col=1;
			foreach($companies as $company){
				$worksheet->setCellValueByColumnAndRow($col, $offset, $outcomes_costs[$company['id']]['pr_distrib_costs']);				
				$total[$col]+=$outcomes_costs[$company['id']]['pr_distrib_costs'];
				$col++;				
			}
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Total Producción & Logística'));
			$col=1;
			foreach($companies as $company){
				$worksheet->setCellValueByColumnAndRow($col, $offset, $total[$col]);
				$col++;
			}			
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Marketing'));
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Publicidad'));
			$offset++;
			$medias=$games->getMedia($game_id);			
			foreach ($medias as $media){
				$col=1;
				$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode($media['name']));
				foreach ($companies as $company){
					$worksheet->setCellValueByColumnAndRow($col, $offset, $outcomes_costs[$company['id']]['mk_advert_costs'][$media['media_number']]);
					$cost_media[$col]+=$outcomes_costs[$company['id']]['mk_advert_costs'][$media['media_number']];
					$total[$col]+=$outcomes_costs[$company['id']]['mk_advert_costs'][$media['media_number']];
					$col++;
				}
				$offset++;
			}
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Trade MKT'));
			$offset++;
			$trade_media=array(utf8_encode('Patrocinio'), utf8_encode('Promoción'));
			for ($i=0; $i<2; $i++){
				$col=1;
				$worksheet->setCellValueByColumnAndRow(0, $offset, $trade_media[$i]);
				foreach($companies as $company){
					$worksheet->setCellValueByColumnAndRow($col, $offset, $outcomes_costs[$company['id']]['mk_trade_costs'][$i+1]);
					$cost_trade[$col]+=$outcomes_costs[$company['id']]['mk_trade_costs'][$i+1];
					$total[$col]+=$outcomes_costs[$company['id']]['mk_trade_costs'][$i+1];
					$col++;
				}
				$offset++;
			}
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Gasto por ventas'));
			$offset++;
			foreach ($channels as $channel){
				$col=1;
				$worksheet->setCellValueByColumnAndRow(0, $offset, $channel['name']);
				foreach ($companies as $company){
					$worksheet->setCellValueByColumnAndRow($col, $offset, $outcomes_costs[$company['id']]['mk_sales_costs'][$channel['channel_number']]);
					$cost_sales[$col]+=$outcomes_costs[$company['id']]['mk_sales_costs'][$channel['channel_number']];
					$total[$col]+=$outcomes_costs[$company['id']]['mk_sales_costs'][$channel['channel_number']];
					$col++;
				}
				$offset++;
			}
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Gasto fijo por canales de distribución'));
			$offset++;
			
			foreach ($channels as $channel){
				$col=1;
				$worksheet->setCellValueByColumnAndRow(0, $offset, $channel['name']);
				foreach ($companies as $company){
					$worksheet->setCellValueByColumnAndRow($col, $offset, $outcomes_costs[$company['id']]['mk_fixed_costs'][$channel['channel_number']]);
					$cost_sales_fixed[$col]+=$outcomes_costs[$company['id']]['mk_fixed_costs'][$channel['channel_number']];
					$total[$col]+=$outcomes_costs[$company['id']]['mk_fixed_costs'][$channel['channel_number']];
					$col++;
				}
				$offset++;
			}
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Total Marketing'));
			$col=1;
			foreach ($companies as $company){
				$worksheet->setCellValueByColumnAndRow($col, $offset, $cost_media[$col]+$cost_trade[$col]+$cost_sales[$col]+$cost_sales_fixed[$col]);
				$col++;
			}
			$offset++;
			
			//Gastos RRHH
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Recursos Humanos'));
			$offset++;
			
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Contratación'));
			$col=1;
			foreach ($companies as $company){
				$worksheet->setCellValueByColumnAndRow($col, $offset, $outcomes_costs[$company['id']]['hr_hiring_costs']);
				$hiring_costs[$col]=$outcomes_costs[$company['id']]['hr_hiring_costs'];
				$total[$col]+=$outcomes_costs[$company['id']]['hr_hiring_costs'];
				$col++;
			}
			$offset++;
			
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Formación'));
			$col=1;
			foreach ($companies as $company){
				$worksheet->setCellValueByColumnAndRow($col, $offset, $outcomes_costs[$company['id']]['hr_training_costs']);
				$training_costs[$col]=$outcomes_costs[$company['id']]['hr_training_costs'];
				$total[$col]+=$outcomes_costs[$company['id']]['hr_training_costs'];
				$col++;
			}
			$offset++;
			
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Salarios'));
			$col=1;
			foreach ($companies as $company){
				$worksheet->setCellValueByColumnAndRow($col, $offset, $outcomes_costs[$company['id']]['hr_wages_costs']);
				$wages_costs[$col]=$outcomes_costs[$company['id']]['hr_wages_costs'];
				$total[$col]+=$outcomes_costs[$company['id']]['hr_wages_costs'];
				$col++;
			}
			$offset++;
			
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Total Recursos Humanos'));
			$col=1;
			foreach ($companies as $company){
				$worksheet->setCellValueByColumnAndRow($col, $offset, $hiring_costs[$col]+$training_costs[$col]+$wages_costs[$col]);
				$col++;
			}
			$offset++;
			
			//Gastos Iniciativas
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Iniciativas'));
			$offset++;
			
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Iniciativas de Producción'));
			$col=1;
			foreach ($companies as $company){
				$worksheet->setCellValueByColumnAndRow($col, $offset, $outcomes_costs[$company['id']]['initiatives_pr_costs']);
				$initiatives_pr[$col]=$outcomes_costs[$company['id']]['initiatives_pr_costs'];
				$total[$col]+=$outcomes_costs[$company['id']]['initiatives_pr_costs'];
				$col++;
			}
			$offset++;
			
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Iniciativas de Marketing'));
			$col=1;
			foreach ($companies as $company){
				$worksheet->setCellValueByColumnAndRow($col, $offset, $outcomes_costs[$company['id']]['initiatives_mk_costs']);
				$initiatives_mk[$col]=$outcomes_costs[$company['id']]['initiatives_mk_costs'];
				$total[$col]+=$outcomes_costs[$company['id']]['initiatives_mk_costs'];
				$col++;
			}
			$offset++;
			
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Iniciativas de Recursos Humanos'));
			$col=1;
			foreach ($companies as $company){
				$worksheet->setCellValueByColumnAndRow($col, $offset, $outcomes_costs[$company['id']]['initiatives_hr_costs']);
				$initiatives_hr[$col]=$outcomes_costs[$company['id']]['initiatives_hr_costs'];
				$total[$col]+=$outcomes_costs[$company['id']]['initiatives_hr_costs'];
				$col++;
			}
			$offset++;
			
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Total Iniciativas'));
			$col=1;
			foreach ($companies as $company){
				$worksheet->setCellValueByColumnAndRow($col, $offset, $initiatives_pr[$col]+$initiatives_mk[$col]+$initiatives_hr[$col]);
				$col++;
			}
			$offset++;
			
			//Estudios de Mercado
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Estudios de Mercado'));
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Costes de los Estudios contratados'));
			$col=1;
			foreach ($companies as $company){
				$worksheet->setCellValueByColumnAndRow($col, $offset, $outcomes_costs[$company['id']]['market_researches_costs']);
				$total[$col]+=$outcomes_costs[$company['id']]['market_researches_costs'];
				$col++;
			}
			$offset++;
			
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Total Estudios de Mercado'));
			$col=1;
			foreach ($companies as $company){
				$worksheet->setCellValueByColumnAndRow($col, $offset, $outcomes_costs[$company['id']]['market_researches_costs']);
				$col++;
			}
			$offset++;
			
			//I+D+i
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('I+D+i'));
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Modificación de Productos'));			
			$col=1;
			foreach ($companies as $company){
				$worksheet->setCellValueByColumnAndRow($col, $offset, $outcomes_costs[$company['id']]['idi_changes_costs']);
				$total[$col]+=$outcomes_costs[$company['id']]['idi_changes_costs'];
				$col++;
			}
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Nuevos Productos'));
			$col=1;
			foreach ($companies as $company){
				$worksheet->setCellValueByColumnAndRow($col, $offset, $outcomes_costs[$company['id']]['idi_new_costs']);
				$total[$col]+=$outcomes_costs[$company['id']]['idi_new_costs'];
				$col++;
			}
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Total I+D+i'));
			$col=1;
			foreach ($companies as $company){
				$worksheet->setCellValueByColumnAndRow($col, $offset, $outcomes_costs[$company['id']]['idi_new_costs']+$outcomes_costs[$company['id']]['idi_changes_costs']);
				$col++;
			}
			$offset++;
		
			//Gastos totales
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Total Gastos'));
			$worksheet->getStyleByColumnAndRow(0, $offset)->applyFromArray($bold);
			$col=1;
			foreach ($companies as $company){
				$worksheet->setCellValueByColumnAndRow($col, $offset, $total[$col]);
				$worksheet->getStyleByColumnAndRow($col, $offset)->applyFromArray($bold);
				$col++;
			}
			$offset++;
			
			//Variación existencias
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Variación de Existencias'));
			$worksheet->getStyleByColumnAndRow(0, $offset)->applyFromArray($bold);
			$outcomes_balance_sheet=$outcomes->getBalanceSheet($game_id, $round_number);
			$prev_outcomes_balance_sheet=0;
			if($round_number>1)
				$prev_outcomes_balance_sheet=$outcomes->getBalanceSheet($game_id, $round_number-1);
			
			$col=1;
			foreach ($companies as $company){
				$worksheet->setCellValueByColumnAndRow($col, $offset, $outcomes_balance_sheet[$company['id']]['stock']
													-$prev_outcomes_balance_sheet[$company['id']]['stock']); //Variación existencias				
				$worksheet->getStyleByColumnAndRow($col, $offset)->applyFromArray($bold);
				$inventories[$col]=$outcomes_balance_sheet[$company['id']]['stock']-$prev_outcomes_balance_sheet[$company['id']]['stock'];
				$col++;
			}
			$offset++;
			
			//CTAS.RESULTADOS --> EBITDA
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('EBITDA'));
			$col=1;
			foreach ($companies as $company){
				$ebitda[$col]=$total_company_incomes[$col]-($total[$col]-$inventories[$col]);
				$worksheet->setCellValueByColumnAndRow($col, $offset, $ebitda[$col]); //EBITDA
				$col++;
			}
			$offset++;
			
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Amortizaciones'));
			$worksheet->getStyleByColumnAndRow(0, $offset)->applyFromArray($bold);
			$col=1;
			foreach ($companies as $company){
				$amortization[$col]=$games->getYearAmortization($game_id, $round_number, $company['id']);
				$worksheet->setCellValueByColumnAndRow($col, $offset, $amortization[$col]); //Amortizaciones
				$col++;
			}
			$offset++;
			
			//Financieros
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Financieros'));
			$worksheet->getStyleByColumnAndRow(0, $offset)->applyFromArray($bold);
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Intereses Financiación a C.P.'));
			$col=1;
			foreach ($companies as $company){
				$worksheet->setCellValueByColumnAndRow($col, $offset, $outcomes_costs[$company['id']]['fi_debt_costs_st']); 
				$col++;
			}
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Intereses Financiación a L.P.'));
			$col=1;
			foreach ($companies as $company){
				$worksheet->setCellValueByColumnAndRow($col, $offset, $outcomes_costs[$company['id']]['fi_debt_costs_lt']);
				$col++;
			}
			$offset++;


			$outcomes_investments=$outcomes->getInterestInvestment($game_id, $round_number);
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Intereses ganados por inversiones financieras'));
			$col=1;
			foreach ($companies as $company){
				$worksheet->setCellValueByColumnAndRow($col, $offset, $outcomes_investments[$company['id']]['fi_investment_earnings']);
				$col++;
			}
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Intereses perdidos por inversiones financieras'));
			$col=1;
			foreach ($companies as $company){
				$worksheet->setCellValueByColumnAndRow($col, $offset, $outcomes_investments[$company['id']]['fi_investment_losses']);
				$col++;
			}
			$offset++;

			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Total Financieros'));
			$col=1;
			foreach ($companies as $company){
				$worksheet->setCellValueByColumnAndRow($col, $offset, $outcomes_costs[$company['id']]['fi_debt_costs_lt']+$outcomes_costs[$company['id']]['fi_debt_costs_st']+$outcomes_investments[$company['id']]['fi_investment_losses']-$outcomes_investments[$company['id']]['fi_investment_earnings']);
				$col++;
			}
			$offset++;
			
			//CTAS.RESULTADOS --> EBT
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('EBT'));
			$col=1;
			foreach ($companies as $company){
				$ebt[$col]=$ebitda[$col]-$outcomes_costs[$company['id']]['fi_debt_costs_st']-$outcomes_costs[$company['id']]['fi_debt_costs_lt']+$outcomes_investments[$company['id']]['fi_investment_losses']-$outcomes_investments[$company['id']]['fi_investment_earnings']-$amortization[$col];
				$worksheet->setCellValueByColumnAndRow($col, $offset, $ebt[$col]);
				$col++;
			}
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Impuestos (25%)'));
			$worksheet->getStyleByColumnAndRow(0, $offset)->applyFromArray($bold);
			$col=1;
			foreach ($companies as $company){
				if ($ebt[$col]<=0)
					$taxes[$col]=0;
				else
					$taxes[$col]=$ebt[$col]*0.25;
				
				$worksheet->setCellValueByColumnAndRow($col, $offset, $taxes[$col]); //Impuestos
				$worksheet->getStyleByColumnAndRow($col, $offset)->applyFromArray($bold);
				$col++;
			}
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('RESULTADO'));
			$col=1;
			foreach ($companies as $company){
				$worksheet->setCellValueByColumnAndRow($col, $offset, $ebt[$col]-$taxes[$col]); //Resultado
				$col++;
			}
			
			//BALANCE
			$worksheet = new PHPExcel_Worksheet($objPHPExcel, utf8_encode('Balance'));
			$objPHPExcel->addSheet($worksheet);
			$objPHPExcel->setActiveSheetIndex(4);
				
			$worksheet->getCell('A1')->setValue(utf8_encode('BALANCES'));
			$col=1;
			$offset=1;
			foreach ($companies as $company){
				$worksheet->setCellValueByColumnAndRow($col, $offset, $company['name']);
				$col++;
			}
			$offset++;			
			$worksheet->getCell('A2')->setValue(utf8_encode('ACTIVO'));
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('A) Activo no corriente (I+II)'));
			$worksheet->getStyleByColumnAndRow(0, $offset)->applyFromArray($bold);
			$col=1;
			foreach($companies as $company){
				$asset_no_current_sum[$col]=$outcomes_balance_sheet[$company['id']]['tied_up']-$outcomes_balance_sheet[$company['id']]['amortization'];
				$worksheet->setCellValueByColumnAndRow($col, $offset, $asset_no_current_sum[$col]);//Activo no corriente
				$worksheet->getStyleByColumnAndRow($col, $offset)->applyFromArray($bold);
				$offset++;
				$worksheet->setCellValueByColumnAndRow($col, $offset, $outcomes_balance_sheet[$company['id']]['tied_up']); //Inmovilizado
				$offset++;
				$worksheet->setCellValueByColumnAndRow($col, $offset, -$outcomes_balance_sheet[$company['id']]['amortization']);
				$offset++;
				$asset_current_sum[$col]=$outcomes_balance_sheet[$company['id']]['stock']
										+$outcomes_balance_sheet[$company['id']]['trade_debtors']
										+$outcomes_balance_sheet[$company['id']]['liquid_assets'];
				$worksheet->setCellValueByColumnAndRow($col, $offset, $asset_current_sum[$col]);
				$worksheet->getStyleByColumnAndRow($col, $offset)->applyFromArray($bold);
				$offset++;
				$worksheet->setCellValueByColumnAndRow($col, $offset, $outcomes_balance_sheet[$company['id']]['stock']);
				$offset++;
				$worksheet->setCellValueByColumnAndRow($col, $offset, $outcomes_balance_sheet[$company['id']]['trade_debtors']);
				$offset++;
				$worksheet->setCellValueByColumnAndRow($col, $offset, $outcomes_balance_sheet[$company['id']]['liquid_assets']);
				$offset++;
				$worksheet->setCellValueByColumnAndRow($col, $offset, $asset_no_current_sum[$col]+$asset_current_sum[$col]);
				$offset=$offset+2;//Inicio del pasivo
				$shareholders_funds[$col]=$outcomes_balance_sheet[$company['id']]['capital']
										 +$outcomes_balance_sheet[$company['id']]['reserves']
										 +$outcomes_balance_sheet[$company['id']]['year_result'];
				$worksheet->setCellValueByColumnAndRow($col, $offset, $shareholders_funds[$col]);
				$worksheet->getStyleByColumnAndRow($col, $offset)->applyFromArray($bold);
				$offset++;
				$worksheet->setCellValueByColumnAndRow($col, $offset, $outcomes_balance_sheet[$company['id']]['capital']);
				$offset++;
				$worksheet->setCellValueByColumnAndRow($col, $offset, $outcomes_balance_sheet[$company['id']]['reserves']);
				$offset++;
				$worksheet->setCellValueByColumnAndRow($col, $offset, $outcomes_balance_sheet[$company['id']]['year_result']);
				$offset++;				
				$liabilities_no_current[$col]=$outcomes_balance_sheet[$company['id']]['long_term_debts'];
				$worksheet->setCellValueByColumnAndRow($col, $offset, $liabilities_no_current[$col]);
				$worksheet->getStyleByColumnAndRow($col, $offset)->applyFromArray($bold);
				$offset++;
				$worksheet->setCellValueByColumnAndRow($col, $offset, $liabilities_no_current[$col]);
				$offset++;
				$liabilities_current[$col]=$outcomes_balance_sheet[$company['id']]['short_term_debts']+$outcomes_balance_sheet[$company['id']]['creditors'];
				$worksheet->setCellValueByColumnAndRow($col, $offset, $liabilities_current[$col]);
				$worksheet->getStyleByColumnAndRow($col, $offset)->applyFromArray($bold);
				$offset++;
				$worksheet->setCellValueByColumnAndRow($col, $offset, $outcomes_balance_sheet[$company['id']]['short_term_debts']);
				$offset++;
				$worksheet->setCellValueByColumnAndRow($col, $offset, $outcomes_balance_sheet[$company['id']]['creditors']);
				$offset++;
				$worksheet->setCellValueByColumnAndRow($col, $offset, $shareholders_funds[$col]+$liabilities_no_current[$col]+$liabilities_current[$col]);
				$worksheet->getStyleByColumnAndRow($col, $offset)->applyFromArray($bold);
				$col++;
				$offset=3;
			}
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('I. Inmovilizado'));
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('II. Amortización de inmovilizado'));
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('B) Activo corriente (I+II+III)'));
			$worksheet->getStyleByColumnAndRow(0, $offset)->applyFromArray($bold);
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('I. Existencias'));
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('II. Deudores comerciales'));
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('III. Tesorería'));
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Total ACTIVO (A+B)'));
			$worksheet->getStyleByColumnAndRow(0, $offset)->applyFromArray($bold);
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('PASIVO'));
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('A) Patrimonio Neto (I+II+III)'));
			$worksheet->getStyleByColumnAndRow(0, $offset)->applyFromArray($bold);
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('I. Capital'));
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('II. Resultados de ejercicios anteriores'));
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('III. Resultado del ejercicio'));
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('B) Pasivo no corriente (I)'));
			$worksheet->getStyleByColumnAndRow(0, $offset)->applyFromArray($bold);
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('I. Deudas a largo plazo'));
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('C) Pasivo corriente (I+II)'));
			$worksheet->getStyleByColumnAndRow(0, $offset)->applyFromArray($bold);
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('I. Deudas a corto plazo'));
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('II. Acreedores comerciales'));
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Total PASIVO (A+B+C)'));
			$worksheet->getStyleByColumnAndRow(0, $offset)->applyFromArray($bold);
			
			
		
			/*Se crean los encabezados para descargar fichero con formato Excel 2007*/
			header("Cache-Control: no-store, no-cache, must-revalidate");
			header("Cache-Control: post-check=0, pre-check=0", false);
			header("Pragma: no-cache");
			/*Formato de cabecera Excel 2007*/
			//header('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			
			/*Formato de cabecera Excel 2003*/
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment; filename="test_excel.xls"');
			header('Cache-Control: max-age=0');			
			
			$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);			
			//PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);		
			ob_end_clean();			
			$objWriter->save('php://output');
			
			//Liberación de memoria
			$objPHPExcel->disconnectWorksheets();
			unset($objPHPExcel);
		
		}

	}
	
	function prepare_array_cuotas_mercado ($thisview) {
		
		//generación del array de cuotas de mercado
		$array_cuotas_mercado = null;
		$array_cuotas_mercado = "[['Producto', 'Pais', 'Canal', 'Equipo', 'Unidades vendidas']";
		foreach ($thisview->products as $product){
			if($thisview->game_product_availability['product_number_'.$product['product_number']]==1){
				foreach ($thisview->regions as $region) {
					foreach ($thisview->channels as $channel){
						foreach ($thisview->companies as $company) {
							$array_cuotas_mercado = $array_cuotas_mercado.",['".$product['name']."','".$region['name']."','".$channel['name']."','".$company['name']."'";
							$array_cuotas_mercado = $array_cuotas_mercado.",".number_format($thisview->outcomes_sales_units['company_'.$company['id']]
																		['product_'.$product['product_number']]
																		['region_'.$region['region_number']]
																		['channel_'.$channel['channel_number']],0, '.', '')."]";
						}
					}
					
				}
			}
		}
		
		$array_cuotas_mercado = $array_cuotas_mercado."]";
		return $array_cuotas_mercado;
	
	}
	
	
	/*function drawChart($chartArray, $namesArray, $marketsArray, $game_id, $round_number, $product_number, $region_number) {
		include_once ('Zend/jpgraph/jpgraph.php'); 
		include_once ('Zend/jpgraph/jpgraph_pie.php');
		include_once ('Zend/jpgraph/jpgraph_pie3d.php');				
			
		$graph  = new PieGraph (1000,1000);
		$theme_class= new VividTheme;
		$graph->SetTheme($theme_class);
		$graph->SetShadow(); 
		$graph->title-> Set("Cuotas de mercado"); 
		$graph->title->SetFont(FF_FONT1,FS_BOLD,24);
		//$graph->xaxis->SetLabelAngle(45);
		//Vemos el numero de mercados existentes
		foreach ($chartArray as $aux_array) {
			$region_counter=$region_number;
			while(isset ($aux_array['region_number_'.$region_counter])){
				$array_aux=$aux_array['region_number_'.$region_counter];
				$channel_counter=1;
				$n_markets=0;
				while(isset ($array_aux['channel_number_'.$channel_counter])){
					$n_markets++;
					$data=$array_aux['channel_number_'.$channel_counter];
					$j=0;
					for ($i = 0; $i <= max(array_keys($data)); $i++) {
						if(($data[$i])!=0){
							$string[$j]="Equipo: ".$namesArray[$i];
							$j++;
						}
					}
					$channel_counter++;
				}
				$region_counter++;
			}
		}
		var_dump($product_number);
		var_dump($region_number);
		var_dump($channel_counter);
		$size=1/(2.5*$n_markets);
		foreach ($chartArray as $array) {
			$x_center=0.5;
			$y_center=0.17;
			$region_counter=$region_number;
			while(isset ($array['region_number_'.$region_counter])){
				$channel_counter=1;
				$array_aux=$array['region_number_'.$region_counter];
				$market_counter=0;
				while(isset ($array_aux['channel_number_'.$channel_counter])){
					$market_counter++;
					$data=array_values($array_aux['channel_number_'.$channel_counter]);
					$p1  = new PiePlot3D($data); 			
					$graph->Add($p1); 
					$p1->ShowBorder();
					$p1->SetSize($size);
					if($n_markets==1){
						$p1->SetCenter(($center*0.5),(0.5*$center));
					}
					else {
						$x_position=$x_center;
						$y_position=$y_center*$channel_counter;
						$p1->SetCenter($x_position,$y_position);
					}
					$p1->title->Set(" Canal: ".$marketsArray['channels']['channel_number_'.$channel_counter]);
					echo("<br/> ");
					//$p1->SetLabelFormat("%1.2f");
					$p1->value->Show();
					$channel_counter++;
				}
				$region_counter++;
			}
		}
		//$p1->SetLegends($string);
		//$graph->Stroke();
		$gdImgHandler = $graph->Stroke(_IMG_HANDLER);
		$fileName = "/var/www/simu2/public/tmp/" . md5("img_".$game_id."_".$round_number."_".$product_number."_".$region_number) .".png";
		$graph->img->Stream($fileName);
	}*/
		
?>