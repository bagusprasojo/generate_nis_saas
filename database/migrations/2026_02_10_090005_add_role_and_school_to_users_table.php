<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->enum('role', ['super_admin', 'school_admin'])->default('school_admin')->after('password');
            $table->softDeletes();

            $table->index('school_id');
            $table->index('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['school_id']);
            $table->dropIndex(['role']);
            $table->dropSoftDeletes();
            $table->dropConstrainedForeignId('school_id');
            $table->dropColumn('role');
        });
    }
};
