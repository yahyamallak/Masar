<?php declare(strict_types=1);

use Masar\Exceptions\NotFoundException;
use Masar\Http\Request;
use Masar\Routing\Router;
use PHPUnit\Framework\TestCase;

class RouterFeatureTest extends TestCase {


    public function testSimpleRouting() {

        $router = new Router();

        $router->get('/about', function() {
            return 'Hello from about page';
        });

        $request = $this->createMock(Request::class);
        $request->method('getUrl')->willReturn('/about');
        $request->method('getMethod')->willReturn('GET');

        ob_start();
        $router->dispatch($request);
        $output = ob_get_clean();

        $this->assertEquals('Hello from about page', $output);
    }


    public function test_route_not_found() {
        
        $router = new Router();

        $router->get('/about', function() {
            return 'Hello from about page';
        });

        $request = $this->createMock(Request::class);
        $request->method('getUrl')->willReturn('/');
        $request->method('getMethod')->willReturn('GET');

        $this->expectException(NotFoundException::class);

        $router->dispatch($request);

    }


    public function test_routes_with_parameters() {
        $router = new Router();

        $router->get('/posts/{id}', function($id) {
            return 'Hello from post : ' . $id;
        });

        $post = "1";

        $request = $this->createMock(Request::class);
        $request->method('getUrl')->willReturn("/posts/{$post}");
        $request->method('getMethod')->willReturn('GET');


        ob_start();
        $router->dispatch($request);
        $output = ob_get_clean();


        $this->assertEquals("Hello from post : {$post}", $output);
    }

}