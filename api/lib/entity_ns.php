<?php

namespace WebDrac;

require_once 'Database.php';
require_once 'action_ns.php';
require_once 'KLogger.php';
require_once 'filter_ns.php';

/*
    Vocabulary :
    entity : regroupment of attributs

    object : associative array with all metadata of the element
    application : software of the program

*/

use Database;
use KLogger;
use Filter;

class Entity extends Database{

    private $application;
    private $entity;
    private $data_table;
    private $constructed = false;
    private $log;

    function __construct()
    {
                parent::__construct();
                $this->log = KLogger::instance('../logs/', KLogger::DEBUG);
                $this->log->logDebug('x = 5'); 



    }
    function ConstructwithContext($application,$entity)
    {

        $this->application = $application;
        $this->entity = $entity;

        $this->data_table = 'data_'.$application.'_'.$entity;
    }

    /*
        return objectId
    */
    function createOrUpdate($data) {

        if(isset($data->id))
        {
            //update
            //...
        }else{
            //create
            $sql = "INSERT INTO ";
            $sql .= "$this->data_table (";
            foreach($data->attributes as $attribute)
            {
                $sql .= $attribute->name;
                $sql .= ',';
            }
            $sql = substr($sql ,0,-1);
            $sql .= ') values (';
            foreach($data->attributes as $attribute)
            {
                //Les nombres et les utilisateurs n'ont pas besoin de quotes !
                if(in_array($attribute->type,array('number','user')))
                {
                    $sql .= $attribute->value;
                }else
                {
                    $sql .= '"'.$attribute->value.'"';
                }

                $sql .= ',';
            }
            $sql = substr($sql ,0,-1);
            $sql .= ');';
            $this->transaction_start();
            $this->log->logDebug('Sql request : '.$sql);
            if($this->execute($sql))
            {
                $object_id =  $this->last_id();
                $this->log->logDebug('Seems ok !');
                $this->transaction_finish();

                $rtn = array();
                $rtn['status'] = 'ok';
                $rtn['value'] = $object_id;
                return $rtn;
            }
            else
            {
                $this->log->logDebug('Seems ko !');
                $this->transaction_rollback();
                $rtn = array();
                $rtn['status'] = 'ko';
                return $rtn;
            }            

        }

        $action = new Action();
        $action->ConstructwithContext($this->application,$this->entity);
       // $action->perform($object_id,);


        return $object_id;
    }

    /*
        $depth : depth of the request (-1 for an unlimited request) @TODO : Implement
    */
    function get($object_id,$depth=0)
    {
        $element = $this->query("select '$this->application' as _application,'$this->entity' as _entity, t.* from $this->data_table t where t.id = $object_id");
        return $element[0];
    }
    function getListWithFilters($list,$body,$depth=0)
    {   

        $sql_columns = "";
        foreach ($body->columns as $column) {
            var_dump($column);
           
            break;
            if($column->parameters->source == "db")
            {
                var_dump($column->parameters->source );
                if(strlen($sql_columns)>0)
                    $sql_columns .= ', ';
                $sql_columns .= 't.'.$column->title;
            }
        }

        $sql = "select '$this->application' as _application,'$this->entity' as _entity,$sql_columns from $this->data_table t where 1=1 ";

        $sql .= Filter_ns::getSqlFilter($body);
        var_dump($sql);
        $element = $this->query($sql);
        return $element;
    }


    /*
        $depth : depth of the request (-1 for an unlimited request) @TODO : Implement
    */
    function get_all($filters=0,$depth=0)
    {
        $elements = $this->query("select '$this->application' as _application,'$this->entity' as _entity,t.* from $this->data_table t");
        return $elements;
    }



}