<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adding indexes for better query performance
     */
    public function up(): void
    {
        // Songs table indexes
        Schema::table('songs', function (Blueprint $table) {
            $table->index('title', 'idx_songs_title');
            $table->index('play_count', 'idx_songs_play_count');
            $table->index('created_at', 'idx_songs_created_at');
            $table->index('release_date', 'idx_songs_release_date');
            // Composite index for common queries
            $table->index(['album_id', 'created_at'], 'idx_songs_album_created');
        });

        // Albums table indexes
        Schema::table('albums', function (Blueprint $table) {
            $table->index('title', 'idx_albums_title');
            $table->index('created_at', 'idx_albums_created_at');
            $table->index('release_date', 'idx_albums_release_date');
            $table->index('year', 'idx_albums_year');
        });

        // Artists table indexes
        Schema::table('artists', function (Blueprint $table) {
            $table->index('name', 'idx_artists_name');
            $table->index('monthly_listeners', 'idx_artists_monthly_listeners');
        });

        // song_artist pivot table - composite index for faster joins
        Schema::table('song_artist', function (Blueprint $table) {
            $table->index(['song_id', 'artist_id'], 'idx_song_artist_composite');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->dropIndex('idx_songs_title');
            $table->dropIndex('idx_songs_play_count');
            $table->dropIndex('idx_songs_created_at');
            $table->dropIndex('idx_songs_release_date');
            $table->dropIndex('idx_songs_album_created');
        });

        Schema::table('albums', function (Blueprint $table) {
            $table->dropIndex('idx_albums_title');
            $table->dropIndex('idx_albums_created_at');
            $table->dropIndex('idx_albums_release_date');
            $table->dropIndex('idx_albums_year');
        });

        Schema::table('artists', function (Blueprint $table) {
            $table->dropIndex('idx_artists_name');
            $table->dropIndex('idx_artists_monthly_listeners');
        });

        Schema::table('song_artist', function (Blueprint $table) {
            $table->dropIndex('idx_song_artist_composite');
        });
    }
};
