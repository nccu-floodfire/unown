<?php

use Illuminate\Database\Seeder;
use App\Result;

class ResultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rs = new Result;
        $rs->encoder_id = 1;
        $rs->article_id = 1;
        $rs->quote_content = 'test';
        $rs->save();
    }
}
