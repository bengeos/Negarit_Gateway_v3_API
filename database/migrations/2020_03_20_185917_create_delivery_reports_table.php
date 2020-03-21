<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_reports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('delivery_type', \App\Models\DeliveryReport::DELIVERY_REPORT_TYPE);
            $table->string('message_id');
            $table->string('message_status')->nullable();
            $table->string('level')->nullable();
            $table->integer('delivered')->default(0);
            $table->integer('error')->default(0);
            $table->boolean('is_sent')->default(false);
            $table->integer('attempts')->default(0);
            $table->dateTime('process_time')->nullable();
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
        Schema::dropIfExists('delivery_reports');
    }
}
