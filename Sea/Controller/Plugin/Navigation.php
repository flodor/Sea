<?php

/**
 * @package Sea_Controller_Plugin
 * @name /Sea/Controller/Plugin/Navigation.php
 * @version 1.0
 * @author Pierre-Yves Aillet
 * @since 08/01/2010
 * Ce plugin initialise le menu du layout avec les éléments du module courant.
 * 
 */
class Sea_Controller_Plugin_Navigation extends Zend_Controller_Plugin_Abstract
{
    /**
     * On récupère l'ensemble des informations de navigation depuis la base de 
     * données.
     * 
     * @param string $module Module pour lequel on récupère les pages 
     * (tous par défaut).
     * 
     * @return Zend_Acl l'ensemble des pages sélectionnées.
     */
    protected function getNavigation($module = null) {
        if ($module == null) {
            $module = 'default';
        }
        $pages = Default_Model_Navigation::getMenu($module);
        
        $navigation = new Zend_Navigation($pages);
        
        return $navigation;
    }
    
        
    /**
     * On injecte un traitement avant le dispatch de la requête
     * afin de récupérer le menu du module.
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request) {     
        if (!Zend_Session::isReadable()) {
            Zend_Session::start();
        }

        $moduleName = $request->getModuleName();
        $nsModule = null;
        $navigation = null;
        
        if (Zend_Session::namespaceIsset($moduleName)) {
            $nsModule = Zend_Session::namespaceGet($moduleName);
            if (isset($nsModule["navigation"])) {
                $navigation = $nsModule['navigation'];
            }
        }
        if ($navigation === null) {
            $navigation = $this->getNavigation($moduleName);
            if ($nsModule === null) {
                $nsModule = new Zend_Session_Namespace($moduleName);
            }
            $nsModule->navigation = $navigation;
        }
        $acl = Zend_Registry::get('Zend_Acl');
        Zend_View_Helper_Navigation_HelperAbstract::setDefaultAcl($acl);
        
        $user = Default_Model_DbTable_User::getCurrentUser();
        if ($user !== null) {
            $role = $user->getRole();
        }
        else {
            $role = 'guest';
        }
        Zend_View_Helper_Navigation_HelperAbstract::setDefaultRole($role);
        
        Zend_Registry::set('Zend_Navigation', $navigation);
    }

}   