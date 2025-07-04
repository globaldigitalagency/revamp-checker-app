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

        $currentYear = (int)(new DateTime())->format('Y');
        for ($years = 1; $years < 6; $years++) {
            try {
                $similarityCheck = $this->createSimilarityCheck($url, $years, $revampScan, $currentYear);

                $this->entityManager->persist($similarityCheck);
                $revampScan->addSimilarityCheck($similarityCheck);
            } catch (Exception $e) {
                continue;
            }
        }

        $revampScan->setLoadingChecks(false);
        $this->entityManager->flush();
    }

    private function createSimilarityCheck(
        string $url,
        int $years,
        RevampScan $revampScan,
        int $currentYear
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

        $similarityRate = $this->checkerHelper->compareFromDate($fromUrl, $toUrl);
        $isRevamp = $this->checkerHelper->isRevampResolve($similarityRate);

        $similarityCheck = new SimilarityCheck();
        $similarityCheck->setYearFrom($currentYear - $fromYears);
        $similarityCheck->setYearTo($currentYear - $toYears);
        $similarityCheck->setIsRevamp($isRevamp);
        $similarityCheck->setRevampScan($revampScan);
        $similarityCheck->setSimilarityRate((float)number_format($similarityRate, 2));

        return $similarityCheck;
    }

    private function getWebArchiveUrl(\DateTimeInterface $date, string $url): ?string
    {
        return 'https://web.archive.org/web/'.$date->format('YmdHis').'/'.$url;
    }
}
