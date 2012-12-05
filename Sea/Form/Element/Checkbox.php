<?php
require_once 'Zend/Form/Element/Checkbox.php';

class Sea_Form_Element_Checkbox extends Zend_Form_Element_Checkbox {
   
 	/**
     * surcharge du constructeur
     * 
     * @param unknown_type $spec
     * @param unknown_type $options
     * @param unknown_type $multi
     */
    public function __construct($spec, $label = '', $required = false ) {
    	
    	// construction du parent
    	parent::__construct($spec);
    	
    	// traitement des paramètres
    	$this->setLabel($label)->setRequired($required)->setAllowEmpty(!$required);
    	
    	if ($required) {$this->addValidator('NotEmpty', false, Zend_Validate_NotEmpty::ZERO);}
    }

      /**
     * chargement des decorateurs par default
     * 
     * (non-PHPdoc)
     * @see Zend_Form_Element::loadDefaultDecorators()
     */
    public function loadDefaultDecorators() {

		/* on ajoute un emplacement e decorateurs à l'élément */
		$this->addPrefixPath('Sea_Form_Decorator', 'Sea/Form/Decorator', 'decorator');

		/* Remplace les decorateurs par default */
		$this->setDecorators(array(	array('SeaErrors'),
									array('ViewHelper', array('placement' => 'PREPEND')),
						            array(array('input' => 'HtmlTag'), array('tag' => 'td', 'class' => 'form-input form-checkbox')),
								    array('SeaLabel'),
								    array(array('div' => 'HtmlTag'), array('tag' => 'tr'))));

	}
}
