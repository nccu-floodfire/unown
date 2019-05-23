<?php

use Illuminate\Database\Seeder;
use App\Encoder;

class EncoderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ecd = new Encoder;
        $ecd->name = '測試';
        $ecd->article_list = '1:24,30';
        $ecd->note = '只是備註';
        $ecd->save();
    }
}
