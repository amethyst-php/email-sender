<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class CreateEmailSendersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create(Config::get('amethyst.email-sender.managers.email-sender.table'), function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->integer('data_builder_id')->unsigned()->nullable();
            $table->foreign('data_builder_id')->references('id')->on(Config::get('amethyst.data-builder.managers.data-builder.table'));
            $table->string('subject')->nullable();
            $table->text('body')->nullable();
            $table->string('sender')->nullable();
            $table->text('recipients')->nullable();
            $table->text('attachments')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists(Config::get('amethyst.email-sender.managers.email-sender.table'));
    }
}
