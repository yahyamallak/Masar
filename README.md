# Masar
A lightweight and flexible PHP router with support for dynamic route parameters.

![PHP Version](https://img.shields.io/badge/php-%5E8.0-blue.svg)

## Requirements

- PHP 8.0 or higher

## Features

- Simple and intuitive API
- Support for ( GET / POST / PUT / PATCH / DELETE ) requests
- Dynamic route parameters
- Support for parameters rules
- Easy to integrate and extend
- Support for named routes
- Support for controllers
- Support for middlewares 

## Installation

Run this command to install the library:

```bash

composer require yahyamallak/masar

```

## Usage

### 1. Basic Setup

```php

require_once dirname(__DIR__) . "/vendor/autoload.php";

use Masar\Http\Request;
use Masar\Routing\Router;

$router = new Router();

// Define routes
$router->get('/', function() {
    return "Welcome to the homepage!";
});

$router->get('/about', function() {
    return "About us page";
});

$router->post('/submit', function() {
    return "Form submitted";
});

$router->put('/users/{id}/change', function($id) {
    return "User " . $id . " has been edited.";
});

$router->patch('/users/{id}/edit', function($id) {
    return "edit name of user : " . $id;
});

$router->delete('/users/{id}/delete', function($id) {
    return "delete user " . $id;
});

// Create a request object
$request = new Request();

// Dispatch the router
try {
    $router->dispatch($request);
} catch (NotFoundException $e) {
    echo $e->getMessage();
}

```

### 2. Using Route Parameters

You can define dynamic segments in your routes using curly braces:


```php

$router->get('/user/{id}', function($id) {
    return "User profile for user with ID: " . $id;
});

$router->get('/post/{slug}', function($slug) {
    return "Displaying post: " . $slug;
});

```

### 3. Using Where For Parameters Rules

#### Supported Rules :

- ":digit"
- ":number"
- ":letter"
- ":word"
- ":slug"

```php

$router->get('/user/{id}', function($id) {

    return "User profile for user with ID: " . $id;

})->where(["id" => ":number"]);

```

### 4. Using Named Routes

#### naming the route : 

```php

$router->get('/users', function() {

    return "All users.";

})->name("users");

```

#### getting the route name :

```php

use Masar\Routing\Route;

Route::get("users");

```

### 5. Using Controllers

#### Before using controllers you need to go through some quick steps to tell the router where to find them.

#### Step 1 : Create a config file or just an array with some configuration.

You create an associative array with keys ( controllers | middlewares ) and as values you put their namespaces.

```php

$config = [
    "controllers" => "App\Controllers",
    "middlewares" => "App\Middlewares"
];

```

#### Step 2 : You pass the configuration to the router.

All you got to do is to give the config array to the router and it will handle the rest.

```php

$router = new Router($config);

```

#### Step 3 : You define the routes with controllers

##### Example 1 :

```php

$router->get('/profile/{id}', [UserController::class, "index"]);

```

##### Example 2 :

```php

$router->patch('/posts/{id}/edit', "PostController@edit");

```

### 6. Using Middlewares

```php

$router->get("/admin", function() {
    
    return "Admin dahsboard.";

})->middleware("auth");

```

### 7. Using route grouping

Example 1 :

```php

$router->middleware("auth")->group(function() use($router) {
    
    $router->get("/", [HomeController::class, "index"]);

    $router->get("/about", [AboutController::class, "index"]);

});

```

Example 2 :

```php

$router->middleware(["auth", "role"])->group(function() use($router) {
    
    $router->get("/", [HomeController::class, "index"]);

    $router->get("/profile", [UserController::class, "profile"]);

});

```

Example 3 :

```php

$router->prefix("admin")->group(function() use($router) {
    
    $router->get("/", [AdminController::class, "index"]);

    $router->get("/settings", [AdminController::class, "settings"]);

});

```


## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is open-sourced software licensed under the MIT license.

