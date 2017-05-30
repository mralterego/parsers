<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->increments('id');

            //Название баннера
            $table->string('title');

            //Название используемого шаблона
            $table->string('template_name');

            //Слот где выводится баннер
            $table->string('slot_name');

            //Данные баннера
            $table->json('data');

            //Дата действия баннера
            $table->date('date_start');
            $table->date('date_end');

            //статус сайта
            $table->boolean('published')->default(false);

            //порядок вывода
            $table->integer('sort')->default(0);

            //id сайтов на которых скрыт данный баннер
            $table->json('hidden_site_ids');

            // Принадлежность к сайту
            $table->integer('site_id')->unsigned()->nullable();
            $table->foreign('site_id')->references('id')->on('sites');

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
        Schema::dropIfExists('banners');
    }
}
