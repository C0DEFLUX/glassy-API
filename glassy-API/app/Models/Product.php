<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_title_lv',
        'product_title_eng',
        'product_title_ru',
        'product_desc_lv',
        'product_desc_eng',
        'product_desc_ru',
        'category_id',
        'main_img'
    ];

    protected $casts = [
        'created_at' => 'datetime:d/m/Y',
        'updated_at' => 'datetime:d/m/Y',
    ];
}
