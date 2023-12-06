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
        Schema::create('shows', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Category::class)->nullable()->constrained()->nullOnDelete();
            $table->string('name')->unique();
            $table->longText('description')->nullable();
            $table->string('link')->nullable();
            $table->json('owner')->nullable();
            $table->longText('subtitle')->nullable();
            $table->longText('summary')->nullable();
            $table->string('explicit')->nullable();
            $table->string('type')->nullable();
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
        Schema::dropIfExists('shows');
    }
};
