<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nis_patterns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('pattern');
            $table->enum('reset_rule', ['yearly', 'intake', 'never'])->default('never');
            $table->timestamps();
            $table->softDeletes();

            $table->unique('school_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nis_patterns');
    }
};
