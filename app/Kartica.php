<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kartica extends Model
{
    protected $table = 'kartice';

    protected $fillable = ['kod'];

    public function radnik()
    {
        return $this->belongsTo(Radnik::class, 'radnik');
    }
}
