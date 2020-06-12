<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FileBase extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('fileMail');
            $table->string('fileName');
            $table->uuid('translator')->nullable();
            $table->string('sourceLang');
            $table->string('targetLang');
            $table->string('fileType');
            $table->longText('contentToTranslate');
            $table->longText('contentTranslate')->nullable();
            $table->string('state')->default('noTranslate');
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
        Schema::dropIfExists('files');
    }
}
