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


 
public function colocation()
{
    return $this->belongsTo(Colocation::class);
}
}
