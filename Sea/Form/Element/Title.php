<?php

require_once ('Zend/Form/Element/Xhtml.php');

/** 
 * @author jhouvion
 * 
 * 
 */
class Sea_Form_Element_Title extends Zend_Form_Element_Xhtml {

	/**
	 * Surcharge du constructeiur
	 * 
	 * @param String $label
	 * @param String $value
	 * @param Array $spec
	 * @param Array $options
	 */    
    public function __construct($value) {
		    
    	// onmerge les information donnée
    	$name = 'title_'.rand(0, 100000);

    	// Inscrip tion du libellé
    	$this->setDescription($value);
    	
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

		/* on ajoute un emplacement e decorateurs à l'élément */
		$this->addPrefixPath('Sea_Form_Decorator', 'Sea/Form/Decorator', 'decorator');

		/* Remplace les decorateurs par default */	
		$this->setDecorators(array(	array('Description', array('tag' => '')),
						            array(array('colspan' => 'HtmlTag'), array('tag' => 'td', 'colspan' => 2, 'class' => 'form-title')),
									array(array('container' => 'HtmlTag'), array('tag' => 'tr', 'class' => 'ui-widget-header'))
									));

	}
}

?>