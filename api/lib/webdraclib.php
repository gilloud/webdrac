<?php

require 'database.php';
require 'WebdracLib_upgrade.php';



class WebdracLib extends Database
{
    public function __construct($server,$user,$pass,$database) {
        $this->server = $server;
        $this->user = $user;
        $this->pass = $pass;
        $this->database = $database;
        
        parent::__construct($server,$user,$pass,$database);
    } 


    public function authentication($username,$password)
    {

        if($this->query("   SELECT 1
                            FROM users t
                            WHERE t.username = '$username'
                            AND t.password = '$password'"))
        {
            return true;
        }else{
            return false;
        }
    }

    public function need_upgrade($json)
    {
        $doing_upgrade = false;

        if(($json_o = json_decode($json))==null)
        {
            $doing_upgrade = false;
        }
        $application_parameters = $this->query("select t.* from applications t where t.name='{$json_o->name}'");

        if(count($application_parameters)==0)
        {
            $doing_upgrade = true;
        }else
        {
            $application_parameters = $application_parameters[0];
            if(md5($json) != $application_parameters['md5'])
            {
                $doing_upgrade = true;
            }
        }
        
        if($doing_upgrade)
        {
            return true;
        } 
        return false;

    }

    public function doing_upgrade($json)
    {
        if( ! $this->need_upgrade($json))
        {
            return false;
        }

        $json_o = json_decode($json);

        //Table management
        $WDL_upgrade = new WebdracLib_upgrade::WebdracLib_upgrade($this->server,$this->user,$this->pass,$this->database);
        return $WDL_upgrade->table($json_o);

    }

    public function doing_createorupdate($json)
    {
        $json_o = json_decode($json);

        if(isset($json_o->id))
        {
            //update
            //...
        }else{
            //create
            $sql = "INSERT INTO ";
            $tablename = 'data_'.$json_o->application_name.'_'.$json_o->object;
            $sql .= $tablename.'(';
            foreach($json_o->attributes as $attribute)
            {
                $sql .= $attribute->name;
                $sql .= ',';
            }
            $sql = substr($sql ,0,-1);
            $sql .= ') values(';
            foreach($json_o->attributes as $attribute)
            {
                $sql .= '"'.$attribute->value.'"';
                $sql .= ',';
            }
            $sql = substr($sql ,0,-1);
            $sql .= ');';
            $this->transaction_start();
            if($this->execute($sql))
            {
                $this->transaction_finish();
                return true;
            }
            else
            {
                $this->transaction_rollback();
                return false;
            }            

        }
        return false;

    }

    public function doing_get_datas($application,$object,$json)
    {
        if(($json_o = json_decode($json))!=null)
        {
            //manage filters
        }
        
        $tablename = 'data_'.$application.'_'.$object;
        $datas = $this->query("select t.* from ".$tablename." t");
        return json_encode($datas);
    }

    public function doing_get_data($json)
    {
        $json_o = json_decode($json);
        
        $tablename = 'data_'.$json_o->application.'_'.$json_o->object;
        $datas = $this->query("select t.* from ".$tablename." t where t.id=".$json_o->id);

        return json_encode($datas[0]);
    }

}