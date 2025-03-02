<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Creazione della tabella map_pointers
        Schema::create('map_pointers', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->string('name');
            $table->string('url'); 
            $table->timestamps();
        });

        // Aggiunta della relazione nella tabella users
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('map_pointer_id')->nullable();
            $table->foreign('map_pointer_id')->references('id')->on('map_pointers')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['map_pointer_id']);
            $table->dropColumn('map_pointer_id');
        });

        Schema::dropIfExists('map_pointers');
    }
};
