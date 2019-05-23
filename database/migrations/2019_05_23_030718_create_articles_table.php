<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('page_id')->index()->comment('文章編號');
            $table->unsignedBigInteger('list_id')->index()->comment('列表編號');
            $table->string('url', 512)->comment('網址');
            $table->string('media', 16)->comment('媒體');
            $table->string('category', 16)->nullable()->comment('版別');
            $table->dateTime('publish_time')->comment('發佈時間');
            $table->string('title', 64)->comment('標題');
            $table->text('body')->comment('內文');
            $table->string('authors', 32)->nullable()->comment('作者');
            $table->string('keywords', 128)->nullable()->comment('關鍵字');
            $table->dateTime('created_time')->comment('爬抓時間');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articles');
    }
}
