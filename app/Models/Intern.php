<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//use Filament\Models\Contracts\FilamentUser;
//use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class Intern extends Authenticatable
{
    use Notifiable;
    protected $guarded = [];

    protected $fillable = [
        'intern_id', 'application_id', 'name', 'username', 'password','name', 'email', 'phone', 'college', 'degree',
        'last_exam_appeared', 'cgpa', 'domain', 'skills',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    // public function canAccessPanel(Panel $panel): bool
    // {
    //     // Allow access only to the 'intern' panel
    //     return $panel->getId() === 'intern'; 
    // }

    // Auto-generate the Intern ID when creating a new record
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($intern) {
            $year = date('YY'); // Gets current year, e.g., 2026
            
            // Count how many interns were created this year to determine the next number
            $latestIntern = static::whereYear('created_at', $year)->count();
            $nextNumber = $latestIntern + 1;
            
            // Pad with zeros (e.g., 001, 002, 010)
            $formattedNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            
            // Set the ID
            $intern->intern_id = "TS{$year}/WD/{$formattedNumber}";
            
            // Hash the password automatically
            if (isset($intern->password)) {
                $intern->password = Hash::make($intern->password);
            }
        });
    }

    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}
