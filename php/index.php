<?php
/**
 * Główny plik wywołujący metody backend 
 * ======================================================================= 
 * @author	Jarosław Żbikowski 
 * @version $Id$
 * @copyright (c)2014 by jzbikowski@apis.com.pl
 * @package desktop
 */

/* poziom raportowania, http://pl.php.net/manual/pl/function.error-reporting.php
error_reporting (E_ALL); 
//*/error_reporting (E_ERROR); 

set_time_limit (30);

ini_set ("display_errors","On");

include_once ('config.php');
include_once (CLASS_PATH.'session.php');

?>
