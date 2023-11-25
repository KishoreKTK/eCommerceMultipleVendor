<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_attributes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attr_id');
            $table->string('sub_attr_name');
            $table->string('summary');
            $table->enum('custom',['0','1'])->comment("0-Readymade Attrubutes; 1-Custom Attributes");
            $table->enum('status',['0','1','2'])->comment('0-Active;1-Inactive;2-Deleted/Suspended');
            $table->timestamps();
            $table->foreign('attr_id')->references('id')->on('attributes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sub_attributes');
    }
}
