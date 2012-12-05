<?php
require_once 'Zend/Form/Decorator/Abstract.php';

/** 
 * @author jhouvion
 * 
 * 
 */
class Sea_Form_Decorator_SeaErrors extends Zend_Form_Decorator_Abstract{
	
	
	public function render($content) {
		
		$e = $this->getElement();// récupération de l'élément
		
		// si l'element a des erreur on les affiche
		if ($e->hasErrors()) {
			// on ajourte la class erreur a l'element
			$e->setAttrib('title', $e->getMessages());
			$e->setAttrib('class', sprintf('%s %s', $e->getAttrib('class'), 'ui-state-error tooltip'));
		}
		
		return $content;
	}
}

?>