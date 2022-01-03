<?php
	$colocacao_por_rodada = file_exists( 'stats/colocacao_por_rodada.json' ) ? file_get_contents( 'stats/colocacao_por_rodada.json' ) : [];
	$tabela_final         = file_exists( 'stats/tabela_final.json' ) ? file_get_contents( 'stats/tabela_final.json' ) : [];
	$tabela_1o_turno      = file_exists( 'stats/tabela_1o_turno.json' ) ? file_get_contents( 'stats/tabela_1o_turno.json' ) : [];
	$tabela_2o_turno      = file_exists( 'stats/tabela_2o_turno.json' ) ? file_get_contents( 'stats/tabela_2o_turno.json' ) : [];
	$tabelas_por_mes      = file_exists( 'stats/tabelas_por_mes.json' ) ? file_get_contents( 'stats/tabelas_por_mes.json' ) : [];
	$top_10_pontuacoes    = file_exists( 'stats/top_10_pontuacoes.json' ) ? file_get_contents( 'stats/top_10_pontuacoes.json' ) : [];
	$rodadas_vencidas     = file_exists( 'stats/rodadas_vencidas.json' ) ? file_get_contents( 'stats/rodadas_vencidas.json' ) : [];
	$menor_pont_vencedor  = file_exists( 'stats/menor_pontuacao_vencedor.json' ) ? file_get_contents( 'stats/menor_pontuacao_vencedor.json' ) : [];
	$rodadas_na_lideranca = file_exists( 'stats/rodadas_na_lideranca.json' ) ? file_get_contents( 'stats/rodadas_na_lideranca.json' ) : [];
