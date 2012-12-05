<?php
/**
 * generation d'un champ formulaire de date
 *
 */

class Sea_Form_Element_DatePicker extends ZendX_JQuery_Form_Element_DatePicker {

	
	/**
	 * surcharge du constructeur
	 * 
	 * @param unknown_type $spec
	 * @param unknown_type $options
	 */
	public function __construct($spec, $label,  $required = false) {
		
		parent::__construct($spec);
		$this->setLabel($label)->setRequired($required)->setAllowEmpty(!$required);
		
		$this->setJQueryParams(array(
                'dateFormat'        => 'dd/mm/yy',//format
                //'minDate'           => '+1',//séleccionable à partir du lendemain
                'firstDay'          => '1',//premier jour = lundi
              ));
	}

	 /**
     * chargement des decorateurs par default
     * 
     * (non-PHPdoc)
     * @see Zend_Form_Element::loadDefaultDecorators()
     */
    public function loadDefaultDecorators() {

		/* on ajoute un emplacement de decorateurs à l'élément */
		//$this->addPrefixPath('Sea_Form_Decorator', 'Sea/Form/Decorator', 'decorator');
		$this->addPrefixPath('Sea_Form_Decorator', 'Sea/Form/Decorator', 'decorator');
		
		$this->setDecorators(array(	array('SeaErrors'),
									array('UiWidgetElement', array('placement' => 'PREPEND')),
						            array(array('input' => 'HtmlTag'), array('tag' => 'td', 'class' => 'form-input')),
								    array('SeaLabel'),
								    array(array('div' => 'HtmlTag'), array('tag' => 'tr'))));
	}

}
