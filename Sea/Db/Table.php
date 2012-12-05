<?php

require_once ('Zend/Db/Table.php');
require_once ('Zend/Db/Expr.php');

/**
 * 
 * 
 * @author jhouvion
 *
 */
class Sea_Db_Table extends Zend_Db_Table {
	
	/**
	 * Format (Zend_date) des date a inserer en base de donnée
	 * 
	 * @var unknown_type
	 */
	protected $_dbDateFormat = 'YYYY-MM-dd';
	
	/**
	 * format (Zend_date) des date affiché dans l'application
	 */
	protected $_appDateFormat = 'dd/MM/YYYY';
	
	/**
	 * surcharge du fetch
	 */
	protected function _fetch($select) {
		
		// on ajoute la gestion de la table courant
		$select->from($this);
		
		foreach ($this->info(Zend_Db_Table::METADATA) as $field => $data) {
			switch (preg_replace('/\(.*/', '', $data['DATA_TYPE'])) {
				case 'date': $select->columns(array($field => 'DATE_FORMAT(' . $field . ', "%d/%m/%Y")'));break;
				case 'datetime' : $select->columns(array('format_' . $field => 'DATE_FORMAT(' . $field . ',  "%d/%m/%Y %H:%i")'));break;
			}
		}
		
		return parent::_fetch($select);	
	}
	
	/**
	 * Renvoie les references (objet mère) de l'objet
	 * 
	 */
	public function getReferences() {
		return $this->_getReferenceMapNormalized();
	}
	
	/**
	 * renvoie les donnée associé a une entrée
	 * 
	 * @param unknown_type $row
	 */
	public function assoc($row) {
		return array_get_assoc(array_keys($this->createRow()->toArray()), $row);// attribution des valeurs du formulaire a la ligne vide
	}
	
	/**
	 * Traitement pre ecriture sur les champs
	 * 
	 */
	protected function _format($row) {
		
		foreach ($this->info(Zend_Db_Table::METADATA) as $field => $data) {
			
			// on zapp le traiteemnt si l'entré n'a pas le champs spécifié
			if (!array_key_exists($field, $row)) continue;
			
			switch (preg_replace('/\(.*/', '', $data['DATA_TYPE'])) {
				// formatage des dates
				case 'date' :
					if (Zend_Date::isDate($row[$field], $this->getAppDateFormat())) {
						$date = new Zend_Date($row[$field], $this->getAppDateFormat());
						$row[$field] = $date->toString($this->getDbDateFormat());
						
					// si la date n'est pas valide, on la remplace par NULL
					} else {$row[$field] = new Zend_Db_Expr('NULL');}
					break;
				case 'enum' :
					
					// récupration des valeurs
					$matches = array();

					if (preg_match_all("/[\(|,]'((.(?!(?<=')[,|\)]))+)/", $data['DATA_TYPE'], $matches)) {$type = array_combine($matches[1],$matches[1]);}
					
					// formatage des enum
					foreach($type as &$t) {$t = str_replace("''", "'", $t);}
					
					if (!empty($row[$field]) && !in_array($row[$field], $type)) {throw new Zend_Exception('Erreur sur la variable : ' . $field );}
					break;
			}
		}
		return $row;
	}
	
	/**
	 * insertion mulatiple dans la base
	 * 
	 * @param unknown_type $data
	 */
	public function insertMulti($data) {
		
		// s'il y a des insertion
		if (count($data) < 1) { throw new Zend_Exception("Erreur lors de l'insertion, aucune donnée présente");}
			
		$insert = array();// initilisation des information a inserer
		$fields = array_keys(current($data));// récurepartion du nom des champs
		
		foreach ($data as $row) {
			// on verifie que tout les enregistrement on la même structure
			if (sort(array_keys($row)) !== sort($a = $fields)) {throw new Zend_Exception('Probleme lors de l\'insertion multiple : une des entrées ne contient pas les bon champs');}
			
			// on formate les données
			array_walk($row, function (&$val){$val = getconnection()->quote($val);});
			
			$insert[] = '(' . implode(',', $row) . ')';// inscription d'une ligne d'insertion
		}

		// construction de la requete
		$insert = 'INSERT INTO '.$this->info(Zend_Db_Table::NAME ) . '('.implode(',', $fields).') VALUES ' . implode(',', $insert);
		$this->getAdapter()->query($insert);// On lance la requete
	}
	
	/**
	 * surcharge de l'update (non-PHPdoc)
	 * @see Zend_Db_Table_Abstract::update()
	 */
	public function update($data, $where) {
		return parent::update($this->_format($data), $where);
	}
	
	/**
	 * Surcharge de l'insert (non-PHPdoc)
	 * @see Zend_Db_Table_Abstract::insert()
	 */
	public function insert($data) {
		return parent::insert($this->_format($data));
	}
	
	/**
	 * @return the $_dbDateFormat
	 */
	public function getDbDateFormat() {
		return $this->_dbDateFormat;
	}

	/**
	 * @return the $_appDateFormat
	 */
	public function getAppDateFormat() {
		return $this->_appDateFormat;
	}

	/**
	 * @param unknown_type $_dbDateFormat
	 */
	public function setDbDateFormat($_dbDateFormat) {
		$this->_dbDateFormat = $_dbDateFormat;
		return $this;
	}

	/**
	 * @param field_type $_appDateFormat
	 */
	public function setAppDateFormat($_appDateFormat) {
		$this->_appDateFormat = $_appDateFormat;
		return $this;
	}
	
	/**
	 * renvoie le nom de la table
	 */
	public function getName(){
		return $this->_name;
	}
}
