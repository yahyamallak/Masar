<?php declare(strict_types=1);

use Masar\Http\Response;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase 
{

    private $response;
    public function setUp(): void {
        $this->response = new Response();
    }

    public function test_set_response_content() {


        $this->response->content('Hello world');

        $this->assertEquals('Hello world', $this->response->getContent());
    }


    public function test_set_response_status_code() {
    
        $this->response->status(200);

        $this->assertEquals(200, $this->response->getStatus());
    }


    public function test_send_response() {
        

        $this->response->content("Welcome to Masar");
        $this->response->status(200);


        $this->assertEquals('Welcome to Masar', $this->response->getContent());
        $this->assertEquals(200, $this->response->getStatus());
    }

}