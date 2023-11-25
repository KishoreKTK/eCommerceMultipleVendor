<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderStatusTracksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_status_tracks', function (Blueprint $table) {
            $table->id();
            $table->string('sub_order_id');
            $table->string('order_status');
            $table->string('remarks')->nullable();
            $table->string('before_packing_imgs')->nullable();
            $table->string('after_packing_imgs')->nullable();
            $table->string('before_unpacking_imgs')->nullable();
            $table->string('after_unpacking_imgs')->nullable();
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
        Schema::dropIfExists('order_status_tracks');
    }
}
