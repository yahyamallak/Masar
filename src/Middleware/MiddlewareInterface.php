<?php declare(strict_types=1);

namespace Masar\Middleware;

interface MiddlewareInterface {
    public function handle(callable $next) {}
}