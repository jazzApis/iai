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
	protected $fields = array (
		'id'	=> "int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identyfikator rekordu'",
		'pi'	=> "int(11) NOT NULL DEFAULT '0' COMMENT 'Wskazanie rodzica (parentId)'",
		'rank'	=> "int(11) NOT NULL DEFAULT '0' COMMENT 'Ranking'",
		'text'	=> "varchar(64) COLLATE utf8_polish_ci NOT NULL COMMENT 'Treść'",
		'cTime'	=> "timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Czas utworzenia'",
		'mTime'	=> "timestamp NULL DEFAULT NULL COMMENT 'Czas ostatniej aktualizacji'"
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
