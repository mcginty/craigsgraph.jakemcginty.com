<?php
	
?>
<html>
<head>
<title>Items so far</title>
<link rel="stylesheet" type="text/css" href="styles/search.css" />
<link type="text/css" href="styles/redmond/jquery-ui-1.8.6.custom.css" rel="stylesheet" />	

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script> 
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.6/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/jquery.scrollTo-min.js"></script>
<script type="text/javascript" src="js/highcharts.js"></script>
<script type="text/javascript">
var dict = {};
var maxes_init = {};
var pmin = 0;
var pmax = 0;
var sid = 0;
var active_hash = 0;
var month=new Array(12);
month[0]="Jan";
month[1]="Feb";
month[2]="Mar";
month[3]="Apr";
month[4]="May";
month[5]="Jun";
month[6]="Jul";
month[7]="Aug";
month[8]="Sep";
month[9]="Oct";
month[10]="Nov";
month[11]="Dec";

function pad(number, length) {
	var str = '' + number;
	while (str.length < length) {
		str = '0' + str;
	}
	return str;
}

function optOut() {
	$.ajax( {
		url: 'optout.php?sid='+sid,
		success: function(whatevah) {
			$('#privacy').html('Successfully deleted.');
		}
	});
}

function requestData() {
	// grab graph x and y data (and potentiall color)
	$.ajax({
		url: (pmin > 0 || pmax > 0) ? 'results.json.php?q=<?php echo $_GET['q']; ?>&pmin='+pmin+'&pmax='+pmax : 'results.json.php?q=<?php echo $_GET['q']; if (isset($_GET['pmin'])&&isset($_GET['pmax'])) echo "&pmin={$_GET['pmin']}&pmax={$_GET['pmax']}"; ?>',
		success: function(points) {
			var series = chart.series[0];

			// add the point
			chart.series[0].setData(eval(points), true);
		},
		cache: false
	});

	// grab extra information
	// * title
	// * description (TODO: check if description is really freakin' necessary. a lot of data.)
	// * 
	$.ajax({
		url: (pmin > 0 || pmax > 0) ? 'results.json.extra.php?q=<?php echo $_GET['q']; ?>&sid='+sid+'&pmin='+pmin+'&pmax='+pmax : 'results.json.extra.php?q=<?php echo $_GET['q']; if (isset($_GET['pmin'])&&isset($_GET['pmax'])) echo "&pmin={$_GET['pmin']}&pmax={$_GET['pmax']}"; ?>',
		success: function(info) {
			dict = JSON.parse(info);
			pmax = dict.properties.pmax;
			pmin = dict.properties.pmin;
			sid	 = dict.properties.sid;
			// only update the slider max and mins the FIRST time, so they can widen search if need be
			if (typeof maxes_init.min == "undefined") {
				maxes_init.max = dict.properties.pmax;
				maxes_init.min = dict.properties.pmin;

				$( "#slider-range" ).slider( "option", "min", parseInt(maxes_init.min) );
				$( "#slider-range" ).slider( "option", "max", parseInt(maxes_init.max) );
				$( "#amount" ).val( "$" + pmin + " - $" + pmax );
				$( "#slider-range" ).slider( "option", "values", [pmin, pmax] );
				$('#privacy').html('I care about my privacy. <a href="javascript:optOut();">Click here to delete your query from our storage.</a>');
			}
			$('#listings').empty();
			$('#listings').html('<ul>');
			for (listing in dict) {
				if (typeof dict[listing].link != "undefined") {
					$('#listings').append('<li id="'+listing+'" class="listing"><a target="_blank" href="'+dict[listing].link+'"><span class="listing_title">'+dict[listing].title+'</span></a><span class="listing_price"> - $'+dict[listing].price+' in '+dict[listing].location+'</span></li>');
				}
			}
			$('#listings').append('</ul>');
			$('#listings li').hover(function() {
				chart.get($(this).attr('id')).select();
				$(this).animate({"background-color": '#BCF011', "padding-left": '15px' }, 200);
				$(this).children('.listing_price').animate({"color": '#000' }, 200);
			},
			function() {
				chart.get($(this).attr('id')).select();
				$(this).animate({"background-color": '#ffffff', "padding-left": '5px' }, 200);
				$(this).children('.listing_price').animate({"color": '#ccc' }, 200);
			});

		},
		cache: false
	});
}
var chart;

