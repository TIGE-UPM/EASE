<?php // content="text/plain; charset=utf-8"
	require_once ('jpgraph/jpgraph.php');
	require_once ('jpgraph/jpgraph_line.php');
	
	function extractChartArray($chartArray) {
		$tmp = stripslashes($chartArray);
		$tmp = urldecode($tmp);
		$tmp = unserialize($tmp);
		return $tmp;
	}
	
	$getIncomes = $_GET['ch4'];
	$getCosts = $_GET['ch5'];

	$incomes = extractChartArray($getIncomes);
	$costs = extractChartArray($getCosts);
	var_dump($incomes);
	var_dump($costs);
	
	$datay1 = array(0,$getIncomes);
	$datay2 = array(0,$getCosts);
	
	// Setup the graph
	$graph = new Graph(800,800);
	$graph->SetScale("textlin");
	
	foreach ($costs as $array) {
		$round_counter=1;
		while(isset ($array['round_'.$round_counter])){
			$channel_counter=1;
			$array_aux=$array['round_'.$round_counter];
			$data_costs[$index]=$array_aux[$company_counter];
			$round_counter++;
		}
	}
	
	
	//$theme_class=new UniversalTheme;
	
	//$graph->SetTheme($theme_class);
	$graph->img->SetAntiAliasing(false);
	$graph->title->Set('Evolucion temporal de gastos e ingresos');
	$graph->SetBox(false);
	
	$graph->img->SetAntiAliasing();
	
	//$graph->yaxis->HideZeroLabel();
	//$graph->yaxis->HideLine(false);
	//$graph->yaxis->HideTicks(false,false);
	
	$graph->xgrid->Show();
	$graph->xgrid->SetLineStyle("solid");
	$graph->xaxis->SetTickLabels(array('Ronda 0','Ronda 1','Ronda 2','Ronda 3','Ronda 4'));
	$graph->xgrid->SetColor('#E3E3E3');
	
	// Create the first line
	$p1 = new LinePlot($datay1);
	$graph->Add($p1);
	$p1->SetColor("#6495ED");
	$p1->SetLegend('Ingresos');
	
	// Create the second line
	$p2 = new LinePlot($datay2);
	$graph->Add($p2);
	$p2->SetColor("#B22222");
	$p2->SetLegend('Gastos');
	
	// Output line
	$graph->Stroke();

?>

