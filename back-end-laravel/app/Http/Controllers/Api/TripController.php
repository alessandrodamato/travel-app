<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Functions\Helper;
use App\Models\Day;
use DateTime;

class TripController extends Controller
{

  public function getTrips(){
    $trips = Trip::orderBy('start_date', 'desc')->get();
    return response($trips, 200);
  }

  public function getTripBySlug($slug){
    $trip = Trip::where('slug', $slug)->first();
    return response($trip, 200);
  }

  public function store(Request $request)
  {
    $rules = [
      'name' => 'required|string|min:1|max:100',
      'start_date' => 'required|date|',
      'end_date' => 'required|date|after:start_date',
      'description' => 'required|string|min:10|max:65534'
    ];

    $messages = [
      'name.required' => 'Il nome è obbligatorio.',
      'name.string' => 'Il nome deve essere una stringa.',
      'name.min' => 'Il nome deve contenere almeno 1 carattere.',
      'name.max' => 'Il nome non può superare i 100 caratteri.',
      'start_date.required' => 'La data di inizio è obbligatoria.',
      'start_date.date' => 'La data di inizio deve essere una data valida.',
      'end_date.required' => 'La data di fine è obbligatoria.',
      'end_date.date' => 'La data di fine deve essere una data valida.',
      'end_date.after' => 'La data di fine deve essere successiva alla data di inizio.',
      'description.required' => 'La descrizione è obbligatoria.',
      'description.string' => 'La descrizione deve essere una stringa.',
      'description.min' => 'La descrizione deve contenere almeno 10 caratteri.',
      'description.max' => 'La descrizione non può superare i 65534 caratteri.'
    ];

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
      $errors = $validator->errors()->toArray();
      $formattedErrors = [];

      foreach ($errors as $field => $messages) {
        $formattedErrors[$field] = implode('; ', $messages);
      }

      return response()->json([
        'errors' => $formattedErrors
      ], 400);
    }

    $form_data = $request->all();

    $new_trip = new Trip();
    $new_trip->fill($form_data);
    $new_trip->slug = Helper::generateSlug($new_trip->name, Trip::class);
    $new_trip->save();

    $start_date = new DateTime($new_trip->start_date);
    $end_date = new DateTime($new_trip->end_date);
    $interval = $end_date->diff($start_date);
    $n_days = $interval->days + 1;

    for ($i = 0; $i < $n_days; $i++) {
      $new_day = new Day();
      $new_day->trip_id = $new_trip->id;
      $new_day->save();
    }

    $validatedData = $validator->validated();
    return response()->json($validatedData, 200);
  }
}
