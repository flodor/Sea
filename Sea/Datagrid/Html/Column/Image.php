<?php

require_once 'Sea/Datagrid/Html/Column.php';

/** 
 * @author jhouvion
 * 
 * 
 */
class Sea_Datagrid_Html_Column_Image extends Sea_Datagrid_Html_Column {	
	
	/**
	 * href
	 * @var string
	 */
	protected $_src;
	
	/**
	 * bind pour le href
	 * @var array
	 */
	protected $_bind;
	
	/**
	 * divers attributs
	 * @var array
	 */
	protected $_attributes;
	
	/**
	 * constructeur
	 * 
	 * @param unknown_type $label
	 * @param unknown_type $indexes
	 * @param unknown_type $src
	 * @param unknown_type $attributes
	 */
	public function __construct($label, $indexes, $src, $attributes = array()) {
		
		parent::__construct($label, $indexes);
		
		$this	->setSrc($src)
				->setAttributes((array) $attributes);
	}
	
	public function render($row, $view = null) {
		
		// conscrution du lien de l'image
		if(($src = vsprintf($this->getSrc(), array_get_assoc($this->getIndexes(), $row))) === false) {throw new Zend_Exception('Erreur lors du bind de l\'url');}
		if (empty($src)) {return '';}// si pas d'iamge on ne renvoie rien
		
		// generationd e l'image
		$this->_attributes['src'] = $src;
		$xhtml = new Sea_Decorator_HtmlTag('img', $this->getAttributes());
		
		// renvoie du rednu
		return $xhtml->render('');
	}
	
	public function getSrc() {
		return $this->_src;
	}

	public function setSrc($src) {
		$this->_src = $src;
		return $this;
	}

	public function getBind() {
		return $this->_bind;
	}

	public function setBind($bind) {
		$this->_bind = $bind;
		return $this;
	}

	public function getAttributes() {
		return $this->_attributes;
	}

	public function setAttributes($attributes) {
		$this->_attributes = $attributes;
		return $this;
	}
}
?>