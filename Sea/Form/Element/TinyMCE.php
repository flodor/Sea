<?php

class Sea_Form_Element_TinyMCE extends ZendX_JQuery_Form_Element_UiWidget {
	
	 public $helper = "tinyMCE";
	
	/**
	 * surcharge du constructeur
	 * 
	 * @param unknown_type $spec
	 * @param unknown_type $options
	 */
	public function __construct($spec, $label,  $required = false) {
		
		parent::__construct($spec);
		$this->setLabel($label)->setRequired($required)->setAllowEmpty(!$required);
		
		// on met par default une taille de colonne et de ligne
		$this->setOptions(['cols' => 50, 'rows' => 5]);
		$this->setJQueryParams(getregistry(false, 'tinymce')->toArray());
	}
	
	/**
	 * pour les textarea, on enleve les slashes(non-PHPdoc)
	 * @see Zend_Form_Element::setValue()
	 */
	public function setValue($value) {
		parent::setValue(stripslashes($value));
	}
	
	 /**
     * chargement des decorateurs par default
     * 
     * (non-PHPdoc)
     * @see Zend_Form_Element::loadDefaultDecorators()
     */
    public function loadDefaultDecorators() {
		// Remplace les decorateurs par default
		$this->setDecorators(array(	array('SeaErrors'),
									array('UiWidgetElement', array('placement' => 'PREPEND')),
						            array(array('input' => 'HtmlTag'), array('tag' => 'td', 'class' => 'form-input')),
								    array('SeaLabel'),
								    array(array('div' => 'HtmlTag'), array('tag' => 'tr'))));

	}
}
?>