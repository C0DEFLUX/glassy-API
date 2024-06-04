<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;


class ProductTest extends TestCase
{
    /** @test */
    public function it_returns_product_data()
    {
        // Send a GET request to the API endpoint
        $response = $this->get('/api/product-data');

        // Assert that the response has a 200 status code
        $response->assertStatus(200);

        // Assert that the response contains the expected JSON data
        $response->assertJsonStructure([
            '*' => [
                'product_title_lv',
                'product_title_eng',
                'product_title_ru',
                'product_desc_lv',
                'product_desc_ru',
                'product_desc_eng',
                'category_id',
                'main_img',
                'gallery',
                'categories'
            ]
        ]);
    }
}
