<?php

/**
 * @package Sea_Controller_Plugin
 * @name /Sea/Controller/Plugin/Auth.php
 * @version 1.0
 * @author Pierre-Yves Aillet
 * @since 11/01/2010
 * Ce plugin vérifie si l'utilisateur est authentifié et le redirige vers la
 * page d'authentification dans le cas contraire.
 * 
 */
class Sea_Controller_Plugin_Auth extends Zend_Controller_Plugin_Abstract
{
    /**
     * Tableau contenant la liste des actions ne nécessitant pas 
     * d'authentification
     *
     * @var Array Tableau des pages
     */
    private $_noauth = array( 
        array(
            'module'        => 'default',
            'controller'    => 'login',
            'action'        => 'index',
        ),
        array(
            'module'        => 'default',
            'controller'    => 'login',
            'action'        => 'submit',
        ),
        array(
            'module'        => 'default',
            'controller'    => 'error',
            'action'        => 'error',
        ),
        array(
            'module'        => 'default',
            'controller'    => 'ws',
        ),
    );
    
    /**
     * Permet de vérifier si la page demandée requiert une authentification 
     * valide
     *
     * @param Zend_Controller_Request_Abstract $request Requête courante
     * @return bool Vraie si l'authentification est requise pour cette 
     * requête
     */
    public function isAuthRequired(Zend_Controller_Request_Abstract $request) {
        $bAuthRequired = true;
        foreach($this->_noauth as $page) {
            $bAuthRequired &= 
                    ((($page['module'] != $request->getModuleName()) && (isset($page['module'])))
                    || (($page['controller'] != $request->getControllerName()) &&(isset($page['controller'])))
                    || (($page['action'] != $request->getActionName()) && (isset($page['action']))));
        }
        return $bAuthRequired;
    }
    
    /**
     * On injecte un traitement qui vérifie si l'authentification est requise
     * pour la requête, et si l'utilisateur est authentifié.
     * Dans le cas contraire on redirige vers la page d'authentification.
     *
     * @param Zend_Controller_Request_Abstract $request Requête courante
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        if ($this->isAuthRequired($request)) {
            $auth = Zend_Auth::getInstance();
            $auth->setStorage(new Zend_Auth_Storage_Session('auth_ldap'));
            if (!$auth->hasIdentity()) {
                $redirector =
    Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
                $redirector->direct('index', 'login', 'default');
            }
        }
    }
}
