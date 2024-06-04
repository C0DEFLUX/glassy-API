<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    /** @test */
    public function it_can_create_a_category()
    {
        $category_data = [
            'category_name_lv' => 'Test category lv',
            'category_name_eng' => 'Test category eng',
            'category_name_ru' => 'Test category ru',
        ];

        $response = $this->postJson('api/add-category', $category_data);

        $response->assertStatus(201);

        $this->assertDatabaseHas('categories', [
            'category_name_lv' => 'Test category lv',
            'category_name_eng' => 'Test category eng',
            'category_name_ru' => 'Test category ru',
        ]);
    }
}
