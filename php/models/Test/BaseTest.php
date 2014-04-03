<?php

namespace models;

class BaseTest extends \PHPUnit_Framework_TestCase {

	public function testDatabaseConnection() {
        $this->assertObjectHasAttribute('db', new Base);
    }

    public function testMemcacheConnection() {
        $this->assertObjectHasAttribute('memcache', new Base);
    }

}
