<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * Таблица для описания параметров разделов и страниц
         */
        Schema::create('pages', function (Blueprint $table) {
            $table->increments('id');

            // Принадлежность к сайту
            $table->integer('site_id')->unsigned();
            $table->foreign('site_id')->references('id')->on('sites');

            //тип страницы/раздела
            $table->string('type');
            $table->text('title');

            //название пункта меню
            $table->text('menu_title')->nullable();

            $table->text('description')->nullable();
            $table->text('keywords')->nullable();

            //блок особых скриптов в заголовок раздела
            $table->text('include_head')->nullable();
            $table->text('include_footer')->nullable();

            //вводный текст
            $table->text('intro')->nullable();
            $table->text('content')->nullable();

            //Статус публикации страницы
            $table->boolean('published')->default(false);

            //конфиг с особыми параметрами страницы
            $table->json('config')->nullable();

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
        Schema::dropIfExists('pages');
    }
}
