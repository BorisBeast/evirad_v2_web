<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Interval extends Model
{
    protected $table = 'intervali';

    protected $fillable = ['prioritet', 'pocetak', 'kraj', 'dani', 'zabranjeno'];

    public function grupa()
    {
        return $this->belongsTo(Grupa::class, 'grupa');
    }

    public function zone()
    {
        return $this->belongsToMany(Zona::class, 'prava', 'interval', 'zona');
    }
}
