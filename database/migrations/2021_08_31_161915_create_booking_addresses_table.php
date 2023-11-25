<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_addresses', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id');
            $table->enum('addr_title',['1','2','3'])->default('1');
            $table->string('first_name',255)->nullable();
            $table->string('last_name',255)->nullable();
            $table->string('phone_num',255)->nullable();
            $table->string('city',255)->nullable();
            $table->string('location',255)->nullable();
            $table->text('address')->nullable();
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
        Schema::dropIfExists('booking_addresses');
    }
}
