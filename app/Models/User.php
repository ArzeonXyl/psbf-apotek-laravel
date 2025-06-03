<?php

namespace App\Models;

// Laravel Core & Traits - Pastikan semua ini ada dan benar
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Penting untuk trait HasApiTokens

// Filament Specific
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser // Implementasikan FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable; // Gunakan traits

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Untuk Laravel 10+
    ];

    /**
     * Determine if the user can access the Filament admin panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Kita akan izinkan 'admin' dan 'kasir' mengakses panel dengan ID 'admin'
        // Nantinya kita bedakan apa yang mereka lihat di dalam panel.
        if ($panel->getId() === 'admin') {
            return in_array($this->role, ['admin', 'kasir']);
        }
        return false;
    }
}