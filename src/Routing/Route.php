<?php declare(strict_types=1);

namespace Masar\Routing;
use Masar\Http\Request;

class Route {

    /**
     * Contains the supported rules for parameters.
     * @var array
     */
    private array $rules = [
        ":digit" => "[0-9]",
        ":number" => "[0-9]+",
        ":letter" => "[a-zA-Z]",
        ":word" => "[a-zA-Z]+",
        ":slug" => "[a-zA-Z0-9_\-]+"
    ];

    /**
     * Contains route's parameters if there are any.
     * @var array
     */
    private array $params = [];

    /**
     * Contains rules for each parameter.
     * @var array
     */
    private array $paramRules = [];

    /**
     * Contains named routes.
     * @var array
     */
    private static array $namedRoutes = [];

    /**
     * Contains route's middlewares.
     * @var array
     */
    private array $middlewares = [];

    /**
     * Constructs the route.
     * @param string $method
     * @param string $path
     * @param mixed $callback
     */
    public function __construct(private string $method, private string $path, private mixed $callback) 
    {}


    /**
     * Checks if the url and method match the method and path of the requested route.
     * @param \Masar\Http\Request $request
     * @return bool
     */
    public function matches(Request $request): bool {

        $method = $this->getRequestMethod($request);
        
        if($method == $this->method) {
            
            $path = $this->convertToRegex($this->path);

            if(preg_match($path, $request->getUrl(), $matches)) {

                $this->params = $this->filterMatches($matches);

                return true;
            }
        }


        return false;

    }

    /**
     * Adds rules to parameters.
     * @param array $rules
     * @return static
     */
    public function where(array $rules): static {

        foreach($rules as $param => $rule) {
            $this->paramRules[$param] = $rule;
        }

        return $this;
    }

    public function middleware(string|array $middleware) {

        $namespace = Router::$middlewareNamespace ?? "";

        if(is_string($middleware)) {
            $this->middlewares[$middleware] = new ( $namespace . ucfirst($middleware) . 'Middleware');
        } else if(is_array($middleware)) {

            foreach($middleware as $middlewareName) {
                $this->middlewares[$middlewareName] = new ($namespace . ucfirst($middlewareName) . 'Middleware');
            }
        }

        return $this;
    }


    /**
     * Sets route's name.
     * @param string $name
     * @return static
     */
    public function name(string $name): static {
        self::$namedRoutes[$name] = $this->path;
        return $this;
    }

    public static function get(string $name) {
        return self::$namedRoutes[$name];
    }

    private function getRequestMethod(Request $request): string {
        return $_POST["_method"] ?? $request->getMethod();;
    }


    /**
     * Filters the matches array to get an associative array with key as name of parameter and value as the value of the parameter.
     * @param array $matches
     * @return array
     */
    private function filterMatches(array $matches): array {
        array_shift($matches);

        return array_filter($matches, function ($match) {
                return !is_numeric($match);
            }, ARRAY_FILTER_USE_KEY);
    }


    /**
     * Converts the path to a regular expression to be able to match it with the url.
     * @param string $path
     * @return string
     */
    private function convertToRegex(string $path): string {

        if(!empty($this->paramRules)) {
            foreach($this->paramRules as $param => $rule) {
                $path = preg_replace("#{($param)}#", "(?<$1>{$this->rules[$rule]})", $path);
            }
        } else {
                $path = preg_replace("#{([\w_\-\s]+)}#", "(?<$1>[\w_\-\s]+)", $path);
        }

        return '#^'. $path . '$#';
    }


    /**
     * Executes the callback of the route after being matched with the url.
     * @return void
     */

    public function execute(): void {

        $next = null;

        $callback = $this->callback;

        if(is_callable($callback)) {

            $next = function () use($callback) {
                return call_user_func_array($callback, $this->params);
            }; 
        
        } else if(is_array($callback)) {

            $controller = new $callback[0];
            $action = $callback[1];

            $next = function () use($controller, $action) {
                return $controller->$action(...$this->params);
            };
            
        } else if(is_string($callback)) {

            $namespace = Router::$controllerNamespace ?? "";

            $callback = explode("@",$callback);
            $controllerNamespace =  $namespace . $callback[0];
            $action = $callback[1];

            $controller = new $controllerNamespace;

            $next = function () use ($controller, $action) {
                return $controller->$action(...$this->params);
            };
        }

        $middlewares = $this->middlewares;
        
        if(!empty($middlewares)) {

            $middlewaresStack = array_reduce(array_reverse($middlewares), function ($next, $middleware) {
                return function () use ($middleware, $next) {
                    return $middleware->handle($next);
                };
            }, $next);
            
            echo $middlewaresStack();
        }else {
            echo $next();
        }
    }
}