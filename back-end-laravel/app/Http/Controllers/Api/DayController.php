<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Trip;
use App\Models\Day;
use App\Models\Image;

class DayController extends Controller
{
  public function getDayByDate($slug, $date)
  {
      $day = Day::where('date', $date)
                 ->whereHas('trip', function ($query) use ($slug) {
                     $query->where('slug', $slug);
                 })
                 ->with('images')
                 ->firstOrFail();

      return response()->json($day);
  }

  public function uploadImages(Request $request, $slug, $date)
  {
      $request->validate([
          'images.*' => 'required|mimes:jpg,jpeg,png,PNG|max:2048'
      ]);

      $day = Day::where('date', $date)
                ->whereHas('trip', function ($query) use ($slug) {
                    $query->where('slug', $slug);
                })
                ->firstOrFail();

      $uploadedImages = [];

      foreach ($request->file('images') as $file) {
          $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
          $file->storeAs('uploads', $filename);

          $image = new Image();
          $image->path = '/uploads/' . $filename;
          $image->day_id = $day->id;
          $image->save();

          $uploadedImages[] = $image->path;
      }

      return response()->json([
          'images' => $uploadedImages,
          'message' => 'Immagini caricate con successo!'
      ]);
  }
}
