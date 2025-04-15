<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('chantiers', function (Blueprint $table) {
            $table->renameColumn('ville', 'adresse');
        });
    }

    public function down(): void
    {
        Schema::table('chantiers', function (Blueprint $table) {
            $table->renameColumn('adresse', 'ville');
        });
    }
};
