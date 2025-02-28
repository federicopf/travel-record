<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Relazione con users
            $table->string('title');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('trips');
    }
};
