<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<?php
	// Inicjacja sesji i stałych aplikacji  
	include_once('php/config.php');
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>
		IAI - zadanie testowe
	</title>
	<link rel="stylesheet" type="text/css" href="http://sencha.erb.pl/resources/css/ext-all.css" />
	<link rel="stylesheet" type="text/css" href="css/iai.css" />

	<script type="text/javascript" src="http://sencha.erb.pl/ext-all-debug-w-comments.js"></script>
	<script type="text/javascript">
		Ext.Loader.setConfig({
			enabled	: true,
			disableCaching	: false,
			paths	: {
				'Ext.ux': 'http://sencha.erb.pl/ux'
			}
		});
        
		var App;
		Ext.onReady(function () {
			App = Ext.create ('app.iai',{
				// url zawiera rzeczywisty url do aplikacji 
				url	: '<?php print (SYS_URL); ?>'
			});
		});
    </script>

</head>
<body>
	<h1>
		IAI - zadanie testowe
	</h1>
	<p>
		Drzewko
	</p>
</body>
</html>
