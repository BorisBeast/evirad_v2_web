<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Zona extends Model
{
    protected $table = 'zone';

    public $timestamps = false;

    public function intervali()
    {
        return $this->belongsToMany(Interval::class, 'prava', 'zona', 'interval');
    }
}
