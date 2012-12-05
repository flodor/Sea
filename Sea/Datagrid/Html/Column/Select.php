<?php

require_once 'Sea/Datagrid/Html/Column.php';


/** 
 * @author jhouvion
 * 
 * 
 */
class Sea_Datagrid_Html_Column_Select extends Sea_Datagrid_Html_Column {

	/**
	 * bond pour le change
	 * 
	 * @var unknown_type
	 */
	protected $_bind;
	
	/**
	 * change non bindé
	 * 
	 * @var unknown_type
	 */
	protected $_change;
	
	/**
	 * option de la balise select
	 * 
	 * @var unknown_type
	 */
	protected $_options;
	
	/**
	 * constrcteur
	 * 
	 * @param unknown_type $label
	 * @param unknown_type $index
	 * @param array $options
	 * @param unknown_type $change
	 * @param array $bind
	 */
	public function __construct($label, $index,  array $options = array(), $change = false, $bind = array()) {
		
		parent::__construct($label, $index);
		
		// attributiond e l'evenement a l'objet
		$this->setChange($change);
		$this->setBind((array) $bind);
		$this->setOptions($options);
	}
	
	/**
	 * calcule du rendu
	 * @see Sea_Datagrid_Html_Column::render()
	 */
	public function render($row, $view = null) {
		
		// creation de l'hyperlien
		$linkAttrs = array();
		if ($this->getChange()) {
			$bind = $this->getBind();
			$filtered_bind = array_get_assoc($this->getBind(), $row);
			if (!empty($bind) && empty($filtered_bind)) { throw new Zend_Exception('Could not find fields ' . Zend_Json::encode($bind)); }
			if(!$change = vsprintf($this->getChange(), $filtered_bind)) {throw new Zend_Exception('Erreur lors du bind de l\'url');}
			$linkAttrs = array('onchange' => $change);
		}
		
		// recuperation de l'index
		$key = current($this->getIndexes());
		
		// creation du contenu
		return $view->formSelect("noname", $row->$key, $linkAttrs, $this->getOptions());
	}
	
	/**
	 * @return the $_bind
	 */
	public function getBind() {
		return $this->_bind;
	}

	/**
	 * @return the $_change
	 */
	public function getChange() {
		return $this->_change;
	}

	/**
	 * @param field_type $_bind
	 */
	public function setBind($_bind) {
		$this->_bind = $_bind;
		return $this;
	}

	/**
	 * @param field_type $_change
	 */
	public function setChange($_change) {
		$this->_change = $_change;
		return $this;
	}
	/**
	 * @return the $_options
	 */
	public function getOptions() {
		return $this->_options;
	}

	/**
	 * @param field_type $_options
	 */
	public function setOptions($_options) {
		$this->_options = $_options;
		return $this;
	}



}

?>