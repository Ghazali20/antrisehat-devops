<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {$table->id();
            $table->string('name');$table->string('poli');
            $table->string('poli_prefix', 5);$table->string('practice_day'); // Contoh: Monday, Tuesday, dst.
            $table->time('start_time');$table->time('end_time');
            $table->integer('max_quota')->default(30);$table->timestamps();
        });

        Schema::create('queues', function (Blueprint $table) {$table->id();
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade');$table->string('poli');
            $table->string('queue_number'); // Contoh: A-001$table->integer('sequence_num');
            $table->string('patient_name');$table->date('queue_date');
            $table->enum('status', ['WAITING', 'CALLED', 'DONE', 'SKIPPED', 'RESET'])->default('WAITING');$table->timestamps();
        });

        Schema::create('appointments', function (Blueprint $table) {$table->id();
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade');$table->string('patient_name');
            $table->date('appointment_date');$table->time('appointment_time');
            $table->enum('status', ['SCHEDULED', 'COMPLETED', 'CANCELLED'])->default('SCHEDULED');$table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
        Schema::dropIfExists('queues');
        Schema::dropIfExists('doctors');
    }
};
