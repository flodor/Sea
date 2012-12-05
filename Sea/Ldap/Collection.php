<?php
/**
* gestion et affichage des collection
* @author Sylvain Cahot
* @since 20/01/2010
*/
	// étend Zend_Ldap_Collection pour prendre en compte le flag de dépassement de sizeLimit
	class Sea_Ldap_Collection extends Zend_Ldap_Collection{
		
		// Flag déterminant si le nb de résultats possibles est supérieur au nbMax à renvoyer
		protected $_overSizeLimit;
		
		public function getOverSizeLimit(){
			return $this->_overSizeLimit;
		}
		
		function __construct($ldapCollection, $sizelimit){									
			$nbElement = count($ldapCollection);				
			if($nbElement >= $sizelimit)
				$this->_overSizeLimit=true;
			else
				$this->_overSizeLimit = false;
			parent::__construct($ldapCollection);			
		}
		
	}
?>