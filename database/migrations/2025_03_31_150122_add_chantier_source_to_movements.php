<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('movements', function (Blueprint $table) {
            $table->foreignId('chantier_source_id')->nullable()->constrained('chantiers')->onDelete('cascade')->after('date');
        });
    }

    public function down(): void
    {
        Schema::table('movements', function (Blueprint $table) {
            $table->dropForeign(['chantier_source_id']);
            $table->dropColumn('chantier_source_id');
        });
    }
};
