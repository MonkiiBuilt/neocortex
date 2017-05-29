<?php

use App\Models\Factories\ItemFactory;

class ItemTypeCheckTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testItemTypeChecks()
    {
        $testUrls = [
            // No known match
            'http://example.com/a.html' => null,

            // Images
            'http://example.com/a.png' => 'image',
            'http://example.com/a.jpg' => 'image',
            'http://example.com/a.gif' => 'image',
            'http://example.com/a/very/long/path.jpg' => 'image',
            'https://example.com/a.png?with=some&other=arguments' => 'image',

            // Videos
            'http://example.com/a.mp4' => 'video',
            'http://example.com/a.gifv' => 'video',
            'http://example.com/a.webm' => 'video',
            'http://example.com/a/very/long/path.mp4' => 'video',
            'http://example.com/a.webm?with=some&other=arguments' => 'video',
        ];

        foreach ($testUrls as $url => $correctType) {
            // Mock an item based on the URL
            $item = null;
            try {
                $item = ItemFactory::create(['details'=>['url' => $url]]);
            } catch (\App\Exceptions\UnknownItemTypeException $e) {
                $this->assertNull($correctType,
                    "URL $url was unidentified, should have been $correctType");
                continue;
            }

            // Use the ItemTypeServiceProvider to determine which type of item
            // this is
            $this->assertTrue($item->type === $correctType,
                "URL $url identified as {$item->type} => should have been $correctType");
        }
    }
}
