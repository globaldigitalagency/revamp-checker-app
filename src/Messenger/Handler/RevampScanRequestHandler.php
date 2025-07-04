<?php

namespace App\Messenger\Handler;

use App\Entity\RevampScan;
use App\Entity\SimilarityCheck;
use App\Helper\CheckerHelper;
use App\Messenger\Object\RevampScanRequest;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class RevampScanRequestHandler
{
    public function __construct(
        private readonly CheckerHelper $checkerHelper,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(RevampScanRequest $revampScanRequest)
    {
        $url = $revampScanRequest->getUrl();

        $revampScan = new RevampScan();
        $revampScan->setUrl($url);
        $revampScan->setLoadingChecks(true);
        $this->entityManager->persist($revampScan);
        $this->entityManager->flush();

        for ($years = 5; $years > 0; $years--) {
            try {
                $similarityCheck = $this->createSimilarityCheck($url, $years, $revampScan);
            } catch (Exception $e) {
                continue;
            }

            $this->entityManager->persist($similarityCheck);
            $revampScan->addSimilarityCheck($similarityCheck);
        }

        $revampScan->setLoadingChecks(false);
        $this->entityManager->flush();
    }

    private function createSimilarityCheck(
        string $url,
        int $years,
        RevampScan $revampScan
    ): SimilarityCheck {
        $fromYears = $years;
        $toYears = $fromYears - 1;

        $fromDate = new DateTime("-{$fromYears} years");
        $toDate = new DateTime("-{$toYears} years");

        $fromUrl = $this->getWebArchiveUrl($fromDate, $url);
        $toUrl = $this->getWebArchiveUrl($toDate, $url);
        if (empty($fromUrl) || empty($toUrl)) {
            throw new Exception(
                sprintf('No archived URL found for %s years ago or %s years ago', $fromYears, $toYears)
            );
        }

        list($fromRequestUrl, $toRequestUrl, $similarityRate) = $this->checkerHelper->compareFromDate($fromUrl, $toUrl);
        $isRevamp = $this->checkerHelper->isRevampResolve($similarityRate);

        $fromDate = $this->getDateFromWebArchiveUrl($fromRequestUrl);
        $toDate = $this->getDateFromWebArchiveUrl($toRequestUrl);

        $similarityCheck = new SimilarityCheck();
        $similarityCheck->setYearFrom($fromDate);
        $similarityCheck->setYearTo($toDate);
        $similarityCheck->setIsRevamp($isRevamp);
        $similarityCheck->setRevampScan($revampScan);
        $similarityCheck->setSimilarityRate((float)number_format($similarityRate, 2));

        return $similarityCheck;
    }

    private function getWebArchiveUrl(\DateTimeInterface $date, string $url): ?string
    {
        return 'https://web.archive.org/web/'.$date->format('YmdHis').'/'.$url;
    }

    private function getDateFromWebArchiveUrl(string $url): ?DateTime
    {
        if (!preg_match('/\/web\/(\d{14})\//', $url, $matches)) {
            return null;
        }

        return DateTime::createFromFormat('YmdHis', $matches[1]);
    }
}
