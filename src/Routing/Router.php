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


    /**
     * Contains the prefix and middlewares of the route grouping.
     * @var array
     */
    private array $groupStack = [];


    
    /**
     * Gets and sets the namespace of the controllers and middlewares.
     * @param array $config
     */
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

        $path = $this->addPrefix($path);
        
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
        
        $path = $this->addPrefix($path);

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
        
        $path = $this->addPrefix($path);

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
        
        $path = $this->addPrefix($path);

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

        $path = $this->addPrefix($path);

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
     * Adds prefix to the path.
     * @param string $path
     * @return string
     */
    private function addPrefix(string $path): string {

        $prefix = $this->groupStack["prefix"] ?? "";

        if($prefix) {
            return $this->normalizePath($prefix) . $path;
        }

        return $path;
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
     * Adds the prefix into the group stack of the route grouping.
     * @param string $prefix
     * @return Router
     */
    public function prefix(string $prefix):static {
        $this->groupStack["prefix"] = $prefix;
        return $this;
    }

    /**
     * Groups routes under a common prefix and applies shared middleware.
     * @param callable $callback
     * @return void
     */
    public function group(callable $callback) {
        call_user_func($callback);
        $this->groupStack = [];
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