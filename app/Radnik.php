<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Radnik extends Model
{
    use SoftDeletes;

    protected $table = 'radnici';

    protected $fillable = ['ime', 'prezime', 'komentar', 'sluzba', 'grupa'];

    protected $dates = ['deleted_at'];

    public function kartice()
    {
        return $this->hasMany(Kartica::class, 'radnik');
    }

    public function sluzba()
    {
        return $this->belongsTo(Sluzba::class, 'sluzba');
    }

    public function grupa()
    {
        return $this->belongsTo(Grupa::class, 'grupa');
    }
}