?>
<!DOCTYPE html>
<html>
<head>

	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
		<!-- Styles -->
		<style>
			.container {
				max-width:1000px;
				margin:0 auto;
				padding:40px 20px;
				text-align:center;
			}
			.chartdiv { width: 100%; height: 500px; }

			.styled-table {
				border-collapse: collapse;
				margin: 25px 0;
				font-size: 0.9em;
				font-family: sans-serif;
				min-width: 400px;
				box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
			}

			.styled-table thead tr {
				background-color: #009879;
				color: #ffffff;
				text-align: left;
			}

			.styled-table thead tr.table-title {
				background-color: #015544;
			}

			.styled-table th,
			.styled-table td {
				padding: 12px 15px;
				text-align:left;
				border-right:1px solid #009879;
			}

			.styled-table td:last-of-type {
				border-right:none;
			}

			.styled-table thead tr.table-title th {
				text-align: center;
			}

			.styled-table tbody tr {
				border-bottom: 1px solid #dddddd;
			}

			.styled-table tbody tr:nth-of-type(even) {
				background-color: #f3f3f3;
			}

			.styled-table tbody tr:last-of-type {
				border-bottom: 2px solid #009879;
			}

			.styled-table tbody tr.active-row {
				font-weight: bold;
				color: #009879;
			}

			/* Responsive table */
			@media screen and (max-width: 600px) {

				table.styled-table {
					min-width:auto;
					width:100%;
					box-shadow: none;
				}

				table.styled-table .mobi-hide {
					display:none;
				}

				table.styled-table tbody tr {
					box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
					margin-bottom:20px;
				}

				table.styled-table tr, table.styled-table tr th {
					display:block;
				}

				table.styled-table tr td {
					display:block;
					text-align:right;
					border-right:none;
					border-bottom:1px solid #00987917;
				}

				table.styled-table tbody tr:nth-of-type(even) {
					background-color: #fff;
				}

				table.styled-table tr td::before {  
					content: attr(data-label);
					float: left;
					font-weight: bold;
					text-transform: uppercase;  
				}


			}

		</style>


	<!-- Resources -->
	<script src="https://cdn.amcharts.com/lib/5/index.js"></script>
	<script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
	<script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>

	<!-- Chart code -->
	<script>
	am5.ready(function() {

	// Data
	var allData = <?php print_r( $colocacao_por_rodada ); ?>;

	// Create root element
	// https://www.amcharts.com/docs/v5/getting-started/#Root_element
	var root = am5.Root.new("bar_race_chart");

	root.numberFormatter.setAll({
	numberFormat: "#a",

	// Group only into M (millions), and B (billions)
	bigNumberPrefixes: [
		{ number: 1e6, suffix: "M" },
		{ number: 1e9, suffix: "B" }
	],

	// Do not use small number prefixes at all
	smallNumberPrefixes: []
	});

	var stepDuration = 2000;


	// Set themes
	// https://www.amcharts.com/docs/v5/concepts/themes/
	root.setThemes([am5themes_Animated.new(root)]);


	// Create chart
	// https://www.amcharts.com/docs/v5/charts/xy-chart/
	var chart = root.container.children.push(am5xy.XYChart.new(root, {
	panX: true,
	panY: true,
	wheelX: "none",
	wheelY: "none"
	}));


	// We don't want zoom-out button to appear while animating, so we hide it at all
	chart.zoomOutButton.set("forceHidden", true);


	// Create axes
	// https://www.amcharts.com/docs/v5/charts/xy-chart/axes/
	var yRenderer = am5xy.AxisRendererY.new(root, {
	minGridDistance: 20,
	inversed: true
	});
	// hide grid
	yRenderer.grid.template.set("visible", false);

	var yAxis = chart.yAxes.push(am5xy.CategoryAxis.new(root, {
	maxDeviation: 0,
	categoryField: "network",
	renderer: yRenderer
	}));

	var xAxis = chart.xAxes.push(am5xy.ValueAxis.new(root, {
	maxDeviation: 0,
	min: 0,
	strictMinMax: true,
	extraMax: 0.1,
	renderer: am5xy.AxisRendererX.new(root, {})
	}));

	xAxis.set("interpolationDuration", stepDuration / 10);
	xAxis.set("interpolationEasing", am5.ease.linear);


	// Add series
	// https://www.amcharts.com/docs/v5/charts/xy-chart/series/
	var series = chart.series.push(am5xy.ColumnSeries.new(root, {
	xAxis: xAxis,
	yAxis: yAxis,
	valueXField: "value",
	categoryYField: "network"
	}));

	// Rounded corners for columns
	series.columns.template.setAll({ cornerRadiusBR: 5, cornerRadiusTR: 5 });

	// Make each column to be of a different color
	series.columns.template.adapters.add("fill", function (fill, target) {
	return chart.get("colors").getIndex(series.columns.indexOf(target));
	});

	series.columns.template.adapters.add("stroke", function (stroke, target) {
	return chart.get("colors").getIndex(series.columns.indexOf(target));
	});

	// Add label bullet
	series.bullets.push(function () {
	return am5.Bullet.new(root, {
		locationX: 1,
		sprite: am5.Label.new(root, {
		text: "{valueXWorking.formatNumber('#.# a')}",
		fill: root.interfaceColors.get("alternativeText"),
		centerX: am5.p100,
		centerY: am5.p50,
		populateText: true
		})
	});
	});

	var label = chart.plotContainer.children.push(am5.Label.new(root, {
	text: "Rodada 1",
	fontSize: "5em",
	opacity: 0.2,
	x: am5.p100,
	y: am5.p100,
	centerY: am5.p100,
	centerX: am5.p100
	}));

	// Get series item by category
	function getSeriesItem(category) {
	for (var i = 0; i < series.dataItems.length; i++) {
		var dataItem = series.dataItems[i];
		if (dataItem.get("categoryY") == category) {
		return dataItem;
		}
	}
	}

	// Axis sorting
	function sortCategoryAxis() {
	// sort by value
	series.dataItems.sort(function (x, y) {
		return y.get("valueX") - x.get("valueX"); // descending
		//return x.get("valueX") - y.get("valueX"); // ascending
	});

	// go through each axis item
	am5.array.each(yAxis.dataItems, function (dataItem) {
		// get corresponding series item
		var seriesDataItem = getSeriesItem(dataItem.get("category"));

		if (seriesDataItem) {
		// get index of series data item
		var index = series.dataItems.indexOf(seriesDataItem);
		// calculate delta position
		var deltaPosition =
			(index - dataItem.get("index", 0)) / series.dataItems.length;
		// set index to be the same as series data item index
		if (dataItem.get("index") != index) {
			dataItem.set("index", index);
			// set deltaPosition instanlty
			dataItem.set("deltaPosition", -deltaPosition);
			// animate delta position to 0
			dataItem.animate({
			key: "deltaPosition",
			to: 0,
			duration: stepDuration / 2,
			easing: am5.ease.out(am5.ease.cubic)
			});
		}
		}
	});
	// sort axis items by index.
	// This changes the order instantly, but as deltaPosition is set, they keep in the same places and then animate to true positions.
	yAxis.dataItems.sort(function (x, y) {
		return x.get("index") - y.get("index");
	});
	}

	var year = 1;

	// update data with values each 1.5 sec
	var interval = setInterval(function () {
	year++;

	if (year > 38) {
		clearInterval(interval);
		clearInterval(sortInterval);
	}

	updateData();
	}, stepDuration);

	var sortInterval = setInterval(function () {
	sortCategoryAxis();
	}, 100);

	function setInitialData() {
	var d = allData[year];

	for (var n in d) {
		series.data.push({ network: n, value: d[n] });
		yAxis.data.push({ network: n });
	}
	}

	function updateData() {
	var itemsWithNonZero = 0;

	if (allData[year]) {
		label.set("text", year.toString());

		am5.array.each(series.dataItems, function (dataItem) {
		var category = dataItem.get("categoryY");
		var value = allData[year][category];

		if (value > 0) {
			itemsWithNonZero++;
		}

		dataItem.animate({
			key: "valueX",
			to: value,
			duration: stepDuration,
			easing: am5.ease.linear
		});
		dataItem.animate({
			key: "valueXWorking",
			to: value,
			duration: stepDuration,
			easing: am5.ease.linear
		});
		});

		yAxis.zoom(0, itemsWithNonZero / yAxis.dataItems.length);
	}
	}

	setInitialData();
	setTimeout(function () {
	year++;
	updateData();
	}, 50);

	// Make stuff animate on load
	// https://www.amcharts.com/docs/v5/concepts/animations/
	series.appear(1000);
	chart.appear(1000, 100);

	}); // end am5.ready()

	</script>
