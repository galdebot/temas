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

class IndexController extends AbstractActionController
{
    public function indexAction()
    { 
        $user_session = new Container('login_user');

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
        
        print_r($user_session->USER_NAME);
        return new ViewModel();
    }
}
