<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id');
            $table->string('bill');
            $table->string('bill_type');
            $table->string('bill_batch');
            $table->date('payment_date');
            $table->decimal('cost', 14, 2)->nullable();  
            $table->text('notes')->nullable();
            $table->string('payment_status')->nullable();            
            $table->timestamps('paid_date');
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
        Schema::dropIfExists('histories');
    }
}
