<?php
require_once ('Sea/Datagrid/Html/Column/Link.php');
/** 
 * @author jhouvion
 * 
 * 
 */
class Sea_Datagrid_Html_Column_IconeUi extends Sea_Datagrid_Html_Column_Link {
	
	/**
	 * sous titre de l'icone
	 * 
	 * @var unknown_type
	 */
	protected $_title;
	
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
	public function __construct($title, $icon, $href = '#', $bind = array(), $attributes = array()) {
		parent::__construct('', $icon, $href, $bind, $attributes);
		$this->setTitle($title);// on set le titre
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Sea_Datagrid_Html_Column_Link::render()
	 */
	public function render($row, $view = null) {
		
		// Construction de l'url du lien
		$href = $this->getHref();
		if (!empty($href)) {
			
			$filtered_bind = [];
			foreach($this->getBind() as $bind) {$filtered_bind[] = property_exists($row, $bind) ? $row->$bind : $bind;}
			if (!empty($bind) && empty($filtered_bind)) { throw new Zend_Exception('Could not find fields ' . Zend_Json::encode($bind)); }
			if(!$href = vsprintf($href, $filtered_bind)) {throw new Zend_Exception('Erreur lors du bind de l\'url');}
		} else {$href = '#';}
		
		// constrcution des attribut en rapport avec l'url
		if (preg_match('/^javascript:/', $href)) {$linkAttrs = array('href' => '#', 'onclick' => $href);}// cas du javascrip
		else {$linkAttrs = array('href' => $href);}//cas d'un url autre
		
		// gestion des attribut
		$attribs = $this->getAttributes();
		$class = ['button'];
		if (isset($attribs['class'])) {$class[] = $attribs['class'];unset($attribs['class']);}

		// construction du lien
		$tag = new Sea_Decorator_HtmlTag('a', array('class' => implode(' ', $class), 'data-icon-primary' => current($this->getIndexes()), 'data-icon-only' => 'true') + $attribs + $linkAttrs);
		
		return $this->getHidden() ? '' : $tag->render($this->getTitle());
	}
	
	/**
     * @return the $_title
     */
    public function getTitle () {return $this->_title;}

	/**
     * @param unknown_type $_title
     */
    public function setTitle ($_title){$this->_title = $_title;return $this;}
}
?>