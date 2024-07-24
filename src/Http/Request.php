<?php declare(strict_types=1);

namespace Masar\Http;

class Request
{
    /**
     * Contains the HTTP method.
     * @var string
     */
    private string $method;

    /**
     * Contains the url after parsing it and normalizing it.
     * @var string
     */
    private string $url;

    public function __construct() {
        $this->method = strtoupper($_SERVER['REQUEST_METHOD']);
        $this->url = $this->normalizeUrl($this->parseUrl($_SERVER['REQUEST_URI'])); 
    }

    /**
     * Parses the url by getting just the path and dropping the rest.
     * @param string $url
     * @return string
     */
    private function parseUrl(string $url): string {
        return parse_url($url, PHP_URL_PATH);
    }

    /**
     * Normalizes the url by trimming the slashes.
     * @param string $url
     * @return string
     */
    private function normalizeUrl(string $url): string {
        return '/' . trim($url, '/');
    }

    
    /**
     * Gets the HTTP method.
     * @return string
     */
    public function getMethod(): string {
        return $this->method;
    }

    /**
     * Gets the url.
     * @return string
     */
    public function getUrl(): string {
        return $this->url;
    }
}