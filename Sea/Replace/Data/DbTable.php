<?php

require_once 'Sea/Replace/Data/Abstract.php';
require_once 'Zend/Db/Table.php';
require_once 'Zend/Registry.php';

/**
 * Contrôleur de données d'entrée et de sortie pour base de données
 * 
 * 
 * @author Julien Houvion
 *
 */
class Sea_Replace_Data_DbTable extends Sea_Replace_Data_Abstract {
	
	/**
	 * adapter de base de donnée
	 * 
	 * @var Zend_Db_Table_Abstract
	 */
	protected $_table = null;
	
	/**
	 * Resultat requete
	 * 
	 * @var Zend_Db_Table_Rowset
	 */
	protected $_rowset;
	
	/**
	 * les champ a modifié dans la base
	 * 
	 * @var Array
	 */
	protected $_fields = array();
	
	

	/**
	 * constructeur
	 * @param String | Zend_Db_Select $request
	 * @param unknown_type $adapter
	 * @throws Zend_Exception
	 */
	public function __construct($name, array $fields, $where = null, $count = null, $offset = null, $db = null){
		
		// on charge la connexion si nnon spécifié deja
		if ($db instanceof Zend_Db_Adapter_Abstract) {Zend_Db_Table::setDefaultAdapter($db);}
		
		// constructoion de l'objet de gestion de table
		$table = new Zend_Db_Table($name);
		
		// récuperation du jeux de donnée
		foreach ($table->fetchAll($where, null, $count, $offset) as $row) {	$this->append($row->toArray());}

		// attribution des champ a changer
		$this->_fields = $fields;
		
		//attribution de la table en parmètre
		$this->_table = $table;
	}
	
	
	/**
	 * lecture d'un enregistrement
	 * 
	 * @see Sea_Replace_Data_Abstract::read()
	 */
	public function read() {return $this->current();}
	
	/**
	 * inscription des valeurs modifiés
	 * 
	 * @see Sea_Replace_Data_Abstract::write()
	 */
	public function write($modified) {
		
		// recuperation de la gestion de la table
		$table = $this->_table;

		// récupération des cle primaire
		$primary = $table->info('primary');
		
		// récupération de l'entrée courante
		$current = $this->current();
		
		// constrcution clause where
		$where = array();
		foreach ($table->info('primary') as $primary) {$where[] = $table->getAdapter()->quoteInto($primary . ' = ?', $current[$primary]);}
		
		// on update la base
		$table->update(array_intersect_key($modified, array_flip($this->_fields)), $where);
	}
}
