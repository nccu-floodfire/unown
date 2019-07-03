<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    public $timestamps = false;

    public function encoder()
    {
        return $this->belongsTo('App\Encoder');
    }

    public function article()
    {
        return $this->belongsTo('App\Article');
    }
}
