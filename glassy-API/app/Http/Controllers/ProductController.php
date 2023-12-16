<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller

{
    function index() {

        $data = Product::all();

        return response()->json($data);

    }

    function addProduct (Request $request) {

        $data = [
            'prodTitleErr' => '',
        ];

        if($request->hasFile('image')) {

            $product_title = $request->input('product_title');

            //Check if title is empty
            if(empty($product_title)) {
                return response()->json([
                    'prodTitleErr' => 'Ievadiet produkta nosaukumu!',
                ]);
            }

            if(empty($data['prodTitleErr'])) {
                //Get the image file
                $file = $request->file('image');
                //Get the original image name
                $filename = $file->getClientOriginalName();
                //Add current time to image to make sure image names never match
                $finalName = date('His') . $filename;

                //Get path of image
                $path = $request->file('image')->storeAs('images', $finalName,'public');

                //Get the full path of image to store in db
                $imgUrl = asset('storage/'. $path);

                //Make clean data to store in the db
                $data_clean = [
                    'product_title' => $product_title,
                    'main_img' => $imgUrl
                ];

                if(Product::create($data_clean)) {
                    return response()->json([
                        'success_msg' => 'Bilde vieksmīgi augšupielādēts!',
                        'status' => 200,
                    ]);
                }
            }

        }else {

            return response()->json([
                'imgErr' => 'Ievietojiet bildi!'
            ]);
        }

    }

    function removeProduct($id)
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
            'code' => 200,
        ]);

    }
}
