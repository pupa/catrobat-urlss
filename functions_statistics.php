<?php	
	function get_stats_to_url_id ($url_id, $stats) {
		include 'config.php';	
					
		// open connection to DB
		$connection = pg_connect('host=' .$host. ' port=' .$port. ' dbname=' .$database. ' user=' .$user. ' password=' .$password) 
			or die("DB ERROR: connect failed!");
		
		switch ($stats) {
			case "country_map":
				$countries = array();
				
				$query = 'SELECT country_code, count(log_id) FROM log WHERE url_id=' .$url_id. ' GROUP BY country_code';
				$result = pg_query($query) or die ('Executing query failed: ' .pg_last_error());
				
				while ($line = pg_fetch_array($result, NULL, PGSQL_ASSOC)) {						
					$countries[$line['country_code']] = $line['count'];	
				}	
				return $countries;
			case "total_clicks":
				break;
			case "referrer":
				break;
			case "browser":
				break;
			case "platforms":
				break;
			default:
				echo "ERROR: No valid stats!";
				break;
		}
		
		// close connection to DB
		pg_close($connection);
	}
	
	function stats_country_map ($url_id) {
		$countries = get_stats_to_url_id($url_id, 'country_map');
		
		/*
		 *  Falls JAVASCRIPT DEAKTIVIERT ist muss hier noch eine statische Map angezeigt werden!
		 *  Am besten wŸrde sich dies mit CSS lšsen lassen siehe YOURLS: functions-infos.php
		 */
		 
		// !!! wie erfolgt Deaktivierung -> muss noch angesehen werden, aber vermutlich auch Ÿber CSS !!!
		// dynamic map; will be hidden if Javascript is disabled
		
		// assemble the data array for the google chart
		$data_array = "[";
		foreach ($countries as $country_code => $number_of_hits) {
			$data_array .= "['" .$country_code. "', " .$number_of_hits. "],";
		}
		$data_array .= "], true";
		
		// use the google API for charts - in this case geo charts
		echo "
			<html><head>
			<title>Test Country Chart</title>
			<script type='text/javascript' src='https://www.google.com/jsapi'></script>
			<script type='text/javascript'>
				google.load('visualization', '1', {'packages': ['geochart']});
				
				function draw_country_map() {
					var data = google.visualization.arrayToDataTable(" .$data_array. ");
					
					var geochart = new google.visualization.GeoChart(document.getElementById('country_chart'));
					geochart.draw(data, {width: 900, height: 500});
				}
				
				google.setOnLoadCallback(draw_country_map);
			</script>
			</head>
			<body>
				<h3>Country Map</h3>
				<div id='country_chart'></div>
			</body>
			</html>";
	}
?>