<?php
// app/Models/BlockedTime.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockedTime extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'start_time',
        'end_time',
        'reason',
        'is_recurring',
        'recurrence_type',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_recurring' => 'boolean',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}