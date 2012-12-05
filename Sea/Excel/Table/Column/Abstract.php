<?php

/** 
 * @author jhouvion
 * 
 * 
 */
abstract class Sea_Excel_Table_Column_Abstract {
	//TODO - Insert your code here
	
	/**
	 * Libelle ou header de la colonne
	 * 
	 * @var $_label unknown_type
	 */
	protected $_label;
	
	/**
	 * coinstructeur
	 * 
	 * @param $id
	 */
	function __construct($id) {
		$this->_id = $id;
	}
	
	/**
	 * getter pour le libelle de la colonne
	 * 
	 */
	public function getLabel() {
		return $this->_label;
	}
	
	/**
	 * Calcul le rendu de la ligne
	 * 
	 * @param Interger $l
	 * @param Char $c
	 * @param Multi $c
	 */
	abstract public function render($sheet, $l, $c, $data);
	
}

?>