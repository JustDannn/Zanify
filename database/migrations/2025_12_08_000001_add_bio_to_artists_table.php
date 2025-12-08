<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('artists', function (Blueprint $table) {
            $table->text('bio')->nullable()->after('photo');
            $table->unsignedBigInteger('monthly_listeners')->default(0)->after('bio');
        });
    }

    public function down(): void
    {
        Schema::table('artists', function (Blueprint $table) {
            $table->dropColumn(['bio', 'monthly_listeners']);
        });
    }
};
