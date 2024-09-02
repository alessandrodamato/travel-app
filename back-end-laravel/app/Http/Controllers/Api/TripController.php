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

  public function getTrips()
  {
    $trips = Trip::orderBy('start_date', 'desc')->get();
    return response($trips, 200);
  }

  public function getTripBySlug($slug)
  {
    $trip = Trip::where('slug', $slug)->with('days')->first();
    return response($trip, 200);
  }

  public function store(Request $request)
  {
    $rules = [
      'name' => 'required|string|min:1|max:100',
      'start_date' => 'required|date',
      'end_date' => 'required|date|after:start_date',
      'description' => 'required|string|min:10|max:65534',
      'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
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
      'description.max' => 'La descrizione non può superare i 65534 caratteri.',
      'image.image' => 'Il file deve essere un\'immagine.',
      'image.mimes' => 'L\'immagine deve essere un file di tipo: jpg, jpeg, png.',
      'image.max' => 'L\'immagine non può superare i 2MB.',
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

    $form_data = $request->except('image');

    if ($request->hasFile('image')) {
      $image = $request->file('image');
      $originalName = $image->getClientOriginalName();
      $imagePath = $image->storeAs('uploads', $originalName, 'public');
      $form_data['image'] = $imagePath;
    }

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
      $new_day->date = date('y-m-d', strtotime($new_trip->start_date . "+$i days"));
      $new_day->save();
    }

    return response()->json($new_trip, 200);
  }


  public function edit(Request $request, $slug)
  {
    $trip = Trip::where('slug', $slug)->with('days')->first();

    if (!$trip) {
      return response()->json(['message' => 'Trip not found'], 404);
    }

    $rules = [
      'name' => 'required|string|min:1|max:100',
      'description' => 'required|string|min:10|max:65534',
      'days' => 'array',
      'days.*.id' => 'required|exists:days,id',
      'days.*.description' => 'nullable|string'
    ];

    $messages = [
      'name.required' => 'Il nome è obbligatorio.',
      'name.string' => 'Il nome deve essere una stringa.',
      'name.min' => 'Il nome deve contenere almeno 1 carattere.',
      'name.max' => 'Il nome non può superare i 100 caratteri.',
      'description.required' => 'La descrizione è obbligatoria.',
      'description.string' => 'La descrizione deve essere una stringa.',
      'description.min' => 'La descrizione deve contenere almeno 10 caratteri.',
      'description.max' => 'La descrizione non può superare i 65534 caratteri.',
      'days.array' => 'I giorni devono essere un array.',
      'days.*.id.required' => 'L\'ID del giorno è obbligatorio.',
      'days.*.id.exists' => 'Il giorno non esiste.',
      'days.*.description.string' => 'La descrizione deve essere una stringa.',
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

    $trip->update([
      'name' => $request->input('name'),
      'description' => $request->input('description'),
    ]);

    $days = $request->input('days', []);
    foreach ($days as $dayData) {
      $day = Day::find($dayData['id']);
      if ($day) {
        $day->update([
          'description' => $dayData['description']
        ]);
      }
    }

    $trip->load('days');
    return response()->json(['message' => 'Trip updated successfully', 'trip' => $trip], 200);
  }

  public function delete($slug)
  {
    $trip = Trip::where('slug', $slug)->first();

    if (!$trip) {
      return response()->json(['message' => 'Trip not found'], 404);
    }

    $trip->days()->delete();
    $trip->delete();

    return response()->json(['message' => 'Trip deleted successfully'], 200);
  }
}
