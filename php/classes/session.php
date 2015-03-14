<?php
/**
 * Bazowa klasa obsługi sesji
 * ======================================================================= 
 * @author	Jarosław Żbikowski 
 * @version $Id$
 * @copyright (c)2014 by jzbikowski@apis.com.pl
 * @package desktop
 */

require_once (CLASS_PATH.'db.php');
 
class sessionClass {
	
	// Parametry wywołania   	
	public $request = array ();
	// Zwracany wynik   	
	public $result = array ();
	// Nazwa modułu (kontrolera)
	public $module = '';
	// Nazwa modułu (kontrolera)
	public $items  = '';
	// Wywołana akcja
	public $action = '';
	// Kontroler
	public $controler = null;
	// systemowa baza danych
	public $db     = null;

	/**
	 * Konstruktor klasy session
	 * parsuje parametry wywołania
	 * @return void
	 */			
	public function __construct () {

		// Domyślnie pozytywny wynik działania  
		$this->setSuccess (true);
		
		$items = array ();
		if (array_key_exists ('HTTP_RAW_POST_DATA', $GLOBALS)) {
			 
			$raws = $GLOBALS['HTTP_RAW_POST_DATA'];
			// Parsowanie dodatkowych parametrów wywołania
			if (substr ($raws,0,1)=='[') 		
				$items = array ('items' => (array)json_decode ($raws,true));
			elseif (substr ($raws,0,1)=='{') 		
				$items = array ('items' => (array)json_decode ('['.$raws.']',true));
			else 		
				parse_str ($raws,$items);
		}

		// Inicjacja podstawowych parametyrów wywołania
		$this->request	= array_merge ($_REQUEST,$items);
		$this->setResult ('request',$this->request);

		// Inicjacja podstawowych parametyrów wywołania
		$this->items	= $this->request['items'];
		$this->module	= $this->request['module'];
		$this->action	= $this->request['action'];
	}

	/**
	 * Ustanowienie połączenia z domyślną bazą danych
	 * @return void
	 * @throws błedy jeśli wystąpią
	 */
	public function dbConnect () {
		$this->db = new DB ($GLOBALS[SYS_CODE]['db']);
	}
	
	/**
	 * Wykonanie kwerendy na domyslnej bazie danych
	 * @param query string kwerenda do wykonania
	 * @param ph array [opcjonalne] parametry (placeholdery) kwerendy
	 * @param start int [opcjonalny] numer pierwszego wiersza 
	 * @param limit int [opcjonalny] limit zwracanych wierszy
	 * @return array recordset
	 */
	protected function select ($query,$ph=null,$start=0,$limit=0) {
		return $this->db->select ($query,$ph,$start,$limit);
	}
	
	/**
	 * Zwraca wynik działania
	 * @return arrat tablica z wynikami  
	 */
	public function getResult () {
		return $this->result;
	}

	/**
	 * Ustawia element wyniku działania 
	 * @param $item(misc)
	 * - string: nazwa pola (wartość w polu $value)
	 * - array: tablica wyników do wstawienia
	 * @param $value(misc) wartość pola do ustawienia
	 * @return void
	 */
	public function setResult ($item, $value='') {
		if (empty ($item))
			return;
		
		if (!is_array ($item)) 
			$item = array ($item => $value);
		
		$this->result = array_merge ($this->result,$item);
	}

	/**
	 * Ustawia zwracany komunikat 
	 * Jeśli komunikat juz istnieje dodawany jest nowy wiersz
	 * @param $text(string) zwracany komunikat
	 * @return string dodany komunikat
	 */
	public function setMessage ($message) {
		if ($message) {
			$msg = $this->result['msg'];
			$this->setResult ('msg', $msg.($msg?"\n":"").$message);
		}
		return $message;
	}

	/**
	 * Ustawia wartość zwracanej zmiennej success
	 * @param $success(bool) nowa wartość zmiennej success
	 * @param $message(string) opcjonalny komunikat
	 * @return (bool) aktualna wartośc success 
	 */
	public function setSuccess ($success=true, $message='') {
		if ($message)
			$this->setMessage ($message);
		$this->setResult ('success', (bool)$success);
		return $success;
	}
	
	/**
	 * Zwraca do przegladarki wynik działania w JSON
	 * @param $result(array) opcjonalny tablica wyników działania
	 * @throws funkcja zawsze kończy (die) działanie PHP 
	 */
	public function printResult ($result=null) {
		echo json_encode ($result?:$this->getResult ());
		die;
	}

	/**
	 * Pobranie kontrolera na podstawie nazxwy modułu
	 * @param module string [opcjonalna] nazwa modułu - domyslnie brana z parametrów wywołania
	 * @return object kontrolera
	 * @throws błedy jeśli kontroler nieprawidłowy (brak, nie istnieje, etc...)
	 */	
	public function getController ($code) {
		
		$name = $code.'Controller'; 
		$file = CONTROLLER_PATH.$code.PHP_EXT;

		if (!$code)
			throw new Exception ('Nieokreślony kontroler');
		
		if (!file_exists ($file)) 
			throw new Exception ('Plik kontolera ['.$file.'] nie istnieje');
		
		include_once ($file);
		return new $name ();		
	}
	
	/**
	 * Pobranie modelu na podstawie nazxwy
	 * @param name string nazwa modułu - domyslnie brana z parametrów wywołania
	 * @return object modelu
	 * @throws błedy jeśli model nieprawidłowy (brak, nie istnieje, etc...)
	 */	
	public function getModel ($code, $ctrl=null) {
			
		$name = $code."Model";
		$file = MODEL_PATH.$code.PHP_EXT;

		if (!$code)
			throw new Exception ('Nieokreślony model');
		
		if (!file_exists ($file)) 
			throw new Exception ('Plik modelu ['.$file.'] nie istnieje');

		include_once ($file);
		return new $name ($ctrl);		
	}
	
	/**
	 * Wykonanie akcji (metody) kontrolera
	 * @param module string [opcjonalna] nazwa modułu - domyslnie brana z parametrów wywołania
	 * @param action string [opcjonalna] nazwa akcji - domyslnie brane z parametrów wywołania
	 * @param params string [opcjonalne] parametry - domyslnie brane z parametrów wywołania
	 * @return void
	 * @throws błedy jeśli kontroler lub akcja nieprawidłowe (brak, nie istnieje, etc...)
	 */	
	public function executeAction ($module=null,$action=null,$request=null) {

		$module  = $module ? : $this->module;
		$action  = $action ? : $this->action;
		$request = $request ? : $this->request;
		
		$controller = $this->getController ($module);

		if (!method_exists ($controller,$action)) {
			throw new Exception ("Nieistniejąca metoda ".$controller->getName ().".".$action." ()");
		}
		
		call_user_func (array ($controller, $action), $this->request);
	}
}

global $session;
try {
	$session = new sessionClass ();
	$session->dbConnect ();
	$session->executeAction ();
} catch (Exception $e) {
	$session->setSuccess (false,$e->getMessage ());
}

$session->printResult ();
?>
