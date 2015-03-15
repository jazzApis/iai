<?php
/**
 * Klasa modelu dokumentu  
 * ======================================================================= 
 * @author	Jarosław Żbikowski 
 * @version $Id$
 * @copyright  (c)2014 by jzbikowski@apis.com.pl
 * @package desktop
 */

include_once (CLASS_PATH.'model.php');
class iaiModel extends modelClass {

	// Domyślna tabela
	protected $table  = 'jz_tree';
	protected $comment = 'Zadanie zaliczeniowe j.zbikowski';
	protected $fields = array (
		'id'	=> array('type' => "int(11)", 'null' =>  0, 'incr' => 1, 'comment' => 'Identyfikator rekordu'),
		'pi'	=> array('type' => "int(11)", 'null' =>  0, 'comment' => 'Wskazanie rodzica (parentId)'),
		'rank'	=> array('type' => "int(11)", 'null' =>  0, 'comment' => 'Ranking'),
		'text'	=> array('type' => "varchar(64)", 'null' =>  0, 'comment' => 'Treść'),
		'cTime'	=> array('type' => "timestamp", 'null' =>  0, 'default' => "CURRENT_TIMESTAMP", 'comment' => 'Czas utworzenia'),
		'mTime'	=> array('type' => "timestamp", 'null' =>  0, 'comment' => 'Czas utworzenia')
	);
	 
	/**
	 * Konstruktor klasy
	 * @param	name string nazwa modułu
	 * @return void
	 */	
	public function __construct ($parent) {
		parent::__construct ($parent);
	}

	/**
	 * Zapisuje rekord do bieżącej db
	 */
	public function validate ($values) {
		if ((int)$values['parentId'])
			$values['pi'] = (int)$values['parentId'];
		return parent::validate ($values);
	}
	
	

}
	
?>
