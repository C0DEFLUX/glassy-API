<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;


class ProductController extends Controller
{
    function index()
    {
        //Select the categories
        $products = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('products.*',
                'categories.category_name_lv as category_lv',
                'categories.category_name_eng as category_eng',
                'categories.category_name_ru as category_ru'
            )
            ->get();

        //Add the category array to product return
        return $products->map(function($product) {
            $product->category = [
                'lv' => $product->category_lv,
                'eng' => $product->category_eng,
                'ru' => $product->category_ru
            ];
            unset($product->category_lv);
            unset($product->category_eng);
            unset($product->category_ru);

            //Select and add the gallery img to product return
            $galleryImages = DB::table('galleries')
                ->where('product_id', $product->id)
                ->pluck('img_url');
            $product->gallery = $galleryImages;

            return $product;
        });
    }

    function galleryIndex(): JsonResponse
    {
        $data = Gallery::all();

        return response()->json($data);

    }

    public static function create (): JsonResponse
    {
        $validation = Validator::make(request()->all(), [
            'product_title_lv' => 'required|unique:products,product_title_lv|string|max:50|min:5',
            'product_title_eng' => 'required|unique:products,product_title_eng|string|max:50|min:5',
            'product_title_ru' => 'required|unique:products,product_title_ru|string|max:50|min:5',
            'product_desc_lv' => 'required|string|max:1000|min:10',
            'product_desc_eng' => 'required|string|max:1000|min:10',
            'product_desc_ru' => 'required|string|max:1000|min:10',
            'category_id' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg',
            'images.*' => 'image|mimes:jpeg,png,jpg'

        ],
        [
            'product_title_lv.required' => "Produkta nosaukums ir obligāts!",
            'product_title_lv.unique' => "Produkta nosaukums nedrīkst atkārtoties!",
            'product_title_lv.string' => "Produkta nosaukmam jābūt tekstam!",
            'product_title_lv.max' => "Produkta nosaukums nedrīkst pārsniegt 50 rakstu zīmes!",
            'product_title_lv.min' => "Produkta nosaukums nedrīkst būt īsāks par 5 rakstu zīmēm!",

            'product_title_eng.required' => "Produkta nosaukums ir obligāts!",
            'product_title_eng.unique' => "Produkta nosaukums nedrīkst atkārtoties!",
            'product_title_eng.string' => "Produkta nosaukmam jābūt tekstam!",
            'product_title_eng.max' => "Produkta nosaukums nedrīkst pārsniegt 50 rakstu zīmes!",
            'product_title_eng.min' => "Produkta nosaukums nedrīkst būt īsāks par 5 rakstu zīmēm!",

            'product_title_ru.required' => "Produkta nosaukums ir obligāts!",
            'product_title_ru.unique' => "Produkta nosaukums nedrīkst atkārtoties!",
            'product_title_ru.string' => "Produkta nosaukmam jābūt tekstam!",
            'product_title_ru.max' => "Produkta nosaukums nedrīkst pārsniegt 50 rakstu zīmes!",
            'product_title_ru.min' => "Produkta nosaukums nedrīkst būt īsāks par 5 rakstu zīmēm!",

            'product_desc_lv.required' => "Produkta apraksts ir obligāts!",
            'product_desc_lv.string' => "Produkta aprakstam jābūt tekstam!",
            'product_desc_lv.max' => "Produkta apraksts nedrīkst pārsniegt 1000 rakstu zīmes!",
            'product_desc_lv.min' => "Produkta apraksts nedrīkst būt īsāks par 10 rakstu zīmēm!",

            'product_desc_eng.required' => "Produkta apraksts ir obligāts!",
            'product_desc_eng.string' => "Produkta aprakstam jābūt tekstam!",
            'product_desc_eng.max' => "Produkta apraksts nedrīkst pārsniegt 1000 rakstu zīmes!",
            'product_desc_eng.min' => "Produkta apraksts nedrīkst būt īsāks par 10 rakstu zīmēm!",

            'product_desc_ru.required' => "Produkta apraksts ir obligāts!",
            'product_desc_ru.string' => "Produkta aprakstam jābūt tekstam!",
            'product_desc_ru.max' => "Produkta apraksts nedrīkst pārsniegt 1000 rakstu zīmes!",
            'product_desc_ru.min' => "Produkta apraksts nedrīkst būt īsāks par 10 rakstu zīmēm!",

            'category_id.required' => 'Kategorija ir obligāta!',

            'image.required' => 'Produkta titula bilde ir obligāta!',
            'image.image' => 'Produkta titula bildei ir jābūt bildei!',
            'image.mimes' => 'Produkta titula bilde tikai var būt JPEG, PNG, JPG!',

            'images.image' => 'Produkta papildus bildēm ir jābut bildēm!',
            'images.mimes' => 'Produkta papildus bildes var būt JPEG, PNG, JPG!'

        ]);

        if($validation->fails()) {
            return response()->json([
                'status' => 422,
                'message' => "Neizdevās ieveitot produktu!",
                'errors' => $validation->errors()
            ], 422);
        }

        //Get the image file
        $file = request()->file('image');
        //Get the original image name
        $filename = $file->getClientOriginalName();
        //Add current time to image to make sure image names never match
        $final_name = date('His') . $filename;
        //Get path of image
        $path = request()->file('image')->storeAs('images', $final_name, 'public');
        //Get the full path of image to store in db
        $img_url = asset('storage/' . $path);

        //Create data
        $product = Product::create([
            'product_title_lv' => request('product_title_lv'),
            'product_title_eng' => request('product_title_eng'),
            'product_title_ru' => request('product_title_ru'),
            'product_desc_lv' => request('product_desc_lv'),
            'product_desc_eng' => request('product_desc_eng'),
            'product_desc_ru' => request('product_desc_ru'),
            'category_id' => request('category_id'),
            'main_img' => $img_url
        ]);

        $product_id = $product->id;

        if (request()->hasFile('images')) {
            foreach (request()->file('images') as $file) {
                // Get the original image name
                $filename = $file->getClientOriginalName();

                // Add current time to image to make sure image names never match
                $final_name = date('His') . $filename;

                // Define the path to store the image
                $path = 'images/' . $final_name;

                Storage::disk('public')->putFileAs('images', $file, $final_name);

                // Get the full path of the image to store in the database
                $img_url = asset('storage/' . $path);

                Gallery::create([
                    'product_id' => $product_id,
                    'img_url' => $img_url
                ]);
            }
        }

        return response()->json([
            'success_msg' => 'Produkts vieksmīgi augšupielādēts!',
            'status' => 201,
        ], 201);
    }

