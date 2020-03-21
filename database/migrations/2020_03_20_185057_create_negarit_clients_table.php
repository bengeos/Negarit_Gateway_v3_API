<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNegaritClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('negarit_clients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('gateway_code')->unique();
            $table->string('company_name')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('unique_code')->nullable();
            $table->float('incoming_rate')->default(0);
            $table->float('outgoing_rate')->default(0);
            $table->string('short_code');
            $table->enum('port_type', \App\Models\NegaritClient::PORT_TYPE);
            $table->boolean('status')->default(false);
            $table->string('description')->nullable();
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
        Schema::dropIfExists('negarit_clients');
    }
}
