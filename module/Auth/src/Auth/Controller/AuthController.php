<?php

namespace Auth\Controller;

use \Zend\Mvc\Controller\AbstractActionController,
\Zend\View\Model\ViewModel,
\Zend\Session\Container;

class AuthController extends AbstractActionController
{
    
    private function getUserModel()
    {      
        $sm = $this->getServiceLocator();
        
        return new \Auth\Model\User( $sm->get('Zend\Db\Adapter\Adapter') );
    }
    
    private function setUserSeesion($data)
    {
        $db = $this->getUserModel();
                
        $user_data = $db->getUserData($data);

        if($user_data->ADMIN_USER == 'Y')
        {

            $user_session = new Container('login_user');
            $user_session->USER_NAME  = $user_data->USER_NAME;
            $user_session->EMAIL      = $user_data->EMAIL;
            $user_session->ADMIN_USER = $user_data->ADMIN_USER;
        }else{

            $user_session = new Container('login_user');
            $user_session->USER_NAME  = $user_data->USER_NAME;
            $user_session->EMAIL      = $user_data->EMAIL;
            //$user_session->ADMIN_USER = $user_data->ADMIN_USER;
        }
        
    }
    
    private function setUserLogin(){
        
        $db = $this->getUserModel();
        
        $user_name = $this->getRequest()->getPost('USER_NAME');
        $password  = $this->getRequest()->getPost('PASSWORD');
        
        if( isset($user_name) && !empty($user_name) )
        {

            $data = new \stdClass();
            $data->USER_NAME = $user_name;
            $data->PASSWORD  = $password;

            $login = $db->validateUser($data);

            if($login == TRUE)
            {
                echo 'User Validated';
                
                $this->setUserSeesion($data);
            }else{

                echo 'User not Validated';
            }
        }
    }
    
    public function indexAction()
    {
        //$db = $this->getUserModel();
        
        $user_session = new Container('login_user');
        
        print_r($user_session->USER_NAME);
        
        $post_captcha = $this->getRequest()->getPost('captcha');
        
        $captcha = new \Zend\Captcha\Image();
        
        $captcha->setWordLen(4)
        ->setHeight(60)
        ->setFont('/public/fonts/arial.ttf')
        ->setImgDir('public/images/captcha/login')
        ->setDotNoiseLevel(5)
        ->setExpiration(1)        
        ->setLineNoiseLevel(5);
        
        $captcha->getExpiration();

        if ( isset($post_captcha) && !empty($post_captcha) )
        {
            
            if ( $captcha->isValid($post_captcha) )
            {
                
                $this-> setUserLogin();
            }else{
                
                echo "Failed!";
            }    
        }
        
        $id = $captcha->generate();
       
        return new ViewModel(array(
            //'Test' => $db->test(64),
            'CaptchaID' => $id,
        ));
    }
    
    public function newuserAction()
    {
        //$db = $this->getUserModel();
        
        $captcha = new \Zend\Captcha\Image();
        
        $captcha->setWordLen(4)
        ->setHeight(60)
        ->setFont('/public/fonts/arial.ttf')
        ->setImgDir('public/images/captcha/signup')
        ->setDotNoiseLevel(5)
        ->setExpiration(1)
        ->setLineNoiseLevel(5);
        
        $captcha->getExpiration();

        if ( isset($_POST['captcha']) && !empty($_POST['captcha']) )
        {
            
            if ( $captcha->isValid($_POST['captcha']) )
            {
                echo "Success!";
                exit(0);
            }
            else
            {
                echo "Failed!";
            }    
        }
        
        $id = $captcha->generate();
       
        return new ViewModel(array(
            
            'CaptchaID' => $id,
        ));
    }
}