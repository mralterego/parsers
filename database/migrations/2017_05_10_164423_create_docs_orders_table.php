<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocsOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('docs_orders', function (Blueprint $table) {
            $table->increments('id');

            // Принадлежность к сайту
            $table->integer('site_id')->unsigned();
            $table->foreign('site_id')->references('id')->on('sites');

            //Данные формы заказа
            $table->string('fullname')->nullable();
            $table->string('email')->nullable();
            $table->string('org')->nullable();
            $table->string('profession')->nullable();
            $table->string('phone')->nullable();
            $table->text('comment')->nullable();

            //Статус заявки
            $table->integer('status')->default(0);

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
        Schema::dropIfExists('docs_orders');
    }
}
