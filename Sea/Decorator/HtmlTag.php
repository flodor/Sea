<?php
require_once 'Trait/Html/Css.php';
require_once 'Trait/Html/Attrib.php';

class Sea_Decorator_HtmlTag {
	
	use Trait_Html_Css;
	use Trait_Html_Attrib;
	
    /**
     * HTML tag to use
     * @var string
     */
    protected $_tag;

	/**
	 * surcharge constructeur
	 * 
	 * @param unknown_type $tag
	 * @param unknown_type $options
	 */
	public function __construct($tag, array $options = array()) {
		$this->setTag($tag);
		$this->attrib($options);
	}
	
	/**
	 * @return the $_tag
	 */
	public function getTag() {
		return $this->_tag;
	}

	/**
	 * @param string $_tag
	 */
	public function setTag($_tag) {
		$this->_tag = $_tag;
		return $this;
	}

	/**
     * Render content wrapped in an HTML tag
     *
     * @param  string $content
     * @return string
     */
    public function render($content) {
    	$inside = '';
   
    	if (($css = $this->renderCss()) != '') {$inside .= sprintf(' style="%s"',$css);}
    	if (($class = $this->renderClass()) != '') {$inside .= sprintf(' class="%s"',$class);}
    	if (($attrib = $this->renderAttrib()) != '') {$inside .= sprintf(' %s',$attrib);}
    
    	$inside = empty($inside) ? '' : (' ' . $inside);
    	return sprintf('<%s%s>%s</%1$s>', $this->getTag(), $inside, $content);
    }
}
