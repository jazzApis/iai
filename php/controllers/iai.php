	<?php

/**
 * Klasa kontrolera dokumentów (magazynowych)  
 * ======================================================================= 
 * @author	Jarosław Żbikowski 
 * @version $Id$
 * @copyright  (c)2014 by jzbikowski@apis.com.pl
 * @package desktop
 */

include_once (CLASS_PATH.'controller.php');
class iaiController extends controllerClass {

	protected $models = array (
		'iai'
	);
	
	/**
	 * Zwraca drzewo (tree)
	 */
	public function load ($options) {
		$this->setResultTree ($this->iai->getTable ());
	}

	/**
	 * Zapisuje zmiany do bazy danych
	 */
	public function save ($options) {
		$ids = '';
		foreach ($this->items as $lp => $item) {
			$ids .= ($ids?',':'').$this->iai->save ($item);
		}
		$this->setResultStore ($this->iai->getSelect ('id in ('.$ids.')'),'children');
	}

	/** 
	 * Usuwa obiekt z bazy danych
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
