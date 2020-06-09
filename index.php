<html>

<head>
	<meta name="mobile-web-app-capable" content="yes">
	<link rel="shortcut icon" sizes="196x196" href="icon.png">
	<script type='text/javascript'>
		function startMC() {
			$('#php').load('monitor.php?func=servicePwr&srv=mc&act=START');
		}

		function stopMC() {
			$('#php').load('monitor.php?func=servicePwr&srv=mc&act=STOP');
		}
	</script>
	<title>Horatio | DashBoard</title>
	<link rel="stylesheet" type="text/css" href="http://bootswatch.com/4/darkly/bootstrap.min.css">
	<meta name="viewport" content="width=device-width">
	<meta charset="UTF-8">
</head>
<body>
	<div class="row">
		<div class="col-sm-6 ">
			<div class="thumbnail">
				<div class="caption">
					<h3>Minecraft</h3>
					<p><b> Server status : </b></p>
					<div id="mon">
						<span class="label label-info">Loading...</span>
					</div>
					<br>
					<p><b> Server control : </b></p>
					<button class="btn btn-primary" onclick="startMC()">Start</button>
					<button class="btn btn-danger" onclick="stopMC()">Stop</button>
					<p>Logs:</p>
					<div id="mc"></div>
				</div>
			</div>
		</div>
		<div class="col-sm-6 col-md-4">
			<div class="thumbnail">
				<div class="caption">
					<h3>SysInfo :</h3>
					<div id='mon3'>
						<div class="progress progress-striped active">
							<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="100"
								aria-valuemin="0" aria-valuemax="100" style="width: 100%">
								<span class="sr-only">Loading sensors...</span>
								Loading sensors...(This may take a while.)
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
<script src='https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js'></script>
<script type='text/javascript'>
	var auto_refresh = setInterval(
		function () {
			$('#mon').load('monitor.php?func=servicePwr&srv=mc&act=ISUP');
			$('#mon2').load('monitor.php?func=srvUP');
			$('#mon3').load('monitor.php?func=srvSENSORS');
			$('#mc').load('monitor.php?func=mc');
		}, 1100); // refresh every  milliseconds
</script>

</html>