$(document).ready(function() {
	$('#rightblock').scrollTo( 0 );

	$(function() {
		$( "#slider-range" ).slider({
			range: true,
			min: 0,
			max: 100,
			values: [ 25, 75 ],
			slide: function( event, ui ) {
				$( "#amount" ).val( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
	
			},
			stop: function(event, ui) {
				pmin = ui.values[ 0 ];
				pmax = ui.values[ 1 ];
				requestData();
			}
		});
		$( "#amount" ).val( "$" + $( "#slider-range" ).slider( "values", 0 ) +
			" - $" + $( "#slider-range" ).slider( "values", 1 ) );
	});
	// define the options
	var options = {
		chart: {
			renderTo: 'graph-container',
			zoomType: 'xy',
			defaultSeriesType: 'scatter',
			events: {
				load: requestData
			}
		},
		title: {
			enabled: false,
			text: ''
		},
		subtitle: {
			enabled: false,
			text: ''
		},
		xAxis: {
			title: {
				text: ''
			},
			labels: {
				formatter: function() {
					var date = new Date(this.value*1000);
	                var monthnum = date.getMonth();
	                var day = date.getDate()+1;
	                return month[monthnum]+' '+day;
				}
			}
		},
		yAxis: {
			min: 0,
			title: {
				text: ''
			},
			labels: {
				formatter: function() {
					if (this.value >= 0)
						return '$' + this.value;
					else
						return '';
				}
			}
		},
		legend: {
			enabled: false
		},
		tooltip: {
			crosshairs: [true, true],
			style: {
				
			},
			formatter: function() {
					var date = new Date(this.x*1000);
	                var hours = date.getHours();
	                var minutes = date.getMinutes();
	                var monthnum = date.getMonth();
	                var day = date.getDate();
	                var year = date.getFullYear();
	                var hash = this.x.toString()+this.y.toString();
	                //$('#reporting').html('<b>'+dict[hash].title+'</b><br>'+month[monthnum]+' '+day+', '+year+' '+ pad(hours,2)+':'+pad(minutes,2) +' - $'+ this.y);
					return '<b>'+dict[hash].title+'</b><br>'+month[monthnum]+' '+day+', '+year+' '+ pad(hours,2)+':'+pad(minutes,2) +' - $'+ this.y;
			}
		},
		plotOptions: {
			series: {
				point: {
					events: {
						click: function() {
							var hash = this.x.toString()+this.y.toString();
							window.open(dict[hash].link,'_blank');
						},
						mouseOver: function() {
							var date = new Date(this.x*1000);
			                var hours = date.getHours();
			                var minutes = date.getMinutes();
			                var monthnum = date.getMonth();
			                var day = date.getDate();
			                var year = date.getFullYear();
			                var hash = this.x.toString()+this.y.toString();
			                //$('#reporting').html('<b>'+dict[hash].title+'</b><br>'+month[monthnum]+' '+day+', '+year+' '+ pad(hours,2)+':'+pad(minutes,2) +' - $'+ this.y);
			                
			                $('#rightblock').stop(true, true);
			                 $('#rightblock li#'+active_hash).stop(true, true);

							if (active_hash != 0) {
								$('li#'+active_hash).animate({"background-color": '#ffffff', "padding-left": '5px' }, 500);
								$('li#'+active_hash+' .listing_price').animate({"color": '#ccc' }, 200);
							}
							active_hash = hash;
							$('#rightblock li#'+hash).animate({ "background-color": '#BCF011', "padding-left": '15px' }, 1000);
							$('li#'+active_hash+' .listing_price').animate({"color": '#000000' }, 200);
							
							$("#rightblock").scrollTo('li#'+hash, 400, {margin:true});
						}
					}
				},
				events: {
					mouseOut: function() {
						if (active_hash != 0) {
							$('li#'+active_hash).animate({"background-color": '#ffffff', "padding-left": '5px' }, 500);
							$('li#'+active_hash+' .listing_price').animate({"color": '#ccc' }, 200);
						}
					}
				}
			},
			scatter: {
				marker: {
					radius: 5,
					states: {
						hover: {
							enabled: true
						}
					}
				},
				states: {
					hover: {
						marker: {
							enabled: false
						}
					}
				}
			}
		},
		series: [{
			name: 'Craigslist Results',
			marker: {
				enabled:true,
				radius: 4
			},
			data: []
		}]
	}
	
	// Load data asynchronously using jQuery. On success, add the data
	// to the options and initiate the chart.
	// http://api.jquery.com/jQuery.getJSON/

		
	chart = new Highcharts.Chart(options);
});

</script>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-322489-4']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</head>

<body>
<div id="main-container">
	<div id="header">
		<form id="search" name="search" method="get" action="search.php">
		<div id="searchbar">
		<div id="title">craigsgraph</div>
		
			<input type="text" id="query" name="q" />
			<input type="submit" name="" id="go" value=" " />

		</div>
		</form>
	</div>

	<div id="leftblock">
		<div id="graph-container" style="width:550px; height:75%;"></div>
		<div id="reporting"></div>
		<div id="filters-container" style="width:550px; height:25%;">
		<p>
			<label for="amount">Price range:</label>
			<input type="text" id="amount" style="border:0; color:#f6931f; font-weight:bold;" />
		</p>
		<div id="slider-range" style="width:80%; margin: 0 auto;"></div>
		<br /><br />
		<div class="instructions">Hint: try holding down the mouse and making a rectangle in the graph. It'll zoom for you. Hit "reset zoom" on the top right of the graph to reset (obviously).</div>
		</div>

		<div id="privacy"></div>
	</div>
	<div id="rightblock">
		<div class="instructions">results in list sorted by relevance</div>
		<div id="listings">
		</div>
	</div>
</div>
</body>
</html>
