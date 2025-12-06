<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::create('songs', function (Blueprint $table) {
        $table->id();
        $table->string('title');

        // album (boleh null kalau single)
        $table->foreignId('album_id')->nullable()->constrained()->nullOnDelete();

        // cover image
        $table->string('cover')->nullable();

        // file audio
        $table->string('audio_path');

        // metadata
        $table->integer('duration')->comment('in seconds'); // durasi lagu dalam detik
        $table->unsignedBigInteger('play_count')->default(0);
        $table->unsignedBigInteger('save_count')->default(0);

        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('songs');
    }
};
