<?php
/**
 * Prosta klasa obsługująca bazę danych MySQL  
 * ======================================================================= 
 * @author	Jarosław Żbikowski 
 * @version $Id$
 * @copyright (c)2014 by jzbikowski@apis.com.pl
 * @package desktop
 */

class DB {
	
	//parametry bazy danych
	private $type;	// typ serwera
	private $host;	// adres serwera
	private $name;	// nazwa bazy danych
	private $user;	// login
	private $connection = null;
		
	/**
	 * Konstruktor klasy DB
	 * Zapisuje parametry i autpo,matycznie łaczy z bazą danych MySQL
	 * @param $db(array) tabela zawierająca parametry bazy danych:
	 * - host: adres serwera bazy danych 
	 * - name: nazwa bazy danych 
	 * - user: Użytkownik (login)  
	 * - pass: hasło użytkownika
	 * @return void
	 * @throws komunikat jeśli nie udało się nawiązac połaczenia z bazą danych
	 */			
	public function __construct ($db) {
		
		$this->host	= $db['host'];
		$this->name	= $db['name'];
		$this->user	= $db['user'];
		$this->pass	= $db['pass'];
		
		$this->connect ();
	}
	
	/**
	 * Łączenie z bazą danych MySQL zgodnie z parametrami
	 * Nic nie robi jeśli połaczenie już jest aktywne 
	 * @return object: połączenie z bazą danych
	 * @throws komunikat jeśli nie udało się nawiązac połaczenia z bazą danych
	 */
	public function connect () {
		
		if ($this->connection) 
			return $this->connection;

		if (!$this->host || !$this->user)
			throw new Exception ("Nieokreślone parametry bazy danych.");
					
		$this->connection = mysql_connect ($this->host, $this->user, $this->pass); 
		if (!$this->connection) 
			throw new Exception ("Brak łączności z bazą danych {$this->name} na {$this->host}");
		
		mysql_select_db ($this->name); 
		mysql_set_charset ("utf8");
		mysql_query ("SET max_sp_recursion_depth=16");
		mysql_query ("SET NAMES 'utf8'");
		
		return $this->connection;
	}
	
	/**
	 * Substytucja placeholderów w treści kwerendy
	 * @param $ph(array) tablica placeholderów (name => value)
	 * @param $text(string) tekst zawierający placeholdery
	 * @param $strict(bool) substytucja tylko zdefiniowanych 
	 * @param $bounds(string) separatory (krawędzie) placeholderów 
	 * @return string - tekst wynikowy
	 */
	function placeholders ($ph, $text, $strict=false, $bound='{}') {
		
		$b1 = substr($bound, 0, 1);
		$b2 = substr($bound,   -1);
		$s  = '\\';
		$s1 = $s.$b1;
		$s2 = $s.$b2;
		$preg = '/'.$s1.'([^'.$s1.$s2.']+)'.$s2.'/';
		if (!preg_match ($preg, $text)) {
			// NOP - brak placeholderów do zamiany
		} elseif ($strict) {
			// Zamiana tylko zdefiniowanych 
			foreach ($ph as $name => $value) 
				$text = str_replace ($b1.$name.$b2,$value,$text);
		} else {
			$call = function ($matches) use ($ph) { 
				return $ph[$matches[1]]; 
			}; 	
		
			while (preg_match ($preg, $text)) {
				$text = preg_replace_callback ($preg, $call, $text);
			}
		}
		
		return $text;
	} 

	
	
	/**
	 * Wykonuje kwerendę i zwraca resultset
	 * @param $query(string) treśc kwerendy do wykonania
	 * @param $ph(array) opcjonalna tabela placeholderów
	 * @param $start(int) opcjonalny numer pierwszego zwracanego wiersza
	 * @param $limit(int) opcjonalna ilośc zwracanych wierszy
	 * @return object resultset kwerendy
	 * @throws komunikat, jeśli brak połaczenia lub błędy w kwerendzie
	 */
	public function query ($query, $ph=array (), $start=0, $limit=0) {

		if (!$this->connect ())
			throw new Exception("Brak aktywnego połączenia z bazą danych");

		$query = $this->placeholders ($ph, $query, true);
		
		$this->total = 0;
		if ($limit > 1) {
			// Jeśli ustawiono limit konieczne jest wstępne obliczenie całkowitej ilości wierszy 
			$rs = $this->fnQuery ("select COUNT(*) from ($query) x", $this->connection);
			$this->total = (int)$this->getResult ($rs);
			$query .= " LIMIT $start,$limit";
		}
		
		// Własciwe wywołanie kwerendy
		$rs = mysql_query ($query, $this->connection);
		
		$this->total  = $this->total?:$this->rowCount ($rs);
		return $rs;
	}

