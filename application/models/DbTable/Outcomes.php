<?php
	class Model_DbTable_Outcomes extends Zend_Db_Table{
		protected $_name = 'outcomes_registry';
		protected $_game_id;
		protected $_round_number;
		function initOutcomes($game_id=null, $round_number=null){
			$this->_game_id=$game_id;
			$this->_round_number=$round_number;
			// var_dump($game_id);
			// var_dump($this->_round_number);
			if ($game_id!=null){
				$games = new Model_DbTable_Games();
				$this->_companies = $games->getCompaniesInGame($this->_game_id);
			}
		}
		function getLatestRoundNumber($game_id){
			$result=$this->fetchRow('game_id ='.$game_id.' AND publish=1', 'round_number DESC');
			return $result['round_number'];
		}
		function getPastOutcomes($game_id){
			$results=$this->fetchAll('game_id ='.$game_id.' AND publish=1', 'round_number ASC');
			return $results;
		}
		function isPublished($game_id=null, $round_number=null){
			$result=$this->fetchRow('game_id ='.$game_id.' AND round_number='.$round_number);
			return $result['publish'];
		}
		function switchPublication($game_id=null, $round_number=null){
			$result=$this->fetchRow('game_id ='.$game_id.' AND round_number='.$round_number);
			if ($result['publish']==0){
				$temp=1;
			}
			else{
				$temp=0;
			}
			self::update(array('publish'=>$temp), 'game_id = '.$game_id.' AND round_number = '.$round_number);
		}
		function register(){
			$date=date( 'Y-m-d H:i:s', time());
			if (! $this->exists()){
				$this->insert(array('game_id'=>$this->_game_id, 'round_number'=>$this->_round_number, 'date'=>$date));
			}
			else{
				$this->update(array('date'=>$date), array('game_id'=>$this->_game_id, 'round_number'=>$this->_round_number));
				//self::update(array('date'=>$date), 'game_id = '.$this->_game_id.' AND round_number = '.$this->_round_number);
			}
		}
		function clean(){
			if ($this->exists()){
				//print_r("hay registro previo");				
				$this->delete('game_id = '.$this->_game_id.' AND round_number = '.$this->_round_number);
			}
			else{
				//print_r("no hay registro previo");
				$registries[]=new Model_DbTable_Outcomes_Pr_Units();
				$registries[]=new Model_DbTable_Outcomes_Pr_Messages();
				$registries[]=new Model_DbTable_Outcomes_De_Sales();
				$registries[]=new Model_DbTable_Outcomes_Co_Costs();
				//$registries[]=new Model_DbTable_Outcomes_Co_Cashflow();
				foreach ($registries as $registry){
					$registry->delete('game_id = '.$this->_game_id.' AND round_number = '.$this->_round_number);
				}
			}
		}
		function exists(){
			return ($this->fetchRow('game_id = '.$this->_game_id.' AND round_number = '.$this->_round_number)!=null);
		}
		function getProductionUnits($game_id, $round_number){
			$outcomes_production_units=new Model_DbTable_Outcomes_Pr_Units();
			return $outcomes_production_units->getOutcomes($game_id, $round_number);
		}
		function getStocksUnits($game_id, $round_number){
			$outcomes_stocks_units=new Model_DbTable_Outcomes_St_Units();
			return $outcomes_stocks_units->getOutcomes($game_id, $round_number);
		}
		//VERO
		function getStocksByProducts($game_id, $round_number, $company_id, $product_number){
			$outcomes_stocks_units=new Model_DbTable_Outcomes_St_Units();
			return $outcomes_stocks_units->getStockByProduct($game_id, $round_number, $company_id, $product_number);
		}
		//VERO
		function getProductionMessages($game_id, $round_number){
			$outcomes_production_messages=new Model_DbTable_Outcomes_Pr_Messages();
			return $outcomes_production_messages->getMessages($game_id, $round_number);
		}
		function getSalesUnits($game_id, $round_number){
			$outcomes_sales=new Model_DbTable_Outcomes_De_Sales();
			return $outcomes_sales->getSales($game_id, $round_number);
		}
		function getIncomes($game_id, $round_number){
			$outcomes_sales=new Model_DbTable_Outcomes_De_Sales();
			return $outcomes_sales->getIncomes($game_id, $round_number);
		}
		function getExportPrices ($game_id, $round_number){
			$outcomes_prices=new Model_DbTable_Decisions_Marketing();
			return $outcomes_prices->getExportPrices($game_id, $round_number);
			
		}
		function getPrices($game_id, $round_number){
			$mk_decisions=new Model_DbTable_Decisions_Marketing();
			$games=new Model_DbTable_Games();
			$array=array();
			$companies=$games->getCompaniesInGame($game_id);
			foreach ($companies as $company){
				$mk_lastDecision=$mk_decisions->getDecisionArray($game_id, $company['id'], $round_number);
				$array['company_'.$company['id']]=$mk_lastDecision['prices'];
			}
			return $array;
		}
		function getCosts($game_id, $round_number){
			$outcomes_costs=new Model_DbTable_Outcomes_Co_Costs();
			return $outcomes_costs->getCosts($game_id, $round_number);
		}
		function getInterestInvestment($game_id, $round_number){
			$outcomes_investment=new Model_DbTable_Outcomes_In_Investment();
			return $outcomes_investment->getInvestment($game_id, $round_number);
		}
		function getInvestmentByCompany($game_id, $company_id, $round_number){
			$outcomes_investment=new Model_DbTable_Outcomes_In_Investment();
			return $outcomes_investment->getInvestmentByCompany($game_id, $company_id, $round_number);
		}
		function getStock($game_id, $round_number){
			$outcomes_stocks_units=new Model_DbTable_Outcomes_St_Units();
			return $outcomes_stocks_units->getOutcomes($game_id, $round_number);
		}
		function getBalanceSheet($game_id, $round_number){
			$outcomes_balance_sheet=new Model_DbTable_Outcomes_Bs_BalanceSheet();
			return $outcomes_balance_sheet->getValues($game_id, $round_number);
		}
		function getPerformance($game_id, $round_number){
			$outcomes_performance=new Model_DbTable_Outcomes_Bs_Performance();
			return $outcomes_performance->getValues($game_id, $round_number);
		}
		function getAssetCurrent($game_id, $round_number, $company_id){
			$outcomes_asset_current=new Model_DbTable_Outcomes_Bs_BalanceSheet();
			$outcomes_balance_sheet=$outcomes_asset_current->getValues($game_id, $round_number);
			$asset+=$outcomes_balance_sheet[$company_id]['stock'];
			$asset+=$outcomes_balance_sheet[$company_id]['trade_debtors'];
			$asset+=$outcomes_balance_sheet[$company_id]['liquid_assets'];
			return $asset;
		}
		function getLiabilitiesTotal($game_id, $round_number, $company_id){
			$outcomes_liabilities=new Model_DbTable_Outcomes_Bs_BalanceSheet();
			$outcomes_balance_sheet=$outcomes_liabilities->getValues($game_id, $round_number);
			$liabilities+=$outcomes_balance_sheet[$company_id]['capital'];
			$liabilities+=$outcomes_balance_sheet[$company_id]['reserves'];
			$liabilities+=$outcomes_balance_sheet[$company_id]['year_result'];
			$liabilities+=$outcomes_balance_sheet[$company_id]['long_term_debts'];
			$liabilities+=$outcomes_balance_sheet[$company_id]['short_term_debts'];
			$liabilities+=$outcomes_balance_sheet[$company_id]['creditors'];
			return $liabilities;
		}
		function getYearIncomes($game_id, $round_number, $company_id){
			$outcomes_incomes=$this->getIncomes($game_id, $round_number);
			$games=new Model_DbTable_Games();
			$channels=$games->getChannels($_GET['game_id']);
			$regions=$games->getRegions($_GET['game_id']);
			$products=$games->getProducts($_GET['game_id']);
			
			foreach ($products as $product){
				foreach ($regions as $region){
					foreach ($channels as $channel){
						$incomes+=$outcomes_incomes['company_'.$company_id]['product_'.$product['product_number']]['region_'.$region['region_number']]['channel_'.$channel['channel_number']];
					}
				}
			}

			return $incomes;
		}
		function getYearCosts($game_id, $round_number, $company_id){
			$outcomes_costs=$this->getCosts($game_id, $round_number);
			$games=new Model_DbTable_Games();
			$channels=$games->getChannels($_GET['game_id']);
			$medias=$games->getMedia($_GET['game_id']);
			$trademedias[0]=array('trademedia_number'=>1, 'name'=>'Patrocinio');
			$trademedias[1]=array('trademedia_number'=>2, 'name'=>'Promoción');
			$costs=0;
			$costs+=$outcomes_costs[$company_id]['pr_fixed_cost'];
			$costs+=$outcomes_costs[$company_id]['pr_var_costs'];
			foreach ($channels as $channel){
				$costs+=$outcomes_costs[$company_id]['pr_rawMaterials_costs'][$channel['channel_number']];
				$costs+=$outcomes_costs[$company_id]['mk_sales_costs'][$channel['channel_number']];
				$costs+=$outcomes_costs[$company_id]['mk_fixed_costs'][$channel['channel_number']];
			}
			$costs+=$outcomes_costs[$company_id]['pr_distrib_costs'];
			foreach ($medias as $media){
				$costs+=$outcomes_costs[$company_id]['mk_advert_costs'][$media['media_number']];
			}
			foreach ($trademedias as $trademedia){
				$costs+=$outcomes_costs[$company_id]['mk_trade_costs'][$trademedia['trademedia_number']];
			}
			$costs+=$outcomes_costs[$company_id]['hr_hiring_costs'];
			$costs+=$outcomes_costs[$company_id]['hr_training_costs'];
			$costs+=$outcomes_costs[$company_id]['hr_wages_costs'];
			$costs+=$outcomes_costs[$company_id]['fi_debt_costs_st'];
			$costs+=$outcomes_costs[$company_id]['fi_debt_costs_lt'];
			$costs+=$outcomes_costs[$company_id]['initiatives_pr_costs'];
			$costs+=$outcomes_costs[$company_id]['initiatives_mk_costs'];
			$costs+=$outcomes_costs[$company_id]['initiatives_hr_costs'];
			$costs+=$outcomes_costs[$company_id]['market_researches_costs'];
			$costs+=$outcomes_costs[$company_id]['idi_changes_costs'];
			$costs+=$outcomes_costs[$company_id]['idi_new_costs'];
			return $costs;
		}
	}
?>