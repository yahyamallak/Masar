<?php declare(strict_types=1);


use Masar\Http\Request;
use Masar\Routing\Route;
use PHPUnit\Framework\TestCase;
use Tests\ReflectionTrait;

class RouteTest extends TestCase 
{
    use ReflectionTrait;

    public function test_route_constructor() {
        
        $callback = function() {
            return "Hello world";
        };

        $route = new Route('GET', '/', $callback);

        $this->assertEquals('GET', $this->getPrivateProperty($route, "method"));
        $this->assertEquals('/', $this->getPrivateProperty($route, "path"));
        $this->assertEquals($callback, $this->getPrivateProperty($route, "callback"));

    }


    public function test_route_matches() {

        $callback = function() {
            return "Hello world";
        };

        $route = new Route('GET', '/', $callback);

        $request = $this->createMock(Request::class);
        $request->method('getUrl')->willReturn('/');
        $request->method('getMethod')->willReturn('GET');

        $this->assertTrue($route->matches($request));
    }

    public function test_route_does_not_match() {

        $callback = function() {
            return "Hello world";
        };

        $route = new Route('GET', '/', $callback);

        $request = $this->createMock(Request::class);
        $request->method('getUrl')->willReturn('/test');
        $request->method('getMethod')->willReturn('GET');

        $this->assertFalse($route->matches($request));
    }


    public function test_execute_route() {

        $callback = function() {
            return "Hello world";
        };

        $route = new Route('GET', '/', $callback);

        ob_start();
        $route->execute();
        $output = ob_get_clean();

        $this->assertEquals("Hello world", $output);
    }

}