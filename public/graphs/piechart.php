<?php // content="text/plain; charset=utf-8"
	include ('jpgraph/jpgraph.php'); 
	include ('jpgraph/jpgraph_pie.php');
	include ('jpgraph/jpgraph_pie3d.php');

	function extractChartArray($chartArray) {
	/*	$tmp = stripslashes($chartArray);
		$tmp = urldecode($tmp);
		$tmp = unserialize($tmp);
		return $tmp; */
	}
	
	$graph  = new PieGraph (1000,1000);
	$graph->SetShadow(); 
	$graph->title-> Set("Cuotas de mercado"); 
	$graph->title->SetFont(FF_FONT1,FS_BOLD,24);
	
	$getChartArray = $_GET['ch1'];
	$getNamesArray = $_GET['ch2'];
	$getMarketsArray = $_GET['ch3'];
	$chartArray = array_values(extractChartArray($getChartArray));
	$namesArray = extractChartArray($getNamesArray);
	$marketsArray = extractChartArray($getMarketsArray);
	//var_dump($marketsArray);
	//var_dump($namesArray);
	$product_counter=1;
	//Vemos el numero de mercados existentes
	$n_markets=0;
	foreach ($chartArray as $aux_array) {
		$region_counter=1;
		while(isset ($aux_array['region_number_'.$region_counter])){
			$array_aux=$aux_array['region_number_'.$region_counter];
			$channel_counter=1;
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
	//$graph->SetLegend(array($array));
	if($n_markets==1){
		$size=1/(4*$n_markets);
		$y=(1/((1.1*$n_markets))/2);
	}
	else {
		$size=1/(2.5*$n_markets);
		$y=(1/((1.1*$n_markets)/2));
	}

	
		$market_counter=0;
		$region_counter=1;
		var_dump($array);
		while(isset ($array['region_number_'.$region_counter])){
			$channel_counter=1;
			$array_aux=$array['region_number_'.$region_counter];
			var_dump($array_aux);
			while(isset ($array_aux['channel_number_'.$channel_counter])){
				$market_counter++;
				$data=array_values($array_aux['channel_number_'.$channel_counter]);
				var_dump($data);die();
				$p1  = new PiePlot($data); 
				$graph->Add($p1); 
				$p1->ShowBorder();
				$p1->SetSize($size);
				if($n_markets==1){
					$p1->SetCenter(($product_counter*0.5),(0+($y*$market_counter)));
				}
				else {
					if($product_counter==1){
						$p1->SetCenter(($product_counter*0.25),(-0.1+(($y)*$market_counter)));
					} else {
						$p1->SetCenter(($product_counter*0.3),(0+(($y)*$market_counter)));
					}
				}
				$p1->SetSliceColors(array('#0099FF','#33CC33','#FFFF33','#E80000'));
				$p1->title->Set("Producto: ".$marketsArray['products']['product_number_'.$product_counter]." Region: ".$marketsArray['regions']['region_number_'.$region_counter]."   Canal: ".$marketsArray['channels']['channel_number_'.$channel_counter]);
				$p1->SetLabelFormat("%1.2f");
				//$p1->value->Show();
				$channel_counter++;
			}
			$region_counter++;
		}

	$p1->SetLegends($string);
	$graph->Stroke();
?>
