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
    private int $statusCode;

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
    public function status(int $code = 200): static {
        $this->statusCode = $code;
        return $this;
    }


    /**
     * Gets the response's status code
     * @return int
     */
    public function getStatus() {
        return $this->statusCode;
    }


    /**
     * Sends the response.
     * @return mixed
     */
    public function send() {
        http_response_code($this->statusCode);
        return $this->content;
    }
   
}