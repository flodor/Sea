<?php
require_once ('Sea/Datagrid/Html/Column.php');

/**
 * plusieurs rendu pour une seule colonne
 * 
 * @author jhouvion
 *
 */
class Sea_Datagrid_Html_Column_Multi extends Sea_Datagrid_Html_Column {
    
    /**
     * colone contenu 
     * 
     * @var unknown_type
     */
    protected $_columns = array();
    
    /**
     * constructeur
     * 
     * @param unknown_type $label
     */
    public function __construct($label = '') {
        return parent::__construct($label);
    }
    
    /**
     * rendu des colonnes
     * 
     * (non-PHPdoc)
     * @see Sea_Datagrid_Html_Column::render()
     */
    public function render($row, $view = null) {
        
        $content = '';// intitliasation du contenu
        
        // rendu des colonne inseré
        foreach($this->_columns as $c) {$content .= $c->render($row, $view) . '&nbsp;';}
        return $content;
    }
    
    public function getColumns() {
    	return $this->_columns;
    }
    
    /**
     * ajoute une colonne
     * 
     * 
     * @param Sea_Datagrid_Column $c
     * @return Sea_Datagrid_Html_Column_Multi
     */
    public function add(Sea_Datagrid_Column $c) {
        $this->_columns[] = $c;
        return $this;
    }
}
?>