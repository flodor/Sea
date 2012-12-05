<?php

require_once 'Sea/Datagrid/Html/Column.php';
require_once 'Sea/Decorator/HtmlTag.php';

/**
 * genere un bouton pour une colonne
 * 
 * @author jhouvion
 *
 */
class Sea_Datagrid_Html_Column_Button extends Sea_Datagrid_Html_Column {	
	
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
	 *  Surcharge du constructeur
	 *  
	 * 
	 * @param unknown_type $label
	 * @param unknown_type $value
	 * @param unknown_type $onclick
	 * @param unknown_type $bind
	 * @param unknown_type $attributes
	 */
	public function __construct($label, $value, $onclick = null, $bind = array(), $attributes = array()  ) {
		
		parent::__construct($label);
		
		// attributiond e l'evenement a l'objet
		if (!is_null($onclick)) {$this->setClick($onclick);}
		$this->setBind($bind);
		
		$this->setAttributes((array)$attributes);
		$this->setValue($value);
	}
	
	/**
	 * calcule du rendu
	 * @see Sea_Datagrid_Html_Column::render()
	 */
	public function render($row, $view = null) {
		
		$value = trim($this->getValue());// recupération de la valeur
		$html = '';// intitilisation du retour
		
		//si la valeur est vide on nb'affiche pas le bouton
		if (!empty($value)) {
			
			// on recupere les attibuts
			$attrs = $this->getAttributes();
		
			// creation de l'hyperlien
			$bind = $this->getBind();
			$filtered_bind = array_get_assoc($this->getBind(), $row);
			if (!empty($bind) && empty($filtered_bind)) { throw new Zend_Exception('Could not find fields ' . Zend_Json::encode($bind)); }

			//recuperation de l'url
			if(!$url = vsprintf($this->getClick(), $filtered_bind)) {throw new Zend_Exception('Erreur lors du bind de l\'url');}
		
			// constrcution des attribut en rapport avec l'url
			if (preg_match('/^javascript:/', $url)) {
				$linkAttrs = array('href' => '#', 'onclick' => $url);}// cas du javascript
			else {$linkAttrs = array('href' => $url);}//cas d'un url autre
			
			// gestion de l'attribut target pour l'hyperlien
			if (!empty($attrs['target'])) {$linkAttrs['target'] = $attrs['target'];unset($attrs['target']);}
			
			// créatiuon du code html du lien
			$xhtml = new Sea_Decorator_HtmlTag('a', $linkAttrs);
		
			//rendu du bouton
			$button = new Sea_Decorator_HtmlTag('button');
			$html = $xhtml->css('text-decoration', 'none')->render($button->render((property_exists($row, $value)) ? $row->$value : $this->getValue()), $attrs);
		}
		
        return $html;
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


}

?>