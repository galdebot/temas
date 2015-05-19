<?php
namespace Auth;

use Zend\Session\Container;

class Module
{
    public function getConfig()
    {

        $user_session = new Container('login_user');

        if( !isset($user_session->USER_NAME) ){

            return include __DIR__ . '/config/module.config.php';
        }else{

            return include __DIR__ . '/config/module2.config.php';
        }

    }

    public function getAutoloaderConfig()
    {
        return array(

            'Zend\Loader\StandardAutoloader' => array(

                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    public function getServiceConfig(){

        return array(

            'factories' => array(
                
                'User' => function($sm){
                    
                    $db = new \Auth\Model\User($sm->get('Zend\Db\Adapter\Adapter'));
                    return $db;
                },
            ),

            'navigation' => array(),
        );
    }
}
