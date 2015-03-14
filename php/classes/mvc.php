<?php
/**
 * Bazowa klasa dla klas MVC
 * Wszystkie kontrolery i modele muszą bezpośrednio po niej dziedziczyć
 * ======================================================================= 
 * @author Jarosław Żbikowski 
 * @version $Id$
 * @copyright (c)2014 by jzbikowski@apis.com.pl
 * @package desktop
 */

class mvcClass {

	// Nazwa modułu
	protected $name;
	// Domyślna tabela
	protected $table='';

	/**
	 * Konstruktor klasy
	 * @param name string nazwa modułu
	 * @param role role wymagane dla kontrolera
	 * @param unauth akcje dozwolone bez autoryzacji
	 * @return void
	 */	
	public function __construct ($parent=null) {
		global $session;
		$this->name	= str_replace (array ('Model','Controller'),'',get_class ($this));
		$this->session	= $session;
		$this->db		= $this->session->db;
		$this->request	= $this->session->request;
		$this->items	= $this->session->items;
	}

	/**
	 * Say hello - do testów
	 */
	public function hello ($options) {
		$this->setResult ('name',$this->getName ());
		$this->setResult ('options',$this->request);
	}

	/**
	 * Nazwa modułu
	 * @return string nazwa modułu
	 */	
	public function getName () {
		return $this->name;
	}	
	
	/**
	 * Pobranie elementu z wyniku na podstawie ścieżki
	 * @param path string - ścieżka do elementu
	 * @return misc - znaleziony element
	 */	
	public function getResult ($path) {
		return $this->session->getResult ($path);
	}
	
	/**
	 * Ustawia wynik działania kontrolera
	 * @param $name misc
	 * * string - nazwa ustawianej zmiennej
	 * * array - tabela ustawianych zmiennych (nazwa => wartość)  
	 * @param $value misc - ustawiana wartość jeśli name=string, w pozostałych wypadkach ignorowane 
	 */
	public function setResult ($name, $value=null) {
		$this->session->setResult ($name, $value);
	}

	/**
	 * Ustawia zwracany komunikat
	 * @param $message string lub tabela - zwracany komunikat 
	 * @return array - tabela z komunikatem
	 */
	public function setMessage ($message) {
		return $this->session->setMessage ($message);
	}

	/**
	 * Ustawia zmienna success operacji
	 * @param $success boolean - success operacji  
	 * @param $message misc - opcjonalny komunikat do ustawienia 
	 * @return boolean - ustawiona wartośc success
	 */
	public function setSuccess ($value=true, $message='') {
		if ($message) $this->setMessage ($message);
		return $this->session->setSuccess ($value);
	}

	/**
	 * Pobranie obiektu kontrolera 
	 * @param name string [opcjonalna] nazwa kontrolera
	 * @return string obiekt kontrolera o podanej nazwie lub bieżacy kontroler
	 */
	public function getController ($name=null) {
		return $this->session->getController ($name);
	}
	
	/**
	 * Pobranie obiektu modelu 
	 * @param name string [opcjonalna] nazwa modelu
	 * @return string obiekt modelu o podanej nazwie lub bieżacy model
	 */
	public function getModel ($name) {
		return $this->session->getModel ($name,$this); 
	}
	
	/**
	 * Wywołanie metody kontrolera na podstawie nazwy
	 * @param controller string nazwa kontrolera
	 * @param method string nazwa metody
	 * @param options array [opcjonalne] parametry wywołania
	 * @return misc zwraca wynik działania metodyakcji
	 */
	public function executeAction ($controller=null,$method=null,$options=null) {
		return $this->session->executeAction ($controller,$method,$options);
	}
	
	/**
	 * Wykonuje kwerendę na domyślnej bazie danych i zwraca jej wynik 
	 * @param $query(strin) kwerenda
	 * @param $ph(array) placeholdery
	 * @param $start(int) numer pierwszego wiersza
	 * @param $limit(int) ilośc wierszy
	 */
	protected function select ($query,$ph=null,$start=0,$limit=0) {
		$start = (int)$start?: (int)$this->request['start'];
		$limit = (int)$limit?: (int)$this->request['limit'];
		return $this->session->db->select ($query,$ph,$start,$limit);
	}
	
	/**
	 * Wykonuje kwerendę i zwraca jeden określony wiersz 
	 * @param $query(string) treść kwerendy
	 * @param $ph(array) tablica placeholderów
	 * @param $row(int) numer wiersza do zwrócenia (domyśłnie 0)
	 * @return array wybrany wiersz 
	 */
	protected function selectRow ($query,$ph=null,$start=0) {
		$start = (int)$start?: (int)$this->request['start'];
		return $this->db->selectRow ($query,$ph,$start);
	}

	/**
	 * Wykonuje kwerendę i zwraca tabele wartośc pierwszej kolumny ()bez nagłówka) 
	 * @param $query(string) treść kwerendy
	 * @param $ph(array) tablica placeholderów
	 * @return array wartości wybranej kolumny 
	 */
	protected function selectCol ($query,$ph=null) {
		return $this->db->selectCol ($query,$ph);
	}

	/**
	 * Wykonuje kwerendę i zwraca wartość pierwszej komórki określonego wiersza 
	 * @param $query(string) treść kwerendy
	 * @param $ph(array) tablica placeholderów
	 * @param $row(int) numer wiersza do zwrócenia (domyśłnie 0)
	 * @return misc wartośc komórki 
	 */
	protected function selectValue ($query,$ph=null,$row=0) {
		return $this->db->selectValue ($query,$ph,$row);
	}

	/**
	 * Zwraca całkowitą ilość rekordów do pobrania 
	 * @return int ilośc rekordów 
	 */
	protected function getTotal () {
		return $this->session->db->getTotal ();
	}
}


?>
