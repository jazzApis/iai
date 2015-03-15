<?php
/**
 * Klasa kontrolera drzewa IAI  
 * ======================================================================= 
 * @author	Jarosław Żbikowski 
 * @version $Id$
 * @copyright  (c)2014 by jzbikowski@apis.com.pl
 * @package desktop
 */

include_once (CLASS_PATH.'controller.php');
class iaiController extends controllerClass {

	// Tylko jeden model
	protected $models = array (
		'iai'
	);
	
	/**
	 * Sprawdza czy istnieje tabela modelu.
	 * Jeśli jej nie ma próbuje ją utworzyć 
	 * @param $options(array) parametry wywołania
	 */
	public function check ($options) {
		$table = $this->iai->tableExists ();
		if (!$table) {
			$this->setResult ('ddl',$this->iai->getCreateTable ());
			$this->setSuccess (false, 'Tabela '.$table.' nie istnieje i nie mogę jej utworzyć');
		}
	}

	/**
	 * Zwraca (w resultset) drzewo elementów modelu IAI
	 * @param $options(array) parametry wywołania
	 */
	public function tree ($options) {
		$this->setResultTree ($this->iai->getTable ());
	}

	/**
	 * Zwraca (w resultset) tabelę elementów modelu IAI
	 * @param $options(array) parametry wywołania
	 */
	public function store ($options) {
		$this->setResultStore ($this->iai->getSelect ());
	}

	/**
	 * Zapisuje zmienione lub nowe rekordy do modelu IAI
	 * Zwraca (w resultsecie) tabelę z zapisanymi rekordami
	 * @param $options(array) parametry wywołania
	 */
	public function save ($options) {
		$ids = '';
		foreach ($this->items as $lp => $item) {
			$ids .= ($ids?',':'').$this->iai->save ($item);
		}
		$this->setResultStore ($this->iai->getSelect ('id in ('.$ids.')'),'children');
	}

	/**
	 * Usuwa rekordy z  modelu IAI
	 * Zwraca (w resultsecie) liste usuniętych identyfikatorów
	 * @param $options(array) parametry wywołania
	 */
	public function remove ($options) {
		$ids = '';
		foreach ($this->items as $lp => $item) {
			$id = (int)$item['id'];
			if ($this->iai->remove ($id)) {
				$ids .= ','.$id;
			}
		}
		$this->setResult ('deleted',$ids);
	}
}

?>
