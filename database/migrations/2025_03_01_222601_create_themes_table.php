<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('themes', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary(); 
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('color_scheme')->nullable();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('theme_id'); 
            $table->foreign('theme_id')->references('id')->on('themes')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['theme_id']);
            $table->dropColumn('theme_id');
        });

        Schema::dropIfExists('themes');
    }
};
