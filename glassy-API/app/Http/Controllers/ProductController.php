<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


class ProductController extends Controller
{
    function index()
    {

        $data = Product::all();

        return response()->json($data);

    }

    public static function create (): JsonResponse
    {

        $validation = Validator::make(request()->all(), [
            'product_title' => 'required|unique:products,product_title|string|max:50|min:5',
            'product_desc' => 'required|string|max:1000|min:10',
            'image' => 'required|image|mimes:jpeg,png,jpg'
        ],
        [
            'product_title.required' => "Produkta nosaukums ir obligāts!",
            'product_title.unique' => "Produkta nosaukums nedrīkst atkārtoties!",
            'product_title.string' => "Produkta nosaukmam jābūt tekstam!",
            'product_title.max' => "Produkta nosaukums nedrīkst pārsniegt 50 rakstu zīmes!",
            'product_title.min' => "Produkta nosaukums nedrīkst būt īsāks par 5 rakstu zīmēm!",

            'product_desc.required' => "Produkta apraksts ir obligāts!",
            'product_desc.string' => "Produkta aprakstam jābūt tekstam!",
            'product_desc.max' => "Produkta apraksts nedrīkst pārsniegt 1000 rakstu zīmes!",
            'product_desc.min' => "Produkta apraksts nedrīkst būt īsāks par 10 rakstu zīmēm!",

            'image.required' => 'Produkta titula bilde ir obligāta!',
            'image.image' => 'Produkta titula bildei ir jābūt bildei!',
            'image.mimes' => 'Produkta titula bilde tikai var būt JPEG, PNG, JPG!'

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
        Product::create([
            'product_title' => request('product_title'),
            'product_desc' => request('product_desc'),
            'main_img' => $img_url
        ]);

        return response()->json([
            'success_msg' => 'Produkts vieksmīgi augšupielādēts!',
            'status' => 201,
        ], 201);
    }

    public static function update ($name): JsonResponse
    {

        $validation = Validator::make(request()->all(), [
            'product_title' => 'required|string|max:50|min:5',
            'product_desc' => 'required|string|max:1000|min:10',
        ],
        [
            'product_title.required' => "Produkta nosaukums ir obligāts!",
            'product_title.unique' => "Produkta nosaukums nedrīkst atkārtoties!",
            'product_title.string' => "Produkta nosaukmam jābūt tekstam!",
            'product_title.max' => "Produkta nosaukums nedrīkst pārsniegt 50 rakstu zīmes!",
            'product_title.min' => "Produkta nosaukums nedrīkst būt īsāks par 5 rakstu zīmēm!",

            'product_desc.required' => "Produkta apraksts ir obligāts!",
            'product_desc.string' => "Produkta aprakstam jābūt tekstam!",
            'product_desc.max' => "Produkta apraksts nedrīkst pārsniegt 1000 rakstu zīmes!",
            'product_desc.min' => "Produkta apraksts nedrīkst būt īsāks par 10 rakstu zīmēm!",

            'image.required' => 'Produkta titula bilde ir obligāta!',
            'image.image' => 'Produkta titula bildei ir jābūt bildei!',
            'image.mimes' => 'Produkta titula bilde tikai var būt JPEG, PNG, JPG!'
        ]);

        if($validation->fails()) {
            return response()->json([
                'status' => 422,
                'message' => "Neizdevās atjaunot produktu!",
                'errors' => $validation->errors()
            ], 422);
        }

        if(empty(request()->file('image'))) {
            //Create data
            Product::where('product_title', $name)->update([
                'product_title' => request('product_title'),
                'product_desc' => request('product_desc'),
            ]);

            return response()->json([
                'success_msg' => 'Produkts vieksmīgi atjaunots!',
                'status' => 201,
            ], 201);
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
        Product::where('product_title', $name)->update([
            'product_title' => request('product_title'),
            'product_desc' => request('product_desc'),
            'main_img' => $img_url
        ]);

        return response()->json([
            'success_msg' => 'Produkts vieksmīgi atjaunots!',
            'status' => 201,
        ], 201);
    }

    public static function destroy($id)
    {
        //Find product by id
        $product = Product::find($id);
        //Make Absolute URL from db into a usable URL
        $product_url = str_replace(asset('storage/'), 'public', $product->main_img);
        //Delete image from storage
        unlink(Storage::path($product_url));
        //Delete book info from db
        $product->delete();

        return response()->json([
            'message' => 'Produkts noņemts veiksmīgi!',
            'status' => 200,
        ]);

    }

    function getByName($name) {

        $data = Product::where('product_title', $name)->first();

        return response()->json($data);

    }
}
