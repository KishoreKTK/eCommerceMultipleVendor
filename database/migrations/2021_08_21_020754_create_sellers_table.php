<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sellers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sellername');
            $table->string('selleremail')->unique();
            $table->string('sellerpassword');
            $table->string('sellerprofile')->nullable();
            $table->string('sellerabout')->nullable();
            $table->string('seller_buss_type');
            $table->string('seller_buss_cat');
            $table->string('seller_full_name_buss');
            $table->text('seller_trade_license');
            $table->date('seller_trade_exp_dt');
            $table->enum('approval',[0,1,2])->default(0)->comment('0-Pending;1-Approved;2-Rejected;');
            $table->integer('commission')->default(0);
            $table->text('remarks')->nullable();
            $table->enum('is_active',[0,1])->default(0)->comment('0-InActive;1-Active;');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sellers');
    }
}
