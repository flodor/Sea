<?php
/**
 * classe de liaisons entre model et controller
 * 
 * @author jhouvion
 *
 */
class Sea_Mapper {
	
	/**
	 * table associé au mapper
	 * 
	 * @var unknown_type
	 */
	protected $_primary = '';
	
	/**
	 * Defini si les dependances et reference seront chargé
	 * 
	 * @var Boolean
	 */
	protected $_link;
	
	/**
	 * contient les elements Table
	 * 
	 * @var Sea_Db_Table
	 */
	protected $_table;
	
	/**
	 * 
	 * contient les information d'un entrée en cache
	 */
	protected $_cache = null;
	
	/**
	 * Constructeur
	 * 
	 */
	public function __construct($primary = null, $link = true) {
		
		if (!is_null($primary)) {$this->_primary = $primary;}
		$this->_link = $link;
		
		// construction de la table principale
		$this->_table = new $this->_primary;
		
		$this->init();
	}
	
	/**
	 * charge en memmoire une entré
	 * 
	 */
	public function load() {
		
		if (is_null($this->_cache)) {
			$data = call_user_func_array([$this, 'find'], func_get_args());
			$this->_cache = $data;
		}
		
		return $this->_cache;
	}
	
	/**
	 * recharge une entrée
	 */
	public function reload() {
		$this->clean();
		return call_user_func_array([$this, 'load'], func_get_args());
	}
	
	/**
	 * vide le cache
	 * 
	 */
	public function clean() {
		$this->_cache = null;
		return $this;
	}
	
	/**
	 * renvoie l'entrée en cache
	 * 
	 * @return NULL
	 */
	public function getCache() {return $this->_cache;}
	
	
	/**
	 * constrcution de la clause select
	 * 
	 */
	public function select() {
		return $this->getTable()->getAdapter()->select()->from($this->getTable()->getName());
	}
	
	/**
	 * renvoie le jeux de donnée par le select()
	 * 
	 * @param unknown_type $columns
	 * @param unknown_type $where
	 * @param unknown_type $order
	 */
	public function fetchAll( $columns = null, $where = null, $order = null) {
		
		$select = $this->select();
		
		// traitement des specificité
		if (!is_null($columns)) {$select->columns($columns);}
		if (!is_null($where)) {$select->where($where);}
		if (!is_null($order)) {$select->order($order);}
		
		$db = Zend_Db_Table::getDefaultAdapter();
		return $db->fetchAll($select);
	}
	
	
	/**
	 * renvoie l'enregistrement par le select()
	 * 
	 * @param unknown_type $columns
	 * @param unknown_type $where
	 * @param unknown_type $order
	 */
	public function fetchRow( $columns = null, $where = null, $order = null) {
		
		$select = $this->select();
		
		// traiatement des specificité
		if (!is_null($columns)) {$select->columns($columns);}
		if (!is_null($where)) {foreach ((array) $where as $w) {$select->where($w);}}
		if (!is_null($order)) {$select->order($order);}
		
		$db = Zend_Db_Table::getDefaultAdapter();
		return $db->fetchRow($select);
	}
	
	/**
	 * renvoie la table principale
	 * @return Sea_Db_Table
	 */
	public function getTable() {return $this->_table;}
	
	/**
	 * initiliasation
	 * Enter description here ...
	 */
	public function init() {}
	
	/**
	 * chargemebt de donnée supplementaires
	 * 
	 * Methode a overloader en fonction des besoins
	 * 
	 * @param Array $row
	 */
	protected function _fullLoading($row) {
		
		// on verifie que l'on a des resultats
		if ($row->count() < 1) { return false;}
		
		// recuperation de la table principale
		$result = $row->current()->toArray();
		
		// recuperation des enregistrement des table dependante
		foreach ($this->_table->getDependentTables() as $name => $dependent) {
			if ($dep = $row->current()->findDependentRowset($dependent)) {$result[$name] = $dep->toArray();}
		}
		
		// gestion des enregistrement parent
		foreach ($this->_table->getReferences() as $title => $reference ) {

			// on negrer que les dependance a une cle
			if (count($reference['columns']) != 1 ) continue;
			
			// recupoerationd e la ligne de resultat
			$ref = new $reference['refTableClass'];
			if ($rowRef = $ref->find($result[current($reference['columns'])])) {
				if ($rowRef->count() > 0) {$result[$title] = $rowRef->current()->toArray();}
			}	
		}
		
		return $result;
	}
	
	/**
	 * Renvoie les donnée en full loading
	 * 
	 * @param unknown_type $primary
	 */
	public function find($primary, $full = true) {
		
		$result = false;// intiialisation du resultat
		
		if ($row = $this->_table->find($primary)) {$result = $full ?  $this->_fullLoading($row) : $row;}
		
		return $result;
	}
	
	
	/**
	 * sauvegarde automatique d'un ligne de resultat
	 * 
	 * @param unknown_type $row
	 */
	public function save($row) {
		
		$action = 'update';
		
		// on verifie que l'on a bien toute les cle primaires pour un update
		foreach ($this->_table->info(Zend_Db_Table::PRIMARY) as $primary) {if (empty($row[$primary])) {$action = 'insert';}}
		return $this->$action($row);
	}
	
	/**
	 * gestion de la suppression
	 * 
	 * 
	 * @param unknown_type $row
	 */
	public function delete($row) {
		
		// récupération de la connection
		$db = Zend_Db_Table::getDefaultAdapter();
			
		// construction de la clause where
		$where = array();
		foreach($this->_table->info(Zend_Db_Table::PRIMARY) as $primary) {$where[] = $db->quoteInto($primary . ' = ?', is_array($row) ? $row[$primary] : $row);}

		// enregistrement dans la table principale
		return $this->_table->delete($where);
	}
	
	/**
	 * Gestion de l'update
	 * 
	 * @param Array $row
	 * @param Bool $transaction
	 */
	public function update($row) {

		// récupération de la connection
		$db = Zend_Db_Table::getDefaultAdapter();
			
		// construction de la clause where
		$where = array();
		foreach($this->_table->info(Zend_Db_Table::PRIMARY) as $primary) {$where[] = $db->quoteInto($primary . ' = ?', $row[$primary]);}

		// enregistrement dans la table principale
		$this->_table->update($this->_table->assoc($row), $where);
		
		return $row[$primary];
	}
	
	/**
	 * gestion de l'insertion
	 * 
	 * @param Array $row
	 * @param Bool $transaction
	 */
	public function insert($row) {
		$this->_table->insert($this->_table->assoc($row));// enregistrement dans la table
		return $this->_table->getAdapter()->lastInsertId();// on renvoie l'identifiant de l'insertion
	}

}

?>