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

// Create a request object
$request = new Request();

// Dispatch the router
try {
    $router->dispatch($request);
} catch (Exception $e) {
    echo '404 Not Found';
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

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is open-sourced software licensed under the MIT license.

