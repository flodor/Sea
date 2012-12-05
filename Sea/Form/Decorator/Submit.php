<?php

require_once ('Zend/Form/Decorator/Abstract.php');

/** 
 * @author jhouvion
 * 
 * 
 */
class Sea_Form_Decorator_Submit extends Zend_Form_Decorator_Abstract {
	
	/**
	 * rendu des bouton de soumission de formulaire(non-PHPdoc)
	 * 
	 * @see Zend_Form_Decorator_Abstract::render()
	 */
	public function render($content) {
		
		// initialisation du rendu
		$render = '';
		
		//construction des bouton
		foreach ((array) $this->getElement()->getSubmit() as $submit) {$render .= $submit->render();}
		
		// container pour les boutons
		if (!empty($render)) {
		    
		    // decoration de la div
			$decorator = new Zend_Form_Decorator_HtmlTag();
	        $decorator->setOptions(array('tag' => 'div','class' => 'form-action'));			
	        $render = $decorator->render($render);
	       
	        // decoration dans un header
	        $decorator = new Zend_Form_Decorator_HtmlTag();
	        $decorator->setOptions(array('tag' => 'div','class' => 'ui-widget-header', 'style' => 'border-top:0px;'));		
	        $content .= $decorator->render($render);
		}
		
		return $content;
	}
}