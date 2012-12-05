<?php
/**
 * Creation de la classe de consultation d'annuaire LDAP
 * @author Sylvain Cahot
 * @since 13/01/2010
 */

require_once 'Zend/Ldap.php';

class Sea_Ldap extends Zend_Ldap
{

	protected $alistUser;
		
	function __construct($option = null)
	{
		parent::__construct($option);		
	}
	
	/**
	 * Récupère une liste filtrée d'utilisateurs
	 *
	 */
	public function searchListUser($filter)
	{		
		$result = $this->search('(objectclass=*)',$filter);		
	
		foreach ($result as $item){
			//if($item["uid"])
			Zend_Debug::dump($item);
			//Zend_Debug::dump($item["uid"]);
			echo '<hr>';
		}
				
	}
	
	
	/**
	 * Récupère les informations d'un utilisateur (en fonction de son uid)
	 *
	 */
	public function searchUserInfo($uid)
	{
		$res = $this->getEntry($uid);
		return $res;
	}
	
	
}


?>