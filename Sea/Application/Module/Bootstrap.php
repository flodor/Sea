<?php
require_once ('Zend/Application/Module/Bootstrap.php');
/** 
 * @author jhouvion
 * 
 * 
 */
class Sea_Application_Module_Bootstrap extends Zend_Application_Module_Bootstrap
{
	/**
     * charge les ressource type suplementaire
     * 
     */
    public function _initResourceLoader() {
    	$this->getResourceLoader()->addResourceTypes(array('datagrid' => array('namespace' => 'Datagrid','path' => 'datagrids')));
    }
}
?>