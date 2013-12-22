<?php

require '../src/lib/webdraclib.php';

header('Content-Type: text/html; charset=utf-8');

// <= PHP 5
$file = file_get_contents('http://localhost/webdrac/applications/planiculte/planiculte.json');

$file = json_decode($file);

$webdraclib = new WebdracLib('localhost','root','','webdrac');

$webdraclib->doing_get_data('planiculte','capacite','');