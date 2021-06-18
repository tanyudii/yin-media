<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("media", function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("name");
            $table->string("encoded_name");
            $table->double("size");
            $table->string("extension");
            $table->string("path");
            $table->string("disk");
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
        Schema::dropIfExists("media");
    }
}
