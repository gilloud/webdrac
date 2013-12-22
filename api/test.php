<?php
require 'lib/webdraclib.php';

$webdraclib = new WebdracLib('localhost','root','','webdrac');

$result = array();
echo "<h1>Personal unit testing framwork</h1>";
echo "<h3>Authentication unit test</h3>";
echo "<ul>";
if($webdraclib->authentication('gilles','password'))
{
        echo "<li>Test #1 : OK | Asset true | good user/pass</li>";
}else{
        echo "<li>Test #1 : KO | Asset true | good user/pass</li>";
}
if( ! $webdraclib->authentication('gilles','fake_password'))
{
        echo "<li>Test #2 : OK | Asset false | good user/ bad pass</li>";
}else{
        echo "<li>Test #2 : KO | Asset false | good user/ bad pass</li>";
}
if( ! $webdraclib->authentication('',''))
{
        echo "<li>Test #3 : OK | Asset false | no user/pass</li>";
}else{
        echo "<li>Test #3 : KO | Asset false | no user/pass</li>";
}
if( ! $webdraclib->authentication('gilles_fake','password_fake'))
{
        echo "<li>Test #4 : OK | Asset false | bad user/bad pass</li>";
}else{
        echo "<li>Test #4 : KO | Asset false | bad user/bad pass</li>";
}
if( ! $webdraclib->authentication('toto','password'))
{
        echo "<li>Test #5 : OK | Asset false | bad user/existing pass</li>";
}else{
        echo "<li>Test #5 : KO | Asset false | bad user/existing pass</li>";
}
echo "</ul>";

echo "<h3>Upgrade managment unit test</h3>";
echo "<ul>";
if($webdraclib->need_upgrade('{"name":"planiculte_ut","version":"10.0"}'))
{
        echo "<li>Test #1 : OK | Asset true | name_exist and md5 different</li>";
}else{
        echo "<li>Test #1 : KO | Asset true | name_exist and md5 different</li>";
}
if( ! $webdraclib->need_upgrade('{"name":"planiculte_ut","version":"1.0"}'))
{
        echo "<li>Test #2 : OK | Asset false | name_exist and md5 unchanged</li>";
}else{
        echo "<li>Test #2 : KO | Asset false | name_exist and md5 unchanged</li>";
}
if($webdraclib->need_upgrade('{"name":"planiculte_ut_2","version":"1.0"}'))
{
        echo "<li>Test #3 : OK | Asset false | install new application</li>";
}else{
        echo "<li>Test #3 : KO | Asset false | install new application</li>";
}
echo "</ul>";
