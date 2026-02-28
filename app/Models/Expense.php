<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
    'colocation_id',
    'payer_id',
    'category_id',
    'title',
    'amount',
    'date',
  ];


public function getIndividualShare(): float
{
    $coloc = $this->colocation; 

    if (!$coloc) {
        return 0.0;
    }

    $activeCount = $coloc->getActiveMembersCount();

    if ($activeCount === 0) {
        return 0.0;
    }

    return round($this->amount / $activeCount, 2);
}
    
    public function colocation()
    {
        return $this->belongsTo(Colocation::class);
    }

    public function payer()
    {
        return $this->belongsTo(User::class, 'payer_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
