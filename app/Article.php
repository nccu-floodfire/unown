<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    public $incrementing = false;
    public $timestamps = false;

    /**
     * 取得 Article 所被答的答案
     *
     * @return void
     */
    public function results()
    {
        return $this->hasMany('App\Result');
    }
}
