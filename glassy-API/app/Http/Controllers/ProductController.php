<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{

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
                        'status' => 200
                    ]);
                }
            }

        }else {

            return response()->json([
                'imgErr' => 'Ievietojiet bildi!'
            ]);
        }

    }
}
