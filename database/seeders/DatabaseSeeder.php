<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $docAndi = Doctor::firstOrCreate(
            ['name' => 'dr. Andi Pratama', 'practice_day' => 'Monday'],
            [
                'poli' => 'Poli Umum',
                'poli_prefix' => 'A',
                'start_time' => '08:00:00',
                'end_time' => '14:00:00',
                'max_quota' => 30,
            ]
        );

        $docSiti = Doctor::firstOrCreate(
            ['name' => 'drg. Siti Rahma', 'practice_day' => 'Tuesday'],
            [
                'poli' => 'Poli Gigi',
                'poli_prefix' => 'G',
                'start_time' => '09:00:00',
                'end_time' => '15:00:00',
                'max_quota' => 30,
            ]
        );

        User::updateOrCreate(
            ['email' => 'operator@antrisehat.id'],
            [
                'name' => 'Admin Operator Klinik',
                'password' => Hash::make('operator123'),
                'role' => 'operator',
                'doctor_id' => null,
            ]
        );

        User::updateOrCreate(
            ['email' => 'dokter@antrisehat.id'],
            [
                'name' => 'dr. Andi Pratama',
                'password' => Hash::make('dokter123'),
                'role' => 'dokter',
                'doctor_id' => $docAndi->id,
            ]
        );

        User::updateOrCreate(
            ['email' => 'dokter.gigi@antrisehat.id'],
            [
                'name' => 'drg. Siti Rahma',
                'password' => Hash::make('dokter123'),
                'role' => 'dokter',
                'doctor_id' => $docSiti->id,
            ]
        );
    }
}
