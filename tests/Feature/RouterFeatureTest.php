<?php declare(strict_types=1);

use Masar\Exceptions\NotFoundException;
use Masar\Http\Request;
use Masar\Routing\Router;
use PHPUnit\Framework\TestCase;

class HomeController {
    public function index() {
        return "Hello from controller";
    }

    public function show($id) {
        return "Hello post {$id} from controller";
    }
}

class AuthMiddleware {
    public function handle($next) {
        echo "Auth middleware ";

        return $next();
    }
}

class RouterFeatureTest extends TestCase {

    private $router;

    public function setUp(): void {
        
        $this->router = new Router();
    }

    public function test_get_request_route() {


        $this->router->get('/about', function() {
            return 'Hello from about page';
        });

        $request = $this->createMock(Request::class);
        $request->method('getUrl')->willReturn('/about');
        $request->method('getMethod')->willReturn('GET');

        ob_start();
        $this->router->dispatch($request);
        $output = ob_get_clean();

        $this->assertEquals('Hello from about page', $output);
    }

    public function test_post_request_route() {


        $this->router->post('/submit', function() {
            return 'Form submitted';
        });

        $request = $this->createMock(Request::class);
        $request->method('getUrl')->willReturn('/submit');
        $request->method('getMethod')->willReturn('POST');

        ob_start();
        $this->router->dispatch($request);
        $output = ob_get_clean();

        $this->assertEquals('Form submitted', $output);
    }

    public function test_put_request_route() {


        $this->router->put('/posts/{id}/edit', function($id) {
            return "Post {$id} has been edited successfully" ;
        });

        $id = 5;

        $request = $this->createMock(Request::class);
        $request->method('getUrl')->willReturn("/posts/{$id}/edit");
        $request->method('getMethod')->willReturn('PUT');

        ob_start();
        $this->router->dispatch($request);
        $output = ob_get_clean();

        $this->assertEquals("Post {$id} has been edited successfully", $output);
    }

    public function test_patch_request_route() {


        $this->router->patch('/posts/{id}/modify', function($id) {
            return "Post {$id} has been modified successfully" ;
        });

        $id = 5;

        $request = $this->createMock(Request::class);
        $request->method('getUrl')->willReturn("/posts/{$id}/modify");
        $request->method('getMethod')->willReturn('PATCH');

        ob_start();
        $this->router->dispatch($request);
        $output = ob_get_clean();

        $this->assertEquals("Post {$id} has been modified successfully", $output);
    }

    public function test_delete_request_route() {


        $this->router->delete('/posts/{id}/delete', function($id) {
            return "Post {$id} has been deleted successfully" ;
        });

        $id = 5;

        $request = $this->createMock(Request::class);
        $request->method('getUrl')->willReturn("/posts/{$id}/delete");
        $request->method('getMethod')->willReturn('DELETE');

        ob_start();
        $this->router->dispatch($request);
        $output = ob_get_clean();

        $this->assertEquals("Post {$id} has been deleted successfully", $output);
    }



    public function test_route_not_found() {

        $this->router->get('/about', function() {
            return 'Hello from about page';
        });

        $request = $this->createMock(Request::class);
        $request->method('getUrl')->willReturn('/');
        $request->method('getMethod')->willReturn('GET');

        $this->expectException(NotFoundException::class);

        $this->router->dispatch($request);

    }


    public function test_routes_with_parameters() {
        
        $this->router->get('/posts/{id}', function($id) {
            return 'Hello from post : ' . $id;
        });

        $id = "5";

        $request = $this->createMock(Request::class);
        $request->method('getUrl')->willReturn("/posts/{$id}");
        $request->method('getMethod')->willReturn('GET');

        ob_start();
        $this->router->dispatch($request);
        $output = ob_get_clean();

        $this->assertEquals("Hello from post : {$id}", $output);
    
    
    }


    public function test_routes_parameters_rules() {

        $this->router->get('/posts/{id}', function($id) {
            return 'Hello from post : ' . $id;
        })->where(["id"=>":number"]);

        $id = "post";

        $request = $this->createMock(Request::class);
        $request->method('getUrl')->willReturn("/posts/{$id}");
        $request->method('getMethod')->willReturn('GET');
        
        $this->expectException(NotFoundException::class);
        
        $this->router->dispatch($request);
    }


    public function test_that_controller_array_works() {

        $this->router->get('/', [HomeController::class, 'index']);

        $request = $this->createMock(Request::class);
        $request->method('getUrl')->willReturn("/");
        $request->method('getMethod')->willReturn('GET');

        ob_start();
        $this->router->dispatch($request);
        $output = ob_get_clean();

        $this->assertEquals("Hello from controller", $output);
    }

    public function test_that_controller_string_works() {

        $router = new Router();

        $router->get('/', "HomeController@index");

        $request = $this->createMock(Request::class);
        $request->method('getUrl')->willReturn("/");
        $request->method('getMethod')->willReturn('GET');

        ob_start();
        $router->dispatch($request);
        $output = ob_get_clean();

        $this->assertEquals("Hello from controller", $output);
    }

    public function test_that_controller_with_params_works() {



        $this->router->get('/posts/{id}', [HomeController::class, 'show']);

        $id = 10;

        $request = $this->createMock(Request::class);
        $request->method('getUrl')->willReturn("/posts/{$id}");
        $request->method('getMethod')->willReturn('GET');

        ob_start();
        $this->router->dispatch($request);
        $output = ob_get_clean();

        $this->assertEquals("Hello post {$id} from controller", $output);
    }


    public function test_that_middleware_works() {

        $config = [];

        $router = new Router();

        $router->get("/", [HomeController::class, "index"])
               ->middleware("auth");
        
        $request = $this->createMock(Request::class);
        $request->method('getUrl')->willReturn("/");
        $request->method('getMethod')->willReturn('GET');

        ob_start();
        $router->dispatch($request);
        $output = ob_get_clean();

        $this->assertEquals("Auth middleware Hello from controller", $output);
    }

}