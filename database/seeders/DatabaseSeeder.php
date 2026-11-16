<?php

namespace Database\Seeders;

use App\Models\Doctor;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Doctor::firstOrCreate(
            ['name' => 'dr. Andi Pratama', 'practice_day' => 'Monday'],
            [
                'poli' => 'Poli Umum',
                'poli_prefix' => 'A',
                'start_time' => '08:00:00',
                'end_time' => '14:00:00',
                'max_quota' => 30,
            ]
        );

        Doctor::firstOrCreate(
            ['name' => 'drg. Siti Rahma', 'practice_day' => 'Tuesday'],
            [
                'poli' => 'Poli Gigi',
                'poli_prefix' => 'G',
                'start_time' => '09:00:00',
                'end_time' => '15:00:00',
                'max_quota' => 30,
            ]
        );
    }
}