	/**
	 * Zwraca ilość wierszy zwróconych / dotknietych
	 * @param $rs(object) resultset do zbadania
	 * @return int - ilośc wierszy
	 */ 
	public function rowCount ($rs) {
		
		return is_bool ($rs) ? mysql_affected_rows ($rs) : mysql_num_rows ($rs);
	}
		
	/**
	 * Zwraca ilość kolumn tabeli
	 * @param $rs(object) resultset do zbadania
	 * @return int - ilośc kolumn
	 */ 
	public function colCount ($rs) {
		
		return is_bool ($rs) ? 0 : mysql_num_fields($rs);
	}
	
	/**
	 * Zwraca identyfikator ostatnio dopisanego rekordu
	 * @param $id(int) opcjonalny identyfikator
	 * @return int identyfikator
	 */	
	public function getLastId ($id=0) {
		
		return $id ?: mysql_insert_id ();
	}	
	
	/**
	 * Zwraca liste kolumn tabeli
	 * @param $rs(object) resultset do zbadania
	 * @return array tablica z listą nazw kolumn
	 */ 
	private function getFields ($rs) {
		
		$cols = $this->colCount ($rs);
		$head = array ();
		
		for ($i=0; $i<$cols; $i++) {
			$head[] = mysql_field_name ($rs,$i);
		}
		
		return $head;
	}
	
	/**
	 * Zwraca wartość określonego pola z określonego wiersza
	 * @param $rs(object) resultset do zbadania
	 * @param $row(int) opcjonalny numer wiersza (domyśłnie 0)
	 * @param $col(int) opcjonalny numer kolumny (domyśłnie 0)
	 * @return misc wartość pola
	 */
	private function getResult ($rs, $row=0, $col=0) {
		
		return mysql_result ($rs,$row,$col);
	}

	/**
	 * Zwraca bieżacy wiersz 
	 * @param $rs(object) resultset do zbadania
	 * @return array tablica asocjacyjna (name => value) bieżacego wiersza
	 */
	private function getRecord ($rs) {
		
		$data = mysql_fetch_array ($rs);
		$ret = array ();
		
		foreach ($this->getFields ($rs) as $field) {
			$ret[strtolower ($field)] = trim ($data[$field]);
		}
		
		return $ret;
	}
	
	/**
	 * Zwraca całkowita ilośc wierszy ostatniej kwerendy
	 * @return int całkowita ilośc wierszy 
	 */	
	public function getTotal () {
		
		return $this->total;
	}
	
	/**
	 * Wykonuje kwerendę i zwraca tablice wszystkich wartosci
	 * @param $query(string) treść kwerendy
	 * @param $ph(array) tablica placeholderów
	 * @param $start(int) numer pierwszego wiersza
	 * @param $limit(int) ilość wierszy 
	 * @return array tablica wartościgotowa do 
	 */
	public function select ($query, $ph=null, $start=0, $limit=0) {
		
		$rs = $this->query ($query, $ph, $start, $limit);
		$ar = array ();
		
		for  ($i=0; $i < $this->rowCount ($rs); $i++) {
			$ar[] = $this->getRecord ($rs);
		}

		return $ar;
	}

	/**
	 * Wykonuje kwerendę i zwraca jeden określony wiersz 
	 * @param $query(string) treść kwerendy
	 * @param $ph(array) tablica placeholderów
	 * @param $row(int) numer wiersza do zwrócenia (domyśłnie 0)
	 * @return array wybrany wiersz 
	 */
	public function selectRow ($query, $ph=null, $row=0) {
		
		$rs = $this->query ($query, $ph, $row, 1);
		$ar = $this->getRecord ($rs);
		return $ar;
	}

	/**
	 * Wykonuje kwerendę i zwraca tabele wartośc pierwszej kolumny ()bez nagłówka) 
	 * @param $query(string) treść kwerendy
	 * @param $ph(array) tablica placeholderów
	 * @return array wartości wybranej kolumny 
	 */
	public function selectCol ($query, $ph=null) {
		
		$rs = $this->query ($query, $ph);
		$ar = array ();
		
		for  ($i=0; $i < $this->rowCount ($rs); $i++) {
			$ar[] = reset ($this->getRecord ($rs));
		}
		
		return $ar;
	}
	
	/**
	 * Wykonuje kwerendę i zwraca wartość pierwszej komórki określonego wiersza 
	 * @param $query(string) treść kwerendy
	 * @param $ph(array) tablica placeholderów
	 * @param $row(int) numer wiersza do zwrócenia (domyśłnie 0)
	 * @return misc wartośc komórki 
	 */
	public function selectValue ($query, $ph=null, $row=0) {
		
		$row = $this->selectRow ($query, $ph, $row);
		return reset ($row);
	}
	
	
	
