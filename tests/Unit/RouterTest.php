<?php declare(strict_types=1);


use Masar\Routing\Route;
use Masar\Routing\Router;
use Tests\ReflectionTrait;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase 
{   
    use ReflectionTrait;

    private $router;
    public function setUp(): void {

        $this->router = new Router();
    }

    public function test_add_route_to_routes() {

        $callback1 = function() {
            return "Hello GET";
        };

        $callback2 =  function() {
            return "Hello POST";
        };

        $this->router->get('/', $callback1);
        $this->router->post('/', $callback2);

        $routes = [
            0 => new Route('GET', '/', $callback1),
            1 => new Route('POST', '/', $callback2)
        ];

        $this->assertEquals($routes, $this->getPrivateProperty($this->router, 'routes'));
    }

}