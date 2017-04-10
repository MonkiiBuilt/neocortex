<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Http\Controllers\Items\ImageItemTypeController;

class ImageURLMatchTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $imageUrls = [
            'http://example.com/image.gif',
            'http://example.com/image.jpg',
            'http://example.com/image.png',
        ];
        foreach ($imageUrls as $url) {
            $imageMatch = ImageItemTypeController::matchURL($url);
            $this->assertTrue($imageMatch > 0);
        }

        $otherURLs = [
            'http://www.google.com',
            'http://example.com/test',
            'http://example.com/test.html',
        ];

        foreach ($otherURLs as $url) {
            $imageMatch = ImageItemTypeController::matchURL($url);
            $this->assertTrue($imageMatch == 0);
        }
    }
}
