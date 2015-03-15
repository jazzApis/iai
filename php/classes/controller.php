<?php
/**
 * Bazowa klasa kontrolera 
 * Wszystkie kontrolery muszą po niej dziedziczyć
 * ======================================================================= 
 * @author	Jarosław Żbikowski 
 * @version $Id$
 * @copyright (c)2014 by jzbikowski@apis.com.pl
 * @package desktop
 */
 
include_once (CLASS_PATH.'mvc.php');
class controllerClass extends mvcClass {

	// Lista modeli controlera
	protected $models = array ();
	
	/**
	 * Konstruktor klasy, inicjuje wszystkie modele 
	 * @return void
	 */	
	public function __construct () {
		parent::__construct ();
		
		if ($this->models)
		foreach ($this->models as $model) {
			unset ($this->models[$model]);
			$this->models[$model] = $this->session->getModel ($model,$this);
			$this->$model = $this->models[$model];
		}
	}

	/**
	 * Wykonuje kwerendę i zapisuje wynik 
	 * @param $query(string) kwerenda SQL
	 * @param $name(string) opcjonalna nazwa sekcji (domyślnie items)
	 * @return void
	 */
	protected function setResultStore ($query, $name='') {
		
		if (is_array($query)) {
			$items = $query;
			$total = count ($query);
		} else {
			$items = $this->select ($query);
			$total = $this->getTotal ();
		}
		
		if (!is_array ($items))
			throw new Exception ("Kwerenda nie zwróciła danych");
		elseif ($name)
			$this->setResult ($name, $items);
		else {
			$this->setResult ('items',$items);
			$this->setResult ('total',$total);
		}
	}
	
	/**
	 * Zwraca gałąź drzewa
	 * @param $table string - nazwa tabeli
	 * @param $where string - klauzula where
	 * @param $node string - identyfikator korzenia gałęzi 
	 * @param $id string - pole identyfikatora 
	 * @param $pi string - pole identyfikatora rodzica
	 * @param $cols string - kolumny do wyświetlenia
	 * @param $sort string - porządek     
	 */
	protected function getTreeBranch ($table, $node, $id, $pi, $sort, $cols='*') {

		$node = $node?"($pi=$node)":"($pi=0)";
		$this->setresult ('select',"select $cols from $table where $node order by $sort");
		$rows = $this->select ("select $cols from $table where $node order by $sort");
		$this->setresult ('select',"select $cols from $table where $node order by $sort");
		if (is_array ($rows) && $this->request['load']!='branch') { 
			for ($i=0; $i < count ($rows); $i++) {
				$child = $this->getTreeBranch ($table, $rows[$i][$id], $id, $pi, $sort, $cols);
				if (count ($child)) {
					$rows[$i]['children'] = $child;
				} else { 
					$rows[$i]['leaf'] = 1;
				}
			}
		}
		return $rows;
	}
	
	/**
	 * Zwraca drzewo obiektów
	 * @param $table string - nazwa tabeli
	 * @param $where string - klauzula where
	 * @param $node string - identyfikator korzenia gałęzi 
	 * @param $id string - pole identyfikatora 
	 * @param $pi string - pole identyfikatora rodzica
	 * @param $cols string - kolumny do wyświetlenia
	 * @param $sort string - porządek     
	 */
	public function getTree ($table, $node, $id, $pi, $sort='rank',$cols='*') {
		return $this->getTreeBranch ($table, $node, $id, $pi, $sort, $cols);
	}

	/**
	 * Zwraca drzewo obiektów
	 * @param $table string - nazwa tabeli
	 * @param $where string - klauzula where
	 * @param $cols string - kolumny do wyświetlenia
	 * @param $id string - pole identyfikatora 
	 * @param $pi string - pole identyfikatora rodzica
	 * @param $order string - porządek     
	 */
	public function setResultTree ($table, $where) {
		$id   = $this->request['nodeId']?:'id';
		$pi   = $this->request['parentId']?:'pi';
		$node = $this->request['node']?:0;
		$sort = $this->request['sort']?:'rank';
		
		$children = $this->getTree ($table, $node, $id, $pi, $sort, '*');
		$this->setResult ('children',$children);
		$this->setResult ('total',count ($children));
	}
}


?>
