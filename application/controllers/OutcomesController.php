<?php	
	class OutcomesController extends Zend_Controller_Action {
		public $_controllerTitle= "Resultados";
		public function preDispatch(){
			$this->view->title = $this->_controllerTitle;			
			$this->_helper->authHelper();
			$front = Zend_Controller_Front::getInstance();			
			$this->game=$front->getParam('activeGame');//carga el juego actual antes de procesar ninguna acciÃ³n
			$this->company=$front->getParam('activeCompany');
	    }
		function indexAction(){
			$this->view->controllerName='outcomes';
			$this->view->actionName="index";
			$outcomes=new Model_DbTable_Outcomes();
			$round_number=$outcomes->getLatestRoundNumber($this->game['id']);
			if (isset ($this->game['id']) && $round_number){
				$this->view->game=$this->game;
				$this->view->company=$this->company;
				$this->view->round_number=$round_number;				
				$this->view->headTitle($this->view->title, 'PREPEND');
				$games = new Model_DbTable_Games();
				$this->view->companies = $games->getCompaniesInGame($this->game['id']);
				$outcomes->init($this->game['id'],$round_number);
				
				$this->view->games = $games;

				//canales
				$channels=$games->getChannels($this->game['id']);
				$this->view->channels=$channels;
				//productos
				$products=$games->getProducts($this->game['id']);
				$this->view->products=$products;
				//regiones
				$regions=$games->getRegions($this->game['id']);
				$this->view->regions=$regions;
				
				$this->view->outcomes=$outcomes;
				$this->view->outcomes_production_units=$outcomes->getProductionUnits($this->game['id'], $round_number);
				$this->view->outcomes_sales_units=$outcomes->getSalesUnits($this->game['id'], $round_number);
				$this->view->outcomes_prices=$outcomes->getPrices($this->game['id'], $round_number);
				$this->view->outcomes_sales_incomes=$outcomes->getIncomes($this->game['id'], $round_number);
				$this->view->outcomes_stocks_units=$outcomes->getStocksUnits($this->game['id'], $round_number);
				$this->view->outcomes_costs=$outcomes->getCosts($this->game['id'], $round_number);
				$this->view->outcomes_balance_sheet=$outcomes->getBalanceSheet($this->game['id'], $round_number);
				$this->view->prev_outcomes_balance_sheet=0;
				if($round_number>1){
					$this->view->prev_outcomes_balance_sheet=$outcomes->getBalanceSheet($this->game['id'], $round_number-1);
					//echo("CHECK POINT 1: this->view->prev_outcomes_balance_sheet = ".$this->view->prev_outcomes_balance_sheet."<br>");
				}
				$this->view->outcomes_performance=$outcomes->getPerformance($this->game['id'], $round_number);
				$this->view->outcomes_production_messages=$outcomes->getProductionMessages($this->game['id'], $round_number);

				//productos
				$this->view->game_product_availability=$games->getProductsAvailibilityBySomeone($this->game['id'], $round_number);

				//regiones
				//Medias
				$game_media=$games->getMedia($this->game['id']);
				$this->view->media=$game_media;

				//Trademedia
				$trademedia[0]=array('trademedia_number'=>1, 'name'=>'Patrocinio');
				$trademedia[1]=array('trademedia_number'=>2, 'name'=>'PromociÃ³n');
				$this->view->trademedia=$trademedia;

				//Iniciativas. De momento no se usa, para el futuro se podr’a desglosar el coste por cada iniciativa de cada area
				$game_initiatives=$games->getInitiatives($this->game['id']);	
				$game_initiatives_prod=$games->getInitiativesProd($this->game['id']);
				$game_initiatives_hr=$games->getInitiativesHR($this->game['id']);
				$game_initiatives_mkt=$games->getInitiativesMKT($this->game['id']);
				$game_initiatives_det=$games->getInitiativesDET($this->game['id']);
				$this->view->initiativesProd=$game_initiatives_prod;
				$this->view->initiativesHR=$game_initiatives_hr;
				$this->view->initiativesMKT=$game_initiatives_mkt;
				$this->view->initiativesDET=$game_initiatives_det;

				$factories[$this->company['id']]=$games->getFactories($this->game['id'],$this->company['id']);
				$companies_factories[$this->company['id']]=$factories[$this->company['id']];
				$companies_deterioration[$this->company['id']]=$games->getFactoryDeterioration($this->game['id'],$round_number,$this->company['id']);
				$companies_atmosphere[$this->company['id']]=$games->getWorkAtmosphere($this->game['id'],$round_number,$this->company['id']);
				$companies_qualification[$this->company['id']]=$games->getQualificationLevel($this->game['id'],$round_number,$this->company['id']);
				$companies_success[$this->company['id']]=$games->getSuccessProbabilityOutcomes($this->game['id'],$round_number,$this->company['id']);
				$this->view->game_factories=$companies_factories;
				$this->view->deterioration=$companies_deterioration;
				$this->view->atmosphere=$companies_atmosphere;
				$this->view->qualification=$companies_qualification;
				$this->view->success=$companies_success;

				//Estudios de Mercado
				$game_marketResearches_costs=$games->getMarketResearchesCosts($this->game['id']);
				$this->view->researchcosts=$game_marketResearches_costs;

				//I+D+i
				$idiProducts=$games->getIdiProducts($this->game['id']);
				$this->view->idiProducts=$idiProducts;
				
				$this->view->outcomes_market_sizes=$games->getMarketsSizesCurrentRound($this->game['id'],$round_number);
				//var_dump($this->view->outcomes_market_sizes);

				//$array[$this->company['id']]=$games->getYearAmortization($this->game['id'], $round_number, $this->company['id']);
				//$this->view->amortization_view=$array;
				
				// 20130509: Esto no lo muestra bien porque envía siempre el mismo CompanyID. Sustituido por la línea posterior y la creación de la función getYearAmortizationArray() en Games.php
				//foreach ($this->view->companies as $company) {
				//	$array[$company['id']]=$games->getYearAmortization($this->game['id'], $round_number, $this->company['id']);
				//}
				//$this->view->amortization_view=$array;
				$this->view->amortization_view=$games->getYearAmortizationArray($this->game['id'], $round_number);
				
				$researches=new Model_DbTable_Decisions_MarketResearches();
				$this->view->market_researches=$researches->getMarketResearchesSolicited($this->game['id'], $this->company['id'], $round_number);

				//$availability_chart=$games->getProductsAvailibilityBySomeone($this->game['id'],$round_number);
				/*foreach ($this->view->products as $product) {
					$product_availability=$this->view->game_product_availability['product_number_'.$product['product_number']];
					if($product_availability==1){
						$products_names['product_number_'.$product['product_number']]=$product['name'];
						foreach ($this->view->regions as $region) {
							$regions_names['region_number_'.$region['region_number']]=$region['name'];							
							foreach ($this->view->channels as $channel) {
								$channels_names['channel_number_'.$channel['channel_number']]=$channel['name'];								
								foreach ($this->view->companies as $company) {								
									$chart['product_number_'.$product['product_number']]['region_number_'.$region['region_number']]['channel_number_'.$channel['channel_number']][$company['id']]=(intval(10000*($games->getRealShare($this->game['id'], $company['id'], $round_number, $product['product_number'], $region['region_number'], $channel['channel_number']))))/10000;
									$names[$company['id']]=$company['name'];
								}
							}							
						}
					}
				}*/


				$markets_names['products']=$products_names;
				$markets_names['regions']=$regions_names;
				$markets_names['channels']=$channels_names;
				/*$this->view->pie_chart=prepareArrayChart($chart);
				$this->view->pie_names=prepareArrayChart($names);
				$this->view->pie_markets=prepareArrayChart($markets_names);*/

				foreach ($this->view->products as $product) {
					foreach ($this->view->regions as $region) {
						foreach ($this->view->channels as $channel) {
							$result=$games->getPriceSituation($this->game['id'],$round_number,$this->company['id'], $product['product_number'], $region['region_number'], $channel['channel_number']);
							$situation['product_number_'.$product['product_number']]['region_number_'.$region['region_number']]['channel_number_'.$channel['channel_number']]=$result['situation'];
							$max['product_number_'.$product['product_number']]['region_number_'.$region['region_number']]['channel_number_'.$channel['channel_number']]=$result['max'];
							$min['product_number_'.$product['product_number']]['region_number_'.$region['region_number']]['channel_number_'.$channel['channel_number']]=$result['min'];						
						}
					}
				}
				$this->view->prices_research=$situation;
				$this->view->prices_max=$max;
				$this->view->prices_min=$min;

				$this->view->mkt_advertising_research=$games->getMktSituation($this->game['id'],$this->view->round_number,$this->company['id']);
				$this->view->mkt_trade_research=$games->getTradeSituation($this->game['id'],$this->view->round_number,$this->company['id']);

				$this->view->lastFactory=$games->getLastFactory($this->game['id'], $this->company['id']);
				$this->view->roundFactory=$games->getRoundFactoryCreated($this->game['id'], $this->company['id']);
				
				//generación del array de producción general
				$this->view->array_production_general = prepare_array_production_general($this->view, $this->company['id']);
				
				
				//generación del array de producción por producto y canal
				$this->view->array_production_productoYcanal = prepare_array_production_productoYcanal($this->view, $this->company['id']);
				
				//generación array general de stocks
				$this->view->array_stocks_general = prepare_array_stocks_general($this->view, $this->company['id']);
				
				//generación del array de stocks por producto y canal
				$this->view->array_stocks_productoYcanal = prepare_array_stocks_productoYcanal($this->view, $this->company['id']);
				
				//generación del array de ventas general
				$this->view->array_ventas_general = prepare_array_ventas_general($this->view, $this->company['id']);
				
				//generación del array de ventas general
				$this->view->array_ventas_productoYcanal = prepare_array_ventas_productoYcanal($this->view, $this->company['id']);
				
				//generacioón del array de cuotas de mercado
				$this->view->array_cuotas_mercado = prepare_array_cuotas_mercado($this->view);

			}
			
		}
		function historyAction(){
			$this->view->title .= " / HistÃ³rico.";			
			$this->view->controllerName='outcomes';
			$this->view->actionName="history";	
			
			$outcomes=new Model_DbTable_Outcomes();
			$this->view->outcomes=$outcomes->getPastOutcomes($this->game['id']);
			$this->view->last_round=$outcomes->getLatestRoundNumber($this->game['id']);						
		}
		function viewAction(){
			$this->view->controllerName='outcomes';
			$this->view->actionName="index";
			$outcomes=new Model_DbTable_Outcomes();
			$round_number=$_GET['round_number'];
			if (isset ($this->game['id']) && $round_number){
				$this->view->game=$this->game;
				$this->view->company=$this->company;
				$this->view->round_number=$round_number;				
				$this->view->headTitle($this->view->title, 'PREPEND');
				$games = new Model_DbTable_Games();
				$this->view->companies = $games->getCompaniesInGame($this->game['id']);
				$outcomes->init($this->game['id'],$round_number);
				
				$this->view->games = $games;
				//canales
				$channels=$games->getChannels($this->game['id']);
				$this->view->channels=$channels;
				//productos
				$products=$games->getProducts($this->game['id']);
				$this->view->products=$products;
				//regiones
				$regions=$games->getRegions($this->game['id']);
				$this->view->regions=$regions;

				$this->view->outcomes=$outcomes;
				$this->view->outcomes_production_units=$outcomes->getProductionUnits($this->game['id'], $round_number);
				$this->view->outcomes_sales_units=$outcomes->getSalesUnits($this->game['id'], $round_number);
				$this->view->outcomes_prices=$outcomes->getPrices($this->game['id'], $round_number);
				$this->view->outcomes_sales_incomes=$outcomes->getIncomes($this->game['id'], $round_number);
				$this->view->outcomes_stocks_units=$outcomes->getStocksUnits($this->game['id'], $round_number);
				$this->view->outcomes_costs=$outcomes->getCosts($this->game['id'], $round_number);
				$this->view->outcomes_balance_sheet=$outcomes->getBalanceSheet($this->game['id'], $round_number);
				$this->view->prev_outcomes_balance_sheet=0;
				if($round_number>1){
					$this->view->prev_outcomes_balance_sheet=$outcomes->getBalanceSheet($this->game['id'], $round_number-1);
					//echo("CHECK POINT 1: this->view->prev_outcomes_balance_sheet = ".$this->view->prev_outcomes_balance_sheet."<br>");
				}
				$this->view->outcomes_performance=$outcomes->getPerformance($this->game['id'], $round_number);
				$this->view->outcomes_production_messages=$outcomes->getProductionMessages($this->game['id'], $round_number);

				//productos
				$this->view->game_product_availability=$games->getProductsAvailibilityBySomeone($this->game['id'], $round_number);

				//Medias
				$game_media=$games->getMedia($this->game['id']);
				$this->view->media=$game_media;

				//Trademedia
				$trademedia[0]=array('trademedia_number'=>1, 'name'=>'Patrocinio');
				$trademedia[1]=array('trademedia_number'=>2, 'name'=>'PromociÃ³n');
				$this->view->trademedia=$trademedia;

				//Iniciativas. De momento no se usa, para el futuro se podr’a desglosar el coste por cada iniciativa de cada area
				$game_initiatives=$games->getInitiatives($this->game['id']);	
				$game_initiatives_prod=$games->getInitiativesProd($this->game['id']);
				$game_initiatives_hr=$games->getInitiativesHR($this->game['id']);
				$game_initiatives_mkt=$games->getInitiativesMKT($this->game['id']);
				$game_initiatives_det=$games->getInitiativesDET($this->game['id']);
				$this->view->initiativesProd=$game_initiatives_prod;
				$this->view->initiativesHR=$game_initiatives_hr;
				$this->view->initiativesMKT=$game_initiatives_mkt;
				$this->view->initiativesDET=$game_initiatives_det;
				
				$factories[$this->company['id']]=$games->getFactories($this->game['id'],$this->company['id']);
				$companies_factories[$this->company['id']]=$factories[$this->company['id']];
				$companies_deterioration[$this->company['id']]=$games->getFactoryDeterioration($this->game['id'],$round_number,$this->company['id']);
				$companies_atmosphere[$this->company['id']]=$games->getWorkAtmosphere($this->game['id'],$round_number,$this->company['id']);
				$companies_qualification[$this->company['id']]=$games->getQualificationLevel($this->game['id'],$round_number,$this->company['id']);
				$companies_success[$this->company['id']]=$games->getSuccessProbabilityOutcomes($this->game['id'],$round_number,$this->company['id']);
					
				$this->view->game_factories=$companies_factories;
				$this->view->deterioration=$companies_deterioration;
				$this->view->atmosphere=$companies_atmosphere;
				$this->view->qualification=$companies_qualification;
				$this->view->success=$companies_success;

				//Estudios de Mercado
				$game_marketResearches_costs=$games->getMarketResearchesCosts($this->game['id']);
				$this->view->researchcosts=$game_marketResearches_costs;

				//I+D+i
				$idiProducts=$games->getIdiProducts($this->game['id']);
				$this->view->idiProducts=$idiProducts;
				
				$this->view->outcomes_market_sizes=$games->getMarketsSizesCurrentRound($this->game['id'], $round_number);
				//var_dump($this->view->outcomes_market_sizes);die();

				//$array[$this->company['id']]=$games->getYearAmortization($this->game['id'], $round_number, $this->company['id']);
				//$this->view->amortization_view=$array;
				//foreach ($this->view->companies as $company) {
				//	$array[$company['id']]=$games->getYearAmortization($this->game['id'], $round_number, $this->company['id']);
				//}
				//$this->view->amortization_view=$array;
				$this->view->amortization_view=$games->getYearAmortizationArray($this->game['id'], $round_number);
				
				$researches=new Model_DbTable_Decisions_MarketResearches();
				$this->view->market_researches=$researches->getMarketResearchesSolicited($this->game['id'], $this->company['id'], $round_number);
				
				/*foreach ($this->view->products as $product) {
					$product_availability=$this->view->game_product_availability['product_number_'.$product['product_number']];
					if($product_availability==1){
						$products_names['product_number_'.$product['product_number']]=$product['name'];
						foreach ($this->view->regions as $region) {
							$regions_names['region_number_'.$region['region_number']]=$region['name'];
							foreach ($this->view->channels as $channel) {
								$channels_names['channel_number_'.$channel['channel_number']]=$channel['name'];								
								foreach ($this->view->companies as $company) {
									$names[$company['id']]=$company['name'];
									$chart['product_number_'.$product['product_number']]['region_number_'.$region['region_number']]['channel_number_'.$channel['channel_number']][$company['id']]=(intval(10000*($games->getRealShare($this->game['id'], $company['id'], $round_number, $product['product_number'], $region['region_number'], $channel['channel_number']))))/10000;
								}
							}							
						}
					}
				}*/

				$markets_names['products']=$products_names;
				$markets_names['regions']=$regions_names;
				$markets_names['channels']=$channels_names;

				/*$this->view->pie_chart=prepareArrayChart($chart);
				$this->view->pie_names=prepareArrayChart($names);
				$this->view->pie_markets=prepareArrayChart($markets_names);*/
				
				foreach ($this->view->products as $product) {
					foreach ($this->view->regions as $region) {
						foreach ($this->view->channels as $channel) {
							$result=$games->getPriceSituation($this->game['id'],$round_number,$this->company['id'], $product['product_number'], $region['region_number'], $channel['channel_number']);
							$situation['product_number_'.$product['product_number']]['region_number_'.$region['region_number']]['channel_number_'.$channel['channel_number']]=$result['situation'];
							$max['product_number_'.$product['product_number']]['region_number_'.$region['region_number']]['channel_number_'.$channel['channel_number']]=$result['max'];
							$min['product_number_'.$product['product_number']]['region_number_'.$region['region_number']]['channel_number_'.$channel['channel_number']]=$result['min'];												}
					}
				}

				$this->view->prices_research=$situation;
				$this->view->prices_max=$max;
				$this->view->prices_min=$min;
				
				$this->view->mkt_advertising_research=$games->getMktSituation($this->game['id'],$round_number,$this->company['id']);
				$this->view->mkt_trade_research=$games->getTradeSituation($this->game['id'],$round_number,$this->company['id']);
				
				$this->view->lastFactory=$games->getLastFactory($this->game['id'], $this->company['id']);
				$this->view->roundFactory=$games->getRoundFactoryCreated($this->game['id'], $this->company['id']);
				
				//generación del array general de producción
				$this->view->array_production_general = prepare_array_production_general($this->view, $this->company['id']);			
				
				//generación del array de producción por producto y canal
				$this->view->array_production_productoYcanal = prepare_array_production_productoYcanal($this->view, $this->company['id']);
				
				//generación array general de stocks
				$this->view->array_stocks_general = prepare_array_stocks_general($this->view, $this->company['id']);
				
				//generación del array de stocks por producto y canal
				$this->view->array_stocks_productoYcanal = prepare_array_stocks_productoYcanal($this->view, $this->company['id']);
				
				//generación del array de ventas general
				$this->view->array_ventas_general = prepare_array_ventas_general($this->view, $this->company['id']);
				
				//generación del array de ventas general
				$this->view->array_ventas_productoYcanal = prepare_array_ventas_productoYcanal($this->view, $this->company['id']);
				
				//generacioón del array de cuotas de mercado
				$this->view->array_cuotas_mercado = prepare_array_cuotas_mercado($this->view);
	
			}
		}
		
		/*Funcion para la descarga de los resultados en modo usuario: Resultados del equipo al que se pertenece*/
		function downloadAction() {
			require_once 'PHPExcel.php';
			$this->view->controllerName='outcomes';
			$this->view->actionName="download";
			
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(true); //No se renderiza la vista, por defecto
			
			$games = new Model_DbTable_Games();
			$outcomes= new Model_DbTable_Outcomes();
			$company= $this->company;			
			$getData=$this->getRequest()->getParams();			
			//$round_number=$outcomes->getLatestRoundNumber($this->game['id']); //MODIFICAR PARA EL CASO DE RONDAS ANTERIORES!!
			$round_number = $getData['round_number'];
			//print_r($round_number);die();
			
			/*Se crea el objeto de tipo PHPExcel para componer la hoja excel a través de las consultas a MySQL*/
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->setActiveSheetIndex(0);
			$worksheet = $objPHPExcel->getActiveSheet();
			$worksheet->setTitle(utf8_encode('Producción')); 
			$worksheet->getCell('A1')->setValue(utf8_encode('PRODUCCIÓN'));
			$worksheet->mergeCells('A1:B1'); //Combinamos las celdas
			
			//Contamos el número de regiones en juego			
			$regions=$games->getRegions($this->game['id']);
			$numRegions=count($regions);
			//Escribimos el encabezado y combinamos celdas en función del número de regiones
			$worksheet->getCell('C1')->setValue('Unidades Producidas');
			//Aplica negrita
			$bold = array ('font' => array('bold' => true));
			$worksheet->getStyle('C1')->applyFromArray($bold);			
			$worksheet->mergeCellsByColumnAndRow(2, 1, $numRegions+1, 1); //Combinamos las celdas	
			
			//Bucle para la escritura de regiones			
			$region = array();
			foreach ($regions as $aux){
				$region[]=$aux['name'];
			}			
			$worksheet->fromArray($region, null, 'C2');
			//Aplica negrita a las regiones	
			$starting_pos=ord('C');
			$final_pos=chr($starting_pos+$numRegions-1);		
			$worksheet->getStyle('C2:' .$final_pos .'2')->applyFromArray($bold);			
			
			//Obtención de los canales y productos y contamos el número de ellos presentes en el juego
			$channels=$games->getChannels($this->game['id']);
			$numChannels=count($channels);								
			$products=$games->getProducts($this->game['id']);
			$numberProductsAvailable = $games->getNumberOfProductsAvailable($this->game['id'], $round_number, $company['id']);		
			$numProducts=count($products);
									
			//Escritura de los productos en la hoja		
			$offset=3;
			foreach ($products as $product){
				if($games->getProductAvailibility($this->game['id'], $round_number, $company['id'], $product['product_number'])==1){
					$worksheet->setCellValueByColumnAndRow(0, $offset, 'Producto: '.$product['name']);
					$offset=$offset + $numChannels;
				}
			}			
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);

			//Escritura de los canales en la hoja
			$offset=3;
			for ($j=1; $j<=$numberProductsAvailable; $j++){				
				foreach ($channels as $channel){
					$worksheet->setCellValueByColumnAndRow(1, $offset, 'Canal: '.$channel['name']);
					$offset++;
				}
			}
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			
			//Obtenemos las unidades producidas
			
			$outcomes_production_units=$outcomes->getProductionUnits($this->game['id'], $round_number);
			$company_outcomes=$outcomes_production_units['company_'.$company['id']];

			$offset=3;
			for ($pr=1; $pr<=$numProducts; $pr++){
				for ($ch=1; $ch<=$numChannels; $ch++){ 
					for ($reg=1; $reg<=$numRegions; $reg++){
						if (is_null($company_outcomes['product_'.$pr]['region_'.$reg]['channel_'.$ch]))
							$output[]='0';
						elseif($games->getProductAvailibility($this->game['id'], $round_number, $company['id'], $pr)==1)
							//$output[]=number_format($company_outcomes['product_'.$pr]['region_'.$reg]['channel_'.$ch], 0, '.', ',');	
							$output[]=$company_outcomes['product_'.$pr]['region_'.$reg]['channel_'.$ch];
									
													
					}
					if($games->getProductAvailibility($this->game['id'], $round_number, $company['id'], $pr)==1){
						$worksheet->fromArray($output, null, 'C'.$offset);						
						$offset++;
					}
					unset($output); //Elimina los valores del array
				}
					
			}//Fin escritura pestaña producción

			//VENTAS				
			$worksheet = new PHPExcel_Worksheet($objPHPExcel, utf8_encode('Ventas'));
			$objPHPExcel->addSheet($worksheet);			
			$worksheet->getCell('A1')->setValue(utf8_encode('VENTAS'));
			$worksheet->mergeCells('A1:B1'); //Combinamos las celdas
			
			//Escribimos el encabezado y combinamos celdas en función del número de regiones
			$worksheet->getCell('C1')->setValue('Unidades Vendidas');
			
			//Aplica negrita a encabezado			
			$worksheet->getStyle('C1')->applyFromArray($bold);				
			$worksheet->mergeCellsByColumnAndRow(2, 1, $numRegions*2, 1); //Combinamos las celdas
			
			//En los resultados de ventas se muestran las unidades vendidas y su precio, lo que obliga a combinar las regiones
			$offset=2;
			for ($i=0; $i<$numRegions; $i++){
				$worksheet->setCellValueByColumnAndRow($offset, 2, $region[$i]);
				$worksheet->mergeCellsByColumnAndRow($offset, 2, $offset+1, 2);
				$offset=$offset+2;
			}			
			//Aplica negrita a las regiones
			$starting_pos=ord('C');
			$final_pos=chr($starting_pos+$numRegions*2);
			$worksheet->getStyle('C2:' .$final_pos .'2')->applyFromArray($bold);
			
			//Escritura del par ventas-precio
			$temp=array('Ventas', 'Precio');			
			$starting_pos=ord('C');
			$final_pos=chr($starting_pos);
			for ($i=0; $i<$numRegions; $i++){
				$worksheet->fromArray($temp, null, $final_pos .'3');
				$starting_pos=$starting_pos+2;
				$final_pos=chr($starting_pos);
			}
			//Escritura de los productos en la hoja
			$offset=4;
			foreach ($products as $product){
				if($games->getProductAvailibility($this->game['id'], $round_number, $company['id'], $product['product_number'])==1){
					$worksheet->setCellValueByColumnAndRow(0, $offset, 'Producto: '.$product['name']);
					$offset=$offset + $numChannels;
				}
			}			
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			
			//Escritura de los canales en la hoja
			$offset=4;
			for ($j=1; $j<=$numberProductsAvailable; $j++){
				foreach ($channels as $channel){
					$worksheet->setCellValueByColumnAndRow(1, $offset, 'Canal: '.$channel['name']);
					$offset++;
				}
			}
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			
			//Obtenemos las unidades vendidas y su precio
			//$round_number=$outcomes->getLatestRoundNumber($this->game['id']); //A MODIFICAR PARA EL CASO DE RONDAS ANTERIORES!!
			$outcomes_sales=$outcomes->getSalesUnits($this->game['id'], $round_number);						
			$outcomes_prizes=$outcomes->getExportPrices($this->game['id'], $round_number);
			$company_sales=$outcomes_sales['company_'.$company['id']];
			$company_prizes=$outcomes_prizes['company_'.$company['id']];				
			
			$col=2;
			$row=4;
			for ($pr=1; $pr<=$numProducts; $pr++){
				for ($ch=1; $ch<=$numChannels; $ch++){
					for ($reg=1; $reg<=$numRegions; $reg++){
						if (is_null($company_sales['product_'.$pr]['region_'.$reg]['channel_'.$ch]))
							$output[]='0';
						elseif($games->getProductAvailibility($this->game['id'], $round_number, $company['id'], $pr)==1)							
							$output[]=$company_sales['product_'.$pr]['region_'.$reg]['channel_'.$ch];
						if (is_null($company_prizes['product_'.$pr]['region_'.$reg]['channel_'.$ch]))
							$output1[]='0';						
						elseif($games->getProductAvailibility($this->game['id'], $round_number, $company['id'], $pr)==1)							
							$output1[]=number_format($company_prizes['product_'.$pr]['region_'.$reg]['channel_'.$ch], 2, '.', ',');												
					}					
					for ($i=0; $i<$numRegions; $i++){
						if ($games->getProductAvailibility($this->game['id'], $round_number, $company['id'], $pr)==1){						
							$worksheet->setCellValueByColumnAndRow($col,$row, $output[$i]);//Ventas						
							$worksheet->setCellValueByColumnAndRow($col+1,$row, $output1[$i]);//Precio
							$col=$col+2;
						}													
					}					
					unset($output); //Elimina los valores del array
					unset($output1);					
					$row++;
					$col=2;
				}
			}//Fin escritura ventas
			
			//STOCKS
			
			$worksheet = new PHPExcel_Worksheet($objPHPExcel, utf8_encode('Stocks'));
			$objPHPExcel->addSheet($worksheet);
			$worksheet->getCell('A1')->setValue(utf8_encode('STOCKS'));
			$worksheet->mergeCells('A1:B1'); //Combinamos las celdas
				
			//Escribimos el encabezado y combinamos celdas en función del número de regiones
			$worksheet->getCell('C1')->setValue('Unidades en Stock');
				
			//Aplica negrita a encabezado			
			$worksheet->getStyle('C1')->applyFromArray($bold);
			$worksheet->mergeCellsByColumnAndRow(2, 1, $numRegions+1, 1); //Combinamos las celdas
			
			$worksheet->fromArray($region, null, 'C2');
			//Aplica negrita a las regiones
			$starting_pos=ord('C');
			$final_pos=chr($starting_pos+$numRegions-1);
			$worksheet->getStyle('C2:' .$final_pos .'2')->applyFromArray($bold);			
			
			//Escritura de los productos en la hoja
			$offset=3;
			foreach ($products as $product){
				if($games->getProductAvailibility($this->game['id'], $round_number, $company['id'], $product['product_number'])==1){
					$worksheet->setCellValueByColumnAndRow(0, $offset, 'Producto: '.$product['name']);
					$offset=$offset + $numChannels;
				}
			}			
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			
			//Escritura de los canales en la hoja
			$offset=3;
			for ($j=1; $j<=$numberProductsAvailable; $j++){
				foreach ($channels as $channel){
					$worksheet->setCellValueByColumnAndRow(1, $offset, 'Canal: '.$channel['name']);
					$offset++;
				}
			}
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			
			//Obtenemos el stock de unidades
			$outcomes_stocks_units=$outcomes->getStocksUnits($this->game['id'], $round_number);
			$company_stock=$outcomes_stocks_units['company_'.$company['id']];
			
			
			//Bucle escritura de las unidades de stock
			$offset=3;
			for ($pr=1; $pr<=$numProducts; $pr++){
				for ($ch=1; $ch<=$numChannels; $ch++){
					for ($reg=1; $reg<=$numRegions; $reg++){
						if (is_null($company_stock['product_'.$pr]['region_'.$reg]['channel_'.$ch]))
							$output[]='0';
						elseif($games->getProductAvailibility($this->game['id'], $round_number, $company['id'], $pr)==1)
							$output[]=$company_stock['product_'.$pr]['region_'.$reg]['channel_'.$ch];							
							
					}
					if ($games->getProductAvailibility($this->game['id'], $round_number, $company['id'], $pr)==1){
						$worksheet->fromArray($output, null, 'C'.$offset);
						$offset++;
					}
					unset($output); //Elimina los valores del array
					
				}
					
			}//Fin escritura pestaña Stocks
			
			//CTAS.RESULTADOS --> INGRESOS			
			$worksheet = new PHPExcel_Worksheet($objPHPExcel, utf8_encode('Cuentas Resultados'));
			$objPHPExcel->addSheet($worksheet);
			$objPHPExcel->setActiveSheetIndex(3);
			$worksheet->getCell('A1')->setValue(utf8_encode('CTAS.RESULTADOS'));
			$worksheet->getCell('B1')->setValue($company['name']);
			$worksheet->getCell('A2')->setValue(utf8_encode('INGRESOS'));
			
			$worksheet->getStyle('A1')->applyFromArray($bold);
			$worksheet->getStyle('B1')->applyFromArray($bold);
						
			//Escritura de los canales en la hoja
			$offset=3;			
			foreach ($channels as $channel){
				$worksheet->setCellValueByColumnAndRow(0, $offset, $channel['name']);
				$offset++;
			}			
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			
			$outcomes_sales_incomes=$outcomes->getIncomes($this->game['id'], $round_number);
			$company_incomes=$outcomes_sales_incomes['company_'.$company['id']];
			$offset=3;
			$total_incomes=0;
			for ($ch=1; $ch<=$numChannels; $ch++){
				for ($pr=1; $pr<=$numProducts; $pr++){
					for ($reg=1; $reg<=$numRegions; $reg++){
						if (is_null($company_incomes['product_'.$pr]['region_'.$reg]['channel_'.$ch]))
							$output[]='0';
						else
							$output[]=$company_incomes['product_'.$pr]['region_'.$reg]['channel_'.$ch];
							
					}					
				}
				$total_incomes+=array_sum($output);
				$worksheet->setCellValueByColumnAndRow(1, $offset, array_sum($output));
				unset($output); //Elimina los valores del array
				$offset++;
			}//Fin escritura ingresos por canal			
			
			$worksheet->setCellValueByColumnAndRow(0, $offset, 'Total Ingresos');
			$worksheet->getStyleByColumnAndRow(0, $offset)->applyFromArray($bold);
			$worksheet->setCellValueByColumnAndRow(1, $offset, $total_incomes);
			$worksheet->getStyleByColumnAndRow(1, $offset)->applyFromArray($bold);
			$offset++;
			
			//CTAS.RESULTADOS --> GASTOS 
			$outcomes_costs=$outcomes->getCosts($this->game['id'], $round_number);			
			$worksheet->setCellValueByColumnAndRow(0, $offset, 'GASTOS');
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Producción & Logística'));
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Fábricas y Maquinaria'));								
			$worksheet->setCellValueByColumnAndRow(1, $offset, $outcomes_costs[$company['id']]['pr_fixed_cost']);
			$total = array();
			$total[0]=$outcomes_costs[$company['id']]['pr_fixed_cost'];//Gastos fijos
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Fabricación de productos'));
			$worksheet->setCellValueByColumnAndRow(1, $offset, $outcomes_costs[$company['id']]['pr_var_costs']);
			$total[1]=$outcomes_costs[$company['id']]['pr_var_costs'];//Gastos variables
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Abastecimiento de materias primas'));
			$offset++;
			$i=1;					
			foreach ($channels as $channel){
				$worksheet->setCellValueByColumnAndRow(0, $offset, $channel['name']);
				$worksheet->setCellValueByColumnAndRow(1, $offset, $outcomes_costs[$company['id']]['pr_rawMaterials_costs'][$i]);				
				$cost_materials+=$outcomes_costs[$company['id']]['pr_rawMaterials_costs'][$i];
				$offset++;
				$i++;
			}
			$total[2]=$cost_materials;//Costes de materiales. GASTOS PRODUCCIÓN Y LOGÍSTICA HASTA AQUÍ
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Distribución'));
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Gastos de Distribución'));
			$worksheet->setCellValueByColumnAndRow(1, $offset, $outcomes_costs[$company['id']]['pr_distrib_costs']);
			$total[3]=$outcomes_costs[$company['id']]['pr_distrib_costs']; //Gastos distribución
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Total Producción & Logística'));			
			$worksheet->setCellValueByColumnAndRow(1, $offset, array_sum($total));
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Marketing'));
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Publicidad'));
			$offset++;
			$medias=$games->getMedia($this->game['id']);
			$publicidad = array();
			foreach ($medias as $media){
				$publicidad[]=$media['name'];
			}
			$numMedias=count($publicidad);								
			for ($i=0; $i<$numMedias; $i++){
				$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode($publicidad[$i]));
				$worksheet->setCellValueByColumnAndRow(1, $offset, $outcomes_costs[$company['id']]['mk_advert_costs'][$i+1]);
				$cost_media+=$outcomes_costs[$company['id']]['mk_advert_costs'][$i+1];
				$offset++;
			}
			$total[4]=$cost_media; //Gastos Publicidad
			unset($publicidad);
			
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Trade MKT'));
			$offset++;
			$trade_media=array(utf8_encode('Patrocinio'), utf8_encode('Promoción'));
			for ($i=0; $i<2; $i++){
				$worksheet->setCellValueByColumnAndRow(0, $offset, $trade_media[$i]);
				$worksheet->setCellValueByColumnAndRow(1, $offset, $outcomes_costs[$company['id']]['mk_trade_costs'][$i+1]);
				$cost_trade+=$outcomes_costs[$company['id']]['mk_trade_costs'][$i+1];
				$offset++;
			}
			$total[5]=$cost_trade; //Costes Trade
			unset($trade_media);
			
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Gasto por ventas'));
			$offset++;
			foreach ($channels as $channel){
				$worksheet->setCellValueByColumnAndRow(0, $offset, $channel['name']);
				$worksheet->setCellValueByColumnAndRow(1, $offset, $outcomes_costs[$company['id']]['mk_sales_costs'][$channel['channel_number']]);				
				$cost_sales+=$outcomes_costs[$company['id']]['mk_sales_costs'][$channel['channel_number']];
				$offset++;
			}
			$total[6]=$cost_sales; //Costes Ventas
			
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Gasto fijo por canales de distribución'));
			$offset++;
			foreach ($channels as $channel){
				$worksheet->setCellValueByColumnAndRow(0, $offset, $channel['name']);
				$worksheet->setCellValueByColumnAndRow(1, $offset, $outcomes_costs[$company['id']]['mk_fixed_costs'][$channel['channel_number']]);
				$cost_mk_fixed+=$outcomes_costs[$company['id']]['mk_fixed_costs'][$channel['channel_number']];
				$offset++;
			}
			$total[7]=$cost_mk_fixed; //Costes fijos canales distribución--> INDICES 4 a 7 Total Marketing
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Total Marketing'));
			$worksheet->setCellValueByColumnAndRow(1, $offset, $total[4]+$total[5]+$total[6]+$total[7]);
			$offset++;
			
			//Gastos RRHH
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Recursos Humanos'));
			$offset++;			
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Contratación'));
			$worksheet->setCellValueByColumnAndRow(1, $offset, $outcomes_costs[$company['id']]['hr_hiring_costs']);
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Formación'));
			$worksheet->setCellValueByColumnAndRow(1, $offset, $outcomes_costs[$company['id']]['hr_training_costs']);
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Salarios'));
			$worksheet->setCellValueByColumnAndRow(1, $offset, $outcomes_costs[$company['id']]['hr_wages_costs']);
			$offset++;			
			$total[8]=$outcomes_costs[$company['id']]['hr_hiring_costs']+$outcomes_costs[$company['id']]['hr_training_costs']+$outcomes_costs[$company['id']]['hr_wages_costs'];
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Total Recursos Humanos'));
			$worksheet->setCellValueByColumnAndRow(1, $offset, $total[8]); //Costes totales RRHH
			$offset++;
			
			//Gastos Iniciativas
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Iniciativas'));
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Iniciativas de Producción'));
			$worksheet->setCellValueByColumnAndRow(1, $offset, $outcomes_costs[$company['id']]['initiatives_pr_costs']);
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Iniciativas de Marketing'));
			$worksheet->setCellValueByColumnAndRow(1, $offset, $outcomes_costs[$company['id']]['initiatives_mk_costs']);
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Iniciativas de Recursos Humanos'));
			$worksheet->setCellValueByColumnAndRow(1, $offset, $outcomes_costs[$company['id']]['initiatives_hr_costs']);
			$offset++;
			$total[9]=$outcomes_costs[$company['id']]['initiatives_pr_costs']+$outcomes_costs[$company['id']]['initiatives_mk_costs']+$outcomes_costs[$company['id']]['initiatives_hr_costs'];
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Total Iniciativas'));
			$worksheet->setCellValueByColumnAndRow(1, $offset, $total[9]); //Costes totales RRHH
			$offset++;
			
			//Estudios de Mercado
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Estudios de Mercado'));
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Costes de los Estudios contratados'));
			$worksheet->setCellValueByColumnAndRow(1, $offset, $outcomes_costs[$company['id']]['market_researches_costs']);
			$offset++;
			$total[10]=$outcomes_costs[$company['id']]['market_researches_costs'];
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Total Estudios de Mercado'));
			$worksheet->setCellValueByColumnAndRow(1, $offset, $total[10]); //Costes totales Estudios de Mercado
			$offset++;
			
			//I+D+i
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('I+D+i'));
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Modificación de Productos'));
			$worksheet->setCellValueByColumnAndRow(1, $offset, $outcomes_costs[$company['id']]['idi_changes_costs']);
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Nuevos Productos'));
			$worksheet->setCellValueByColumnAndRow(1, $offset, $outcomes_costs[$company['id']]['idi_new_costs']);
			$offset++;
			$total[11]=$outcomes_costs[$company['id']]['idi_changes_costs']+$outcomes_costs[$company['id']]['idi_new_costs'];
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Total I+D+i'));
			$worksheet->setCellValueByColumnAndRow(1, $offset, $total[11]); //Costes totales I+D+i
			$offset++;
			
			//Gastos totales
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Total Gastos'));
			$worksheet->getStyleByColumnAndRow(0, $offset)->applyFromArray($bold);
			$worksheet->setCellValueByColumnAndRow(1, $offset, array_sum($total)); //Gastos totales 
			$worksheet->getStyleByColumnAndRow(1, $offset)->applyFromArray($bold);
			$offset++;
			
			//Variación existencias
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Variación de Existencias'));
			$worksheet->getStyleByColumnAndRow(0, $offset)->applyFromArray($bold);
			$outcomes_balance_sheet=$outcomes->getBalanceSheet($this->game['id'], $round_number);
			$prev_outcomes_balance_sheet=0;
			if($round_number>1)
				$prev_outcomes_balance_sheet=$outcomes->getBalanceSheet($this->game['id'], $round_number-1);
			
			$inventories=$outcomes_balance_sheet[$company['id']]['stock']-$prev_outcomes_balance_sheet[$company['id']]['stock'];			
			$worksheet->setCellValueByColumnAndRow(1, $offset, $inventories); //Variación existencias
			$worksheet->getStyleByColumnAndRow(1, $offset)->applyFromArray($bold);
			$offset++;
			
			//CTAS.RESULTADOS --> EBITDA
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('EBITDA'));			
			$ebitda=$total_incomes-(array_sum($total)-$inventories);
			$worksheet->setCellValueByColumnAndRow(1, $offset, $ebitda); //EBITDA
			$offset++;
			
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Amortizaciones'));
			$worksheet->getStyleByColumnAndRow(0, $offset)->applyFromArray($bold);
			$amortization=$games->getYearAmortization($this->game['id'], $round_number, $company['id']);
			$worksheet->setCellValueByColumnAndRow(1, $offset, $amortization); //Amortizaciones
			
			//Financieros
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Financieros'));
			$worksheet->getStyleByColumnAndRow(0, $offset)->applyFromArray($bold);
			$offset++;			
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Intereses Financiación a C.P.'));
			$worksheet->setCellValueByColumnAndRow(1, $offset, $outcomes_costs[$company['id']]['fi_debt_costs_st']);
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Intereses Financiación a L.P.'));
			$worksheet->setCellValueByColumnAndRow(1, $offset, $outcomes_costs[$company['id']]['fi_debt_costs_lt']);
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Total Financieros'));
			$worksheet->setCellValueByColumnAndRow(1, $offset, $outcomes_costs[$company['id']]['fi_debt_costs_lt']+$outcomes_costs[$company['id']]['fi_debt_costs_st']);
			$offset++;
			
			//CTAS.RESULTADOS --> EBT
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('EBT'));
			$ebt=$ebitda-$outcomes_costs[$company['id']]['fi_debt_costs_st']-$outcomes_costs[$company['id']]['fi_debt_costs_lt']-$amortization;
			$worksheet->setCellValueByColumnAndRow(1, $offset, $ebt); //EBT
			$offset++;
			
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Impuestos (25%)'));
			$worksheet->getStyleByColumnAndRow(0, $offset)->applyFromArray($bold);
			if ($ebt<=0) 
				$taxes=0;			
			else
				$taxes=$ebt*0.25;
						
			$worksheet->setCellValueByColumnAndRow(1, $offset, $taxes); //Impuestos
			$worksheet->getStyleByColumnAndRow(1, $offset)->applyFromArray($bold);
			$offset++;
			
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('RESULTADO'));
			$worksheet->setCellValueByColumnAndRow(1, $offset, $ebt - $taxes); //Resultado
			
			//BALANCE
			$worksheet = new PHPExcel_Worksheet($objPHPExcel, utf8_encode('Balance'));
			$objPHPExcel->addSheet($worksheet);
			$objPHPExcel->setActiveSheetIndex(4);
			
			$worksheet->getCell('A1')->setValue(utf8_encode('BALANCES'));
			$worksheet->getCell('B1')->setValue($company['name']);
			$worksheet->getCell('A2')->setValue(utf8_encode('ACTIVO'));
				
			$worksheet->getStyle('A1')->applyFromArray($bold);
			$worksheet->getStyle('B1')->applyFromArray($bold);
			
			$offset=3;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('A) Activo no corriente (I+II)'));
			$worksheet->getStyleByColumnAndRow(0, $offset)->applyFromArray($bold);
			$asset_no_current_sum=$outcomes_balance_sheet[$company['id']]['tied_up']-$outcomes_balance_sheet[$company['id']]['amortization'];
			$worksheet->setCellValueByColumnAndRow(1, $offset, $asset_no_current_sum); 
			$worksheet->getStyleByColumnAndRow(1, $offset)->applyFromArray($bold);
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('I. Inmovilizado'));
			$worksheet->setCellValueByColumnAndRow(1, $offset, $outcomes_balance_sheet[$company['id']]['tied_up']);
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('II. Amortización de inmovilizado'));
			$worksheet->setCellValueByColumnAndRow(1, $offset, -$outcomes_balance_sheet[$company['id']]['amortization']);
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('B) Activo corriente (I+II+III)'));
			$worksheet->getStyleByColumnAndRow(0, $offset)->applyFromArray($bold);
			$asset_current_sum=$outcomes_balance_sheet[$company['id']]['stock']
							   +$outcomes_balance_sheet[$company['id']]['trade_debtors']
							   +$outcomes_balance_sheet[$company['id']]['liquid_assets'];
			$worksheet->setCellValueByColumnAndRow(1, $offset, $asset_current_sum);
			$worksheet->getStyleByColumnAndRow(1, $offset)->applyFromArray($bold);
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('I. Existencias'));
			$worksheet->setCellValueByColumnAndRow(1, $offset, $outcomes_balance_sheet[$company['id']]['stock']);
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('II. Deudores comerciales'));
			$worksheet->setCellValueByColumnAndRow(1, $offset, $outcomes_balance_sheet[$company['id']]['trade_debtors']);
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('III. Tesorería'));
			$worksheet->setCellValueByColumnAndRow(1, $offset, $outcomes_balance_sheet[$company['id']]['liquid_assets']);
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Total ACTIVO (A+B)'));
			$worksheet->getStyleByColumnAndRow(0, $offset)->applyFromArray($bold);
			$worksheet->setCellValueByColumnAndRow(1, $offset, $asset_no_current_sum+$asset_current_sum);
			$worksheet->getStyleByColumnAndRow(1, $offset)->applyFromArray($bold);
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('PASIVO'));
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('A) Patrimonio Neto (I+II+III)'));
			$worksheet->getStyleByColumnAndRow(0, $offset)->applyFromArray($bold);
			$shareholders_funds=$outcomes_balance_sheet[$company['id']]['capital']
							   +$outcomes_balance_sheet[$company['id']]['reserves']
							   +$outcomes_balance_sheet[$company['id']]['year_result'];
			$worksheet->setCellValueByColumnAndRow(1, $offset, $shareholders_funds);
			$worksheet->getStyleByColumnAndRow(1, $offset)->applyFromArray($bold);
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('I. Capital'));
			$worksheet->setCellValueByColumnAndRow(1, $offset, $outcomes_balance_sheet[$company['id']]['capital']);
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('II. Reservas'));
			$worksheet->setCellValueByColumnAndRow(1, $offset, $outcomes_balance_sheet[$company['id']]['reserves']);
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('III. Resultado del ejercicio'));
			$worksheet->setCellValueByColumnAndRow(1, $offset, $outcomes_balance_sheet[$company['id']]['year_result']);
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('B) Pasivo no corriente (I)'));
			$worksheet->getStyleByColumnAndRow(0, $offset)->applyFromArray($bold);
			$liabilities_no_current=$outcomes_balance_sheet[$company['id']]['long_term_debts'];
			$worksheet->setCellValueByColumnAndRow(1, $offset, $liabilities_no_current);
			$worksheet->getStyleByColumnAndRow(1, $offset)->applyFromArray($bold);
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('I. Deudas a largo plazo'));
			$worksheet->setCellValueByColumnAndRow(1, $offset, $liabilities_no_current);
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('C) Pasivo corriente (I+II)'));
			$worksheet->getStyleByColumnAndRow(0, $offset)->applyFromArray($bold);
			$liabilities_current=$outcomes_balance_sheet[$company['id']]['short_term_debts']+$outcomes_balance_sheet[$company['id']]['creditors'];
			$worksheet->setCellValueByColumnAndRow(1, $offset, $liabilities_current);
			$worksheet->getStyleByColumnAndRow(1, $offset)->applyFromArray($bold);
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('I. Deudas a corto plazo'));
			$worksheet->setCellValueByColumnAndRow(1, $offset, $outcomes_balance_sheet[$company['id']]['short_term_debts']);
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('II. Acreedores comerciales'));
			$worksheet->setCellValueByColumnAndRow(1, $offset, $outcomes_balance_sheet[$company['id']]['creditors']);
			$offset++;
			$worksheet->setCellValueByColumnAndRow(0, $offset, utf8_encode('Total PASIVO (A+B+C)'));
			$worksheet->getStyleByColumnAndRow(0, $offset)->applyFromArray($bold);
			$worksheet->setCellValueByColumnAndRow(1, $offset, $shareholders_funds+$liabilities_no_current+$liabilities_current);
			$worksheet->getStyleByColumnAndRow(1, $offset)->applyFromArray($bold);			
			
			
			/*Se crean los encabezados para descargar fichero con formato Excel 2007*/
			header("Cache-Control: no-store, no-cache, must-revalidate");
			header("Cache-Control: post-check=0, pre-check=0", false);
			header("Pragma: no-cache");
			/*Formato de cabecera Excel 2007*/
			//header('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			
			/*Formato de cabecera Excel 2003*/
			$teamName=str_replace(" ", "", $company['name']);			
			$filename="OutcomesEASE_".$teamName."_Round".$round_number.".xls";
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment; filename='.$filename);
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
	
	//generación del array general de producción
	function prepare_array_production_general($thisview, $mycompanyid) {				
		$array_production_general = null;
		$array_production_general = "[['Producto', 'Canal'";
		foreach ($thisview->regions as $region){ 
			$array_production_general =  $array_production_general.','."'".$region['name']."'";
		}
		$array_production_general =  $array_production_general.", 'TOTAL']";
		foreach ($thisview->products as $product){
			if($thisview->game_product_availability['product_number_'.$product['product_number']]==1){
				foreach ($thisview->channels as $channel){
					$array_production_general = $array_production_general.","."["."'".$product['name']."'".","."'".$channel['name']."'";
					$suma_production_regiones = 0;
					foreach ($thisview->regions as $region){
						$suma_production_regiones = $suma_production_regiones + $thisview->outcomes_production_units['company_'.$mycompanyid]
							['product_'.$product['product_number']]
							['region_'.$region['region_number']]
							['channel_'.$channel['channel_number']];
						
						$array_production_general = $array_production_general.",".number_format($thisview->outcomes_production_units['company_'.$mycompanyid]
							['product_'.$product['product_number']]
							['region_'.$region['region_number']]
							['channel_'.$channel['channel_number']], 0, '', '');
					}
					$array_production_general = $array_production_general.",".number_format($suma_production_regiones, 0, '', '')."]";
				}
			}
		}
		$array_production_general = $array_production_general."]";
		return $array_production_general;
	}
	
	
	function prepare_array_production_productoYcanal($thisview, $mycompanyid) {
		$array_production_productoYcanal = null;
		$array_production_productoYcanal = "[['Producto'";
		foreach ($thisview->channels as $channel){
			$array_production_productoYcanal = $array_production_productoYcanal.','."'".$channel['name']."'";
		}
		$array_production_productoYcanal = $array_production_productoYcanal."]";
		foreach ($thisview->products as $product){
			if($thisview->game_product_availability['product_number_'.$product['product_number']]==1){
				$array_production_productoYcanal = $array_production_productoYcanal.","."["."'".$product['name']."'";
				foreach ($thisview->channels as $channel){
					$suma_produccion_regiones_en_canal = 0;
					foreach ($thisview->regions as $region){
						$suma_produccion_regiones_en_canal = $suma_produccion_regiones_en_canal + $thisview->outcomes_production_units['company_'.$mycompanyid]
									['product_'.$product['product_number']]
									['region_'.$region['region_number']]
									['channel_'.$channel['channel_number']];
					}
					$array_production_productoYcanal = $array_production_productoYcanal.",".number_format($suma_produccion_regiones_en_canal,0, '', '');
				}
				$array_production_productoYcanal = $array_production_productoYcanal."]";
			}
		}
		$array_production_productoYcanal = $array_production_productoYcanal."]";
		return $array_production_productoYcanal;
	}
	
	function prepare_array_stocks_general($thisview, $mycompanyid){
		$array_stocks_general = null;
		$array_stocks_general = "[['Producto', 'Canal'";
		foreach ($thisview->regions as $region){ 
			$array_stocks_general =  $array_stocks_general.','."'".$region['name']."'";
		}
		$array_stocks_general =  $array_stocks_general.", 'TOTAL']";
		foreach ($thisview->products as $product){
			if($thisview->game_product_availability['product_number_'.$product['product_number']]==1){
				foreach ($thisview->channels as $channel){
					$array_stocks_general = $array_stocks_general.","."["."'".$product['name']."'".","."'".$channel['name']."'";
					$suma_stocks_regiones = 0;
					foreach ($thisview->regions as $region){
						$suma_stocks_regiones = $suma_stocks_regiones + $thisview->outcomes_stocks_units['company_'.$mycompanyid]
							['product_'.$product['product_number']]
							['region_'.$region['region_number']]
							['channel_'.$channel['channel_number']];
						
						$array_stocks_general = $array_stocks_general.",".number_format($thisview->outcomes_stocks_units['company_'.$mycompanyid]
							['product_'.$product['product_number']]
							['region_'.$region['region_number']]
							['channel_'.$channel['channel_number']], 0, '', '');
					}
					$array_stocks_general = $array_stocks_general.",".number_format($suma_stocks_regiones, 0, '', '')."]";
				}
			}
		}
		$array_stocks_general = $array_stocks_general."]";
		return $array_stocks_general;
	}
	
	function prepare_array_stocks_productoYcanal($thisview, $mycompanyid) {
		//generación del array de stocks por producto y canal
		$array_stocks_productoYcanal = null;
		$array_stocks_productoYcanal = "[['Producto'";
		foreach ($thisview->channels as $channel){
			$array_stocks_productoYcanal = $array_stocks_productoYcanal.','."'".$channel['name']."'";
		}
		$array_stocks_productoYcanal = $array_stocks_productoYcanal."]";
		foreach ($thisview->products as $product){
			if($thisview->game_product_availability['product_number_'.$product['product_number']]==1){
				$array_stocks_productoYcanal = $array_stocks_productoYcanal.","."["."'".$product['name']."'";
				foreach ($thisview->channels as $channel){
					$suma_stocks_regiones_en_canal = 0;
					foreach ($thisview->regions as $region){
						$suma_stocks_regiones_en_canal = $suma_stocks_regiones_en_canal + $thisview->outcomes_stocks_units['company_'.$mycompanyid]
									['product_'.$product['product_number']]
									['region_'.$region['region_number']]
									['channel_'.$channel['channel_number']];
					}
					$array_stocks_productoYcanal = $array_stocks_productoYcanal.",".number_format($suma_stocks_regiones_en_canal,0, '', '');
				}
				$array_stocks_productoYcanal = $array_stocks_productoYcanal."]";
			}
		}
		$array_stocks_productoYcanal = $array_stocks_productoYcanal."]";
		return $array_stocks_productoYcanal;
	}
	
	
	function prepare_array_ventas_general($thisview, $mycompanyid) {
		$array_ventas_general = null;
		$array_ventas_general = "[['Producto', 'Canal'";
		foreach ($thisview->regions as $region){ 
			$array_ventas_general =  $array_ventas_general.','."'".$region['name']."'";
			$array_ventas_general =  $array_ventas_general.','."'".$region['name']."'";
			$array_ventas_general =  $array_ventas_general.','."'".$region['name']."'";
			
		}
		$array_ventas_general =  $array_ventas_general."]";
		foreach ($thisview->products as $product){
			if($thisview->game_product_availability['product_number_'.$product['product_number']]==1){
				foreach ($thisview->channels as $channel){
					$array_ventas_general = $array_ventas_general.","."["."'".$product['name']."'".","."'".$channel['name']."'";
					foreach ($thisview->regions as $region){
						$outcomes_sales_units_regionYcanal = null;
						$outcomes_prices_regionYcanal = null;
						$outcomes_sales_units_regionYcanal  = $thisview->outcomes_sales_units['company_'.$mycompanyid]
																	['product_'.$product['product_number']]
																	['region_'.$region['region_number']]
																	['channel_'.$channel['channel_number']];
						$outcomes_prices_regionYcanal = $thisview->outcomes_prices['company_'.$mycompanyid]
																	['product_'.$product['product_number']]
																	['channel_'.$channel['channel_number']]
																	['region_'.$region['region_number']];
						$outcomes_total_sales_regionYcanal = $outcomes_sales_units_regionYcanal*$outcomes_prices_regionYcanal;
						
						
						$array_ventas_general = $array_ventas_general.",".number_format($outcomes_sales_units_regionYcanal,0, '.', '');
						$array_ventas_general = $array_ventas_general.",".number_format($outcomes_prices_regionYcanal,2, '.', '');
						$array_ventas_general = $array_ventas_general.",".number_format($outcomes_total_sales_regionYcanal,2, '.', '');
					}
					$array_ventas_general = $array_ventas_general."]";
				}
			}
		}
		$array_ventas_general = $array_ventas_general."]";
		return $array_ventas_general;
	}
	
	
	function prepare_array_ventas_productoYcanal($thisview, $mycompanyid) {
				//generación del array de stocks por producto y canal
		$array_ventas_productoYcanal = null;
		$array_ventas_productoYcanal = "[['Producto'";
		foreach ($thisview->channels as $channel){
			$array_ventas_productoYcanal = $array_ventas_productoYcanal.','."'".$channel['name']."'";
			$array_ventas_productoYcanal = $array_ventas_productoYcanal.','."'".$channel['name']."'";
		}
		$array_ventas_productoYcanal = $array_ventas_productoYcanal."]";
		foreach ($thisview->products as $product){
			if($thisview->game_product_availability['product_number_'.$product['product_number']]==1){
				$array_ventas_productoYcanal = $array_ventas_productoYcanal.","."["."'".$product['name']."'";
				foreach ($thisview->channels as $channel){
					$unidades_ventas_regiones_en_canal = 0;
					$importe_ventas_regiones_en_canal = 0;
					$suma_unidades_ventas_regiones_en_canal = 0;
					$suma_importe_ventas_regiones_en_canal = 0;
					foreach ($thisview->regions as $region){
						$unidades_ventas_regiones_en_canal = $thisview->outcomes_sales_units['company_'.$mycompanyid]
									['product_'.$product['product_number']]
									['region_'.$region['region_number']]
									['channel_'.$channel['channel_number']];
						$importe_ventas_regiones_en_canal = $thisview->outcomes_prices['company_'.$mycompanyid]
																	['product_'.$product['product_number']]
																	['channel_'.$channel['channel_number']]
																	['region_'.$region['region_number']];
						$suma_unidades_ventas_regiones_en_canal = $suma_unidades_ventas_regiones_en_canal + $unidades_ventas_regiones_en_canal;
						$suma_importe_ventas_regiones_en_canal = $suma_importe_ventas_regiones_en_canal + $unidades_ventas_regiones_en_canal*$importe_ventas_regiones_en_canal;
					}
					$array_ventas_productoYcanal = $array_ventas_productoYcanal.",".number_format($suma_unidades_ventas_regiones_en_canal,0, '', '');
					$array_ventas_productoYcanal = $array_ventas_productoYcanal.",".number_format($suma_importe_ventas_regiones_en_canal,0, '', '');
				}
				$array_ventas_productoYcanal = $array_ventas_productoYcanal."]";
			}
		}
		$array_ventas_productoYcanal = $array_ventas_productoYcanal."]";
		return $array_ventas_productoYcanal;
		
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
	
	
	
	
	function prepareArrayChart($chart) {
		$tmp = serialize($chart);
		$tmp = urlencode($chart);
		return $tmp;
	}
	
?>