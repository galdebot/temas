<?php

namespace Auth\Model;

use Zend\Db\Adapter\Adapter;

class User 
{  
    private $adapter;
    CONST SALT = 'Bible';

    public function __construct(Adapter $adapter)
    {
        
       $this->adapter = $adapter;
    }
    
    private function passwordSalt($data)
    {
        $salt = \sha1($data);
        return \sha1($salt.$data.self::SALT);
    }
    
    private function newUserSeq()
    {
        $stmt = $this->adapter->createStatement();
        
        $sql = "
        SELECT users_id_seq.nextval FROM dual;
        ";
        
        $stmt->prepare($sql);
        
        $result = $stmt->execute();
        
        $num = $result->current();
        
        return $num->NEXTVAL;
    }
    
    public function newUser($data)
    {
        
        $stmt = $this->adapter->createStatement();
        
        $sql = "
        INSERT INTO 
            (USER_ID,USER_NAME,PASSWORD,ADMIN_USER,EMAIL)
        VALUES
            (':NewUserSeq',':User_Name',':Password','N',':Email')
        ";
        
        $bind = array(
            
            'NewUserSeq' => (INT)$this->newUserSeq,
            'User_Name'  => (STRING)$data->USER_NAME,
            'Password'   => $this->passwordSalt( (STRING)$data->PASSWORD ),
            'Email'      => (STRING)$data->EMAIL,
        );
        
        $stmt->prepare($sql);
        
        $stmt->execute($bind);  
    }
    
    public function test($data)
    {
        
        $stmt = $this->adapter->createStatement();
        
        $sql = "
        SELECT count(BOOKID) AS BOOK_COUNT 
        FROM BIBLEDB_KJV
        WHERE BOOKID = :BOOKID
        ";
        
        //array of values o bind
        $bind = array(
            'BOOKID' => (INT)$data,
        );
        
        $stmt->prepare($sql);
        
        //Bind values to execute
        $result = $stmt->execute($bind);
        
        return $result;
    }
}