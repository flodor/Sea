<?php

require_once ('Zend/Form/Element/Xhtml.php');

/** 
 * @author jhouvion
 * 
 * 
 */
class Sea_Form_Element_Content extends Zend_Form_Element_Xhtml {

	protected $_content = '';
	
	/**
	 * Surcharge du constructeiur
	 * 
	 * @param String $label
	 * @param String $value
	 * @param Array $spec
	 * @param Array $options
	 */    
    public function __construct($content, $label = false) {
		    
    	// onmerge les information donnée
    	$name = 'content_'.rand(0, 100000);
    	$this->setContent($content);
    	$this->setLabel($label);

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

		$this->addPrefixPath('Sea_Form_Decorator', 'Sea/Form/Decorator', 'decorator');
		if ($this->getLabel()) {
			$this->setDecorators(array(	array('Html',array('html' => $this->getContent())),
							            array(array('input' => 'HtmlTag'), array('tag' => 'td', 'class' => 'form-input')),
									    array('SeaLabel'),
									    array(array('div' => 'HtmlTag'), array('tag' => 'tr'))));
		
		} else {
			$this->setDecorators(array(	array('Html',array('html' => $this->getContent())),
							            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'colspan' => 2, 'class' => 'padding')),
									    array(array('div' => 'HtmlTag'), array('tag' => 'tr'))));
		}
	}
	/**
	 * @return the $_content
	 */
	public function getContent() {
		return $this->_content;
	}

	/**
	 * @param string $_content
	 */
	public function setContent($_content) {
		$this->_content = $_content;
		return $this;
	}

}

?>