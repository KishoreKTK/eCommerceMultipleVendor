<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->enum('addr_title',['1','2','3'])->default('1');
            $table->string('first_name',255)->nullable();
            $table->string('last_name',255)->nullable();
            $table->string('phone_num',255)->nullable();
            $table->string('city',255)->nullable();
            $table->string('location',255)->nullable();
            $table->text('address')->nullable();
            $table->enum('same_addr',['0','1'])->default('0');
            $table->enum('billing_addr',['0','1'])->default('0');
            $table->enum('default_addr',['0','1'])->default('0');
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
        Schema::dropIfExists('user_addresses');
    }
}
