<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id');
            $table->string('bill');
            $table->string('bill_type');
            $table->string('bill_batch');
            $table->date('payment_date')->nullable();
            $table->decimal('cost', 14, 2)->nullable();  
            $table->text('notes')->nullable();
            $table->string('payment_status')->nullable();            
         #   $table->timestamps('paid_date')->null();
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
        Schema::dropIfExists('bills');
    }
}
