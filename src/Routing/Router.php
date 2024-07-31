<?php declare(strict_types=1);


namespace Masar\Routing;
use Masar\Exceptions\NotFoundException;
use Masar\Http\Request;

class Router {

    /**
     * Stores the controllers namespace.
     * @var string
     */
    public static string $controllerNamespace;

    /**
     * Stores the middlewares namespace.
     * @var string
     */
    public static string $middlewareNamespace;

    /**
     * Contains all the registered routes.
     * @var array
     */
    private array $routes = [];


    private array $groupStack = [];


    
    public function __construct(array $config = []) 
    {
        if($config) {
            self::$controllerNamespace = $this->normalizeNamespace($config["controllers"]);
            self::$middlewareNamespace = $this->normalizeNamespace($config["middlewares"]);
        }
    }

    /**
     * Adds a GET request route into $routes array.
     * @param string $path
     * @param callable|array|string $callback
     * @return \Masar\Routing\Route
     */
    public function get(string $path, callable|array|string $callback): Route {
        
        
        $route = $this->addRoute('GET', $path, $callback);

        $this->addMiddlewares($route);

        $this->routes[] = $route;
        return $route;
    
    }

    /**
     * Adds a POST request route into $routes array.
     * @param string $path
     * @param callable|array|string $callback
     * @return \Masar\Routing\Route
     */
    public function post(string $path, callable|array|string $callback): Route {
        
        $route = $this->addRoute('POST', $path, $callback);
        
        $this->addMiddlewares($route);
        
        $this->routes[] = $route;
        return $route;
    
    }

    
    /**
     * Adds a PUT request route into $routes array.
     * @param string $path
     * @param callable|array|string $callback
     * @return \Masar\Routing\Route
     */
    public function put(string $path, callable|array|string $callback): Route {
        
        $route = $this->addRoute('PUT', $path, $callback);
        
        $this->addMiddlewares($route);

        $this->routes[] = $route;
        return $route;
    
    }

    /**
     * Adds a PATCH request route into $routes array.
     * @param string $path
     * @param callable|array|string $callback
     * @return \Masar\Routing\Route
     */
    public function patch(string $path, callable|array|string $callback): Route {
        
        $route = $this->addRoute('PATCH', $path, $callback);
        
        $this->addMiddlewares($route);

        $this->routes[] = $route;
        return $route;
    
    }

    /**
     * Adds a DELETE request route into $routes array.
     * @param string $path
     * @param callable|array|string $callback
     * @return \Masar\Routing\Route
     */
    public function delete(string $path, callable|array|string $callback): Route {

        $route = $this->addRoute('DELETE', $path, $callback);
        
        $this->addMiddlewares($route);

        $this->routes[] = $route;
        return $route;
    
    }

    /**
     * Creates a route that will be added to the $routes array later.
     * @param string $method
     * @param string $path
     * @param callable|array|string $callback
     * @return \Masar\Routing\Route
     */
    private function addRoute(string $method, string $path, callable|array|string $callback): Route {
        return new Route($method, $this->normalizePath($path), $callback);
    }

    /**
     * Adds middlewares to each route in the route grouping.
     * @param \Masar\Routing\Route $route
     * @return void
     */
    private function addMiddlewares(Route $route) {

        $middlewares = $this->groupStack["middleware"] ?? [];
        
        if(!empty($middlewares)) {
            $route->middleware($middlewares);
        }
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
     * Normalizes the given namespace by trimming slashes and back-salshes to avoid errors.
     * @param string $namespace
     * @return string
     */
    private function normalizeNamespace(string $namespace): string {
        return trim($namespace, '/\\') . '\\';
    }


    /**
     * adds middlewares to the stack.
     * @param string|array $middleware
     * @return Router
     */
    public function middleware(string|array $middleware): static {
        $this->groupStack["middleware"] = $middleware;
        return $this;
    }


    /**
     * Groups routes under a common prefix and applies shared middleware.
     * @param callable $callback
     * @return void
     */
    public function group(callable $callback) {
        call_user_func($callback);
        array_pop($this->groupStack);
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