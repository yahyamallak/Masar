<?php declare(strict_types=1);


namespace Masar\Routing;
use Masar\Exceptions\NotFoundException;
use Masar\Http\Request;

class Router { 

    /**
     * Contains all the registered routes.
     * @var array
     */
    private array $routes = [];

    /**
     * Adds a GET request route into $routes array.
     * @param string $path
     * @param callable $callback
     * @return \Masar\Routing\Route
     */
    public function get(string $path, callable $callback): Route {
        
        $route = $this->addRoute('GET', $path, $callback);
        
        $this->routes[] = $route;
        return $route;
    
    }

    /**
     * Adds a POST request route into $routes array.
     * @param string $path
     * @param callable $callback
     * @return \Masar\Routing\Route
     */
    public function post(string $path, callable $callback): Route {
        
        $route = $this->addRoute('POST', $path, $callback);
        
        $this->routes[] = $route;
        return $route;
    
    }

    
    /**
     * Adds a PUT request route into $routes array.
     * @param string $path
     * @param callable $callback
     * @return \Masar\Routing\Route
     */
    public function put(string $path, callable $callback): Route {
        
        $route = $this->addRoute('PUT', $path, $callback);
        
        $this->routes[] = $route;
        return $route;
    
    }

    /**
     * Adds a PATCH request route into $routes array.
     * @param string $path
     * @param callable $callback
     * @return \Masar\Routing\Route
     */
    public function patch(string $path, callable $callback): Route {
        
        $route = $this->addRoute('PATCH', $path, $callback);
        
        $this->routes[] = $route;
        return $route;
    
    }

    /**
     * Adds a DELETE request route into $routes array.
     * @param string $path
     * @param callable $callback
     * @return \Masar\Routing\Route
     */
    public function delete(string $path, callable $callback): Route {
        
        $route = $this->addRoute('DELETE', $path, $callback);
        
        $this->routes[] = $route;
        return $route;
    
    }

    /**
     * Creates a route that will be added to the $routes array later.
     * @param string $method
     * @param string $path
     * @param callable $callback
     * @return \Masar\Routing\Route
     */
    private function addRoute(string $method, string $path, callable $callback): Route {
        return new Route($method, $this->normalizePath($path), $callback);
    }

    /**
     * Normalizes the given path by trimming the trailing slashes.
     * @param string $path
     * @return string
     */
    private function normalizePath(string $path): string {
        return '/' . trim($path, '/');
    }


    /**
     * Dispatches the router but before it checks if the url matches a registered route.
     * @param \Masar\Http\Request $request
     * @throws \Masar\Exceptions\NotFoundException
     * @return void
     */
    public function dispatch(Request $request): void {

        foreach($this->routes as $route) {

            if($route->matches($request)) {

                $route->execute();
                return;
            }
        }

        throw new NotFoundException("Route not found");

    }

}