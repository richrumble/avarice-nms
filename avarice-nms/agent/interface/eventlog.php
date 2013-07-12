<html>
	<head>
		<script type = "text/javascript" src = "jquery-2.0.2.js"></script>
	</head>
	<body>
		<form id = "mainForm">
			<input type = "hidden" name = "section" value = "eventLog" />
			<input type = "hidden" name = "action" value = "logSearch" />
			<div id = "selectBoxes"></div>
			<script type = "text/javascript">
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
			</script>
			<input size = 30 type = "text" id = "searchString" name = "searchString" onkeyup=""/>
			<input type = "submit" value = "clickme" />
		</form>
		<script type = "text/javascript">
			var timerid;
			jQuery("#searchString").keyup(function(){
				clearTimeout(timerid);
				timerid = setTimeout(function() { $('#mainForm').submit(); }, 500);
			});
			$("#mainForm").submit(function(){
				var formData = $(this).serialize();
				$.ajax({
					type: "POST",
					url: 'sqlite.ajax.php',
					data: formData,
					dataType: 'json',
					success: function(jsonResult){
						var items = [];
						items.push('<tr><th>Computer Name</th><th>Event Log</th><th>Source</th><th>User</th><th>Time Written</th><th>Type</th><th>Message</th></tr>');
						$.each(jsonResult['data'], function(key, val) {
							items.push('<tr><td>' + val[0] + '</td><td>' +val[1] + '</td><td>' + val[2] + '</td><td>' + val[3] + '</td><td>' + val[4] + '</td><td>' + val[5] + '</td><td>' + val[6] + '</td></tr>');
						});
						$('#resultPane').empty();
						$('<table/>', {
							'id': 'resultTable',
							html: items.join('')
						}).appendTo('#resultPane');
					}
				});
				return false;
			});
		</script>
		<div id = "resultPane"></div>
	</body>
</html>