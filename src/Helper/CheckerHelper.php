<?php

namespace App\Helper;

use Exception;

class CheckerHelper
{
    public function __construct(private ContentHelper $contentHelper)
    {
    }

    public function compareFromDate(string $fromDateUrl, string $toDateUrl): float
    {
        $fromDateContent = $this->contentHelper->getUrlContent($fromDateUrl);
        $toDateContent = $this->contentHelper->getUrlContent($toDateUrl);
        if (empty($fromDateContent) || empty($toDateContent)) {
            throw new Exception('Content from one of the URLs is empty or could not be retrieved.');
        }

        return $this->checkSimilarity($toDateContent, $fromDateContent);
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
