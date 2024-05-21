<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public static function index()
    {
        $data = Category::all();

        return response()->json($data);
    }

    public static function create()
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
                'errors' => $validation->errors()
            ], 422);
        }

        Category::create([
            'category_name_lv' => request('category_name_lv'),
            'category_name_eng' => request('category_name_eng'),
            'category_name_ru' => request('category_name_ru'),
        ]);

        return response()->json([
            'status' => 201,
            'success_msg' => 'Kategorija pievienota veiksmīgi!',
        ],201);
    }

    public static function update($id)
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
                'message' => 'Neizdevās atjaunot kategoriju!',
                'errors' => $validation->errors()
            ], 422);
        }

        Category::where('id', $id)->update([
            'category_name_lv' => request('category_name_lv'),
            'category_name_eng' => request('category_name_eng'),
            'category_name_ru' => request('category_name_ru'),
        ]);

        return response()->json([
            'status' => 201,
            'success_msg' => 'Kategorija atjaunota veiksmīgi!'
        ], 201);
    }
}
