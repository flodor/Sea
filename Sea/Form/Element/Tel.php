<?php

class Sea_Form_Element_Tel extends Sea_Form_Element_Text {
	
	public $helper = 'formTel';

	/**
     * surcharge du constructeur
     * 
     * @param unknown_type $spec
     * @param unknown_type $options
     */
    public function __construct($spec, $label = '', $required = false) {
		 
    	// construction du parent
    	parent::__construct($spec, $label, $required);
    	
    	// ajout des validateur et filtre
    	$this->addFilter('Digits')->addValidators(['Digits', ['StringLength',false, [10,10]]]);
	}
}