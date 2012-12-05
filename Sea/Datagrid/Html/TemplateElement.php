<?php

require_once 'Sea/Decorator/HtmlTag.php';

/**
 * 
 * Elements Html GENERIQUES pour Sea_Datagrid
 * @author tibor
 *
 */
class Sea_Datagrid_Html_TemplateElement extends Sea_Decorator_HtmlTag {
	
	/**
	 * Tableau des fonctions callbacks
	 * @var Array
	 */
	protected $_callbacks = array();
	
	
	/**
	 * apelle des fonction de callback
	 * 
	 * @param unknown_type $dataGrid
	 * @param unknown_type $element
	 */
	public function runCallbacks(Sea_Datagrid_Html $dataGrid, Sea_Datagrid_Html_Element $element = null) {
		foreach($this->_callbacks as $callback) {$callback($dataGrid, $element);}
	}
	
	/**
	 * ajoute une fonction de callback
	 * 
	 * @param unknown_type $callback
	 * @throws Exception
	 */
	public function addCallback($callback) {
		if (!is_callable($callback)) {throw new Exception("addCallback() needs a callback function");}
		$this->_callbacks[] = $callback;
		return $this;
	}
	
	/**
	 * efface les callback
	 * Enter description here ...
	 */
	public function clearCallbacks() {
		$this->_callbacks = array();
		return $this;
	}
	
	/**
	 * renvoie les callback
	 * Enter description here ...
	 */
	public function getCallbacks() {
		return $this->_callbacks;
	}

	/**
	 * definition de tout les callback
	 * 
	 * @param array $callbacks
	 * @throws Exception
	 */
	public function setCallbacks(array $callbacks) {
		foreach($callbacks as $callback) {
			if (!is_callable($callback)) {
				throw new Exception("Non-function element in the array passed to setCallbacks()");
			}
		}
		$this->_callbacks = $callbacks;
		return $this;
	}

}
