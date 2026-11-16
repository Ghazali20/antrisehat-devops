<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $fillable = [
        'name',
        'poli',
        'poli_prefix',
        'practice_day',
        'start_time',
        'end_time',
        'max_quota',
    ];

    public function queues()
    {
        return $this->hasMany(Queue::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
