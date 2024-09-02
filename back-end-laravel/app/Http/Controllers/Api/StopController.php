<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Stop;
use App\Models\Day;

class StopController extends Controller
{

  public function getStops($day_id)
  {
    $stops = Stop::where('day_id', $day_id)->get();

    return response()->json([
      'stops' => $stops
    ]);
  }

  public function getAllStops()
  {
    $stops = Stop::with('day.trip')->get();

    return response()->json([
      'stops' => $stops
    ]);
  }

  public function store(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'day_id' => 'required|exists:days,id',
      'name' => 'required|string|max:255',
      'latitude' => 'required|numeric|between:-90,90',
      'longitude' => 'required|numeric|between:-180,180',
    ]);

    if ($validator->fails()) {
      return response()->json([
        'errors' => $validator->errors()
      ], 422);
    }

    $day = Day::find($request->input('day_id'));

    $stop = new Stop();
    $stop->name = $request->input('name');
    $stop->latitude = $request->input('latitude');
    $stop->longitude = $request->input('longitude');
    $stop->day()->associate($day);
    $stop->save();

    return response()->json([
      'message' => 'Stop created successfully',
      'stop' => $stop
    ], 201);
  }

  public function destroy($id)
    {
        try {
            $stop = Stop::findOrFail($id);
            $stop->delete();

            return response()->json([
                'message' => 'Tappa eliminata con successo'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Errore durante l\'eliminazione della tappa',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
