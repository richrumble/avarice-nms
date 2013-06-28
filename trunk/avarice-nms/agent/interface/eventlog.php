<html>
	<head>
		<script type = "text/javascript" src = "jquery-2.0.2.js"></script>
		<script type = "text/javascript">
			$("#mainForm").submit(function(){
				var formData = $(this).serialize();
				$.ajax({
					type: "POST",
					url: 'sqlite.ajax.php',
					data: formData,
					dataType: 'json',
					success: function(jsonResult){
						var items = [];
						$.each(jsonResult['data'], function(key, val) {
							var n=val.split(",");
							items.push('<tr><td>' + n[0] + '</td><td>' + n[1] + '</td><td>' + n[2] + '</td><td>' + n[3] + '</td><td>' + n[4] + '</td><td>' + n[5] + '</td><td>' + n[6] + '</td></tr>');
						});
						$('<table/>', {
							'id': 'resultTable',
							html: items.join('')
						}).appendTo('#resultPane');
					}
				});
				return false;
			});
		</script>
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
			<input size = 30 type = "text" id = "searchString" name = "searchString" onkeyup="$('#mainForm').submit();"/>
			<input type = "submit" value = "clickme" />
		</form>
		<script type = "text/javascript">
			$("#mainForm").submit(function(){
				var formData = $(this).serialize();
				$.ajax({
					type: "POST",
					url: 'sqlite.ajax.php',
					data: formData,
					dataType: 'json',
					success: function(jsonResult){
						var items = [];
						$.each(jsonResult['data'], function(computerName, eventLog, source, user, timeWritten, type, message) {
							items.push('<tr><td>' + computerName + '</td><td>' + eventLog + '</td><td>' + source + '</td><td>' + user + '</td><td>' + timeWritten + '</td><td>' + type + '</td><td>' + message + '</td></tr>');
						});
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