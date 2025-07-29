<?php

namespace App\Tests\Helper;

use App\Helper\CheckerHelper;
use App\Helper\ContentHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CheckerHelperTest extends TestCase
{
    private CheckerHelper $checkerHelper;
    private ContentHelper|MockObject $contentHelper;

    protected function setUp(): void
    {
        $this->contentHelper = $this->createMock(ContentHelper::class);
        $this->checkerHelper = new CheckerHelper($this->contentHelper);
    }

    public function testCompareFromDateReturnsExpectedArray(): void
    {
        $fromUrl = 'http://example.com/from';
        $toUrl = 'http://example.com/to';
        $this->contentHelper->method('getUrlRequestData')
            ->willReturnMap([
                [$fromUrl, ['url' => $fromUrl, 'content' => 'content1']],
                [$toUrl, ['url' => $toUrl, 'content' => 'content2']],
            ]);
        $this->contentHelper->method('stripText')
            ->willReturnOnConsecutiveCalls('stripped1', 'stripped2');

        $result = $this->checkerHelper->compareFromDate($fromUrl, $toUrl);

        $this->assertIsArray($result);
        $this->assertCount(3, $result);
        $this->assertEquals($fromUrl, $result[0]);
        $this->assertEquals($toUrl, $result[1]);
        $this->assertIsFloat($result[2]);
    }

    public function testCompareFromDateThrowsExceptionOnEmptyContent(): void
    {
        $this->contentHelper->method('getUrlRequestData')
            ->willReturn(null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Content from one of the URLs is empty or could not be retrieved.');

        $this->checkerHelper->compareFromDate('http://example.com/from', 'http://example.com/to');
    }

    public function testIsRevampResolveReturnsTrueWhenPercentBelow50(): void
    {
        $this->assertTrue($this->checkerHelper->isRevampResolve(49.9));
        $this->assertFalse($this->checkerHelper->isRevampResolve(50.0));
    }
}