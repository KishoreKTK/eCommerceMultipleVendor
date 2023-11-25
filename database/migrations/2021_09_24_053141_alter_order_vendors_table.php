<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterOrderVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('order_vendors', function($table) {
            $table->unsignedBigInteger('orderid')->after('id');
            $table->unsignedBigInteger('sellerid');
            $table->unsignedBigInteger('productid');
            $table->string('prod_qty');
            $table->string('price_per_unit');
            $table->string('product_commission');
            $table->string('seller_commission');
            $table->string('seller_commission_perc');
            $table->string('seller_tax');
            $table->string('teller_tax_percent');
            $table->string('shipping_charges');
            $table->string('total_amount');
            $table->string('orderstatus');
            $table->datetime('orderupdatedt');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
