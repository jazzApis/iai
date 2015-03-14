<?php
/**
 * Definicje stałych aplikacji  
 * ======================================================================= 
 * @author	Jarosław Żbikowski 
 * @version $Id$
 * @copyright (c)2014 by jzbikowski@apis.com.pl
 * @package desktop
 */
 
session_start ();

define ('SYS_CODE'		,"IAI");

define ('DATE_FORMAT'	,"Y-m-d");
define ('TIME_FORMAT'	,"H:i:s");
define ('DATETIME_FORMAT'	,DATE_FORMAT.' '.TIME_FORMAT);

// Foldery i rozszerzenia
define ('PHP_DIR'		,"php/");
define ('MODEL_DIR'		,PHP_DIR."models/");
define ('CLASS_DIR'		,PHP_DIR."classes/");
define ('CONTROLLER_DIR'	,PHP_DIR."controllers/");
define ('IMG_DIR'		,"img/");

define ('PHP_EXT'		,".php");
define ('JS_EXT'		,".js");
define ('IMG_EXT'		,".png");
define ('GIF_EXT'		,".gif");
define ('IMG_MASK'		,"*.{png,gif}");

if (isset ($_SESSION[SYS_CODE])) {
	// Kolejne wywołanie - rzeczywiste url i scieżka do aplikacji są zapisane w zmiennej sesji 
	define ('SYS_PATH'	, $_SESSION[SYS_CODE]['path']);
	define ('SYS_URL' 	, $_SESSION[SYS_CODE]['url']);
} else {
	// Pierwsze wywołanie - ustalanie rzeczywistych url i ścieżki do aplikacji 
	$self = strtolower ('http://'.pathinfo ($_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"],PATHINFO_DIRNAME).'/');
	define ('SYS_URL' 	, substr ($self,0,strrpos ($self,'php')?:strlen ($self)));

	$path = strtolower (realpath (dirname ($_SERVER["SCRIPT_FILENAME"])).'/');
	define ('SYS_PATH'	, substr ($path,0,strrpos ($path,'php')?:strlen ($path)));

	// Zapis podstawowych cech sesji 
	$_SESSION[SYS_CODE] = array(
		'id'	=> session_id (),
		'name'	=> session_name (),
		'path'	=> SYS_PATH,
		'url'	=> SYS_URL
	);
}

// Pełne ścieżki i url'e  
define ('PHP_PATH'			,SYS_PATH.PHP_DIR);
define ('CLASS_PATH'		,SYS_PATH.CLASS_DIR);
define ('MODEL_PATH'		,SYS_PATH.MODEL_DIR);
define ('CONTROLLER_PATH'	,SYS_PATH.CONTROLLER_DIR);
define ('IMG_PATH'   		,SYS_PATH.IMG_DIR);

define ('PHP_URL' 			,SYS_URL.PHP_DIR);
define ('IMG_URL'			,SYS_URL.IMG_DIR);

// Załadowanie pozostałych zmiennych
require_once (PHP_PATH.'settings.php');

?>
