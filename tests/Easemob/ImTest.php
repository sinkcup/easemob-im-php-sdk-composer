<?php
require_once __DIR__ . '/../autoload.php';
class ImTest extends PHPUnit_Framework_TestCase
{
    private $conf = array(
        'client_id' => 'change me',
        'client_secret' => 'change me',
        'org_name' => 'change me',
        'app_name' => 'change me',
    );

    public function testRegister()
    {
        $c = new \Easemob\Im($this->conf);
        try{
            $r = $c->register('u' . time(), '123456');
            $this->assertEquals(true, isset($r['entities']));
        } catch (\Exception $e) {
            echo $e->getCode();
            echo $e->getMessage();
        }
    }

    public function testAddFriend()
    {
        $c = new \Easemob\Im($this->conf);
        try{
            $r = $c->addFriend('c2', 'c1');
            $this->assertEquals(true, isset($r['entities']));
        } catch (\Exception $e) {
            echo $e->getCode();
            echo $e->getMessage();
        }
    }
}
