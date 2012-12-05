<?php

require_once ('Zend/Form/Element/Multiselect.php');

class Sea_Form_Element_MultiSelect extends Zend_Form_Element_Multiselect {

 	/**
     * surcharge du constructeur
     * 
     * @param unknown_type $spec
     * @param unknown_type $options
     * @param unknown_type $multi
     */
    public function __construct($spec, $label = '' ,$multi = array(), $required = false ) {
    	
    	// construction du parent
    	parent::__construct($spec);
    	
    	// on ajoute les options
    	$this->addMultiOptions($multi);
    	
    	// traitement des paramètres
    	$this->setLabel($label)->setRequired($required)->setAllowEmpty(!$required);
    }

    /**
     * chargement des decorateurs par default
     * 
     * (non-PHPdoc)
     * @see Zend_Form_Element::loadDefaultDecorators()
     */
    public function loadDefaultDecorators() {

		// on ajoute un emplacement e decorateurs à l'élément
		$this->addPrefixPath('Sea_Form_Decorator', 'Sea/Form/Decorator', 'decorator');

		$this->setDecorators(array(	array('SeaErrors'),
									array('ViewHelper', array('placement' => 'PREPEND')),
						            array(array('input' => 'HtmlTag'), array('tag' => 'td', 'class' => 'form-input')),
								    array('SeaLabel'),
								    array(array('div' => 'HtmlTag'), array('tag' => 'tr'))));

	}
}

?>