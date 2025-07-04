<?php

namespace App\Helper;

class ContentHelper
{
    public function stripText(string $content): string
    {
        return preg_replace(['/>.+?</ism', '/<(.+?) .+?>/ism'], ['><', '<$1>'], $content);
    }

    public function getUrlContent(string $url): ?string
    {
        $content = file_get_contents($url);
        if ($content === false) {
            return null;
        }

        return $content;
    }
}
