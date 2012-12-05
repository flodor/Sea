<?php
require_once ('Zend/Form/Decorator/Abstract.php');
/** 
 * @author jhouvion
 * 
 * 
 */
class Sea_Form_Decorator_Image extends Zend_Form_Decorator_Abstract
{
    
	public function render($content) {

		// recuperartion de l'element
		$element = $this->getElement();

		// rrecuperation des information de l'element
		$height = $element->getHeight();
		$width = $element->getWidth(); 
		
		// construction des option
		$options = array('tag' => 'img', 'src' => $element->getValue());
		if(!empty($height)) {$options['height'] = $height;}
		if(!empty($width)) {$options['width'] = $width;}
		
		
		// création du decorateur
		$decorator = new Zend_Form_Decorator_HtmlTag();
		$decorator->setOptions($options);
        
		
		// renvoie du rendu
       	return $content . $decorator->render('');
	}
}
?>