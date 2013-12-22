<?php
namespace WebDrac;

require_once 'Database.php';



/*
    Vocabulary :
    action_id : id of action
*/

use Database;

class Action extends Database{

    private $application;
    private $object;
    private $data_table;

    function __construct()
    {
                parent::__construct();


    }
    function ConstructwithContext($application,$entity)
    {

        $this->application = $application;
        $this->entity = $entity;

        $this->data_table = 'data_'.$application.'_'.$entity;
    }

    function perform($object_id,$action)
    {

        //@TODO : verify the from of the action
        //@TODO : verify the capacity to do the action

        $status = $this->query("select t.id from wd_status t where t.name='$action'");
        
        if(isset($status[0]['id']) and (int)$status[0]['id'] > 0)
        {
            $status_id = $status[0]['id'];

            $sql = "update $this->data_table t set status=$status_id where t.id=$object_id";
            $this->execute($sql);
            return true;
        }

        return false;
    }

}