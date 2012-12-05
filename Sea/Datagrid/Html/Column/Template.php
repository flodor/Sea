<?php
require_once ('Sea/Datagrid/Html/Column.php');
/** 
 * @author jhouvion
 * 
 * Rend une colonne apartir d'un fichier de vu phtml
 * 
 * 
 */
class Sea_Datagrid_Html_Column_Template extends Sea_Datagrid_Html_Column
{
	
	/**
	 * le fichier de vue
	 * 
	 * @var unknown_type
	 */
	protected $_html;
	
	public function __construct($label, $html = '', $index = '') {
		
		// construction du parent
		parent::__construct($label, $index);
		
		// inscription du paramètre
		$this->_html = $html;
	}
	
	/**
	 * calcule du rendu
	 * 
	 * (non-PHPdoc)
	 * @see Sea_Datagrid_Html_Column::render()
	 */
	public function render($row, $view = null) {
		
		// on verfifcie que l'on trouve bien le fichier de script
		if (!empty($this->_html) && !$view->getScriptPath($this->_html)) {throw new Exception('Impossible de trouver le script : ' . $this->_html);}
		
		// on renvoie le contenu
		return $view->partial($this->_html, 'default', $row);
	}
}
