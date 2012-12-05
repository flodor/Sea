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
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Form_Element_Xhtml */
require_once 'Sea/Form/Element/File.php';

/**
 * Zend_Form_Element
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Element
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: File.php 12267 2008-11-02 21:13:14Z thomas $
 */
class Sea_Form_Element_FileImage extends Sea_Form_Element_File
{

    /**
     * @var string Default view helper
     */
	var $helper = 'FormFileUploadify';

	/**
	 * Option relative au parametrage d'uploadify
	 *
	 * @var unknown_type
	 */
	protected $_attribs = array(	'upl_uploader' => '/public/flash/uploader.swf',
									'upl_cancelImg' => '/public/img/picto/error.png',
									'upl_auto'      => 'true',
									'upl_multi' => 'false');

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
		$this->setDecorators(array(	array('SeaErrors'),
									array('ViewHelper', array('placement' => 'PREPEND')),
						            array(array('input' => 'HtmlTag'), array('tag' => 'td', 'class' => 'form-input')),
								    array('SeaLabel'),
								    array(array('div' => 'HtmlTag'), array('tag' => 'tr'))));

		/* on envoie le nom du fichier */
		$this->setUpload('fileDataName', $this->getName());
	}

	/**
	 * Setter pour les atribut relatif a uploadify
	 *
	 * @param unknown_type $name
	 * @param unknown_type $value
	 * @return unknown
	 */
	function setUpload($name, $value) {
		if (!empty($name)) {$this->_attribs['upl_'.$name] = $value;}
		return $this;
	}

	/**
	 * Setter des data a envoyer pour gestion de l'upload
	 *
	 * @param unknown_type $name
	 * @param unknown_type $value
	 */
	function setUploadData($name, $value) {
		if (!empty($name)) {$this->_attribs['upl_data'][$name] = $value;}
	}

	/**
	 * Surcharge du rendu
	 *
	 * @param Zend_View_Interface $view
	 */
	public function render( 	Zend_View_Interface $view = null) {
		$this->setAttribs($this->_attribs);

		return parent::render($view);
	}


	/**
	 * Surcharge du setter pour la value
	 *
	 * @param unknown_type $value
	 * @return unknown
	 */
	public function setValue($value)
    {

		if (empty($value)) {return $this;}

		/* recuperation du fichier et de ses propriété */
    	$this->_value = file_exists($value) ? $value : $this->getDestination().'/'.$value;

    	if (file_exists($this->_value)){

	    	$front = Zend_Controller_Front::getInstance();
	    	$url = $front->getBaseUrl().'/'.$this->_value . '?time='.time();

	    	$oImg = new Sea_Image($this->_value);
	    	$this->setUpload('buttonImg', $url);
	    	$this->setUpload('height', $oImg->getHeight());
	    	$this->setUpload('width', $oImg->getWidth());
    	}

        return $this;
    }

    /**
     * surcharge de la fonction de validation
     *
     * @param unknown_type $value
     * @param unknown_type $context
     * @return unknown
     */
     public function isValid($value, $context = null) {
     	$this->setValue($value);
     	return parent::isValid($value, $context);
     }
}