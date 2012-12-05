<?php
/** 
 * @author jhouvion
 * 
 * 
 */

/** Zend_Form_Element_Xhtml */
require_once 'Zend/Form/Element/Xhtml.php';

class Sea_Form_Element_Image extends Zend_Form_Element_Xhtml
{
 	/**
     * Default form view helper to use for rendering
     * @var string
     */
    public $helper = 'formHidden';
    
    /**
     * taille maximum de l'image (pour l'affichage)
     * 
     * 0 => taille reel
     * 
     * @var unknown_type
     */
    protected $_width;
    
     /**
     * taille maximum de l'image (pour l'affichage)
     * 
     * 0 => taille reel
     * 
     * @var unknown_type
     */
    protected $_height;
    
	/**
	 * Surcharge du constructeur
	 * 
	 * @param String $label
	 * @param String $value
	 * @param Array $spec
	 * @param Array $options
	 */    
    public function __construct($spec, $label = '', $width = 0, $height = 0) {
		    
    	// on merge les information donnée
    	if (is_string($spec)) {$this->setName($spec);}
    	
    	// paramètrage de l'objet
    	$this	->setLabel($label)
    			->setHeight($height) 
    			->setWidth($width);
    	
    	// constrcuteur parent
    	parent::__construct($spec);
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
									array('Image'),
						            array(array('input' => 'HtmlTag'), array('tag' => 'td', 'class' => 'form-input')),
								    array('SeaLabel'),
								    array(array('div' => 'HtmlTag'), array('tag' => 'tr'))));

	}
	/**
	 * @return the $_width
	 */
	public function getWidth() {
		return $this->_width;
	}

	/**
	 * @return the $_height
	 */
	public function getHeight() {
		return $this->_height;
	}

	/**
	 * @param unknown_type $_width
	 */
	public function setWidth($_width) {
		$this->_width = $_width;
		return $this;
	}

	/**
	 * @param unknown_type $_height
	 */
	public function setHeight($_height) {
		$this->_height = $_height;
		return $this;
	}

}
?>