<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public function memberships()
    {
        return $this->hasMany(Membership::class);
    }

    public function expenses() // dépenses payées par l'user
    {
        return $this->hasMany(Expense::class, 'payer_id');
    }

    public function settlementsFrom()
    {
        return $this->hasMany(Settlement::class, 'from_user_id');
    }

    public function settlementsTo()
    {
        return $this->hasMany(Settlement::class, 'to_user_id');
    }

public function hasActiveColocation(): bool
{
    return $this->memberships()
        ->whereHas('colocation', fn($q) => $q->where('status', 'active'))
        ->whereNull('left_at')
        ->exists();
}

public function colocations()
{
    return $this->belongsToMany(Colocation::class, 'memberships')
                ->withPivot('role', 'joined_at', 'left_at')
                ->withTimestamps();
}

public function activeColocations()
{
    return $this->colocations()->wherePivotNull('left_at');
}

public function ownedColocations()
{
    return $this->hasMany(Colocation::class, 'owner_id');
}

}
