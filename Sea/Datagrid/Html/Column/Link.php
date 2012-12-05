<?php

require_once 'Sea/Datagrid/Html/Column.php';
require_once 'Sea/Decorator/HtmlTag.php';

class Sea_Datagrid_Html_Column_Link extends Sea_Datagrid_Html_Column {	
	
	/**
	 * href
	 * @var string
	 */
	protected $_href;
	
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
	 * @param unknown_type $href
	 * @param unknown_type $bind
	 * @param unknown_type $attributes
	 */
	public function __construct($label, $indexes, $href, $bind = array(), $attributes = array()) {
		
		parent::__construct($label, $indexes);
		$this	->setHref($href)
				->setBind((array) $bind)
				->setAttributes((array) $attributes);
	}
	
	/**
	 * calcule du rendu
	 * 
	 * (non-PHPdoc)
	 * @see Sea_Datagrid_Html_Column::render()
	 */
	public function render($row, $view = null) {
		
		// génération de l'url
		$bind = $this->getBind();
		$filtered_bind = array_get_assoc($this->getBind(), $row);
		if (!empty($bind) && empty($filtered_bind)) { throw new Zend_Exception('Could not find fields ' . Zend_Json::encode($bind)); }
		if(!$url = vsprintf($this->getHref(), $filtered_bind)) {throw new Zend_Exception('Erreur lors du bind de l\'url');}
		
		// constrcution des attribut en rapport avec l'url
		if (preg_match('/^javascript:/', $url)) {$linkAttrs = array('href' => '#', 'onclick' => $url);}// cas du javascrip
		else {$linkAttrs = array('href' => $url);}//cas d'un url autre
			
		$xhtml = new Sea_Decorator_HtmlTag('a', array_merge($linkAttrs, $this->getAttributes()));// constrcution du lien
		$label = $this->getLabel();// recuperation du label
		$key = current($this->_indexes);// récuperation de l'index
		
		// redu final
		return $xhtml->render($row->$key);
	}
	
	public function getHref() {
		return $this->_href;
	}

	public function setHref($href) {
		$this->_href = $href;
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
	
	public function setAttribute($name, $value) {
		$this->_attributes[$name] = $value;
		return $this;
	}
}