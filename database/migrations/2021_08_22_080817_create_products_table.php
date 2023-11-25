<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->integer('seller_id');
            $table->integer('category_id');
            $table->string('sub_category')->nullable();
            $table->text('image');
            $table->integer('price');
            $table->integer('available_qty');
            $table->enum('is_featured',[0,1])->default(0)->comment('0-No;1-Yes;');
            $table->enum('status',[0,1])->default(1)->comment('0-disable;1-enable;');
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
        Schema::dropIfExists('products');
    }
}
