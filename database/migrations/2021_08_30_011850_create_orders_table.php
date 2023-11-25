<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('address_id');
            $table->integer('order_status_id');
            $table->integer('tax')->nullable();
            $table->integer('shipping_charge')->nullable();
            $table->integer('discount')->nullable();
            $table->integer('promocode')->nullable();
            $table->integer('grand_total');
            $table->enum('payment_type',['1','2','3'])->default('1')
                    ->comment('1-Cash On Delivery; 2-Credit Card; 3-Debit Card;');
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
        Schema::dropIfExists('orders');
    }
}
