<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hods', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('first_name');
            $table->string('last_name');
            $table->string('hod_email')->unique();
            $table->string('hod_phone')->nullable();
            $table->unsignedBigInteger('campus_id');
            $table->string('department_code');
            $table->string('password');
            $table->timestamps();

            // Foreign keys
            $table->foreign('campus_id')->references('campus_id')->on('campuses')->onDelete('cascade');
            $table->foreign('department_code')->references('department_code')->on('departments')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hods');
    }
};