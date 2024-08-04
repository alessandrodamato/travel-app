<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
  use HasFactory;

  protected $fillable = ['stop_id', 'url', 'description'];

  public function stop()
  {
      return $this->belongsTo(Stop::class);
  }
}
