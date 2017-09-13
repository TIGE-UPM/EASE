<?php
	class Model_DbTable_Outcomes_Bs_BalanceSheet extends Zend_Db_Table{
		protected $_name = 'outcomes_balance_sheet';
		
		function getValues($game_id, $round_number){
			$results=$this->fetchAll('game_id = '.$game_id.' AND round_number = '.$round_number);
			foreach ($results as $result){
					$values[$result['company_id']][$result['type']]=$result['value'];
			}
			return $values;
		}
		
		function getCompanyAssets($game_id, $round_number, $company_id){
			$assets=$this->fetchRow('game_id = '.$game_id.' AND company_id = '.$company_id.' AND round_number = '.$round_number.' AND type = "liquid_assets"');
			return $assets['value'];
		}
		
		function getCompanyCreditors($game_id, $round_number, $company_id){
			$creditors=$this->fetchRow('game_id = '.$game_id.' AND company_id = '.$company_id.' AND round_number = '.$round_number.' AND type = "creditors"');
			return $creditors['value'];
		}
		
		function getCompanyDebtors($game_id, $round_number, $company_id){
			$debtors=$this->fetchRow('game_id = '.$game_id.' AND company_id = '.$company_id.' AND round_number = '.$round_number.' AND type = "trade_debtors"');
			return $debtors['value'];
		}
		function getCompanyReserves($game_id, $round_number, $company_id){
			$reserves=$this->fetchRow('game_id = '.$game_id.' AND company_id = '.$company_id.' AND round_number = '.$round_number.' AND type = "reserves"');
			return $reserves['value'];
		}
		function getCompanyStockValue($game_id, $round_number, $company_id){
			$stock=$this->fetchRow('game_id = '.$game_id.' AND company_id = '.$company_id.' AND round_number = '.$round_number.' AND type = "stock"');
			return $stock['value'];
		}
		function getCompanyLastResult($game_id, $round_number, $company_id){
			$result=$this->fetchRow('game_id = '.$game_id.' AND company_id = '.$company_id.' AND round_number = '.$round_number.' AND type = "year_result"');
			return $result['value'];
		}
	}
?>