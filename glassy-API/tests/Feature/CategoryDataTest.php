<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryDataTest extends TestCase
{
    /** @test */
    public function it_returns_category_data()
    {
        // Send a GET request to the API endpoint
        $response = $this->get('/api/category-data');

        // Assert that the response has a 200 status code
        $response->assertStatus(200);

        // Assert that the response contains the expected JSON data
        $response->assertJsonStructure([
            '*' => [
                'category_name_lv',
                'category_name_eng',
                'category_name_ru',

            ]
        ]);
    }
}
