<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Element
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Form_Element_Xhtml */
require_once 'Zend/Form/Element/Xhtml.php';

/**
 * Text form element
 *
 */
class Sea_Form_Element_Text extends Zend_Form_Element_Xhtml
{
    /**
     * Default form view helper to use for rendering
     * @var string
     */
    public $helper = 'formText';
    
    /**
     * surcharge du constructeur
     * 
     * @param unknown_type $spec
     * @param unknown_type $options
     */
    public function __construct($spec, $label = '', $required = false) {
    
    	// construction du parent
    	parent::__construct($spec);
    	
    	// traitement des paramètres
    	$this->setLabel($label)->setRequired($required)->setAllowEmpty(!$required);

		//ajoute les validateur de Sea
		$this->addPrefixPath('Sea_Validate', 'Sea/Validate', 'VALIDATE');
    	
    	// on ajout le filtre trim par default
    	$this->addFilter('StringTrim');
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
