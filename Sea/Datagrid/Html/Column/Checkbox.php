<?php
require_once 'Sea/Datagrid/Html/Column.php';

/** 
 * @author jhouvion
 * 
 * 
 */
class Sea_Datagrid_Html_Column_Checkbox extends Sea_Datagrid_Html_Column {
	
	/**
	 * Defini le valeur sur laquel on def ini si la checkbox est checker
	 * 
	 * @var String
	 */
	protected $_checked;
	/**
	 * Libellé du bouton
	 * .
	 * @var String
	 */
	protected $_value;
	
	/**
	 * evenement on click
	 * 
	 * @var String
	 */
	protected $_click;
	
	/**
	 * bind sur l'evenemlent onclick
	 * 
	 * @var 
	 */	
	protected $_bind;
	
	/**
	 * 
	 * 
	 * @var Array
	 */
	protected $_attributes = array();

	/**
	 * Surcharge du constructeur
	 * 
	 * 
	 * @param unknown_type $value
	 * @param unknown_type $label
	 */
	public function __construct($label, $value = '', $checked = '', $onclick = null, $bind = array(), $attributes = array()  ) {
		parent::__construct($label);
		
		// attributiond e l'evenement a l'objet
		if (!is_null($onclick)) {$this->setClick($onclick);}
		$this->setBind($bind);
		$this->setChecked($checked);
		
		$this->setAttributes((array)$attributes);
		$this->setValue($value);
	}
	
	/**
	 * calcule du rendu
	 * @see Sea_Datagrid_Html_Column::render()
	 */
	public function render($row, $view = null) {
		
		$value =  $row->{$this->getValue()};// recupération de la valeur
		$html = '';// intitilisation du retour
		
		// creation de l'hyperlien
		$bind = $this->getBind();
		$filtered_bind = array_get_assoc($this->getBind(), $row);
		
		$click = $this->getClick();
		$javascript = false;
		
		if (!empty($click)) {
			if (!empty($bind) && empty($filtered_bind)) { throw new Zend_Exception('Could not find fields ' . Zend_Json::encode($bind)); }
			$javascript = 'javascript:' . vsprintf($click, $filtered_bind);
		}
		
		$attributes = $this->getAttributes();
		$attributes['checked'] = isset($attributes['checked']) ? $attributes['checked'] : !empty($row->{$this->getChecked()});
		$attributes['onclick'] = isset($attributes['onclick']) ? $attributes['onclick'] : $javascript;
		
		$xhtml = $view->formCheckbox($this->getValue() . '[]', $value, $attributes);
	

        return $xhtml;
	}
	
	/**
	 * @return the $_value
	 */
	public function getValue() {
		return $this->_value;
	}

	/**
	 * @return the $_attribute
	 */
	public function getAttributes() {
		return $this->_attributes;
	}

	/**
	 * @param String $_value
	 */
	public function setValue($_value) {
		$this->_value = $_value;
		return $this;
	}

	/**
	 * @param Array $_attribute
	 */
	public function setAttributes($_attributes) {
		$this->_attributes = $_attributes;
		return $this;
	}
	/**
	 * @return the $_click
	 */
	public function getClick() {
		return $this->_click;
	}

	/**
	 * @return the $_bind
	 */
	public function getBind() {
		return $this->_bind;
	}

	/**
	 * @param String $_click
	 * @return self
	 */
	public function setClick($_click) {
		$this->_click = $_click;
		return $this;
	}

	/**
	 * @param field_type $_bind
	 * @return self
	 */
	public function setBind($_bind) {
		$this->_bind = $_bind;
		return $this;
	}
	/**
	 * @return the $_checked
	 */
	public function getChecked() {
		return $this->_checked;
	}

	/**
	 * @param String $_checked
	 */
	public function setChecked($_checked) {
		$this->_checked = $_checked;
		return $this;
	}

}

?>