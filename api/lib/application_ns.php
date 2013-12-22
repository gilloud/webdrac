<?php

require_once 'Database.php';

use Database;

class Application extends Database{

    public static function getApplication($application,$uid)
    {
        $json = file_get_contents($json_url);
        $data = json_decode($json, TRUE);
    }
}