<?php

/** Zend_Controller_Action_HelperBroker */
require_once 'Zend/Controller/Action.php';


abstract class Sea_Controller_Action extends Zend_Controller_Action {

	/**
	 * raccourcie pour le passage des variable a la vue
	 * 
	 * @param unknown_type $name
	 * @param unknown_type $value
	 */
	public function __($name, $value) {return $this->view->assign($name, $value);}
	
	/**
	 * effectue un rendu rapide
	 * 
	 * @param unknown_type $data
	 */
	public function _simple() {
		
		// opn force l'affichage de la vue simple
		$this->_helper->viewRenderer->setNoController()->setRender('simple');
		
		// ajoute a la pile de rendu les arguments
		foreach (func_get_args() as $k => $arg) {$this->__('var_' . $k, $arg);}
	}
	
	/**
	 * rendu ajax avec jaavscript
	 */
	public function _simpleNoLayout() {
		
		// definition du rendu
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$this->getResponse()->setBody(implode('', func_get_args()))
							->appendBody($this->view->JQuery()->setRenderMode(ZendX_JQuery::RENDER_JQUERY_ON_LOAD));
	}
}