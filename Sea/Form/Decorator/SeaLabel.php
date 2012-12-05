<?php
require_once 'Zend/Form/Decorator/HtmlTag.php';
require_once 'Zend/Form/Decorator/Label.php';

class Sea_Form_Decorator_SeaLabel extends Zend_Form_Decorator_Label {
    
     protected $_tag = 'label';
   	
    /**
     * Render a label
     *
     * @param  string $content
     * @return string
     */
    public function render($content)
    {
        $element = $this->getElement();
        $view    = $element->getView();
        if (null === $view) {
            return $content;
        }

        $label     = $this->getLabel();
        $separator = $this->getSeparator();
        $placement = $this->getPlacement();
        $tag       = $this->getTag();
        $id        = $this->getId();
        $class     = $this->getClass();
        $options   = $this->getOptions();
        
       // Decoration si le champ est obligatoire
       $pattern = 'required';              
       if(preg_match("/".$pattern."/",$class)!=0){$label.= ' <em>*</em>';}
       
       // gestion de la dexcirption
       $description = $element->getDescription();
       if (!empty($description)) {$label.= sprintf('<small>%s</small>', $description);}
       if (empty($label) && empty($tag)) {return $content;}
       unset($options['id']);

        // rendu principale
        if (null !== $tag) {
        	// label
            $decorator = new Zend_Form_Decorator_HtmlTag();
            $decorator->setOptions(array('tag' => $tag,' for'  => $element->getName()));			
            $label = $decorator->render($label);
            
            //td
            $decorator = new Zend_Form_Decorator_HtmlTag();
            $decorator->setOptions(array('tag' => 'td') + ['class' => 'form-label'] + $options);
            $label = $decorator->render($label);
        }       
        
		// positionnement
        switch ($placement) {
            case self::APPEND:
                return $content . $separator . $label;
            case self::PREPEND:
                return $label . $separator . $content;
        }
    }
}