<?php
require_once ('Sea/Datagrid/Html/Column/Link.php');
/** 
 * @author jhouvion
 * 
 * 
 */
class Sea_Datagrid_Html_Column_Icone extends Sea_Datagrid_Html_Column_Link {
	
	/**
	 * taille des icones
	 * 
	 * @var unknown_type
	 */
	protected $_size = 24;
	
	/**
	 * sous titre de l'icone
	 * 
	 * @var unknown_type
	 */
	protected $_alt;
	
	/**
	 * pattern de l'url de l'image
	 * Enter description here ...
	 * @var unknown_type
	 */
	static protected $_pattern = '/images/icones/%s/%s.png';
	
	/**
	 * Constrcuetur
	 * 
	 * @param unknown_type $alt
	 * @param unknown_type $icon
	 * @param unknown_type $href
	 * @param unknown_type $bind
	 * @param unknown_type $size
	 * @param unknown_type $attributes
	 */
	public function __construct($alt, $icon, $href = '#', $bind = array(), $size = 24,$attributes = array()) {
		parent::__construct('', $icon, $href, $bind, $attributes);
		$this->setAlt($alt);
		if (!empty($size)) {$this	->setSize($size);}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Sea_Datagrid_Html_Column_Link::render()
	 */
	public function render($row, $view = null) {
		
		// Construction de l'url du lien
		$href = $this->getHref();
		if (!empty($href)) {
			$bind = $this->getBind();
			$filtered_bind = array_get_assoc($this->getBind(), $row);
			if (!empty($bind) && empty($filtered_bind)) { throw new Zend_Exception('Could not find fields ' . Zend_Json::encode($bind)); }
			if(!$href = vsprintf($href, $filtered_bind)) {throw new Zend_Exception('Erreur lors du bind de l\'url');}
		} else {$href = '#';}
		
		// constrcution des attribut en rapport avec l'url
		if (preg_match('/^javascript:/', $href)) {$linkAttrs = array('href' => '#', 'onclick' => $href);}// cas du javascrip
		else {$linkAttrs = array('href' => $href);}//cas d'un url autre
		
		// construction du lien
		$xhtml = new Sea_Decorator_HtmlTag('a', array_merge($linkAttrs,$this->_attributes ));

		// constrcyution de l'icone
		$key = current($this->_indexes);
		$img = '';// initliasation
		if (!empty($key)) {
			$img = new Sea_Decorator_HtmlTag('img', array(	'src' => vsprintf($this->getPattern(), array($this->getSize(), $key)), 	
															'alt' => $this->getAlt(), 'title' => $this->getAlt()));
			// rendu de l'image
			$img = $img->render('');
		}
		
		return $xhtml->render($img);
	}
	/**
	 * @return the $_size
	 */
	public function getSize() {
		return $this->_size;
	}

	/**
	 * @param field_type $_size
	 */
	public function setSize($_size) {
		$this->_size = $_size;
	}
	/**
	 * @return the $_alt
	 */
	public function getAlt() {
		return $this->_alt;
	}

	/**
	 * @return the $_pattern
	 */
	static public function getPattern() {
		return self::$_pattern;
	}

	/**
	 * @param unknown_type $_alt
	 */
	public function setAlt($_alt) {
		$this->_alt = $_alt;
		return $this;
	}

	/**
	 * @param unknown_type $_pattern
	 */
	static public function setPattern($_pattern) {
		self::$_pattern = $_pattern;
	}


}
?>