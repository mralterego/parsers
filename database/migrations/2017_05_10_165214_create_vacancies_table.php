<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVacanciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vacancies', function (Blueprint $table) {
            $table->increments('id');

            // Принадлежность к сайту
            $table->integer('site_id')->unsigned();
            $table->foreign('site_id')->references('id')->on('sites');

            //Заголовок новости
            $table->string('title');

            //seo
            $table->text('description')->nullable();
            $table->text('keywords')->nullable();

            //Краткая запись
            $table->text('intro')->nullable();

            //Полная новость
            $table->text('content')->nullable();

            //Параметры публикации
            $table->boolean('published')->default(false);
            $table->timestamp('published_at')->nullable();

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
        Schema::dropIfExists('vacancies');
    }
}
