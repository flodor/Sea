<?php

require_once ('Zend/View/Helper/Url.php');

/** 
 * @author jhouvion
 * 
 * construit une url
 * 
 */
class Sea_View_Helper_Urlinclude extends Zend_View_Helper_Url {

	public function urlinclude($file , $module = null) {
		
		// récuperation de controller front
		$front = Zend_Controller_Front::getInstance();
		
		// insertion du bind
		if (func_num_args() > 2 ) {$file = vsprintf($file,  array_slice(func_get_args(), 2));}
		
		// paramètrage de la rewrite
		$urlOptions = array('uri' => $file, 'module' => empty($module) ? str_replace('default', 'www', $front->getRequest()->getModuleName()) : $module);
        
		// Constrcution de l'url
		return parent::url($urlOptions, $front->getPlugin('Application_Plugin_Router')->getIncludeRouteName(), false, false);
    }
}

?>