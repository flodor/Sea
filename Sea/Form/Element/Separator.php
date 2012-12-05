<?php

require_once ('Zend/Form/Element/Xhtml.php');

/** 
 * @author jhouvion
 * 
 * 
 */
class Sea_Form_Element_Separator extends Zend_Form_Element_Xhtml {

	/**
	 * Surcharge du constructeiur
	 * 
	 * @param String $label
	 * @param String $value
	 * @param Array $spec
	 * @param Array $options
	 */    
    public function __construct() {
		    
    	// onmerge les information donnée
    	$name = 'separator_'.rand(0, 100000);
    	
    	// constrcuteur parent
    	parent::__construct($name);
    }

     /**
     * chargement des decorateurs par default
     * 
     * (non-PHPdoc)
     * @see Zend_Form_Element::loadDefaultDecorators()
     */
    public function loadDefaultDecorators() {
    	
    	$this->setDecorators(array(	array(array('input' => 'HtmlTag'), array('tag' => 'td', 'class' => 'form-separator', 'colspan' => 2)),
								    array(array('div' => 'HtmlTag'), array('tag' => 'tr'))));
    	
	}
}

?>