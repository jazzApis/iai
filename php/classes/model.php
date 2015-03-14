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

	protected $table  = '';
	protected $fields = array ();
	
	/**
	 * Konstruktor klasy
	 * @param	name string nazwa modułu
	 * @return void
	 */	
	public function __construct ($parent) {
		parent::__construct ($parent);
	}

	public function getTable () {
		return $this->table;
	}

	public function getSelect ($where) {
		return 'select * from '.$this->getTable ().' where '.$where;
	}	
	/**
	 * Zapisuje rekord do bieżącej db
	 */
	public function validate ($values) {
		$data = array ();
		foreach ($values as $name => $value) {
			$name = strtolower($name);
			if (array_key_exists($name, $this->fields)) {
				$data[$name] = $value;
			}
		}
		$this->setResult('fields',$this->fields);
		$this->setResult('values',$values);
		$this->setResult('data',$data);
		return $data;
	}
	
	
	/**
	 * Zapisuje rekord do bieżącej db
	 */
	public function save ($values) {
		$values = $this->validate ($values);
		return $this->db->save ($this->getTable (), $values, 'id');
	}
	
	public function remove ($id, $cascade='') {
		if ((int)$id)
			return $this->db->delete ($this->getTable (),"id=$id",$cascade);
	}
	
	/**
	 * Standardowy opróżnianie tabeli 
	 */
	public function truncate ($table='') {
		$this->session->query ("truncate table ".$this->getTable ());
	}

}


?>
