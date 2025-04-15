<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('movements', function (Blueprint $table) {
            // Supprimer la colonne 'type' avant de la recréer
            $table->dropColumn('type');
        });

        Schema::table('movements', function (Blueprint $table) {
            // Recréer la colonne 'type' sous forme de string
            $table->string('type')->after('user_id'); 
        });
    }

    public function down(): void
    {
        Schema::table('movements', function (Blueprint $table) {
            $table->dropColumn('type'); // Supprimer la colonne 'type'

            // Recréer en ENUM pour annuler la migration
            $table->enum('type', ['entrée', 'sortie'])->after('user_id');
        });
    }
};
