<?php

namespace App\Tests\Helper;

use App\Helper\ContentHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class ContentHelperTest extends TestCase
{
    protected function setUp(): void
    {
    }

    public function testStripTextRemovesAttributesAndContentBetweenTags(): void
    {
        $html = '<p class="test">Hello</p><div id="id">World</div>';
        $expected = '<p></p><div></div>';

        $client = new MockHttpClient();
        $contentHelper = new ContentHelper($client);
        try {
            $replacedContent = $contentHelper->stripText($html);
        } catch (\Exception $e) {
            $this->fail('stripText method threw an exception: ' . $e->getMessage());
        }

        $this->assertEquals($expected, $replacedContent);
    }

    public function testGetUrlRequestDataReturnsNullOnNon200Status(): void
    {
        $client = new MockHttpClient([new MockResponse('', ['http_code' => 404])]);
        $contentHelper = new ContentHelper($client);
        $this->assertNull($contentHelper->getUrlRequestData('http://example.com'));
    }

    public function testGetUrlRequestDataReturnsExpectedArray(): void
    {
        $client = new MockHttpClient([
            new MockResponse('<p>Test</p>', ['http_code' => 200, 'url' => 'http://example.com'])
        ]);
        $contentHelper = new ContentHelper($client);
        $result = $contentHelper->getUrlRequestData('http://example.com');

        $this->assertIsArray($result);
        $this->assertEquals('http://example.com', $result['url']);
        $this->assertEquals('<p>Test</p>', $result['content']);
    }
}