    public static function update($id): JsonResponse
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => 404,
                'message' => 'Produkts nav atrasts!',
            ], 404);
        }

        $validation = Validator::make(request()->all(), [
            'product_title_lv' => 'required|unique:products,product_title_lv,' . $id . '|string|max:50|min:5',
            'product_title_eng' => 'required|unique:products,product_title_eng,' . $id . '|string|max:50|min:5',
            'product_title_ru' => 'required|unique:products,product_title_ru,' . $id . '|string|max:50|min:5',
            'product_desc_lv' => 'required|string|max:1000|min:10',
            'product_desc_eng' => 'required|string|max:1000|min:10',
            'product_desc_ru' => 'required|string|max:1000|min:10',
            'category_id' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg'
        ], [
            'product_title_lv.required' => "Produkta nosaukums ir obligāts!",
            'product_title_lv.unique' => "Produkta nosaukums nedrīkst atkārtoties!",
            'product_title_lv.string' => "Produkta nosaukmam jābūt tekstam!",
            'product_title_lv.max' => "Produkta nosaukums nedrīkst pārsniegt 50 rakstu zīmes!",
            'product_title_lv.min' => "Produkta nosaukums nedrīkst būt īsāks par 5 rakstu zīmēm!",

            'product_title_eng.required' => "Produkta nosaukums ir obligāts!",
            'product_title_eng.unique' => "Produkta nosaukums nedrīkst atkārtoties!",
            'product_title_eng.string' => "Produkta nosaukmam jābūt tekstam!",
            'product_title_eng.max' => "Produkta nosaukums nedrīkst pārsniegt 50 rakstu zīmes!",
            'product_title_eng.min' => "Produkta nosaukums nedrīkst būt īsāks par 5 rakstu zīmēm!",

            'product_title_ru.required' => "Produkta nosaukums ir obligāts!",
            'product_title_ru.unique' => "Produkta nosaukums nedrīkst atkārtoties!",
            'product_title_ru.string' => "Produkta nosaukmam jābūt tekstam!",
            'product_title_ru.max' => "Produkta nosaukums nedrīkst pārsniegt 50 rakstu zīmes!",
            'product_title_ru.min' => "Produkta nosaukums nedrīkst būt īsāks par 5 rakstu zīmēm!",

            'product_desc_lv.required' => "Produkta apraksts ir obligāts!",
            'product_desc_lv.string' => "Produkta aprakstam jābūt tekstam!",
            'product_desc_lv.max' => "Produkta apraksts nedrīkst pārsniegt 1000 rakstu zīmes!",
            'product_desc_lv.min' => "Produkta apraksts nedrīkst būt īsāks par 10 rakstu zīmēm!",

            'product_desc_eng.required' => "Produkta apraksts ir obligāts!",
            'product_desc_eng.string' => "Produkta aprakstam jābūt tekstam!",
            'product_desc_eng.max' => "Produkta apraksts nedrīkst pārsniegt 1000 rakstu zīmes!",
            'product_desc_eng.min' => "Produkta apraksts nedrīkst būt īsāks par 10 rakstu zīmēm!",

            'product_desc_ru.required' => "Produkta apraksts ir obligāts!",
            'product_desc_ru.string' => "Produkta aprakstam jābūt tekstam!",
            'product_desc_ru.max' => "Produkta apraksts nedrīkst pārsniegt 1000 rakstu zīmes!",
            'product_desc_ru.min' => "Produkta apraksts nedrīkst būt īsāks par 10 rakstu zīmēm!",

            'category_id.required' => 'Kategorija ir obligāta!',

            'image.required' => 'Produkta titula bilde ir obligāta!',
            'image.image' => 'Produkta titula bildei ir jābūt bildei!',
            'image.mimes' => 'Produkta titula bilde tikai var būt JPEG, PNG, JPG!',

            'images.image' => 'Produkta papildus bildēm ir jābut bildēm!',
            'images.mimes' => 'Produkta papildus bildes var būt JPEG, PNG, JPG!'
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => 422,
                'message' => "Neizdevās atjaunot produktu!",
                'errors' => $validation->errors()
            ], 422);
        }

        if (request()->hasFile('image')) {
            // Unlink old main image
            $oldMainImg = basename($product->main_img);
            if (Storage::disk('public')->exists('images/' . $oldMainImg)) {
                Storage::disk('public')->delete('images/' . $oldMainImg);
            }

            // Upload new main image
            $file = request()->file('image');
            $filename = $file->getClientOriginalName();
            $final_name = date('His') . $filename;
            $path = $file->storeAs('images', $final_name, 'public');
            $img_url = asset('storage/' . $path);

            // Update product with new main image URL
            $product->main_img = $img_url;
            $product->save();
        }


        if (request()->hasFile('images')) {
            // Unlink old gallery images
            $oldImages = Gallery::where('product_id', $id)->get();
            foreach ($oldImages as $oldImage) {
                $oldImgPath = basename($oldImage->img_url);
                if (Storage::disk('public')->exists('images/' . $oldImgPath)) {
                    Storage::disk('public')->delete('images/' . $oldImgPath);
                }
                $oldImage->delete();
            }

            // Upload new gallery images
            foreach (request()->file('images') as $file) {
                $filename = $file->getClientOriginalName();
                $final_name = date('His') . $filename;
                $path = 'images/' . $final_name;
                Storage::disk('public')->putFileAs('images', $file, $final_name);
                $img_url = asset('storage/' . $path);

                Gallery::create([
                    'product_id' => $product->id,
                    'img_url' => $img_url
                ]);
            }
        }

        // Update product details
        $product->update([
            'product_title_lv' => request('product_title_lv'),
            'product_title_eng' => request('product_title_eng'),
            'product_title_ru' => request('product_title_ru'),
            'product_desc_lv' => request('product_desc_lv'),
            'product_desc_eng' => request('product_desc_eng'),
            'product_desc_ru' => request('product_desc_ru'),
            'category_id' => request('category_id'),
        ]);

        return response()->json([
            'success_msg' => 'Produkts veiksmīgi atjaunināts!',
            'status' => 200,
        ], 200);
    }


    public static function getById($id) : JsonResponse
    {
        $product = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('products.id', $id)
            ->select('products.*',
                'categories.category_name_lv as category_lv',
                'categories.category_name_eng as category_eng',
                'categories.category_name_ru as category_ru'
            )
            ->first();

        if (!$product) {
            return response()->json([
                'message' => 'Produkts nav atrasts!',
                'status' => 404
            ], 404);
        }

        $product->category = [
            'lv' => $product->category_lv,
            'eng' => $product->category_eng,
            'ru' => $product->category_ru
        ];
        unset($product->category_lv);
        unset($product->category_eng);
        unset($product->category_ru);

        $galleryImages = DB::table('galleries')
            ->where('product_id', $product->id)
            ->pluck('img_url');
        $product->gallery = $galleryImages;

        return response()->json($product);
    }

    public static function getByCategoryId($categoryId) : JsonResponse
    {
        $products = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('products.category_id', $categoryId)
            ->select('products.*',
                'categories.category_name_lv as category_lv',
                'categories.category_name_eng as category_eng',
                'categories.category_name_ru as category_ru'
            )
            ->get();

        if ($products->isEmpty()) {
            return response()->json([
                'message' => 'Produkt nevarēja atrast pēc šis kategorijas!',
                'status' => 404
            ], 404);
        }

        $products = $products->map(function ($product) {
            $product->category = [
                'lv' => $product->category_lv,
                'eng' => $product->category_eng,
                'ru' => $product->category_ru
            ];
            unset($product->category_lv);
            unset($product->category_eng);
            unset($product->category_ru);

            $galleryImages = DB::table('galleries')
                ->where('product_id', $product->id)
                ->pluck('img_url');
            $product->gallery = $galleryImages;

            return $product;
        });

        return response()->json($products);
    }

    public static function destroy($id): JsonResponse
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'message' => 'Produkts nav atrasts!',
                'status' => 404
            ], 404);
        }

        // Unlink main image
        $mainImgPath = str_replace(asset('storage/'), 'public/', $product->main_img);
        if (Storage::exists($mainImgPath)) {
            Storage::delete($mainImgPath);
        }
        // Unlink gallery images
        $galleryImages = Gallery::where('product_id', $id)->get();
        foreach ($galleryImages as $galleryImage) {
            $galleryImgPath = str_replace(asset('storage/'), 'public/', $galleryImage->img_url);
            if (Storage::exists($galleryImgPath)) {
                Storage::delete($galleryImgPath);
            }
            $galleryImage->delete(); // Delete gallery record
        }
        // Delete product info from db
        $product->delete();

        return response()->json([
            'message' => 'Produkts noņemts veiksmīgi!',
            'status' => 200,
        ], 200);
    }


}
