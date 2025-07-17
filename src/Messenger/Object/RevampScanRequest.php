<?php

namespace App\Messenger\Object;

class RevampScanRequest
{
    public function __construct(private readonly string $url)
    {
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
