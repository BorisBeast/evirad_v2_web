<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Grupa extends Model
{
    protected $table = 'grupe';

    protected $fillable = ['ime'];

    public function radnici()
    {
        return $this->hasMany(Radnik::class, 'grupa');
    }

    public function intervali()
    {
        return $this->hasMany(Interval::class, 'grupa');
    }
}
