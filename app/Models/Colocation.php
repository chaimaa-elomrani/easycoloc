<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Colocation extends Model
{
        
    protected $fillable = [
        'name',
        'owner_id',
        'status',
        'description',
    ];


    public function owner(){
        return $this->belongsTo(User::class , 'owner_id');
    }

    public function members(){
        return $this->belongsToMany(User::class , 'memberships')->withPivot('role', 'joined_at' , 'left_at');
    }

    public function activeMembers(){
    return $this->members()->whereNull('memberships.left_at');
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

    }
