<?php

namespace Database\Seeders;

use App\Models\Campaign;
use Illuminate\Support\Facades\File;
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
        $json = File::get('database/data/campaigns.json');
        $campaigns = json_decode($json);

        foreach ($campaigns as $key => $value) {
            Campaign::create([
                'campaign_id' => $value->campaign_id,
                'campaign_name' => $value->campaign_name,
                'conditions' => $value->conditions,
                'discount_type' => $value->discount_type,
                'max_condition' => $value->max_condition,
                'min_condition' => $value->min_condition,
                'gift_condition' => $value->gift_condition,
            ]);
        }
    }
}