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
	<link rel="stylesheet" type="text/css" href="http://sencha.erb.pl/resources/css/ext-all-neptune.css" />
	<link rel="stylesheet" type="text/css" href="css/iai.css" />

	<script type="text/javascript" src="http://sencha.erb.pl/ext-all.js"></script>
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
				// przekazanie rzeczywistego url do frontend 
				url	: '<?php print (SYS_URL); ?>'
			});
		});
    </script>

</head>
<body>
	<h1>
		IAI - zadanie testowe
	</h1>
	<p>	Wyświetlono okno zawiera drzewo elementów przechowywanych w bazie danych<br/>
		Elementy można dopisywać (nowy element będzie potomkiem elementu zaznaczonego),<br/>
		usuwać i przenosić metodą drag & drop<br/>
	</p>
	<p>	Aplikacja wykonana jest w technologii MVC i składa się z:<ul>
		<li>front end: ExtJs 4.x</li>
		<li>back end: PHP 5.x</li>
		<li>baza danych MySQL 5.x</li>
		</ul>
	</p>
	<p>Aplikacja nie jest adaptatywna.</br/>
	   Testowałem na aktualnych IE, Firefox i Chrome.
	</p>
	<p>	Źródła tego zadania można pobrać z <a href="https://github.com/jazzApis/iai/tree/master" target=_blank>github.com</a>
	</p>
</body>
</html>
