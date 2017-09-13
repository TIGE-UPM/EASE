<?php
	class Model_DbTable_Games_Param_Pr_Suppliers extends Model_DbTable_Games_Param_Base{
		protected $_name = 'games_param_pr_suppliers';
		
		function add($parameters){		
			$this->insert($parameters);
		}
		function updateSuppliers($game_id, $data){
			$this->delete('game_id = '.$game_id);
			$payterms=$data['prSuppliersPayterms'];
			$ideal_number=$data['prSuppliersIdealNumber'];
			$this->add(array('game_id'=>$game_id, 'payTerms'=>implode(';', $payterms), 'ideal_number'=>$ideal_number));
		}
	}
?>