	/**
	 * Usuwanie  (DELETE) rekordy ze tabeli
	 * @param $table(string) nazwa tabeli
	 * @param $where(string) warunki wierszy do skasowania (musi wystąpić)
	 * @param $cascade string [opcjonalna] nazwa pola wskazującego potomków do kasowania kaskadowego
	 * @return array tablica z identyfikatorami skasowanych rekordów
	 * @throws błedy jeśli wystąpią
	 */
	public function delete ($table, $where, $cascade='') {

		if (!$where) 
			throw new Exception("Nieokreślony warunek kasowania danych");
		
		$sis = array (); 
		$ids = $this->selectCol ("select id from $table where $where");
		if ($ids) {
			// kaskadowe kasowanie potomków		
			if ($cascade) {
				foreach ($ids as $id) {
					$sis = array_merge ($sis, $this->delete ($table, "$cascade=$id", $cascade));
				} 
			}
			
			$this->query ("delete from $table where $where");
		}
		
		return array_merge ($ids, $sis);
	}

	/**
	 * Wstawianie  (INSERT) rekordu do tabeli
	 * @param table string - nazwa tabeli do zaktualizowania
	 * @param data array - tabela wartości do zapisania
	 * @param key string - nazwa identyfikatora  (PK) rekordu w tabeli  (id)
	 * @return int - identyfikator rekordu
	 * @throws błedy jeśli wystąpią
	 */
	public function insert ($table, $data, $key='id') {

		$update = 'mtime=CURRENT_TIMESTAMP';
		$fields = '';
		$values = '';

		if (!(int)$data[$key]) {
			unset ($data[$key]);
		}
				
		foreach ($data as $field => $value) {
			
			if (!$field || !isset ($value)) continue;
			if (is_array($value)) $value = implode(',',$value);
			$value = "'".mysql_real_escape_string ($value)."'";
			
			$update .= ($update?',':'')."$field=$value";
			$fields .= ($fields?',':'').$field;
			$values .= ($values?',':'').$value;
		}
		
		$query = "insert into $table ($fields) values ($values)";
		$query .= "\non duplicate key update $update";
		$this->query ($query);
		
		return $this->getLastId ((int)$data[$key]);	
	}

	/**
	 * Aktualizacja  (UPDATE) rekordu w tabeli
	 * @param table string - nazwa tabeli do zaktualizowania
	 * @param data array - tabela wartości do zapisania
	 * @param key string - nazwa identyfikatora  (PK) rekordu w tabeli  (id)
	 * @return misc - ;lista zaktualizowanych rekordów
	 * @throws błedy jeśli wystąpią
	 */
	public function update ($table, $data, $key='id') {
		
		$update = $noSys?"":'mtime=CURRENT_TIMESTAMP';
		
		$id = $data[$key];
		
		if (is_array ($id))
			$id = implode (',',$id);
		
		if (!$id) 
			throw new Exception("Nieokreślony identyfikator rekord(ów) do aktualizacji");
		
		foreach ($data as $field => $value) {
			
			if (!$field || !isset ($value) || $field==$key) continue;
			
			if (is_array($value)) $value = implode(',',$value);
			
			$value = mysql_real_escape_string ($value);
			
			$update .=  ($update?',':'')."$field='$value'";
		}
		
		$this->query ("update $table set $update where $key in ($id)");
		return $id;	
	}

	/**
	 * Zapis rekordu do bazy danych 
	 * Jeśli pole kluczowe (id) wieksze od zera aktualizowany jest wskazany rekord.
	 * Jeśli pole kluczowe (id) mniejsze od zera wskazany rekord (abs) jest usuwany.
	 * W pozostałych przypadkach dopisywany jest nowy rekord
	 * @param table string - nazwa tabeli do zaktualizowania
	 * @param data array - tabela wartości do zapisania
	 * @param key string - nazwa identyfikatora  (PK) rekordu w tabeli  (id)
	 * @return int - identyfikator rekordu
	 * @throws błedy jeśli wystąpią
	 */
	public function save ($table, $data, $key='id') {
		
		if (!$key || !isset ($data[$key]))
			throw new Exception ("Próba zapisu rekordu bez identyfikatora ($table:$key:$id)");
			
		$id = (int)$data[$key];
		
		if ($id<0) 
			return $this->delete ($table, -$id,$key);
		elseif ($id==0) 
			return $this->insert ($table,$data,$key);
		else 
			return $this->update ($table,$data,$key);
	}
} 

?>
