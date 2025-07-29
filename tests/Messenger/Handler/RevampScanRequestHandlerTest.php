<?php

namespace App\Tests\Messenger\Handler;

use App\Entity\RevampScan;
use App\Entity\SimilarityCheck;
use App\Helper\CheckerHelper;
use App\Messenger\Handler\RevampScanRequestHandler;
use App\Messenger\Object\RevampScanRequest;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RevampScanRequestHandlerTest extends KernelTestCase
{
    private RevampScanRequestHandler $handler;
    private CheckerHelper|MockObject $checkerHelper;
    private EntityManagerInterface|MockObject $entityManager;

    protected function setUp(): void
    {
        $this->checkerHelper = $this->createMock(CheckerHelper::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->handler = new RevampScanRequestHandler($this->checkerHelper, $this->entityManager);
    }

    public function testInvokePersistsRevampScanAndSimilarityChecks(): void
    {
        $url = 'http://example.com';
        $revampScanRequest = new RevampScanRequest($url);

        $date = new DateTime();

        $this->checkerHelper->method('compareFromDate')
            ->willReturn([
                $this->getWebArchiveUrl($date->modify('-1 year'), $url),
                $this->getWebArchiveUrl($date, $url),
                45.0,
            ]);
        $this->checkerHelper->method('isRevampResolve')
            ->willReturn(true);

        $this->entityManager->expects($this->atLeastOnce())
            ->method('persist')
            ->with(
                $this->logicalOr(
                    $this->isInstanceOf(RevampScan::class),
                    $this->isInstanceOf(SimilarityCheck::class)
                )
            );
        $this->entityManager->expects($this->atLeastOnce())
            ->method('flush');

        ($this->handler)($revampScanRequest);
    }

    public function testInvokeSkipsFailedSimilarityChecks(): void
    {
        $url = 'http://example.com';
        $revampScanRequest = new RevampScanRequest($url);

        $this->checkerHelper->method('compareFromDate')
            ->willThrowException(new \Exception('No archived URL found'));

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(RevampScan::class));
        $this->entityManager->expects($this->exactly(2))
            ->method('flush');

        ($this->handler)($revampScanRequest);
    }

    private function getWebArchiveUrl(\DateTimeInterface $date, string $url): ?string
    {
        return 'https://web.archive.org/web/'.$date->format('YmdHis').'/'.$url;
    }
}