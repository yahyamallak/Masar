<?php declare(strict_types=1);

namespace Masar\Routing;
use Masar\Http\Request;

class Route { 

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

        $method = $request->getMethod();
        
        if($method != $this->method) {
            return false;
        }

        $path = $this->convertToRegex($request->getUrl());

        var_dump($path);

        if(preg_match($path, $this->path)) {
            return true;
        }

        return false;

    }


    /**
     * Converts the path to a regular expression to be able to match it with the url.
     * @param string $path
     * @return string
     */
    private function convertToRegex(string $path): string {
        return '#^' . preg_replace('#{[\w]+}#','([\w]+)',$path) . '$#';
    }


    /**
     * Executes the callback of the route after being matched with the url.
     * @return void
     */

    public function execute(): void {
        echo call_user_func($this->callback);
    }
}