<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'date', 'time_in', 'time_out', 'status', 'match_distance'
    ];

    protected $casts = [
        'date' => 'date',
        'match_distance' => 'decimal:4',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
