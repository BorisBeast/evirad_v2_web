<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Radnik extends Model
{
    protected $table = 'radnici';

    protected $fillable = ['ime', 'prezime', 'komentar', 'sluzba'];

    public function kartice()
    {
        return $this->hasMany(Kartica::class, 'radnik');
    }

    public function sluzba()
    {
        return $this->belongsTo(Sluzba::class, 'sluzba');
    }
}
