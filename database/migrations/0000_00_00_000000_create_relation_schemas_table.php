<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class CreateRelationSchemasTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create(Config::get('amethyst.relation-schema.data.relation-schema.table'), function (Blueprint $table) {
            $table->increments('id');
            $table->string('data');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type');
            $table->text('payload');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists(Config::get('amethyst.relation-schema.data.relation-schema.table'));
    }
}
