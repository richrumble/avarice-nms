<html>
	<head>
		<script type = "text/javascript" src = "jquery-2.0.2.js"></script>
	</head>
	<body>
		<form id = "mainForm">
			<input type = "hidden" name = "section" value = "eventLog" />
			<input type = "hidden" name = "action" value = "logSearch" />
			<input id = "offset-value" type = "hidden" name = "offset" value = 0 />
			<input id = "maxPage" type = "hidden" name = "maxPage" value = 0 />
			<div id = "selectBoxes"></div>
			<input size = 30 type = "text" id = "searchString" name = "searchString" placeholder = "Search String" /><br />
			Results per page: <input type = "radio" name = "limit" value = "50" checked /> 50 <input type = "radio" name = "limit" value = "100" /> 100 <input type = "radio" name = "limit" value = "500" /> 500 <input type = "radio" name = "limit" value = "1000" /> 1,000<br />
			<input type = "submit" value = "clickme" />
		</form>
		<div id = "paginationPane" style = "display: none;">
			<a href = "#" class = "first">&lt;&lt;</a>
			<a href = "#" class = "previous">&lt;</a>
			<input id = "pagination-page" type = "text" size = 1 value = 1 readonly />
			<a href = "#" class = "next">&gt;</a>
			<a href = "#" class = "last">&gt;&gt;</a>
		</div>
		<div id = "resultPane"></div>
		<script type = "text/javascript">
			// Get Users for selection
			$.getJSON('sqlite.ajax.php?section=eventLog&action=getUser', function(jsonResult) {
				var items = [];
				$.each(jsonResult['data'], function(key, val) {
					if (key == 0)
					{
						items.push('<option value="' + key + '" selected>' + val + '</option>');
					}
					else
					{
						items.push('<option value="' + key + '">' + val + '</option>');
					}
				});
				$('<select/>', {
					'multiple': 'multiple',
					'name': 'userID[]',
					'id': 'selectUser',
					'size': '3',
					html: items.join('')
				}).appendTo('#selectBoxes');
			});
			
			//Get EventLogs for selection
			$.getJSON('sqlite.ajax.php?section=eventLog&action=getEventLog', function(jsonResult) {
				var items = [];
				$.each(jsonResult['data'], function(key, val) {
					if (key == 0)
					{
						items.push('<option value="' + key + '" selected>' + val + '</option>');
					}
					else
					{
						items.push('<option value="' + key + '">' + val + '</option>');
					}
				});
				$('<select/>', {
					'multiple': 'multiple',
					'name': 'eventLogID[]',
					'id': 'selectEventLog',
					'size': '3',
					html: items.join('')
				}).appendTo('#selectBoxes');
			});
			
			//Get Sources for selection
			$.getJSON('sqlite.ajax.php?section=eventLog&action=getSource', function(jsonResult) {
				var items = [];
				$.each(jsonResult['data'], function(key, val) {
					if (key == 0)
					{
						items.push('<option value="' + key + '" selected>' + val + '</option>');
					}
					else
					{
						items.push('<option value="' + key + '">' + val + '</option>');
					}
				});
				$('<select/>', {
					'multiple': 'multiple',
					'name': 'sourceID[]',
					'id': 'selectSource',
					'size': '3',
					html: items.join('')
				}).appendTo('#selectBoxes');
			});
			
			// Search string delayed submission
			var timerid;
			$("#searchString").keyup(function(){
				clearTimeout(timerid);
				timerid = setTimeout(function() { $('#mainForm').submit(); }, 500);
			});
			
			var fullResultSet;
			$("#mainForm").submit(function(){
				var formData = $(this).serialize();
				$.ajax({
					type: "POST",
					url: 'sqlite.ajax.php',
					data: formData,
					dataType: 'json',
					success: function(jsonResult){
						$("#pagination-page").attr('value', 1);
						$("#offset-value").attr('value', 0);
						resultPopulation(jsonResult['data'])
					}
				});
				$.ajax({
					type: "POST",
					url: 'sqlite.ajax.php',
					data: formData + '&fullResultSet=true',
					dataType: 'json',
					success: function(jsonResult){
						fullResltSet = jsonResult['data'];
						$("#maxPage").attr("value", Math.floor(fullResltSet.length / parseInt($('input:radio[name=limit]:checked').val())));
						$('#paginationPane').show();
					}
				});
				return false;
			});
			
			// search result population function
			function resultPopulation(arr)
			{
				var items = [];
				items.push('<tr><th>Computer Name</th><th>Event Log</th><th>Source</th><th>User</th><th>Time Written</th><th>Type</th><th>Message</th></tr>');
				$.each(arr, function(key, val) {
					items.push('<tr><td>' + val[0] + '</td><td>' +val[1] + '</td><td>' + val[2] + '</td><td>' + val[3] + '</td><td>' + val[4] + '</td><td>' + val[5] + '</td><td>' + val[6] + '</td></tr>');
				});
				$('#resultPane').empty();
				$('<table/>', {
					'id': 'resultTable',
					html: items.join('')
				}).appendTo('#resultPane');
			}
			
			// Pagination links
			$("div#paginationPane a").click(function(){
				if ($(this).attr('class') == "first")
				{
					$("#pagination-page").attr('value', 1);
					$("#offset-value").attr('value', 0);
					resultPopulation(fullResltSet.slice(0, parseInt($('input:radio[name=limit]:checked').val())));
				}
				else if (($(this).attr('class') == "previous") && (parseInt($("#pagination-page").attr('value')) > 1))
				{
					$("#pagination-page").attr('value', parseInt($("#pagination-page").attr('value')) - 1);
					$("#offset-value").attr('value', parseInt($("#offset-value").attr('value')) - 1);
					resultPopulation(fullResltSet.slice(parseInt($("#offset-value").attr('value')) * parseInt($('input:radio[name=limit]:checked').val()), (parseInt($("#offset-value").attr('value')) + 1) * parseInt($('input:radio[name=limit]:checked').val())));
				}
				else if (($(this).attr('class') == "next") && (parseInt($("#pagination-page").attr('value')) < (parseInt($("#maxPage").attr('value')))))
				{
					$("#pagination-page").attr('value', parseInt($("#pagination-page").attr('value')) + 1);
					$("#offset-value").attr('value', parseInt($("#offset-value").attr('value')) + 1);
					resultPopulation(fullResltSet.slice(parseInt($("#offset-value").attr('value')) * parseInt($('input:radio[name=limit]:checked').val()), (parseInt($("#offset-value").attr('value')) + 1) * parseInt($('input:radio[name=limit]:checked').val())));
				}
				else if ($(this).attr('class') == "last")
				{
					$("#pagination-page").attr('value', Math.floor(fullResltSet.length / parseInt($('input:radio[name=limit]:checked').val())));
					$("#offset-value").attr('value', Math.floor(fullResltSet.length / parseInt($('input:radio[name=limit]:checked').val())) - 1);
					resultPopulation(fullResltSet.slice(Math.floor(fullResltSet.length / parseInt($('input:radio[name=limit]:checked').val())) * parseInt($('input:radio[name=limit]:checked').val())));
				}
				return false;
			});
		</script>
	</body>
</html>