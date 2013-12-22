<?php

namespace WebDrac;


class Filter_ns
{

    public static function getSqlFilter($body)
    {
        $array_filter = array();
        foreach($body->filters as $filter)
        {
            //var_dump($body->attributes->{$filter->attribute}->type);
            $type = $body->attributes->{$filter->attribute}->type;

            switch($type)
            {
                case 'datetime':
                    $_filter = '';
                    $_filter .= $filter->attribute.' ';
                    switch ($filter->operator)
                    {
                        case 'upper':
                            $_filter .= '>';
                            break;
                        case 'lower':
                            $_filter .= '<';
                            break;
                        case 'equal':
                            $_filter .= '=';
                            break;
                    }
                    switch ($filter->value)
                    {
                        case 'today':
                        case 'now';
                            $_filter .= 'now()';
                            break;
                    }

                    $array_filter[] = $_filter;
            }
        }

        //Concatenator :
        $sql_filter = '';
        if(count($array_filter)>0)
        {
            $sql_filter .= ' and ';
        }
        
        foreach($array_filter as $k=>$f)
        {
            
            $sql_filter .= $f;
        }
        
        return $sql_filter;
    }
}