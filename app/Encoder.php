<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Webpatser\Uuid\Uuid;

class Encoder extends Model
{
    public $timestamps = false;
    /**
     *  Setup model event hooks for UUID (version.4)
     */
    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->access_token = (string) Uuid::generate(4);
        });
    }

    /**
     * 取得 Encoder所作的答案
     *
     * @return void
     */
    public function results()
    {
        return $this->hasMany('App\Result');
    }

    /**
     * 檢查Encoder需做的問題List
     *
     * @return boolean
     */
    public function getArticleList()
    {
        $article_list = $this->article_list;
        $output_list = array();
        foreach (explode(',', $article_list) as $range) {
            if (strpos($range, ':') !== false) {
                $output_list = array_merge($output_list, range(explode(':', $range)[0], explode(':', $range)[1]));
            } else {
                array_push($output_list, intval($range));
            }
        }
        return $output_list;
    }
}
