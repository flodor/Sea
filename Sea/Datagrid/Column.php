<?php

/**
 * 
 * Modèle de colonne pour Sea_Datagrid
 * @author Tibor Vass
 *
 */
abstract class Sea_Datagrid_Column {
	
	/**
	 * Le nom de la colonne
	 * @var string
	 */
	protected $_label;
	
	/**
	 * Les indices nécessaires à la définition de la colonne
	 * @var array of int
	 */
	protected $_indexes = array();
		
	/**
	 * constructeur
	 * 
	 * @param unknown_type $label
	 * @param unknown_type $indexes
	 */
	public function __construct($label, $indexes = null) {

		$this->setLabel($label);
		$this->setIndexes((is_array($indexes) ? $indexes : array(is_null($indexes) ? $label : $indexes)));
	}
	
	/**
	 * calcul le rendu de l'objet
	 * 
	 * @param unknown_type $row
	 * @param unknown_type $view
	 */
	abstract public function render($row, $view = null);
	
	/**
	 * Retourne le nom de la colonne
	 */
	public function getLabel() {
		return $this->_label;
	}
	
	/**
	 * Retourne les indices nécessaires à la définition de la colonne
	 */
	public function getIndexes() {
		return $this->_indexes;
	}
	
	
	/**
	 * Définit le nom de la colonne
	 * @param string $label
	 */
	public function setLabel($label) {
		$this->_label = $label;
		return $this;
	}
	
	
	/**
	 * Définit les indices nécessaires à la définition de la colonne
	 * @param array $indexes
	 */
	public function setIndexes(array $indexes) {
		$this->_indexes = $indexes;
		return $this;
	}	
}