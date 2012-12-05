<?php

require_once 'Sea/Datagrid/Column.php';
require_once 'Sea/Datagrid/Html/TemplateElement.php';


class Sea_Datagrid_Html_Column extends Sea_Datagrid_Column {
	
	const DATAGRID_CLASS = 'Sea_Datagrid_Html';
	
	/**
	 * @var Sea_Datagrid_Html_TemplateElement
	 */
	protected $_cell;
	
	/**
	 * @var Sea_Datagrid_Html_TemplateElement
	 */
	protected $_header;
	
	/**
	 * filtre pour la colonne
	 * 
	 * @var Sea_Form_Element
	 */
	protected $_strainer;
	
	/**
	 * definit le trie
	 * 
	 * @var unknown_type
	 */
	protected $_sort = false;
	
	/**
	 * 
	 * visible
	 * 
	 * si la colonne sera montrée
	 * 
	 */
	protected $_visible = true;
	
	/**
	 * flag de rendu du contenu de la colonne
	 * 
	 * @var unknown_type
	 */
	protected $_hidden = false;
	
	/**
	 * @return the $_visible
	 */
	public function isVisible() {
		return $this->_visible;
	}

	/**
	 * @param boolean $_visible
	 */
	public function setVisible($_visible) {
		$this->_visible = (bool) $_visible;
		return $this;
	}

	/**
	 * @return the $_hidden
	 */
	public function getHidden() {
		return $this->_hidden;
	}

	/**
	 * @param unknown_type $_hidden
	 */
	public function setHidden($_hidden) {
		$this->_hidden = $_hidden;
		return $this;
	}

	/**
	 * constrcuteur
	 * 
	 * @param unknown_type $label
	 * @param unknown_type $indexes
	 */
	public function __construct($label, $indexes = null) {
		parent::__construct($label, $indexes);
		$tags = Sea_Datagrid_Html::$TAGS;
		$this->_cell = new Sea_Datagrid_Html_TemplateElement($tags['_cell']);
		$this->_cell->addClass('aCenter');// centrgae par default
		$this->_header = new Sea_Datagrid_Html_TemplateElement($tags['_header']);
	}
	
	/**
	 * effectue le rendu graphique de l'objet
	 * 
	 * (non-PHPdoc)
	 * @see Sea_Datagrid_Column::render()
	 */
	public function render($row, $view = null) {throw new Exception("Render method not implemented");}
	
	/**
	 * renvoie l'objet de gestion de cellule
	 * @return Sea_Datagrid_Html_TemplateElement
	 */
	public function getCell() {return $this->_cell;}
	
	/**
	 * renvoie le header
	 * @return Sea_Datagrid_Html_TemplateElement
	 */
	public function getHeader() {return $this->_header;}


	/**
	 * @return the $_strainer
	 */
	public function getStrainer() {
		return $this->_strainer;
	}

	/**
	 * defini l index de trie
	 * @param unknown_type $index
	 */
	public function sort($index = null) {
		if(is_null($index)) {return $this->_sort;}// getter
		else {$this->_sort = $index;return $this;}// setter
	}
	
	/**
	 * @param Sea_Form_Element $_strainer
	 */
	public function setStrainer(Zend_Form_Element $_strainer) {
		
		$_strainer->clearDecorators()->setDecorators(['SeaErrors',['ViewHelper', ['placement' => 'PREPEND']]]);
		$this->_strainer = $_strainer;
		return $this;
	}

	/**
	 * ajoute un filtre de type select
	 * 
	 * @return self
	 * 
	 */
	public function setStrainerSelect($options = null) {
	    // construction de l'element form
	    $element = new Sea_Form_Element_Select(current($this->getIndexes()), '', $options);
	    $this->setStrainer($element);
	    return $this;
	}
	
	/**
	 * gestion de l'alignement des cellule
	 * 
	 * @param unknown_type $align
	 */
	public function align($align) {
		// ion supprime toute les classe de position pour eviter le conflit
		$this->getCell()->removeClass('aCenter')->removeClass('aRight')->removeClass('aLeft');
	    $this->getCell()->addClass('a' . ucfirst($align));// ajout de l'attribut css a la cellule
	    return $this;
	}
	
	/**
	 * gestion de la taille de la cellule
	 * @param unknown_type $w
	 */
	public function width($w) {
	    $this->getCell()->css('width', $w);//ajout de l'attribut css a la cellule
	    return $this;
	}
}