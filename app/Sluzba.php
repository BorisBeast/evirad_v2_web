<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sluzba extends Model
{
    protected $table = 'sluzbe';

    protected $fillable = ['ime'];

    public function radnici()
    {
        return $this->hasMany(Radnik::class, 'sluzba');
    }
}
