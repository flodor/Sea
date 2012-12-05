<?php

require_once ('Zend/Form/Element/Button.php');

class Sea_Form_Element_Hidden extends Zend_Form_Element_Hidden {
	
	/**
	 * constrcueteur
	 * 
	 * @param unknown_type $id
	 * @param unknown_type $required
	 */
	public function __construct($id, $required = false) {
		parent::__construct($id);
		$this->setRequired($required)->setAllowEmpty(!$required);
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
		
		$this->setDecorators(array(	array('ViewHelper')));
	}
}

?>