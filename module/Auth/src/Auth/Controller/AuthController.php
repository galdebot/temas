<?php

namespace Auth\Controller;

use \Zend\Mvc\Controller\AbstractActionController,
\Zend\View\Model\ViewModel;

class AuthController extends AbstractActionController
{
    
    private function getUserModel()
    {      
        $sm = $this->getServiceLocator();
        
        return new \Auth\Model\User( $sm->get('Zend\Db\Adapter\Adapter') );
    }
    
    public function indexAction()
    {
        $db = $this->getUserModel();
        
        $captcha = new \Zend\Captcha\Image();
        
        $captcha->setWordLen(4)
        ->setHeight(60)
        ->setFont('/public/fonts/arial.ttf')
        ->setImgDir('public/images/captcha/login')
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
            //'Test' => $db->test(64),
            'CaptchaID' => $id,
        ));
    }
    
    public function newuserAction()
    {
        $db = $this->getUserModel();
        
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