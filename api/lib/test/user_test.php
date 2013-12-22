<?php

namespace webdrac\test\units;

require_once __DIR__.'/../../../test/api/mageekguy.atoum.phar';

include __DIR__.'/../user_ns.php';

use \mageekguy\atoum;
use webdrac;

class user extends atoum\test
{
    public function test_bonjour()
    {
        $hello = "Hello World!";

        $this->string($hello)->isEqualTo('Hello World!');

    }

    public function test_authenticate()
    {
        $user = new WebDrac\User();
        
        $this->boolean($user->authenticate('gilles','password'))->isTrue();
        $this->boolean($user->authenticate('gilles','fake_password'))->isFalse();
        $this->boolean($user->authenticate('',''))->isFalse();
        $this->boolean($user->authenticate('fake_login','fake_password'))->isFalse();
        $this->boolean($user->authenticate('fake_login','password'))->isFalse();
    }
}