<?php declare(strict_types=1);

use Masar\Exceptions\NotFoundException;
use Masar\Http\Request;
use Masar\Routing\Router;
use PHPUnit\Framework\TestCase;

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

}