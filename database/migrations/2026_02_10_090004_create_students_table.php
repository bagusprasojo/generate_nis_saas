<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->year('intake_year');
            $table->string('nis');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['school_id', 'nis']);
            $table->index(['school_id', 'intake_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
