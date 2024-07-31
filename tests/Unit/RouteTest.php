<?php declare(strict_types=1);


use Masar\Http\Request;
use Masar\Routing\Route;
use Masar\Routing\Router;
use PHPUnit\Framework\TestCase;
use Tests\ReflectionTrait;

class RouteTest extends TestCase 
{
    use ReflectionTrait;

    private $route;

    private $callback;

    public function setUp(): void {
        $this->callback = function() {
            return "Hello world";
        };

        $this->route = new Route('GET', '/', $this->callback);
    }
    public function test_route_constructor() {
        
        $this->assertEquals('GET', $this->getPrivateProperty($this->route, "method"));
        $this->assertEquals('/', $this->getPrivateProperty($this->route, "path"));
        $this->assertEquals($this->callback, $this->getPrivateProperty($this->route, "callback"));

    }


    public function test_route_matches() {

        $request = $this->createMock(Request::class);
        $request->method('getUrl')->willReturn('/');
        $request->method('getMethod')->willReturn('GET');

        $this->assertTrue($this->route->matches($request));
    }

    public function test_route_does_not_match() {

        $request = $this->createMock(Request::class);
        $request->method('getUrl')->willReturn('/test');
        $request->method('getMethod')->willReturn('GET');

        $this->assertFalse($this->route->matches($request));
    }

    public function test_named_routes() {
        $router = new Router();

        $router->get("/", function () {
            return "Hello world";
        })->name("home");

        $this->assertEquals('/', Route::get("home"));
    }

    public function test_execute_route() {

        ob_start();
        $this->route->execute();
        $output = ob_get_clean();

        $this->assertEquals("Hello world", $output);
    }

}