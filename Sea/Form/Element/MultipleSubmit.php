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
 * Submit form element
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Element
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Submit.php 8585 2008-03-06 19:32:34Z matthew $
 */
class Sea_Form_Element_MultipleSubmit extends Zend_Form_Element
{

    /**
     * tableau contenant les element submit a affiché
     *
     * @var unknown_type
     */
    protected $_elements;

    /**
     * Ajoute un element a la pie d'éléments a afficher
     *
     * @param Zend_Form_Element_Submit $o
     */
	public function addSubmit(Zend_Form_Element $o) {
		$o->clearDecorators();
		$this->_elements[] = $o;
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
		
		
		$this->setDecorators(array(	array('Multiple'),
								    array(array('div' => 'HtmlTag'), array('tag' => 'tr'))));
	}

	/**
	 * recupration des elements
	 *
	 * @return unknown
	 */
	public function getSubElements() {
		return $this->_elements;
	}

	/**
	 * rendu
	 *
	 * @param Zend_View_Interface $view
	 * @return unknown
	 */
	public function render(Zend_View_Interface $view = null)
    {
        if (null !== $view) {
            $this->setView($view);
        }

        $content = '';
        foreach ($this->getDecorators() as $key => $decorator) {
            $decorator->setElement($this);
            $content = $decorator->render($content);
        }

        return $content;
    }
}
