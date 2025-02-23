<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('places', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('address')->nullable();
            $table->decimal('lat', 10, 7); // Precisione per le coordinate GPS
            $table->decimal('lng', 10, 7);
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('places');
    }
};