</head>
<body>

	<div class="container">
		<div id="bar_race_chart" class="chartdiv"></div>

		<!-- ## COLOCACAO GERAL -->
		<table class="styled-table">
			<thead>
				<tr class="table-title">
					<th colspan="6">Colocação Geral</th>
					<th colspan="10" class="mobi-hide">Nº de pontuações por faixa de pontos</th>
				</tr>
				<tr class="mobi-hide">
					<th>#</th>
					<th>Time</th>
					<th>Pontos</th>
					<th>Média/<br>Rodada</th>
					<th>Maior <br>Pontuação</th>
					<th>Menor <br>Pontuação</th>
					<th><20</th>
					<th>20 e 30</th>
					<th>30 e 40</th>
					<th>40 e 50</th>
					<th>50 e 60</th>
					<th>60 e 70</th>
					<th>70 e 80</th>
					<th>80 e 90</th>
					<th>90 e 100</th>
					<th>>100</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$tabela_final = json_decode( $tabela_final );
					$pos = 1;
					foreach ( $tabela_final as $pont ) { ?>
						<tr>
							<td data-label="Posição"><?php echo $pos++; ?>º</td>
							<td data-label="Time"><?php echo $pont->time; ?></td>
							<td data-label="Pontos"><?php echo $pont->pontos; ?></td>
							<td data-label="Média/Rodada"><?php echo $pont->avg; ?></td>
							<td data-label="Maior Pontuação"><?php echo $pont->max; ?></td>
							<td data-label="Menor Pontuação"><?php echo $pont->min; ?></td>
							<td data-label="Menor que 20"><?php echo $pont->menor_q_20; ?></td>
							<td data-label="Entre 20 e 30"><?php echo $pont->entre_20_30; ?></td>
							<td data-label="Entre 30 e 40"><?php echo $pont->entre_30_40; ?></td>
							<td data-label="Entre 40 e 50"><?php echo $pont->entre_40_50; ?></td>
							<td data-label="Entre 50 e 60"><?php echo $pont->entre_50_60; ?></td>
							<td data-label="Entre 60 e 70"><?php echo $pont->entre_60_70; ?></td>
							<td data-label="Entre 70 e 80"><?php echo $pont->entre_70_80; ?></td>
							<td data-label="Entre 80 e 90"><?php echo $pont->entre_80_90; ?></td>
							<td data-label="Entre 90 e 100"><?php echo $pont->entre_90_100; ?></td>
							<td data-label="Maior que 100"><?php echo $pont->maior_q_100; ?></td>
						</tr>
					<?php } ?>
			</tbody>
		</table>

		<!-- ## 1º Turno -->
		<table class="styled-table">
			<thead>
				<tr class="table-title">
					<th colspan="6">Colocação 1º Turno</th>
				</tr>
				<tr class="mobi-hide">
					<th>#</th>
					<th>Time</th>
					<th>Pontos</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$tabela_1o_turno = json_decode( $tabela_1o_turno );
					$pos = 1;
					foreach ( $tabela_1o_turno as $pont ) { ?>
						<tr>
							<td data-label="Posição"><?php echo $pos++; ?>º</td>
							<td data-label="Time"><?php echo $pont->time; ?></td>
							<td data-label="Pontos"><?php echo $pont->{'1o_turno'} ; ?></td>
						</tr>
					<?php } ?>
			</tbody>
		</table>

		<!-- ## 2º Turno -->
		<table class="styled-table">
			<thead>
				<tr class="table-title">
					<th colspan="6">Colocação 2º Turno</th>
				</tr>
				<tr class="mobi-hide">
					<th>#</th>
					<th>Time</th>
					<th>Pontos</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$tabela_2o_turno = json_decode( $tabela_2o_turno );
					$pos = 1;
					foreach ( $tabela_2o_turno as $pont ) { ?>
						<tr>
							<td data-label="Posição"><?php echo $pos++; ?>º</td>
							<td data-label="Time"><?php echo $pont->time; ?></td>
							<td data-label="Pontos"><?php echo $pont->{'2o_turno'} ; ?></td>
						</tr>
					<?php } ?>
			</tbody>
		</table>


		<!-- ## Tabelas por Mês -->
		<?php $tabelas_por_mes = json_decode( $tabelas_por_mes ); ?>
		<?php foreach ( $tabelas_por_mes as $mes => $tabela ) { ?>
		<table class="styled-table">
			<thead>
				<tr class="table-title">
					<th colspan="6">Tabela Mês <?php echo $mes; ?></th>
				</tr>
				<tr class="mobi-hide">
					<th>#</th>
					<th>Time</th>
					<th>Pontos</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$pos = 1;
					foreach ( $tabela as $time => $pont ) { ?>
						<tr>
							<td data-label="Posição"><?php echo $pos++; ?>º</td>
							<td data-label="Time"><?php echo $time; ?></td>
							<td data-label="Pontos"><?php echo $pont; ?></td>
						</tr>
					<?php } ?>
			</tbody>
		</table>
		<?php } ?>
	


		<!-- ## TOP 10 PONTUACOES -->
		<table class="styled-table">
			<thead>
				<tr class="table-title">
					<th colspan="3">10 Maiores Pontuações</th>
				</tr>
				<tr>
					<th>Time</th>
					<th>Pontos</th>
					<th>Rodada</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$top_10_pontuacoes = json_decode( $top_10_pontuacoes );
					foreach ( $top_10_pontuacoes as $pont ) { ?>
						<tr>
							<td><?php echo $pont->time; ?></td>
							<td><?php echo $pont->pontos; ?></td>
							<td><?php echo $pont->rodada; ?>a</td>
						</tr>
					<?php } ?>
			</tbody>
		</table>

		<!-- ## RODADAS VENCIDAS -->
		<table class="styled-table">
			<thead>
				<tr class="table-title">
					<th colspan="3">Nº de Rodadas Vencidas</th>
				</tr>
				<tr>
					<th>Time</th>
					<th>Rodadas</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$rodadas_vencidas = json_decode( $rodadas_vencidas );
					foreach ( $rodadas_vencidas as $time => $rodadas ) { ?>
						<tr>
							<td><?php echo $time; ?></td>
							<td><?php echo $rodadas; ?></td>
						</tr>
					<?php } ?>
			</tbody>
		</table>

		<!-- ## MENOR PONTUAÇÃO PARA VENCER UMA RODADA -->
		<table class="styled-table">
			<thead>
				<tr class="table-title">
					<th colspan="3">Menor Pontuação p/ Vencer uma Rodada</th>
				</tr>
				<tr>
					<th>Time</th>
					<th>Pontos</th>
					<th>Rodada</th>
				</tr>
			</thead>
			<tbody>
				<?php $menor_pont_vencedor = json_decode( $menor_pont_vencedor ); ?>
				<tr>
					<td><?php echo $menor_pont_vencedor->time; ?></td>
					<td><?php echo $menor_pont_vencedor->pontos; ?></td>
					<td><?php echo $menor_pont_vencedor->rodada; ?>ª</td>
				</tr>
			</tbody>
		</table>


		<!-- ## RODADAS NA LIDERANÇA -->
		<table class="styled-table">
			<thead>
				<tr class="table-title">
					<th colspan="3">Rodadas na Liderança</th>
				</tr>
				<tr>
					<th>Time</th>
					<th>Rodadas</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$rodadas_na_lideranca = json_decode( $rodadas_na_lideranca );
					foreach ( $rodadas_na_lideranca as $time => $rodadas ) { ?>
						<tr>
							<td><?php echo $time; ?></td>
							<td><?php echo $rodadas; ?></td>
						</tr>
					<?php } ?>
			</tbody>
		</table>

	</div>
</body>
</html>
