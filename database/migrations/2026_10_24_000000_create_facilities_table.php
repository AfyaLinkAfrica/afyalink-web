<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // hospital, health_centre, dispensary, clinic
            $table->string('county');
            $table->string('sub_county')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('source'); // where this record came from, e.g. "synthetic"
            $table->timestamp('verified_at')->nullable(); // null = unverified
            $table->timestamps();

            $table->index('county');
            $table->index('type');
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};
