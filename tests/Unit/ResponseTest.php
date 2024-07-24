<?php declare(strict_types=1);

use Masar\Http\Response;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase 
{


    public function test_set_response_content() {

        $response = new Response();

        $response->content('Hello world');

        $this->assertEquals('Hello world', $response->getContent());
    }


    public function test_set_response_status_code() {
        
        $response = new Response();

        $response->status(200);

        $this->assertEquals(200, $response->getStatus());
    }


    public function test_send_response() {
        
        $response = new Response();

        $response->content("Welcome to Masar");
        $response->status(200);


        $this->assertEquals('Welcome to Masar', $response->getContent());
        $this->assertEquals(200, $response->getStatus());
    }

}