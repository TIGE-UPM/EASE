<?php
	class Model_DbTable_Decisions_St_Distribution extends Zend_Db_Table{
		protected $_name = 'decision_stock_distribution';
		
		function add($parameters, $game_id=null, $company_id=null, $round_number=null){	
			$front = Zend_Controller_Front::getInstance();
			if ($game_id==null){
				$game=$front->getParam('activeGame');
				$game_id=$game['id'];
			}
			if ($company_id==null){
				$company=$front->getParam('activeCompany');
				$company_id=$company['id'];
			}
			if ($round_number==null){
				$round=$front->getParam('activeRound');
				$round_number=$round['round_number'];
			}
			$distributionData=$parameters['stock']['distributionInformation'];

			//Opción 1: Se inserta todas las convinaciones (demasiados registros en mi opinión)
			$product_number=1;
			$distributionData['product_'.$product_number];

			while (isset($distributionData['product_'.$product_number])){
				$units_product=$distributionData['product_'.$product_number];
				$channelO_number=1;
				while (isset($units_product['channelO_'.$channelO_number])){
					$units_channelO=$units_product['channelO_'.$channelO_number];
					$regionO_number=1;
					while (isset($units_channelO['regionO_'.$regionO_number])){
						$units_regionO=$units_channelO['regionO_'.$regionO_number];
						$channelD_number=1;
						while (isset($units_regionO['channelD_'.$channelD_number])){
							$units_channelD=$units_regionO['channelD_'.$channelD_number];
							$regionD_number=1;
							while (isset($units_channelD['regionD_'.$regionD_number])){
								$units=$units_channelD['regionD_'.$regionD_number];
								self::insert(array('game_id'=>$game_id, 'company_id' => $company_id, 'round_number' => $round_number, 'product_number' => $product_number, 'channelO_number'=>$channelO_number, 'regionO_number'=>$regionO_number, 'channelD_number'=>$channelD_number, 'regionD_number'=>$regionD_number,'units'=>$units));
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

			//Opción 2: Sólo se inserta aquellos que tenga cambios
			/*for ($i=0; $i<count($distributionData);$i++){		
				$product_number=$distributionData[$i]['product'];
				$channelO=$distributionData[$i]['channelO'];
				$regionO=$distributionData[$i]['regionO'];
				$channelD=$distributionData[$i]['channelD'];
				$regionD=$distributionData[$i]['regionD'];
				$units=$distributionData[$i]['value'];
				self::insert(array('game_id'=>$game_id, 'company_id' => $company_id, 'round_number' => $round_number, 'product_number' => $product_number, 'channelO_number'=>$channelO, 'regionO_number'=>$regionO, 'channelD_number'=>$channelD, 'regionD_number'=>$regionD,'units'=>$units));
			}*/
		}

		function updateDecision($parameters, $game_id=null, $company_id=null, $round_number=null){	
			$front = Zend_Controller_Front::getInstance();
			if ($game_id==null){
				$game=$front->getParam('activeGame');
				$game_id=$game['id'];
			}
			if ($company_id==null){
				$company=$front->getParam('activeCompany');
				$company_id=$company['id'];
			}
			if ($round_number==null){
				$round=$front->getParam('activeRound');
				$round_number=$round['round_number'];
			}
			
			$distributionData=$parameters['stock']['distributionInformation'];

			//Opción 1: Se updatea todas las opciones (muchísimos registros)
			$product_number=1;
			$distributionData['product_'.$product_number];

			while (isset($distributionData['product_'.$product_number])){
				$units_product=$distributionData['product_'.$product_number];
				$channelO_number=1;
				while (isset($units_product['channelO_'.$channelO_number])){
					$units_channelO=$units_product['channelO_'.$channelO_number];
					$regionO_number=1;
					while (isset($units_channelO['regionO_'.$regionO_number])){
						$units_regionO=$units_channelO['regionO_'.$regionO_number];
						$channelD_number=1;
						while (isset($units_regionO['channelD_'.$channelD_number])){
							$units_channelD=$units_regionO['channelD_'.$channelD_number];
							$regionD_number=1;
							while (isset($units_channelD['regionD_'.$regionD_number])){
								$units=$units_channelD['regionD_'.$regionD_number];
								self::update(array('units'=>$units),'game_id= '.$game_id.' AND company_id= '.$company_id.' AND round_number= '.$round_number.' AND product_number= '.$product_number.' AND channelO_number= '.$channelO_number.' AND regionO_number= '.$regionO_number.' AND channelD_number= '.$channelD_number.' AND regionD_number= '.$regionD_number);
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

			//Opción 2: Hacer los deletes de los anteriores e insertar los nuevos:


			/*self::delete(array('game_id'=>$game_id, 'company_id' => $company_id, 'round_number' => $round_number));

			for ($i=0; $i<count($distributionData);$i++){		
				$product_number=$distributionData[$i]['product'];
				$channelO=$distributionData[$i]['channelO'];
				$regionO=$distributionData[$i]['regionO'];
				$channelD=$distributionData[$i]['channelD'];
				$regionD=$distributionData[$i]['regionD'];
				$units=$distributionData[$i]['value'];
				self::insert(array('game_id'=>$game_id, 'company_id' => $company_id, 'round_number' => $round_number, 'product_number' => $product_number, 'channelO_number'=>$channelO, 'regionO_number'=>$regionO, 'channelD_number'=>$channelD, 'regionD_number'=>$regionD,'units'=>$units));
			}	*/

		}
		function getActiveRoundLastDecisionSaved(){
			$front = Zend_Controller_Front::getInstance();
			$game=$front->getParam('activeGame');
			$company=$front->getParam('activeCompany');
			$round=$front->getParam('activeRound');
			//$array=array();
			$decisions=$this->fetchAll('game_id = '.$game['id'].' AND company_id = '.$company['id'].
									   ' AND round_number = '.$round['round_number'], 
									   array('product_number ASC', 'channelO_number ASC', 'regionO_number ASC', 'channelD_number ASC', 'regionD_number ASC'));
			
			//Opción 1
			foreach ($decisions as $decision){
				$array['product_'.$decision['product_number']]
					  ['channelO_number_'.$decision['channelO_number']]
					  ['regionO_number_'.$decision['regionO_number']]
					  ['channelD_number_'.$decision['channelD_number']]
					  ['regionD_number_'.$decision['regionD_number']]=$decision['units'];
			}
			//Opción 2
			/*$i=0;
			foreach ($decisions as $decision){
				$array[$i]=array('product'=> $decision['product_number'], 'channelO'=>$decision['channelO_number'], 'regionO'=>$decision['regionO_number'], 'channelD'=>$decision['channelD_number'], 'regionD'=>$decision['regionD_number'], 'value'=>$decision['units']);
				$i++;
			}
			return $array;*/
		}
		function getDecision($game_id, $company_id, $round_number){
			$array=array();
			$decisions=$this->fetchAll('game_id = '.$game_id.' AND company_id = '.$company_id.
									   ' AND round_number = '.$round_number, 
									    array('product_number ASC', 'regionO_number ASC', 'channelO_number ASC', 'regionD_number ASC', 'channelD_number ASC'));
			//Opción 1
			foreach ($decisions as $decision){
				$array['product_'.$decision['product_number']]
					  ['channelO_number_'.$decision['channelO_number']]
					  ['regionO_number_'.$decision['regionO_number']]
					  ['channelD_number_'.$decision['channelD_number']]
					  ['regionD_number_'.$decision['regionD_number']]=$decision['units'];
			}

			//Opción 2
			/*$i=0;
			foreach ($decisions as $decision){
				$array[$i]=array('product'=> $decision['product_number'], 'channelO'=>$decision['channelO_number'], 'regionO'=>$decision['regionO_number'], 'channelD'=>$decision['channelD_number'], 'regionD'=>$decision['regionD_number'], 'value'=>$decision['units']);
				$i++;
			}*/
			return $array;

		}

		function setCostDistribution($game_id, $round_number, $company_id, $product_number, $channelO_number, $regionO_number, $channelD_number, $regionD_number, $cost){

			self::update(array('unit_price'=>doubleval($cost)),'game_id= '.intval($game_id).' AND company_id= '.intval($company_id).' AND round_number= '.intval($round_number).' AND product_number= '.intval($product_number).' AND channelO_number= '.intval($channelO_number).' AND regionO_number= '.intval($regionO_number).' AND channelD_number= '.intval($channelD_number).' AND regionD_number= '.intval($regionD_number));	
		}

		function getCostStockDistribution($game_id, $round_number, $company_id, $product_number, $channel0_number, $regionO_number, $channelD_number, $regionD_number){
			$result= $this->fetchRow('game_id = '.intval($game_id).' AND round_number = '.intval($round_number).' AND company_id = '.intval($company_id).
									 ' AND product_number = '.intval($product_number).' AND regionO_number = '.intval($regionO_number).' AND channelO_number = '.intval($channel0_number).' AND regionD_number = '.intval($regionD_number).' AND channelD_number = '.intval($channelD_number));
			return $result['unit_price'];
		}

		function getUnitsDistribution($game_id, $round_number, $company_id, $product_number, $channel0_number, $regionO_number, $channelD_number, $regionD_number){
			$result= $this->fetchRow('game_id = '.intval($game_id).' AND round_number = '.intval($round_number).' AND company_id = '.intval($company_id).
									 ' AND product_number = '.intval($product_number).' AND regionO_number = '.intval($regionO_number).' AND channelO_number = '.intval($channel0_number).' AND regionD_number = '.intval($regionD_number).' AND channelD_number = '.intval($channelD_number));
			return $result['units'];
		}

	/*	function getAllDecisions($game_id, $round_number){
			$array=array();
			$decisions=$this->fetchAll('game_id = '.$game_id.' AND round_number = '.$round_number, 
									    array('company_id ASC', 'product_number ASC', 'regionO_number ASC', 'channelO_number ASC', 'regionD_number ASC', 'channelD_number ASC'));
			foreach ($decisions as $decision){
				$array['company_id_'.$decision['company_id']]
					  ['product_'.$decision['product_number']]
					  ['channelO_number_'.$decision['channelO_number']]
					  ['regionO_number_'.$decision['regionO_number']]
					  ['channelD_number_'.$decision['channelD_number']]
					  ['regionD_number_'.$decision['regionD_number']]=$decision['units'];
			}
			return $array;
		}*/
	}
?>