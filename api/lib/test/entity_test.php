<?php

namespace webdrac\test\units;

require_once __DIR__.'/../../../test/api/mageekguy.atoum.phar';

include __DIR__.'/../entity_ns.php';

use \mageekguy\atoum;
use webdrac;

class entity extends atoum\test
{
    public function test_bonjour()
    {
        $hello = "Hello World!";

        $this->string($hello)->isEqualTo('Hello World!');

    }

    public function test_get_all()
    {
        $entity = new webdrac\entity();
        $entity->ConstructwithContext('planiculte','capacite');

        $objects = $entity->get_all();
        
        $this->integer(count($objects))->isGreaterThan(0);
    }

    public function test_get()
    {
        $entity = new webdrac\entity();
        $entity->ConstructwithContext('planiculte','capacite');

        $objects = $entity->get_all();

        $e = $entity->get($objects[0]['id']);
        
        $this->integer(intval($e['id']))->isGreaterThanOrEqualTo(0);
    }
}