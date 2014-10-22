<?php

namespace Auth\Controller;

use \Zend\Mvc\Controller\AbstractActionController,
\Zend\View\Model\ViewModel,
\Zend\Session\Container,
\Zend\Form\Annotation\AnnotationBuilder,
\Auth\Model\Login;

class AuthController extends AbstractActionController
{
    
    private function getUserModel()
    {      
        $sm = $this->getServiceLocator();
        
        return new \Auth\Model\User( $sm->get('Zend\Db\Adapter\Adapter') );
    }
    
    private function setUserSession($data)
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
        
        $user_name = $this->getRequest()->getPost('username');
        $password  = $this->getRequest()->getPost('password');
        $captcha   = $this->getCaptcha();
        $post_captcha = $this->getRequest()->getPost('captcha');
        
        if( isset($user_name) && !empty($user_name) )
        {

            $data = new \stdClass();
            $data->USER_NAME = $user_name;
            $data->PASSWORD  = $password;

            $login = $db->validateUser($data);

            if($login == TRUE && $captcha->isValid($post_captcha))
            {
                echo 'User Validated';
                
                $this->setUserSession($data);
            }else{

                echo 'User not Validated';
            }
        }
    }
    
    private function getCaptcha()
    {
        
      $captcha = new \Zend\Captcha\Image();
        
        $captcha->setWordLen(4)
        ->setHeight(60)
        ->setFont('/public/fonts/arial.ttf')
        ->setImgDir('public/images/captcha')
        ->setDotNoiseLevel(5)
        ->setExpiration(1)        
        ->setLineNoiseLevel(5);
        
        $captcha->getExpiration();
        
        return $captcha;
    }
    
    private function addNewUser()
    {
        $db = $this->getUserModel();
  
        $user_name = $this->getRequest()->getPost('username');
        $password  = $this->getRequest()->getPost('password');
        $email     = $this->getRequest()->getPost('email');
        $post_captcha = $this->getRequest()->getPost('captcha');
        $captcha   = $this->getCaptcha();
        
        $data = new \stdClass();
        $data->User_Name = $user_name;
        $data->Password  = $password;
        $data->Email     = $email;
        
        if ( $captcha->isValid($post_captcha) )
        {
            echo "Success!";
            $db->newUser($data);
        }
        else
        {
            echo "Failed!";
        }     
    }
    
    public function indexAction()
    {
        
        $user_session = new Container('login_user');
        
        print_r($user_session->USER_NAME);
        
        $login      = new Login();
        $builder    = new AnnotationBuilder();
        $form       = $builder->createForm($login);
 
        $form->add(array(
            'type' => 'Zend\Form\Element\Captcha',
            'name' => 'captcha',
            'options'         => array(
                    'label'   => 'Please verify you are human',
                    'captcha' => $this->getCaptcha(),
            ), 
        ));
         
        $form->add(array(
            'name'       => 'submit',
            'attributes' => array(
                'type'   => 'submit',
                'value'  => 'Login',
                'id'     => 'submitbutton',
            ),
        ));
         
        $request = $this->getRequest();

        if ($request->isPost()){
            
            $form->bind($login);
            
            $form->setData($request->getPost());

                
            if ($form->isValid()){

                $this->setUserLogin();
            }  
        }
       
        return array('form'=>$form);
    }
    
    public function newuserAction()
    {
          
        $signup     = new \Auth\Model\Signup();
        $builder    = new AnnotationBuilder();
        $form       = $builder->createForm($signup);
 
        $form->add(array(
            'type' => 'Zend\Form\Element\Captcha',
            'name' => 'captcha',
            'options'         => array(
                    'label'   => 'Please verify you are human',
                    'captcha' => $this->getCaptcha(),
            ),
        ));
         
        $form->add(array(
            'name'       => 'submit',
            'attributes' => array(
                'type'   => 'submit',
                'value'  => 'Login',
                'id'     => 'submitbutton',
            ),
        ));
        
        $request = $this->getRequest();

        if ($request->isPost()){
            
            $form->bind($signup);
            
            $form->setData($request->getPost());
             
            if ($form->isValid()){

                $this->addNewUser();
            }  
        }

        return array('form'=>$form);
    }
}