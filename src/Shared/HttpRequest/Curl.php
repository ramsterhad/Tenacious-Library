<?php
declare(strict_types=1);

namespace Ramsterhad\TenaciousLibrary\Shared\HttpRequest;

use Curl\Curl as PhpCurl;
use Ramsterhad\TenaciousLibrary\Shared\HttpRequest\Contract\HttpRequestInterface;

class Curl implements HttpRequestInterface
{
    public ?bool $error = null;

    public ?int $error_code = null;

    private PhpCurl $curl;

    public function __construct()
    {
        $this->curl = new PhpCurl();
        $this->error = &$this->curl->error;
        $this->error_code = &$this->curl->error_code;
    }

    public function setHeader(string $key, string $value): HttpRequestInterface
    {
        $this->curl->setHeader($key, $value);
        return $this;
    }

    public function setOpt(int $option, mixed $value): bool
    {
        return $this->curl->setOpt($option, $value);
    }

    public function get(string $url, array $data = []): HttpRequestInterface
    {
        $this->curl->get($url, $data);
        return $this;
    }

    public function getResponse(): false|null|string
    {
        return $this->curl->getResponse();
    }

    public function close(): HttpRequestInterface
    {
        $this->curl->close();
        return $this;
    }
}
