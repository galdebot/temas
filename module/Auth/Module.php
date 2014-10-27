<?php
namespace Auth;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
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
                
                'User_Session' => function(){
                    
                    $login = new Container('login_user');
                    return  $login;
                }
            )
        );
    }
}
