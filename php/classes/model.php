<?php
/**
 * Bazowa klasa modelu 
 * Wszystkie modele muszą bezpośrednio po niej dziedziczyć
 * ======================================================================= 
 * @author	Jarosław Żbikowski 
 * @version $Id$
 * @copyright  (c)2014 by jzbikowski@apis.com.pl
 * @package desktop
 * załącznik
 */
 
include_once (CLASS_PATH.'mvc.php');
class modelClass extends mvcClass {

	// Nazwa tabeli
	protected $table = '';
	// Komentarz (opis) tabeli
	protected $comment = '';
	// Lista pól tabeli
	protected $fields = array ();
	
	/**
	 * Konstruktor klasy
	 * @param	name string nazwa modułu
	 * @return void
	 */	
	public function __construct ($parent) {
		parent::__construct ($parent);
	}

	/**
	 * Zwraca nazwę (domyślnej) tabeli dla modelu
	 * @return string - nazwa tabeli
	 */
	public function getTable () {
		return $this->table;
	}

	/**
	 * Zwraca kwerendę SQL tworzącą domyślna tabele modelu
	 * @param $where(string) klauzula where 
	 */
	public function getCreateTable () {
		$ddl = '';
		foreach ($this->fields as $name => $pars) { 
			$ddl .= "\n".($ddl ? "," : "(").$name."\t".$pars['type'];
			$ddl .= ($pars['null'] ? "" : " not")." null"; 
			$ddl .= ($pars['incr'] ? " auto_increment": ""); 
			$ddl .= ($pars['default'] ? " default ".$pars['default'] : ""); 
			$ddl .= ($pars['comment'] ? " comment '".$pars['comment']."'" : ""); 
		}
		$ddl.= "\n,primary key (id)";
		$ddl.= "\n,unique key id_unique (id)";
		return "create table ".$this->getTable ().$ddl
			."\n) comment '".$this->comment."'";
	}	
	
	/**
	 * Tworzy tabele modelu
	 * @param $where(string) klauzula where 
	 */
	public function tableExists () {
		
		$select = "show tables like '".$this->getTable ()."'";
		$create = $this->getCreateTable ();
		
		$result = $this->selectValue ($select);
		if (!$result) {
			$this->db->query ($this->getCreateTable ());
			$result = $this->selectValue ($select);
		}
		
		return $result;
	}	
	
	/**
	 * Zwraca kwerendę SQL na bieżacej tabeli
	 * @param $where(string) klauzula where 
	 */
	public function getSelect ($where='') {
		return 'select * from '.$this->getTable ().($where ? ' where '.$where : '');
	}	
	
	/**
	 * Podstawowa kontrola danych przed zapisem do bazy danych
	 * Sprawdzane jest tylko występowanie pola w tabeli
	 * @param $values(array) tablica wartości pól
	 * @return array tablica dopuszczalnych wartości pól 
	 */
	public function validate ($values) {
		$data = array ();
		foreach ($values as $name => $value) {
			$name = strtolower($name);
			if (array_key_exists($name, $this->fields)) {
				$data[$name] = $value;
			}
		}
		return $data;
	}

	/**
	 * Zapisuje rekord do domyślnej tabeli modelu
	 * @param $values(array) tablica wartości pól
	 * @return int identyfikator zapisanego rekordu 
	 */
	public function save ($values) {
		$values = $this->validate ($values);
		return $this->db->save ($this->getTable (), $values, 'id');
	}
	
	/**
	 * Usuwa rekord z domyślnej tabeli modelu
	 * @param $id(int) identyfikator rekordu do usunięcia
	 * @param $cascade(string) opcjonalny identyfikator rodzica (do kasowania kaskadowego) 
	 * @return string lista identyfikatorów usuniętych rekordów 
	 */
	public function remove ($id, $cascade='') {
		if ((int)$id)
			return $this->db->delete ($this->getTable (),"id=$id",$cascade);
	}
	
	/**
	 * Opróżnia domyślną tabelę modelu 
	 */
	public function truncate () {
		$this->session->query ("truncate table ".$this->getTable ());
	}

}


?>
