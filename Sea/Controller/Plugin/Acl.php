<?php

/**
 * @package Sea_Controller_Plugin
 * @name /Sea/Controller/Plugin/Acl.php
 * @version 1.0
 * @author Pierre-Yves Aillet
 * @since 20/01/2010
 * Ce plugin vérifie les droits
 * 
 */
class Sea_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{
    /**
     * Ce plugin vérifie si l'accès à la ressource associée à la requête est
     * autorisé pour l'utilisateur courant.
     * Dans le cas contraire on log la tentative d'accès et on redirige vers
     * une page 403 Forbidden.
     *
     * @param Zend_Controller_Request_Abstract $request Requête courante
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request) 
    {
        $action = Default_Model_DbTable_Action::getAction(
            $request->getActionName(),
            $request->getControllerName(),
            $request->getModuleName()
        );
        if ($action !== null) {
            $acl = Zend_Registry::get('Zend_Acl');
            if (Default_Model_DbTable_User::getCurrentUser() !== null) {
                $role = Default_Model_DbTable_User::getCurrentUser()->getRole();
            }
            else {
                $role = 'guest';
            }
            $resource = $action->findParentRow('Default_Model_DbTable_Resource')->name;
            if (($resource != null) && (!$acl->isAllowed($role, $resource))) {
                $date = new Zend_Date();
                Zend_Registry::get('log')->info(
                    $date->toString("YYYY-MM-dd HH:mm:ss")." ".$role." tried to access ".$resource);
                $request->setModuleName('default');
                $request->setControllerName('error');
                $request->setActionName('forbidden');
            }
        }
    }
}