<?php
// app/Models/Service.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'duration',
        'is_active',
        'availability_days',
        'start_time',
        'end_time',
        'slot_interval',
        'max_bookings_per_slot',
        'advance_booking_days',
    ];

    protected $casts = [
        'availability_days' => 'array',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function blockedTimes()
    {
        return $this->hasMany(BlockedTime::class);
    }

    public function getAvailableSlots($date)
    {
        $date = Carbon::parse($date);
        $dayOfWeek = $date->dayOfWeek === 0 ? 7 : $date->dayOfWeek; // Convertir domingo de 0 a 7

        // Verificar si el día está disponible
        if (!in_array($dayOfWeek, $this->availability_days ?? [])) {
            return [];
        }

        $slots = [];
        $startTime = $date->copy()->setTimeFromTimeString($this->start_time);
        $endTime = $date->copy()->setTimeFromTimeString($this->end_time);

        while ($startTime->lt($endTime)) {
            $slotEnd = $startTime->copy()->addMinutes($this->duration);
            
            if ($slotEnd->lte($endTime)) {
                // Verificar disponibilidad
                if ($this->isSlotAvailable($startTime, $slotEnd)) {
                    $slots[] = [
                        'start' => $startTime->format('H:i'),
                        'end' => $slotEnd->format('H:i'),
                        'datetime' => $startTime->toDateTimeString(),
                    ];
                }
            }
            
            $startTime->addMinutes($this->slot_interval);
        }

        return $slots;
    }

    private function isSlotAvailable($startTime, $endTime)
    {
        // Verificar citas existentes
        $existingAppointments = $this->appointments()
            ->where('appointment_date', '>=', $startTime)
            ->where('appointment_date', '<', $endTime)
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        if ($existingAppointments >= $this->max_bookings_per_slot) {
            return false;
        }

        // Verificar tiempos bloqueados
        $blockedTimes = $this->blockedTimes()
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where('start_time', '<=', $startTime)
                      ->where('end_time', '>', $startTime);
            })
            ->orWhere(function ($query) use ($startTime, $endTime) {
                $query->where('start_time', '<', $endTime)
                      ->where('end_time', '>=', $endTime);
            })
            ->exists();

        return !$blockedTimes;
    }
}





// app/Models/AppointmentSetting.php
// namespace App\Models;

// use Illuminate\Database\Eloquent\Model;

// class AppointmentSetting extends Model
// {
//     protected $fillable = ['key', 'value', 'type'];

//     public static function get($key, $default = null)
//     {
//         $setting = static::where('key', $key)->first();
        
//         if (!$setting) {
//             return $default;
//         }

//         return match($setting->type) {
//             'boolean' => (bool) $setting->value,
//             'integer' => (int) $setting->value,
//             'json' => json_decode($setting->value, true),
//             default => $setting->value
//         };
//     }

//     public static function set($key, $value, $type = 'string')
//     {
//         if ($type === 'json') {
//             $value = json_encode($value);
//         }

//         return static::updateOrCreate(
//             ['key' => $key],
//             ['value' => $value, 'type' => $type]
//         );
//     }
// }