<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    public $timestamps = false;

    protected $casts = [
        'note' => 'array',
    ];

    public function encoder()
    {
        return $this->belongsTo('App\Encoder');
    }

    public function article()
    {
        return $this->belongsTo('App\Article');
    }
}
