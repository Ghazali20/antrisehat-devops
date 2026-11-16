<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    protected $fillable = [
        'doctor_id',
        'poli',
        'queue_number',
        'sequence_num',
        'patient_name',
        'queue_date',
        'status',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
