<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Gallery;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public static function index(): JsonResponse
    {
        $data = Category::all();

        return response()->json($data);
    }

    public static function create(): JsonResponse
    {
        $validation = Validator::make(request()->all(), [
            'category_name_lv' => 'required|unique:categories,category_name_lv|string|max:50|min:3',
            'category_name_eng' => 'required|unique:categories,category_name_eng|string|max:50|min:3',
            'category_name_ru' => 'required|unique:categories,category_name_ru|string|max:50|min:3',
        ],
        [
            //LV
            'category_name_lv.required' => 'Kategorijas nosaukums ir obligāts!',
            'category_name_lv.unique' => 'Kategorijas nosaukumam nedrīkst atkārtoties!',
            'category_name_lv.string' => 'Kategorijas nosaukumam jābūt tekstam!',
            'category_name_lv.max' => 'Kategorijas nosaukums nedrīkst pārsniegt 50 rakstu zīmes!',
            'category_name_lv.min' => 'Kategorijas nosaukums nedrīkst būt mazāks par 3 rakstu zīmēm!',
            //ENG
            'category_name_eng.required' => 'Kategorijas nosaukums ir obligāts!',
            'category_name_eng.unique' => 'Kategorijas nosaukumam nedrīkst atkārtoties!',
            'category_name_eng.string' => 'Kategorijas nosaukumam jābūt tekstam!',
            'category_name_eng.max' => 'Kategorijas nosaukums nedrīkst pārsniegt 50 rakstu zīmes!',
            'category_name_eng.min' => 'Kategorijas nosaukums nedrīkst būt mazāks par 3 rakstu zīmēm!',
            //RU
            'category_name_ru.required' => 'Kategorijas nosaukums ir obligāts!',
            'category_name_ru.unique' => 'Kategorijas nosaukumam nedrīkst atkārtoties!',
            'category_name_ru.string' => 'Kategorijas nosaukumam jābūt tekstam!',
            'category_name_ru.max' => 'Kategorijas nosaukums nedrīkst pārsniegt 50 rakstu zīmes!',
            'category_name_ru.min' => 'Kategorijas nosaukums nedrīkst būt mazāks par 3 rakstu zīmēm!',
        ]);

        if($validation->fails()) {
            return response()->json([
                'status' => 422,
                'message' => 'Neizdevās pievienot kategoriju!',
                'errors' => $validation->errors(),
            ], 422);
        }

        $product = Category::create([
            'category_name_lv' => request('category_name_lv'),
            'category_name_eng' => request('category_name_eng'),
            'category_name_ru' => request('category_name_ru'),
        ]);

        return response()->json([
            'status' => 201,
            'success_msg' => 'Kategorija pievienota veiksmīgi!',
            $product
        ],201);
    }

    public static function update($id): JsonResponse
    {
        $validation = Validator::make(request()->all(), [
            'category_name_lv' => 'required|unique:categories,category_name_lv, '. $id .'|string|max:50|min:3',
            'category_name_eng' => 'required|unique:categories,category_name_eng, '. $id .'|string|max:50|min:3',
            'category_name_ru' => 'required|unique:categories,category_name_ru, '. $id .'|string|max:50|min:3',
        ],
            [
                //LV
                'category_name_lv.required' => 'Kategorijas nosaukums ir obligāts!',
                'category_name_lv.unique' => 'Kategorijas nosaukumam nedrīkst atkārtoties!',
                'category_name_lv.string' => 'Kategorijas nosaukumam jābūt tekstam!',
                'category_name_lv.max' => 'Kategorijas nosaukums nedrīkst pārsniegt 50 rakstu zīmes!',
                'category_name_lv.min' => 'Kategorijas nosaukums nedrīkst būt mazāks par 3 rakstu zīmēm!',
                //ENG
                'category_name_eng.required' => 'Kategorijas nosaukums ir obligāts!',
                'category_name_eng.unique' => 'Kategorijas nosaukumam nedrīkst atkārtoties!',
                'category_name_eng.string' => 'Kategorijas nosaukumam jābūt tekstam!',
                'category_name_eng.max' => 'Kategorijas nosaukums nedrīkst pārsniegt 50 rakstu zīmes!',
                'category_name_eng.min' => 'Kategorijas nosaukums nedrīkst būt mazāks par 3 rakstu zīmēm!',
                //RU
                'category_name_ru.required' => 'Kategorijas nosaukums ir obligāts!',
                'category_name_ru.unique' => 'Kategorijas nosaukumam nedrīkst atkārtoties!',
                'category_name_ru.string' => 'Kategorijas nosaukumam jābūt tekstam!',
                'category_name_ru.max' => 'Kategorijas nosaukums nedrīkst pārsniegt 50 rakstu zīmes!',
                'category_name_ru.min' => 'Kategorijas nosaukums nedrīkst būt mazāks par 3 rakstu zīmēm!',
            ]);

        if($validation->fails()) {
            return response()->json([
                'status' => 422,
                'message' => 'Neizdevās atjaunot kategoriju!',
                'errors' => $validation->errors()
            ], 422);
        }

        Category::where('id', $id)->update([
            'category_name_lv' => request('category_name_lv'),
            'category_name_eng' => request('category_name_eng'),
            'category_name_ru' => request('category_name_ru'),
        ]);

        $categories = Category::where('id', $id)->first();

        return response()->json([
            'status' => 201,
            'success_msg' => 'Kategorija atjaunota veiksmīgi!',
            'data' => $categories
        ], 201);
    }

    public static function destroy($id): JsonResponse
    {
        // Find the category
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Kategorija nav atrasta!',
                'status' => 404
            ], 404);
        }

        // Find all products under category
        $products = Product::where('category_id', $id)->get();

        // Delete each product
        foreach ($products as $product) {
            // Unlink main image
            $mainImgPath = str_replace(asset('storage/'), 'public/', $product->main_img);
            if (Storage::exists($mainImgPath)) {
                Storage::delete($mainImgPath);
            }
            // Unlink gallery images
            $galleryImages = Gallery::where('product_id', $product->id)->get();
            foreach ($galleryImages as $galleryImage) {
                $galleryImgPath = str_replace(asset('storage/'), 'public/', $galleryImage->img_url);
                if (Storage::exists($galleryImgPath)) {
                    Storage::delete($galleryImgPath);
                }
                $galleryImage->delete();
            }
            // Delete product info from db
            $product->delete();
        }

        // Delete the category
        $category->delete();

        return response()->json([
            'message' => 'Kategorija un saistītie produkti dzēsti veiksmīgi!',
            'status' => 200,
        ], 200);
    }
}
