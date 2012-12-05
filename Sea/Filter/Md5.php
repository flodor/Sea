<?php
/**
 * renvoie le md5 de la valeur
 * 
 * @author jhouvion
 *
 */
class Sea_Filter_Md5 implements Zend_Filter_Interface {
	
	public function filter($value) {return md5($value);}

}

?>