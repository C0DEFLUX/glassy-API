<?php

namespace App\Http\Controllers;

use App\Models\BgImages;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MarketingController extends Controller
{
    public static function index() : JsonResponse
    {
        $data = BgImages::all();

        return response()->json($data);
    }

    public static function create() : JsonResponse
    {
        $validation = Validator::make(request()->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg'
        ],[
            'image.required' => 'Titula bilde ir obligāta!',
            'image.image' => 'Titula bildei ir jābūt bildei!',
            'image.mimes' => 'Titula bilde tikai var būt JPEG, PNG, JPG!',
        ]);

        if($validation->fails()) {
            return response()->json([
                'status' => 422,
                'message' => "Neizdevās ieveitot titula bildi!",
                'errors' => $validation->errors()
            ], 422);
        }

        $title_img = BgImages::first();

        if (request()->hasFile('image')) {
            // Unlink old main image
            $oldMainImg = basename($title_img->image_url);
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
            $title_img->image_url = $img_url;
        }

        return response()->json([
            'success_msg' => 'Titula veiksmīgi pievienota!',
            'status' => 200,
        ], 200);



    }
}
