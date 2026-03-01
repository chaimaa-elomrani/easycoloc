<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Colocation extends Model
{
        
    protected $fillable = [
        'name',
        'owner_id',
        'status',
        'description',
    ];

    public function generateInvitationToken(){
        return Str::random(32);
    }

    public function createInvitation(string $email): Invitation{
        return $this->invitations()->create([
            'token' => $this->generateInvitationToken(),
            'email' => $email,
            'status' => 'pending',
            'expires_at' => now()->addDays(7),
        ]);    
        }


    public function owner(){
        return $this->belongsTo(User::class , 'owner_id');
    }

    public function members(){
        return $this->belongsToMany(User::class , 'memberships')->withPivot('role', 'joined_at' , 'left_at');
    }

    public function activeMembers()
{
    return $this->members()
        ->whereNull('left_at')
        ->where('memberships.role', 'member') 
        ->orWhere('memberships.user_id', $this->owner_id); 
}

    public function expenses(){
        return $this->hasMany(Expense::class);
    }

    public function categories(){
        return $this->hasMany(Category::class);
    }

    public function settlements(){
        return $this->hasMany(Settlement::class);
    }

    public function invitations(){
        return $this->hasMany(Invitation::class);
    }

    public function getActiveMembersCount(): int
    {
        return $this->activeMembers()->count();
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function memberships(){
        return $this->hasMany(Membership::class);
    }


    }
