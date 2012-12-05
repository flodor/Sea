<?php

class Sea_Form_Element_Email extends Sea_Form_Element_Text {
	
	public $helper = 'formEmail';

	/**
     * surcharge du constructeur
     * 
     * @param unknown_type $spec
     * @param unknown_type $options
     */
    public function __construct($spec, $label = '', $required = false) {
		 
    	// construction du parent
    	parent::__construct($spec, $label, $required);
    	$this->addValidator('EmailAddress');
	}
}