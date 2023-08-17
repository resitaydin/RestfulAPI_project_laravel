<?php

namespace Database\Seeders;

use App\Models\Campaign;
use Illuminate\Database\Seeder;

class CampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Campaign::create([
            'campaign_name' => 'Sabahattin Alinin Roman kitaplarında 2 üründen 1 tanesi bedava'
        ]);
        Campaign::create([
            'campaign_name' => 'Yerli Yazar Kitaplarında %5 indirim'
        ]);
        Campaign::create([
            'campaign_name' => '200 TL ve üzeri alışverişlerde sipariş toplamına %5 indirim'
        ]);
    }
}
