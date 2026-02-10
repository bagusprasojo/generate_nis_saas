<?php

namespace Database\Seeders;

use App\Models\NisPattern;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@platform.test'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role' => User::ROLE_SUPER_ADMIN,
                'school_id' => null,
            ]
        );

        $school = School::firstOrCreate(
            ['code' => 'SCH01'],
            [
                'name' => 'SMP Contoh',
                'address' => 'Jl. Contoh No. 1',
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@sekolah.test'],
            [
                'name' => 'Admin Sekolah',
                'password' => Hash::make('password'),
                'role' => User::ROLE_SCHOOL_ADMIN,
                'school_id' => $school->id,
            ]
        );

        NisPattern::updateOrCreate(
            ['school_id' => $school->id],
            [
                'pattern' => '{SCHOOL_CODE}-{YEAR_SHORT}-{SEQ:4}',
                'reset_rule' => 'yearly',
            ]
        );
    }
}
