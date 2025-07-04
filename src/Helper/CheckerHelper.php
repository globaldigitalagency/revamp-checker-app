<?php

namespace App\Helper;

use DateTime;
use Exception;

class CheckerHelper
{
    public function __construct(private ContentHelper $contentHelper)
    {
    }

    public function compareFromDate(string $fromDateUrl, string $toDateUrl): array
    {
        $fromDateRequest = $this->contentHelper->getUrlRequestData($fromDateUrl);
        $toDateRequest = $this->contentHelper->getUrlRequestData($toDateUrl);
        if (empty($fromDateRequest) || empty($toDateRequest)) {
            throw new Exception('Content from one of the URLs is empty or could not be retrieved.');
        }

        return [
            $fromDateRequest['url'],
            $toDateRequest['url'],
            $this->checkSimilarity($toDateRequest['content'], $fromDateRequest['content']),
        ];
    }

    private function checkSimilarity(string $content1, string $content2): float
    {
        $content1 = $this->contentHelper->stripText($content1);
        $content2 = $this->contentHelper->stripText($content2);
        similar_text($content1, $content2, $percent);

        return $percent;
    }

    public function isRevampResolve(float $percent): bool
    {
        return $percent < 50.0;
    }
}