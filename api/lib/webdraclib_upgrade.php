<?php

namespace WebdracLib_upgrade;

class WebdracLib_upgrade extends Database
{
    public function __construct($server,$user,$pass,$database) {
        parent::__construct($server,$user,$pass,$database);
    } 

    function table($json_o)
    {
        $v_sql = '';
        $v_foreign = '';
        foreach($json_o->objects as $object_name => $details)
        {
            $table_name = 'data_'.$json_o->name.'_'.$object_name;

            $v_sql .= 'CREATE TABLE '.$table_name.'(id INT NOT NULL AUTO_INCREMENT) COMMENT "'.$json_o->description.'";';
            $v_sql .= 'ALTER TABLE '.$table_name.' ADD PRIMARY KEY (id);';
            $v_sql .= 'ALTER TABLE '.$table_name.' ADD UNIQUE  KEY (id);';
            foreach($details->attributes as $column_name => $column_details)
            {
                $add_index = false;
                $mysql_type = "";
                switch($column_details->type)
                {
                    case 'integer':
                        $sum = 0;
                        $signed = false;
                        if($column_details->parameters->min < 0)
                        {
                            $sum += $column_details->parameters->min;
                            $sum += $column_details->parameters->max;
                            $signed = true;
                        }else
                        {
                            $sum += $column_details->parameters->max;
                            $signed = false;
                        }
                        switch($sum)
                        {
                            case $sum <pow(2,1*8):
                                $mysql_type = 'tinyint';
                                break;
                            case $sum < pow(2,2*8):
                                $mysql_type = 'smallint';
                                break;
                            case $sum < pow(2,3*8):
                                $mysql_type = 'mediumint';
                                break;
                            case $sum < pow(2,4*8):
                                $mysql_type = 'int';
                                break;
                            case $sum < pow(2,8*8):
                                $mysql_type = 'bigint';
                                break;
                        }
                        $v_sql .= "ALTER TABLE ".$table_name." ADD ".$column_name." ".$mysql_type.' COMMENT "'.$column_details->description.'";';

                        break;

                    case 'string':
                        $mysql_type = 'varchar('.$column_details->parameters->length.')';
                        $v_sql .= "ALTER TABLE ".$table_name." ADD ".$column_name." ".$mysql_type.' COMMENT "'.$column_details->description.'";';

                        break;
                    case 'link':
                        $mysql_type = 'int';
                        $v_sql .= "ALTER TABLE ".$table_name." ADD ".$column_name." ".$mysql_type.' COMMENT "'.$column_details->description.'";';
                        $v_sql .= 'ALTER TABLE '.$table_name.' ADD INDEX ('.$column_name.');';
                        $v_sql .= 'CREATE TABLE link_'.$json_o->name.'_'.$object_name.'_'.$column_details->parameters->linked_object.' (local_id INT, linked_id INT) COMMENT "Link between '.$object_name.' and '.$column_details->parameters->linked_object.'";';

                        $v_foreign .= 'ALTER TABLE link_'.$json_o->name.'_'.$object_name.'_'.$column_details->parameters->linked_object.' ADD CONSTRAINT fk_'.$json_o->name.'_'.$object_name.'_'.$column_details->parameters->linked_object.'_local FOREIGN KEY (local_id) references '.$table_name.' (id);';
                        $v_foreign .= 'ALTER TABLE link_'.$json_o->name.'_'.$object_name.'_'.$column_details->parameters->linked_object.' ADD CONSTRAINT fk_'.$json_o->name.'_'.$object_name.'_'.$column_details->parameters->linked_object.'_linked FOREIGN KEY (linked_id) references '.$table_name.' (id);';

                        break;
                    break;
                    default:
                        //TODO : log the error
                    break 2;
                }
            }
        }
        
        echo $v_sql.$v_foreign;
        $this->transaction_start();
        if($this->execute($v_sql.$v_foreign))
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
}