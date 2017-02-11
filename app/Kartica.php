<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kartica extends Model
{
    use SoftDeletes;

    protected $table = 'kartice';

    protected $fillable = ['kod'];

    protected $dates = ['deleted_at'];

    public function radnik()
    {
        return $this->belongsTo(Radnik::class, 'radnik');
    }
}
