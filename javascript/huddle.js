
var json = d3.json("php/huddleSQL.php", function(error,data) {
	var xAxis, xScale, yAxis, yScale, defaultScales = {};
	d3.select("#selectCov").on("change",covChange);
	
	var numbers = "nb-number";
	covChange();
	function covChange() {
		var dataUsed = "Yesterday";
		var covSelect = d3.select("#selectCov").property("value");
		var headings = {"nb-number" : d3.sum(data.filter(function(d) {return d.SPECIFIC_DATE == dataUsed;}), function(d) {return +d.NB_POLICIES;}),
			"nb-prem" : d3.sum(data.filter(function(d) {return d.SPECIFIC_DATE == dataUsed;}), function(d) {return +d.NB_WRITTEN;}),
			"nb-average" : (d3.sum(data.filter(function(d) {return d.SPECIFIC_DATE == dataUsed;}), function(d) {return +d.NB_WRITTEN;})/
				d3.sum(data.filter(function(d) {return d.SPECIFIC_DATE == dataUsed;}), function(d) {return +d.NB_POLICIES;})),
			"all-number" : d3.sum(data.filter(function(d) {return d.SPECIFIC_DATE == dataUsed;}), function(d) {return +d.TOTAL_POLICIES;}),
			"all-prem" : d3.sum(data.filter(function(d) {return d.SPECIFIC_DATE == dataUsed;}), function(d) {return +d.TOTAL_WRITTEN;}),
			"all-average" : (d3.sum(data.filter(function(d) {return d.SPECIFIC_DATE == dataUsed;}), function(d) {return +d.TOTAL_WRITTEN;})/
				d3.sum(data.filter(function(d) {return d.SPECIFIC_DATE == dataUsed;}), function(d) {return +d.TOTAL_POLICIES;})),
			"cancellations" : d3.sum(data.filter(function(d) {return d.SPECIFIC_DATE == dataUsed;}), function(d) {return +d.CANCELLATIONS;}),
			"inforce" : d3.sum(data.filter(function(d) {return d.SPECIFIC_DATE == dataUsed;}), function(d) {return +d.POLICIES_INFORCE;}),
			"endorsements" : d3.sum(data.filter(function(d) {return d.SPECIFIC_DATE == dataUsed;}), function(d) {return +d.ENDORSEMENTS;}),
			"reported" : d3.sum(data.filter(function(d) {return d.SPECIFIC_DATE == dataUsed && 
				(covSelect == "All" || d.COVERAGE == covSelect ||
				(covSelect == "Other" && ["PIP","PD","BI","CMP","COL"].indexOf(d.COVERAGE) < 0));}), function(d) {return +d.REPORTED_CLAIMS;}),
			"closed" : d3.sum(data.filter(function(d) {return d.SPECIFIC_DATE == dataUsed && 
				(covSelect == "All" || d.COVERAGE == covSelect ||
				(covSelect == "Other" && ["PIP","PD","BI","CMP","COL"].indexOf(d.COVERAGE) < 0));}), function(d) {return +d.CLOSED_CLAIMS;}),
			"mms" : d3.sum(data.filter(function(d) {return d.SPECIFIC_DATE == dataUsed && 
				(covSelect == "All" || d.COVERAGE == covSelect ||
				(covSelect == "Other" && ["PIP","PD","BI","CMP","COL"].indexOf(d.COVERAGE) < 0));}), function(d) {return +d.CLOSED_MMS;}),
			"reopens" : d3.sum(data.filter(function(d) {return d.SPECIFIC_DATE == dataUsed && 
				(covSelect == "All" || d.COVERAGE == covSelect ||
				(covSelect == "Other" && ["PIP","PD","BI","CMP","COL"].indexOf(d.COVERAGE) < 0));}), function(d) {return +d.REOPEN_CLAIMS;}),
			"open" : d3.sum(data.filter(function(d) {return d.SPECIFIC_DATE == dataUsed && 
				(covSelect == "All" || d.COVERAGE == covSelect ||
				(covSelect == "Other" && ["PIP","PD","BI","CMP","COL"].indexOf(d.COVERAGE) < 0));}), function(d) {return +d.OPEN_CLAIMS;})};
		
		
		d3.selectAll(".number-labels")
			.on("click",function(d) {numbers = d3.select(this).attr("id");
				redoGraphs(numbers);})
			.select("div").select("h3")
			.text(function() {return d3.format(",.0f")(headings[d3.select(this.parentNode.parentNode).attr("id")]);});
		redoGraphs(numbers);
		function redoGraphs (numbersUsed) {
			var tableValues = [{"heading" : "Yesterday", "filter" : "Yesterday"},
				{"heading" : "2 Weeks Ago", "filter" : "Yesterday"},
				{"heading" : "1 Year Ago", "filter" : "Yesterday"},
				{"heading" : "2 Week Average", "filter" : "Yesterday"},
				{"heading" : "2 Week Average 2 Weeks Ago", "filter" : "Yesterday"},
				{"heading" : "2 Week Average 1 Year Ago", "filter" : "Yesterday"}];
			var tableHeadings = {"nb-number" : "New Business Counts",
								"nb-prem" : "New Business Premium",
								"nb-average" : "New Business Premium Per Policy",
								"all-number" : "Total Counts",
								"all-prem" : "Total Premium",
								"all-average" : "Total Premium Per Policy",
								"cancellations" : "Cancellations",
								"inforce" : "Policies Inforce",
								"endorsements" : "Endorsements",
								"reported" : "Reported Claims - "+covSelect,
								"closed" : "Closed Claims - "+covSelect,
								"mms" : "Closed MM Claims - "+covSelect,
								"reopens" : "Reopened Claims - "+covSelect,
								"open" : "Open Claims - "+covSelect}
			d3.select("#bar-chart-title").text(tableHeadings[numbersUsed]);
			d3.select("#table-title").text(tableHeadings[numbersUsed]);
			var mainTable = d3.select("#mainTable");
			mainTable.selectAll("*").remove();
			var header = mainTable.append("thead").append("tr");
			header.append("th").text("Date Range");
			header.append("th").text(tableHeadings[numbersUsed]);
			var body = mainTable.append("tbody");
			var tr = body.selectAll("tr").data(tableValues).enter().append("tr");
			tr.append("td").text(function(d) {return d.heading;});
			tr.append("td").text(function(d) {
				var dataUsed = d.heading; 
				var headings = {"nb-number" : d3.sum(data.filter(function(d) {
					return (d.SPECIFIC_DATE == dataUsed || d.TRAIL_SPECIFIC_DATE == dataUsed);}), 
					function(d) {return +d.NB_POLICIES;}),
				"nb-prem" : d3.sum(data.filter(function(d) {return (d.SPECIFIC_DATE == dataUsed || d.TRAIL_SPECIFIC_DATE == dataUsed);}), function(d) {return +d.NB_WRITTEN;}),
				"nb-average" : (d3.sum(data.filter(function(d) {return (d.SPECIFIC_DATE == dataUsed || d.TRAIL_SPECIFIC_DATE == dataUsed);}), function(d) {return +d.NB_WRITTEN;})/
					d3.sum(data.filter(function(d) {return (d.SPECIFIC_DATE == dataUsed || d.TRAIL_SPECIFIC_DATE == dataUsed);}), function(d) {return +d.NB_POLICIES;})),
				"all-number" : d3.sum(data.filter(function(d) {return (d.SPECIFIC_DATE == dataUsed || d.TRAIL_SPECIFIC_DATE == dataUsed);}), function(d) {return +d.TOTAL_POLICIES;}),
				"all-prem" : d3.sum(data.filter(function(d) {return (d.SPECIFIC_DATE == dataUsed || d.TRAIL_SPECIFIC_DATE == dataUsed);}), function(d) {return +d.TOTAL_WRITTEN;}),
				"all-average" : (d3.sum(data.filter(function(d) {return (d.SPECIFIC_DATE == dataUsed || d.TRAIL_SPECIFIC_DATE == dataUsed);}), function(d) {return +d.TOTAL_WRITTEN;})/
					d3.sum(data.filter(function(d) {return (d.SPECIFIC_DATE == dataUsed || d.TRAIL_SPECIFIC_DATE == dataUsed);}), function(d) {return +d.TOTAL_POLICIES;})),
				"cancellations" : d3.sum(data.filter(function(d) {return (d.SPECIFIC_DATE == dataUsed || d.TRAIL_SPECIFIC_DATE == dataUsed);}), function(d) {return +d.CANCELLATIONS;}),
				"inforce" : d3.sum(data.filter(function(d) {return (d.SPECIFIC_DATE == dataUsed || d.TRAIL_SPECIFIC_DATE == dataUsed);}), function(d) {return +d.POLICIES_INFORCE;}),
				"endorsements" : d3.sum(data.filter(function(d) {return (d.SPECIFIC_DATE == dataUsed || d.TRAIL_SPECIFIC_DATE == dataUsed);}), function(d) {return +d.ENDORSEMENTS;}),
				"reported" : d3.sum(data.filter(function(d) {return (d.SPECIFIC_DATE == dataUsed || d.TRAIL_SPECIFIC_DATE == dataUsed) && 
					(covSelect == "All" || d.COVERAGE == covSelect ||
					(covSelect == "Other" && ["PIP","PD","BI","CMP","COL"].indexOf(d.COVERAGE) < 0));}), function(d) {return +d.REPORTED_CLAIMS;}),
				"closed" : d3.sum(data.filter(function(d) {return (d.SPECIFIC_DATE == dataUsed || d.TRAIL_SPECIFIC_DATE == dataUsed) && 
					(covSelect == "All" || d.COVERAGE == covSelect ||
					(covSelect == "Other" && ["PIP","PD","BI","CMP","COL"].indexOf(d.COVERAGE) < 0));}), function(d) {return +d.CLOSED_CLAIMS;}),
				"mms" : d3.sum(data.filter(function(d) {return (d.SPECIFIC_DATE == dataUsed || d.TRAIL_SPECIFIC_DATE == dataUsed) && 
					(covSelect == "All" || d.COVERAGE == covSelect ||
					(covSelect == "Other" && ["PIP","PD","BI","CMP","COL"].indexOf(d.COVERAGE) < 0));}), function(d) {return +d.CLOSED_MMS;}),
				"reopens" : d3.sum(data.filter(function(d) {return (d.SPECIFIC_DATE == dataUsed || d.TRAIL_SPECIFIC_DATE == dataUsed) && 
					(covSelect == "All" || d.COVERAGE == covSelect ||
					(covSelect == "Other" && ["PIP","PD","BI","CMP","COL"].indexOf(d.COVERAGE) < 0));}), function(d) {return +d.REOPEN_CLAIMS;}),
				"open" : d3.sum(data.filter(function(d) {return (d.SPECIFIC_DATE == dataUsed || d.TRAIL_SPECIFIC_DATE == dataUsed) && 
					(covSelect == "All" || d.COVERAGE == covSelect ||
					(covSelect == "Other" && ["PIP","PD","BI","CMP","COL"].indexOf(d.COVERAGE) < 0));}), function(d) {return +d.OPEN_CLAIMS;})};
				return d3.format(",.0f")(headings[numbersUsed]/
			((dataUsed.indexOf("Average") < 0 || numbersUsed.indexOf("average") >= 0)?1:14));});
			d3.selectAll("#dailyToggle, #monthlyToggle, #rollingToggle")
				.on("click",function() {var tFrame = d3.select(this).attr("id");
				runGraph(tFrame);});
			var tFrame = d3.select(".active").attr("id");
			runGraph(tFrame);
			
			
			
			function runGraph(timeFrame) {
				var t = 1000;
				var timeParse;
				switch(timeFrame) {
					case "dailyToggle":
						var timeParse = d3.timeParse("%Y-%m-%d");
						break;
					case "monthlyToggle":
						var timeParse = d3.timeParse("%Y-%m");
						break;
					case "rollingToggle":
						var timeParse = d3.timeParse("%Y-%m-%d");
						break;					
				}
				if(d3.select("#dailyToggle").attr("class") == "active")
				{
					
				}
				
				
				var chartData = d3.nest()
					.key(function(d) {return (timeFrame=="monthlyToggle")?d.METRIC_DATE.substring(0,7):d.METRIC_DATE;})
					.rollup(function(sum) {
						var summations = {"nb-number" : d3.sum(sum, function(d) {return +d.NB_POLICIES;}),
							"nb-prem" : d3.sum(sum, function(d) {return +d.NB_WRITTEN;}),
							"nb-average" : d3.sum(sum, function(d) {return +d.NB_WRITTEN;})/d3.sum(sum, function(d) {return +d.NB_POLICIES;}),
							"all-number" : d3.sum(sum, function(d) {return +d.TOTAL_POLICIES;}),
							"all-prem" : d3.sum(sum, function(d) {return +d.TOTAL_WRITTEN;}),
							"all-average" : d3.sum(sum, function(d) {return +d.TOTAL_WRITTEN;})/d3.sum(sum, function(d) {return +d.TOTAL_POLICIES;}),
							"cancellations" : d3.sum(sum, function(d) {return +d.CANCELLATIONS;}),
							"inforce" : d3.sum(sum.filter(function(d) {
								return timeFrame != "monthlyToggle" || d.METRIC_DATE.substring(8,10) == "01"})
								, function(d) {return +d.POLICIES_INFORCE;}),
							"endorsements" : d3.sum(sum, function(d) {return +d.ENDORSEMENTS;}),
							"reported" : d3.sum(sum.filter(function(d) {return (covSelect == "All" || d.COVERAGE == covSelect ||
								(covSelect == "Other" && ["PIP","PD","BI","CMP","COL"].indexOf(d.COVERAGE) < 0));}), 
								function(d) {return +d.REPORTED_CLAIMS;}),
							"closed" : d3.sum(sum.filter(function(d) {return (covSelect == "All" || d.COVERAGE == covSelect ||
								(covSelect == "Other" && ["PIP","PD","BI","CMP","COL"].indexOf(d.COVERAGE) < 0));}), 
								function(d) {return +d.CLOSED_CLAIMS;}),
							"mms" : d3.sum(sum.filter(function(d) {return (covSelect == "All" || d.COVERAGE == covSelect ||
								(covSelect == "Other" && ["PIP","PD","BI","CMP","COL"].indexOf(d.COVERAGE) < 0));}), 
								function(d) {return +d.CLOSED_MMS;}),
							"reopens" : d3.sum(sum.filter(function(d) {return (covSelect == "All" || d.COVERAGE == covSelect ||
								(covSelect == "Other" && ["PIP","PD","BI","CMP","COL"].indexOf(d.COVERAGE) < 0));}), 
								function(d) {return +d.REOPEN_CLAIMS;}),
							"open" : d3.sum(sum.filter(function(d) {return (covSelect == "All" || d.COVERAGE == covSelect ||
								(covSelect == "Other" && ["PIP","PD","BI","CMP","COL"].indexOf(d.COVERAGE) < 0)) &&
								(timeFrame != "monthlyToggle" || d.METRIC_DATE.substring(8,10) == "01");}), 
								function(d) {return +d.OPEN_CLAIMS;})
						};
						return {"sumed"  : summations[numbersUsed]}})
					.entries(data);
					
				chartData.forEach(function(d, i) {
					if(d.key.split('-').join('') > 20160113)
					{
						var roll = 0;
						chartData.forEach(function(e, j) {
							if(j > i - 14 && j <= i)
							{	
								roll += +e.value.sumed;
							}
						});
						d.rolling = roll;
					}
				});	
				
				chartData = chartData.filter(function(d) {return d.key.split('-').join('') > 20160113 || timeFrame != "rollingToggle";});
				
				chartData.forEach(function(d) {
					if(d.rolling && timeFrame == "rollingToggle")
					{
						d.value.sumed = d.rolling/14;
					}
				});
					
				
				var svg = d3.select("#barChart").select("svg");
				var t = svg.transition().duration(750);
				var chartHeight = (svg.style("height")
					.substring(0,svg.style("height").length - 2))/1;
			
				var chartWidth = (svg.style("width")
					.substring(0,svg.style("width").length - 2))/1;
					
				var h = chartHeight*(7.75/10), w = chartWidth*(.84);
				
				
				if(d3.select("#bar-chart").empty())
				{svg.append("g").attr("id","bar-chart")
					.append("defs").append("clipPath")
					.attr("id","clip")
					.append("rect").attr("class","overlay");
				}
				var barChart = d3.select("#bar-chart")
					.attr("transform","translate(" + (chartWidth*(1/15)) + "," + (chartHeight*(1/40)) + ")");
				
				barChart.select("#clip")
					.select(".overlay")
					.attr("width",w)
					.attr("height",h);
										
					
				var brush = d3.brush()
					.extent([[0,0],[w,h]])
					.on("end",brushended),
					idleTimeout,
					idleDelay = 350;
					
				if(d3.select(".brush").empty())
					{barChart.append("g").attr("class","brush")}
				d3.select(".brush").call(brush);
				
				function brushended() {
					var s = d3.event.selection;
					if(!s) {
							if(!idleTimeout) return idleTimeout = setTimeout(idled, idleDelay);
							xScale.domain(defaultScales.xScale);
							yScale.domain(defaultScales.yScale);
					} else {
						xScale.domain([s[0][0], s[1][0]].map(xScale.invert, xScale));
						yScale.domain([s[1][1], s[0][1]].map(yScale.invert, yScale));
						d3.select(".brush").call(brush.move, null);
					}
					zoom();
				}
				
				function idled() {
					idleTimeout = null;
				}
					
				function zoom() {
					svg.select(".x.axis").transition(t).call(xAxis);
					svg.select(".y.axis").transition(t).call(yAxis);
					if(numbersUsed.indexOf("average")>=0)
					{
						dataLine.transition(t).attr("d",line);
					} else {
						barWidth = xScale(timeParse(chartData[1].key)) - xScale(timeParse(chartData[0].key));
						d3.selectAll(".bar").data(chartData).transition(t)
						.attr("x",function(d) {return xScale(timeParse(d.key)) - (barWidth/2);})
						.attr("y",function(d) {return yScale(+d.value.sumed);})
						.attr("height",function(d) {return d3.max([0,h - yScale(d.value.sumed)]);})
						.attr("width",barWidth);
					}
				}
				
				defaultScales.xScale = [d3.min(chartData, function(d) {return timeParse(d.key);}),
										d3.max(chartData, function(d) {return timeParse(d.key);})];
				
				defaultScales.yScale = [0,
										d3.max(chartData, function(d) {
											return d.value.sumed;})];
											
				xScale = d3.scaleTime()
					.domain(defaultScales.xScale)
					.range([0,w]);
					
				yScale = d3.scaleLinear()
					.domain(defaultScales.yScale)
					.range([h,0]);
				
				
				xAxis = d3.axisBottom()
					.scale(xScale);
				
				yAxis = d3.axisLeft()
					.scale(yScale);
					
					// test comment
				
				if(d3.select(".x.axis.main").empty())
					{barChart.append("g").attr("class","x axis main");}
				
				if(d3.select(".y.axis.main").empty())
					{barChart.append("g").attr("class","y axis main");}
				
				d3.select(".x.axis").transition(t).call(xAxis);
				d3.select(".x.axis").attr("transform","translate(0," + h + ")");
				d3.select(".y.axis").transition(t).call(yAxis);
				if(d3.select(".clip-path").empty())
				{	barChart.append("g")
					.attr("class","clip-path")
					.attr("clip-path","url(#clip)");
				}
				var barChart2 = d3.select(".clip-path");
				if(numbersUsed.indexOf("average")>=0)
				{	
					var dataLine;
					if(d3.select(".chartLine").empty())
					{
						dataLine = barChart2
							.append("path")
							.style("fill","none")
							.style("stroke-width","1.5px")
							.style("stroke","blue")
							.attr("class","chartLine");	
					}
					dataLine = barChart2.select(".chartLine");
					var line = d3.line()
						.x(function(d) {return xScale(timeParse(d.key));})
						.y(function(d) {return yScale(+d.value.sumed);});
					barChart2.selectAll(".bar").remove();
					dataLine.datum(chartData)
						.transition(t)
						.attr("d",line);
				} else {
					d3.selectAll(".chartLine").remove();
					var rects = barChart2.selectAll(".bar").data(chartData);
					barWidth = xScale(timeParse(chartData[1].key)) - xScale(timeParse(chartData[0].key));
					rects.enter().append("rect").transition(t)
						.attr("class","bar")
						.style("fill","steelblue")
						.attr("x",function(d) {return xScale(timeParse(d.key)) - (barWidth/2);})
						.attr("y",function(d) {return yScale(d.value.sumed);})
						.attr("height",function(d) {return h - yScale(d.value.sumed);})
						.attr("width",barWidth);
						
					rects.transition(t)
						.attr("x",function(d) {return xScale(timeParse(d.key)) - (barWidth/2);})
						.attr("y",function(d) {return yScale(+d.value.sumed);})
						.attr("height",function(d) {return h - yScale(d.value.sumed);})
						.attr("width",barWidth);
					rects.exit().remove();
				}
			}
			
			window.addEventListener("resize",runGraph);
			
			
		}
	}
	
});
