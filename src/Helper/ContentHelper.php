<?php

namespace App\Helper;

use RuntimeException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ContentHelper
{
    public function __construct(private readonly HttpClientInterface $client)
    {
    }

    public function stripText(string $content): string
    {
        return preg_replace(['/>.*?</ism', '/<(.+?) .+?>/ism'], ['><', '<$1>'], $content) ?? '';
    }

    public function getUrlRequestData(string $url): ?array
    {
        $response = $this->client->request('GET', $url, ['max_redirects' => 10]);
        if ($response->getStatusCode() !== 200) {
            return null;
        }

        $effectiveUrl = $response->getInfo('url');
        $content = $response->getContent(false);
        if (!$content) {
            return null;
        }

        return [
            'url' => $effectiveUrl,
            'content' => $content,
        ];
    }
}
