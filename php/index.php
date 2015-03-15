<?php
/**
 * Główny plik wywołujący metody backend 
 * ======================================================================= 
 * @author	Jarosław Żbikowski 
 * @version $Id$
 * @copyright (c)2014 by jzbikowski@apis.com.pl
 * @package desktop
 */

/* poziom raportowania błedów
error_reporting (E_ALL); 
//*/error_reporting (E_ERROR); 

set_time_limit (30);

ini_set ("display_errors","On");

require_once ('config.php');
require_once (CLASS_PATH.'session.php');

?>
