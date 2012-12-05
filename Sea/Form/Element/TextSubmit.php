<?php
require_once 'Sea/Form/Element/Container.php';
require_once 'Sea/Form/Element/Text.php';
require_once 'Sea/Form/Element/Submit.php';


class Sea_Form_Element_TextSubmit extends Sea_Form_Element_Container {
	
	/**
	 * Constructeur
	 * 
	 * @param unknown_type $text
	 * @param unknown_type $submit
	 * @param unknown_type $options
	 */
	public function __construct($text, $submit, $label = '', $options = array()) {
		
		// traitement des arguments
		$text = $text instanceof Sea_Form_Element_Text ? $text : new Sea_Form_Element_Text($text);
		$submit = $submit instanceof Sea_Form_Element_Submit ? $submit : new Sea_Form_Element_Submit($submit);
		
		// constrcutruction de l'objet
		parent::__construct($text->getName().$this->_suffix, $options);
		
		// on enleve les decorateur pour ne garder que le rendu de la vue
		$text->clearDecorators()->addDecorator('ViewHelper');
		$submit->clearDecorators()->addDecorator('ViewHelper');
		
		// on ajoute les elements
		$this->addElement($text);
		$this->addElement($submit);
		$this->setLabel($label);
	}
}

?>