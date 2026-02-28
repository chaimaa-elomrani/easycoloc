<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{

 protected $fillable = [
    'colocation_id',
    'token',
    'email',
    'status',
    'expires_at',
 ];

public function isValidForUser(User $user): bool
{
    return $this->status === 'pending'                          
        && $this->email === $user->email                        
        && (!$this->expires_at || $this->expires_at->isFuture()) 
        && !$user->hasActiveColocation();                       
}


public function accept(User $user){
    if(!$this->isValidForUser($user)){
        return  false ; 
    }

    $this->update([
        'status' => 'accepted',
        'expires_at' => now(),
    ]);

    $this->colocation->memberships()->create([
        'user_id' => $user->id, 
        'role' => 'member', 
        'joined_at' => now(),
    ]);
    return true ; 
}

public function refuse(){
    $this->update([
        'status' => 'refused',
        'expires_at' => now(),
    ]);
}
 
public function colocation()
{
    return $this->belongsTo(Colocation::class);
}
}
