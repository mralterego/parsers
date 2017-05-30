<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDistributorFilialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distributor_filials', function (Blueprint $table) {
            $table->increments('id');

            // Принадлежность к сайту
            $table->integer('site_id')->unsigned();
            $table->foreign('site_id')->references('id')->on('sites');

            // Идентификатор региона и название города, где работает филиал дистрибьютора
            $table->integer('region_id')->nullable();
            $table->string('city')->nullable();

            //наименование юр. лица
            $table->text('org')->nullable();

            //логотип и название компании в шапке сайта
            $table->text('header_logo')->nullable();
            $table->text('header_title')->nullable();
            $table->text('header_description')->nullable();

            // Контакты на странице контактов
            $table->json('contacts')->nullable();

            $table->boolean('published')->default(false);

            //порядок вывода
            $table->integer('sort')->nullable();

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
        Schema::dropIfExists('distributor_filials');
    }
}
