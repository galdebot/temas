<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;


use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel,
        \Zend\Session\Container;
use \Exception;

class IndexController extends AbstractActionController
{
    public function indexAction()
    { 
        $user_session = new Container('login_user');
        
        $container = $this->getServiceLocator()->get('Config');
        
        //print_r($container);

        return new ViewModel(array(
                
            'User_Name' => $user_session->USER_NAME,
        ));
    }
    
    public function subpagesAction()
    {
        return new ViewModel();
    }
    
    public function contactAction()
    {
        $user_session = new Container('login_user');
        
        try
        {
            $sftp = new \Application\Model\Sftp("temas.sandbox", 22);
            $sftp->login("galdebot", "JOhn1983*");
            //$sftp->uploadFile("/tmp/to_be_sent", "/tmp/to_be_received");
            ///home/galdebot/Desktop/test.txt
            $file = '/home/galdebot/Desktop/test.txt';
            echo $sftp->getFileSize($file);
            
            //print_r($sftp);
        }
        catch (Exception $e)
        {
            echo $e->getMessage() . "\n";
        }
        
        
        
        //print_r($user_session->USER_NAME)
        
        return new ViewModel();
    }
}
