<?php

namespace Auth\Controller;

use \Zend\Mvc\Controller\AbstractActionController,
\Zend\Session\Container,
\Zend\Form\Annotation\AnnotationBuilder,
\Auth\Model\Login,
\Auth\Model;

class AuthController extends AbstractActionController
{

    private $db;


    public function setDatabase( ){

        $this->db = $this->getServiceLocator()->get('User');
    }
    
    private function setUserSession($data)
    {

        $user_data = $this->db->getUserData($data);

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

        $user_name = $this->getRequest()->getPost('username');
        $password  = $this->getRequest()->getPost('password');
        
        if( isset($user_name) && !empty($user_name) )
        {

            $data = new \stdClass();
            $data->USER_NAME = $user_name;
            $data->PASSWORD  = $password;

            $login = $this->db->validateUser($data);

            if($login == TRUE)
            {

                
                $this->setUserSession($data);

                return $this->redirect()->toRoute('application',
                    array('controller'=>'Application\Controller\Index', 'action'=>'index'));
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

        $this->db = $this->getServiceLocator()->get('User');

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

            $data2 = new \stdClass();
            $data2->USER_NAME = $user_name;
            $data2->PASSWORD  = $password;

            $this->db->newUser($data);

            $this->setUserSession($data2);

           return $this->redirect()->toRoute('application',
               array('controller'=>'Application\Controller\Index', 'action'=>'index'));
        }
        else
        {
            echo "Failed!";
        }     
    }
    
    public function indexAction()
    {

       $this->db = $this->getServiceLocator()->get('User');
        
        $user_session = new Container('login_user');
        
       // print_r($user_session->USER_NAME);
        
        $login      = new Login();
        $builder    = new AnnotationBuilder();
        $form       = $builder->createForm($login);
         
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

    public function logoutAction()
    {

        $this->db = $this->getServiceLocator()->get('User');

        $user_session = new Container('login_user');

       $user_session->getManager()->getStorage()->clear('login_user');

        return $this->redirect()->toRoute('application',
            array('controller'=>'Application\Controller\Index', 'action'=>'index'));

        //$response = $this->getResponse();
        //$response->setStatusCode(200);
        //$response->setContent("Hello World");
        //return $response;

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