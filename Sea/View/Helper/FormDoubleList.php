<?php
class Sea_View_Helper_FormDoubleList extends Zend_View_Helper_FormSelect
{
    
    public function formDoubleList($name, $value = null, $attribs = null,
        $options = null, $listsep = "<br />\n")
    {
    	$this->view->headScript()->appendFile('/js/doublelist.js');
    	 
        $ret = "";
        $attribs['multiple'] = true;
        if (!isset($attribs['width'])) {
            $attribs['width'] = '180px';
        }
        if (!isset($attribs['height'])) {
            $attribs['height'] = '300px';
        }
        
        $attribs['style'] = empty($attribs['style']) ?  '' : $attribs['style'];
        $attribs['style'] .= 'width:'.$attribs['width'].";height:".$attribs['height'].";";
        $attribs_select = $attribs;
        $attribs_select['ondblclick'] = "javascript:doublelist_unselect('".$name."');";
        $attribs_select['id'] = $name."_selected";
        $attribs_unselect = $attribs;
        $attribs_unselect['ondblclick'] = "javascript:doublelist_select('".$name."');";
        $attribs_unselect['id'] = $name."_unselected";
        $selected = array();
        if (count($value) > 0) {
            foreach($value as $k) {
            	if (!array_key_exists($k, $options)) {continue;}
                $selected[$k] = $options[$k];
                unset($options[$k]);
            }
        }
        $hidden_helper = new Zend_View_Helper_FormHidden();
        $hidden_helper->setView($this->view);

        $ret .= "<div id=\"".$name."_container\">";
        foreach($selected as $k => $v) { $ret .= $hidden_helper->formHidden($name."[]", $k, array('id' => $name.$k)); }
        $ret .= "</div>";
        $ret .= "<table><tr><td rowspan=\"2\">Selected<br />";
        $ret .= $this->formSelect($name."_selected", null, $attribs_select, $selected, $listsep);
        $ret .= "</td>";
        $ret .= "<td>&nbsp;</td>";
        $ret .= "<td rowspan=\"2\">Unselected<br />";
        $ret .= $this->formSelect($name."_unselected", null, $attribs_unselect, $options, $listsep);
        $ret .= "</td>";
        $ret .= "</tr>";
        $ret .= "<tr>";
        $ret .= "<td><input class=\"doublelist_select\" type=\"button\" value=\"-->\" onclick=\"javascript:doublelist_unselect('".$name."');\">";
        $ret .= "<input type=\"button\" value=\"<--\" onclick=\"javascript:doublelist_select('".$name."');\"></td>";
        $ret .= "</tr></table>";
        return $ret;
    }
}
