<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->increments('id');

            $table->string('lang')->index();
            $table->string('group')->index();
            $table->string('key')->index();
            $table->text('value')->nullable();
            $table->timestamps();


            $table->index(['lang', 'group']);
            $table->index(['lang', 'group', 'key']);
            $table->unique(['lang', 'key']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('translations');
    }
}
