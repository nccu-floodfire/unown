<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEncodersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('encoders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 16)->comment('使用者名稱');
            $table->uuid('access_token')->comment('編碼員代碼');
            $table->text('article_list')->nullable()->comment('需編碼文章列表');
            $table->string('note', 256)->nullable()->comment('其他備註');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('encoders');
    }
}
