<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaUsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("media_uses", function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->uuid("media_id")->index();
            $table->morphs("subject");
            $table->timestamps();

            $table
                ->foreign("media_id")
                ->references("id")
                ->on("media")
                ->onUpdate("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("media_uses");
    }
}
