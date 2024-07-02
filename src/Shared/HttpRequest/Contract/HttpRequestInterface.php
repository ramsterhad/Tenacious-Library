<?php
declare(strict_types=1);

namespace Ramsterhad\TenaciousLibrary\Shared\HttpRequest\Contract;

interface HttpRequestInterface
{
    public function setHeader(string $key, string $value) : self;

    public function setOpt(int $option, mixed $value) : bool;

    public function get(string $url, array $data = []) : self;

    public function getResponse() : false|null|string;

    public function close() : self;
}
