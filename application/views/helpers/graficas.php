<!--Load the AJAX API for tables creation-->
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
	
<script type="text/javascript">
	// Load the Visualization API and the piechart package.
	google.load('visualization', '1.0', {'packages':['corechart']});
	
	// Set a callback to run when the Google Visualization API is loaded.
	google.setOnLoadCallback(drawChart);
	
	
	// Callback that creates and populates a data table,
	// instantiates the pie chart, passes in the data and
	// draws it.
	function drawChart() {
	// Create the data table.
		var data = google.visualization.arrayToDataTable([
			['Canal', 'Importe'],
			<?php 
			$incomes_sum=array();
			foreach($this->channels as $channel){
			?>
			['<?php echo $channel['name']?>', <?php 
												if (!isset ($incomes_sum[$this->company['id']])){
													$incomes_sum[$this->company['id']]=0;
												}
												$incomes=0;
												foreach ($this->products as $product){
													if($this->game_product_availability['product_number_'.$product['product_number']]==1){
														foreach ($this->regions as $region){
															$incomes+=$this->outcomes_sales_incomes['company_'.$this->company['id']]
																						['product_'.$product['product_number']]
																						['region_'.$region['region_number']]
																						['channel_'.$channel['channel_number']];
														}					
													}
												}
												$incomes_sum[$this->company['id']]+=$incomes;
												?>
												<?php echo number_format($incomes, 2, '', '').''?>],
		<?php } ?>
		  ['TOTAL',  <?php echo number_format($incomes_sum[$this->company['id']], 2, '', '').''?>]

		]);

		// Set chart options
		var options = {'title':'INGRESOS',
					   'width':600,
					   'height':300,
					   chartArea: {  width: "50%"}
					   };

		// Instantiate and draw our chart, passing in some options.
		var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
		chart.draw(data, options);
  }
</script>