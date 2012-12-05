<?php

class Sea_Form_Decorator_LabelDescription extends Zend_Form_Decorator_Abstract
{
    public function render($content)
    {
        if ($this->getOption("tag") != "") {
            $tag = $this->getOption("tag");
            $startTag = "<".$tag.">";
            $endTag = "</".$tag.">";
        }
        if ($this->getOption("description") != "") {
            $desc = $this->getOption("description");
            $startDesc = "<".$desc.">";
            $endDesc = "</".$desc.">";
        }        
        $element = $this->getElement();
        
        $str  = $startTag."<label for=\"".$element->getName()."\">".$element->getLabel()."<br />";
        $str .= $startDesc.$element->getDescription().$endDesc;
        $str .= "</label>".$endTag.$content;
        return $str;
    }
}

?>