<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->integer('campaign_id');
            $table->string('campaign_name');
            $table->json('conditions')->nullable();
            $table->integer('discount_type');
            $table->integer('max_condition');
            $table->integer('min_condition');
            $table->integer('gift_condition');
            $table->timestamps();
        }); 
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('campaigns');
    }
}
