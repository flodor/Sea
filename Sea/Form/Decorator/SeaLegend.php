<?php
require_once ('Zend/Form/Decorator/Abstract.php');

class Sea_Form_Decorator_SeaLegend extends Zend_Form_Decorator_Abstract{
    
    public function render($content) {
        
        // recuperation de la legend
        $legend = $this->getElement()->getLegend();
        
        if (!empty($legend)) {
            require_once 'Zend/Form/Decorator/HtmlTag.php';
            $decorator = new Zend_Form_Decorator_HtmlTag();
			$decorator->setOptions(array('tag'   => 'div', 'class' => 'ui-widget-header padding',  'style' => 'border-bottom:0px'));
			$content = $decorator->render($legend) . $content;
        }
        
       return $content;
    }
}
?>