<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('videos')) {
            return;
        }

        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->string('title');
            $table->string('slug')->nullable();
            $table->enum('type', ['video', 'music'])->default('video');
            $table->integer('firsts_views')->nullable();
            $table->integer('decay_rate')->nullable();
            $table->integer('media')->nullable();
            $table->date('published_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
