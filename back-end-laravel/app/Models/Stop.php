<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stop extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',
    'latitude',
    'longitude',
    'day_id'
  ];

  public function day()
  {
    return $this->belongsTo(Day::class);
  }

  public function images()
  {
    return $this->hasMany(Image::class);
  }
}
