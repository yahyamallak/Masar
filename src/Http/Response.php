<?php declare(strict_types=1);

namespace Masar\Http;

class Response
{


    /**
     * Contains the content that will be sent as a response.
     * @var mixed
     */
    private mixed $content;

    /**
     * Contains the status code of the response.
     * @var int
     */
    private int $statusCode = 200;


    /**
     * Contains the response headers.
     * @var array
     */
    private array $headers = [];


    /**
     * Contains the response content and some other information.
     * @var array
     */
    private array $body = [];


    /**
     * Sets the response's content
     * @param mixed $content
     * @return static
     */
    public function content(mixed $content): static {
        $this->content = $content;
        return $this;
    }

    /**
     * Gets the response's content.
     * @return mixed
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * Sets the response's status code
     * @param int $code
     * @return static
     */
    public function statusCode(int $statusCode): static {
        $this->statusCode = $statusCode;
        return $this;
    }


    /**
     * Gets the response's status code
     * @return int
     */
    public function getStatusCode() {
        return $this->statusCode;
    }

    /**
     * Sets the response headers.
     * @param string $name
     * @param string $value
     * @return Response
     */
    public function header(string $name, string $value): static {
        $this->headers[strtolower($name)] = strtolower($value);
        return $this;
    }

    /**
     * Gets the response headers and set them.
     * @return void
     */
    private function getHeaders(): void {
        foreach($this->headers as $name => $value) {
            header($name .":". $value);
        }
    }


    /**
     * Gets the response content and put it in the body.
     * @return void
     */
    private function body(): void  {
        $this->body = [
            "statusCode"=> $this->getStatusCode(),
            "data" => $this->getContent(),
        ];
    }

    /**
     * Sends the response.
     * @return mixed
     */
    public function send(): mixed {
        http_response_code($this->statusCode);
        $this->getHeaders();
        return $this->content;
    }


    /**
     * Sends the response as JSON format.
     * @return bool|string
     */
    public function sendAsJSON(): bool|string {
        http_response_code($this->statusCode);
        header("Content-type: application/json");
        $this->getHeaders();
        $this->body();
        return json_encode($this->body);
    }
   
}