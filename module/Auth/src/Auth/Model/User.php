<?php

namespace Auth\Model;

use Zend\Db\Adapter\Adapter;

class User 
{  
    private $adapter;
    CONST SALT = 'Try_To_Guess';

    public function __construct(Adapter $adapter)
    {
        
       $this->adapter = $adapter;
    }
    
    private function passwordSalt($data)
    {
        $salt = \sha1($data);
        return \sha1($salt.$data.self::SALT);
    }
    
    public function newUserSeq()
    {
        $stmt = $this->adapter->createStatement();
        
        $sql = "
        SELECT users_pk_seq.nextval FROM dual
        ";
        
        $stmt->prepare($sql);
        
        $result = $stmt->execute();
        
        $num = (OBJECT)$result->current();
               
        return $num->NEXTVAL;
    }
    
    public function newUser($data)
    {
        
        $stmt = $this->adapter->createStatement();
        
        $sql = "
        INSERT INTO 
        USERS
            (USER_ID,USER_NAME,PASSWORD,ADMIN_USER,EMAIL,INSERT_DATE)
        VALUES
            (:NewUserSeq,:User_Name,:Password,:Admin_User,:Email, CURRENT_TIMESTAMP)
        ";
        
        $bind = array(
            
            'NewUserSeq' => (INT)$this->newUserSeq(),
            'User_Name'  => (STRING)$data->User_Name,
            'Password'   => $this->passwordSalt( (STRING)$data->Password ),
            'Admin_User' => 'N',
            'Email'      => (STRING)$data->Email,
        );
        
        $stmt->prepare($sql);
        
        $stmt->execute($bind);  
    }
    
    public function validateUser($data)
    {
        
        $stmt = $this->adapter->createStatement();
        
        $sql = "
        SELECT USER_NAME,PASSWORD 
        FROM USERS
        WHERE USER_NAME = :USER_NAME
        AND PASSWORD = :PASSWORD
        ";
        
        //array of values o bind
        $bind = array(
            'USER_NAME' => (STRING)$data->USER_NAME,
            'PASSWORD'  => $this->passwordSalt((STRING)$data->PASSWORD),
        );
        
        $stmt->prepare($sql);
        
        //Bind values to execute
        $result = $stmt->execute($bind);
        
        $value = (OBJECT)$result->current();
        
        $bool = ( !empty($value->USER_NAME) && !empty($value->PASSWORD) ? \TRUE : \FALSE );
        
        return $bool;
    }
    
    
    public function getUserData($data)
    {
        
        $stmt = $this->adapter->createStatement();
        
        $sql = "
        SELECT USER_NAME,EMAIL,ADMIN_USER 
        FROM USERS
        WHERE USER_NAME = :USER_NAME
        AND PASSWORD = :PASSWORD
        ";
        
        //array of values o bind
        $bind = array(
            'USER_NAME' => (STRING)$data->USER_NAME,
            'PASSWORD'  => $this->passwordSalt((STRING)$data->PASSWORD),
        );
        
        $stmt->prepare($sql);
        
        //Bind values to execute
        $result = $stmt->execute($bind);
        
        $values = (OBJECT)$result->current();
        
        return $values;
    }
}