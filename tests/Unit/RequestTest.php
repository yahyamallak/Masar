<?php declare(strict_types=1);

use Masar\Http\Request;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase 
{
   
    
    public function test_that_constructor_sets_url_and_method() {
        
        $_SERVER['REQUEST_URI'] = '/users';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $request = new Request();

        $this->assertEquals('/users', $request->getUrl());
        $this->assertEquals('GET', $request->getMethod());


    }

    public function test_that_we_can_get_requested_url() {

        $_SERVER['REQUEST_URI'] = '/users';
        
        $request = new Request();
        
        $this->assertEquals('/users', $request->getUrl());
        
    }
    
    
    public function test_that_we_can_get_http_method() {
        
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $request = new Request();

        $this->assertEquals('GET', $request->getMethod());
    }


    public function test_that_url_is_normalized() {

        $_SERVER['REQUEST_URI'] = '/users///';
        
        $request = new Request();

        $this->assertEquals('/users', $request->getUrl());

    }


    public function test_that_url_is_parsed() {
        $_SERVER['REQUEST_URI'] = '/users?p=5';
        
        $request = new Request();

        $this->assertEquals('/users', $request->getUrl());
    }

}