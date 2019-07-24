<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('results', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('encoder_id')->index()->comment('填寫人編號');
            $table->unsignedBigInteger('article_id')->index()->comment('文章編號');
            $table->text('quote_content')->comment('報導引述內容');
            $table->string('quote_origin', 128)->nullable()->comment('報導來源名稱');
            $table->string('quote_actual', 128)->nullable()->comment('報導來源本名');
            $table->enum('quote_pos', ['0', '1', '2'])->nullable()->comment('引述對象位置');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('results');
    }
}
