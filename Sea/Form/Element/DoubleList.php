<?php
/**
 * Generation d'une double liste de sélection
 *
 */
class Sea_Form_Element_DoubleList extends Zend_Form_Element_Multi
{
    /**
     * Use formMultiDoubleList view helper by default
     * @var string
     */
    public $helper = 'formDoubleList';
    
     /**
     * surcharge du constructeur
     * 
     * @param unknown_type $spec
     * @param unknown_type $options
     * @param unknown_type $multi
     */
    public function __construct($spec, $options = array(),$multi = array(), $required = false) {
    	
    	// construction du parent
    	parent::__construct($spec, $options);
    	
    	// lmise en place du label si specifié a la place des options
    	if (is_string($options)) {$this->setLabel($options);}
    	
    	// onajoute les options
    	$this->addMultiOptions($multi)->setRequired($required)->setAllowEmpty(!$required);
    	
    	// on zap le validator (bug multiselection)
		$this->setRegisterInArrayValidator(false);
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
		
		$this->setDecorators(array(	array('SeaErrors'),
									array('ViewHelper', array('placement' => 'PREPEND')),
						            array(array('input' => 'HtmlTag'), array('tag' => 'td', 'class' => 'form-input')),
								    array('SeaLabel'),
								    array(array('div' => 'HtmlTag'), array('tag' => 'tr'))));
		
	}    
}
