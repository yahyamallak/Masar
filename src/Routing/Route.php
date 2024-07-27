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
     * @param mixed $rules
     * @return void
     */
    public function where($rules) {

        foreach($rules as $param => $rule) {
            $this->paramRules[$param] = $rule;
        }
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
        echo call_user_func_array($this->callback, $this->params);
    }
}