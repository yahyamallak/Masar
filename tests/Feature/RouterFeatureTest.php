<?php declare(strict_types=1);

use Masar\Exceptions\NotFoundException;
use Masar\Http\Request;
use Masar\Routing\Router;
use PHPUnit\Framework\TestCase;

class RouterFeatureTest extends TestCase {


    public function test_simple_routing() {

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

        $id = "5";

        $request = $this->createMock(Request::class);
        $request->method('getUrl')->willReturn("/posts/{$id}");
        $request->method('getMethod')->willReturn('GET');

        ob_start();
        $router->dispatch($request);
        $output = ob_get_clean();

        $this->assertEquals("Hello from post : {$id}", $output);
    
    
    }


    public function test_routes_parameters_rules() {
        $router = new Router();

        $router->get('/posts/{id}', function($id) {
            return 'Hello from post : ' . $id;
        })->where(["id"=>":number"]);

        $id = "post";

        $request = $this->createMock(Request::class);
        $request->method('getUrl')->willReturn("/posts/{$id}");
        $request->method('getMethod')->willReturn('GET');
        
        $this->expectException(NotFoundException::class);
        
        $router->dispatch($request);
    }

}