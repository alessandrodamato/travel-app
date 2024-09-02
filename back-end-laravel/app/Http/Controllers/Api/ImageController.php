<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ImageController extends Controller
{
    public function uploadImages(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'images.*' => 'required|file|mimes:jpg,jpeg,png,JPG,JPEG,PNG|max:20480',
            'day_id' => 'required|exists:days,id'
        ], [
            'images.*.required' => 'Ogni immagine è obbligatoria.',
            'images.*.file' => 'Ogni file deve essere un file valido.',
            'images.*.mimes' => 'Solo i file con estensione jpg, jpeg, o png sono accettati.',
            'images.*.max' => 'Ogni immagine non può superare i 20MB di dimensione.',
            'day_id.required' => 'L\'ID del giorno è obbligatorio.',
            'day_id.exists' => 'L\'ID del giorno deve esistere nel database.'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        $dayId = $request->input('day_id');
        $uploadedImages = $request->file('images');
        $images = [];

        foreach ($uploadedImages as $file) {
            $originalName = $file->getClientOriginalName();
            $path = $file->storeAs('uploads', uniqid() . '_' . $originalName, 'public');

            $image = Image::create([
                'path' => $path,
                'day_id' => $dayId,
                'name' => $originalName
            ]);

            $images[] = $image;
        }

        return response()->json(['images' => $images], 200);
    }
}
