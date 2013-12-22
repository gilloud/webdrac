<?php
namespace WebDrac;

require_once 'Database.php';

use Database;

class User extends Database{

    function __construct()
    {
        parent::__construct();
    }
    
    public function authenticate($username,$password)
    {

        if($this->query("   SELECT 1
                            FROM data_core_users t
                            WHERE t.username = '$username'
                            AND t.password = '$password'"))
        {
            return true;
        }else{
            return false;
        }
    }

    public function find($partial_user)
    {
        return $this->query("SELECT id,username,upper(name) as name,CONCAT(UCASE(LEFT(surname, 1)),SUBSTRING(surname, 2)) as surname FROM `data_core_users` WHERE upper(name) like upper('%".$partial_user."%') or upper(surname) like upper('%".$partial_user."%') or upper(username) like upper('%".$partial_user."%')");
    }
}