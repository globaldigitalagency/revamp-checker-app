<?php

namespace App\Helper;

class ContentHelper
{
    public function stripText(string $content): string
    {
        return preg_replace(['/>.+?</ism', '/<(.+?) .+?>/ism'], ['><', '<$1>'], $content);
    }

    public function getUrlRequestData(string $url): ?array
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $content = curl_exec($ch);

        $effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);

        if (!$content) {
            return null;
        }

        return [
            'url' => $effectiveUrl,
            'content' => $content,
        ];
    }
}
