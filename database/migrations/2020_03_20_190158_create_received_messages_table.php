<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceivedMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('received_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('negarit_client_id');
            $table->string('message_id')->nullable();
            $table->string('sent_from');
            $table->string('sent_to');
            $table->string('coding')->nullable();
            $table->longText('message')->nullable();
            $table->boolean('is_sent')->default(false);
            $table->boolean('is_delivered')->default(false);
            $table->integer('attempts')->default(0);
            $table->string('description')->nullable();
            $table->dateTime('process_time')->nullable();
            $table->timestamps();
            $table->foreign('negarit_client_id')->references('id')->on('negarit_clients')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('received_messages');
    }
}
