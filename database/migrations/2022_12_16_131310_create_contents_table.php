<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Category;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('contentable');
            $table->string('title')->nullable();
            $table->longText('description')->nullable();
            $table->string('guid')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->string('enclosure_url')->nullable()->unique();
            $table->string('type')->nullable();
            $table->string('author')->nullable();
            $table->longText('subtitle')->nullable();
            $table->longText('summary')->nullable();
            $table->string('duration')->nullable();
            $table->string('explicit')->nullable();
            $table->integer('season')->nullable();
            $table->string('episode_type')->nullable();
            $table->string('image')->nullable();
            $table->string('image_url')->nullable();
            $table->string('file_driver')->nullable();
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
        Schema::dropIfExists('contents');
    }
};
